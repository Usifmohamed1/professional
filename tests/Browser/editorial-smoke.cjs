#!/usr/bin/env node
/**
 * Read-only editorial UI smoke checks against a running local preview.
 * Requires existing Botble page/shortcode/menu/property fixtures, required plugins,
 * published assets, and a migrated database. This script never creates fixtures,
 * signs in, sends inquiries, or mutates application records.
 *
 * BASE_URL=http://127.0.0.1:8001 node tests/Browser/editorial-smoke.cjs
 * Optional: CHROMIUM_PATH, HOME_PATH, LISTING_PATH, LOGIN_PATH, SEARCH_TERM,
 *           SCREENSHOT_DIR (screenshots are omitted by default).
 * Listing filtering uses the existing read-only search endpoint, which uses POST.
 */
const assert = require('node:assert/strict')
const fs = require('node:fs/promises')
const path = require('node:path')
const puppeteer = require('puppeteer')

const base = new URL(process.env.BASE_URL || 'http://127.0.0.1:8001')
assert(['127.0.0.1', 'localhost', '[::1]'].includes(base.hostname), 'Use a local preview; this script does not exercise production forms.')
const routes = {
    home: process.env.HOME_PATH || '/',
    listing: process.env.LISTING_PATH || '/properties',
    login: process.env.LOGIN_PATH || '/login',
}
const screenshotDir = process.env.SCREENSHOT_DIR
const results = []
let browser

async function main() {
    if (screenshotDir) await fs.mkdir(screenshotDir, { recursive: true })
    browser = await puppeteer.launch({
        executablePath: process.env.CHROMIUM_PATH || '/usr/bin/chromium',
        headless: true,
        args: ['--no-sandbox', '--disable-dev-shm-usage'],
    })
    const page = await browser.newPage()
    const pageErrors = []
    const consoleErrors = new Set()
    const failedRequests = new Set()
    page.on('pageerror', error => pageErrors.push(error.message))
    page.on('requestfailed', request => {
        const url = new URL(request.url())
        // Omit query strings and fragments; diagnostic URLs can contain tokens.
        failedRequests.add(`${url.origin}${url.pathname}: ${request.failure()?.errorText}`)
    })
    page.on('console', message => {
        if (message.type() === 'error') consoleErrors.add(message.text())
    })
    await page.setRequestInterception(true)
    page.on('request', request => {
        const url = new URL(request.url())
        const readOnlySearch = url.origin === base.origin && url.pathname === new URL(routes.listing, base).pathname
        if (!['GET', 'HEAD', 'OPTIONS'].includes(request.method()) && !readOnlySearch) {
            return request.abort('blockedbyclient')
        }
        request.continue()
    })

    const visible = element => !!(element.getClientRects().length && getComputedStyle(element).visibility !== 'hidden')
    for (const width of [1440, 390]) {
        await page.setViewport({ width, height: width === 390 ? 844 : 1000, deviceScaleFactor: 1 })
        for (const [name, route] of Object.entries(routes)) {
            const response = await page.goto(new URL(route, base).href, { waitUntil: 'networkidle2', timeout: 45000 })
            assert.equal(response.status(), 200, `${name} at ${width}px must return HTTP 200`)
            await page.evaluate(() => document.fonts.ready)
            const structure = await page.evaluate(() => ({
                viewport: document.documentElement.clientWidth,
                scrollWidth: document.documentElement.scrollWidth,
                headings: document.querySelectorAll('h1').length,
                postForms: [...document.querySelectorAll('form')]
                    .filter(form => form.method.toLowerCase() === 'post')
                    .map(form => !!form.querySelector('input[name="_token"][value]')?.value),
            }))
            assert(structure.scrollWidth <= structure.viewport + 1, `${name} at ${width}px overflows horizontally (${structure.scrollWidth}/${structure.viewport})`)
            assert.equal(structure.headings, 1, `${name} should have one h1`)
            assert(structure.postForms.every(Boolean), `${name} must preserve CSRF tokens in POST forms`)
            if (name === 'login') assert(structure.postForms.length > 0, 'Login form must exist')
            if (screenshotDir) {
                // Trigger the actual lazy-media and intersection-reveal behavior before capture.
                await page.evaluate(async () => {
                    for (let top = 0; top < document.documentElement.scrollHeight; top += innerHeight * 0.8) {
                        scrollTo(0, top)
                        await new Promise(resolve => setTimeout(resolve, 100))
                    }
                    scrollTo(0, 0)
                    await new Promise(resolve => setTimeout(resolve, 300))
                })
                await page.screenshot({ path: path.join(screenshotDir, `${name}-${width}.png`), fullPage: true })
            }
            results.push(`${name}: HTTP 200, one h1, no overflow at ${width}px; CSRF retained`)
        }
        if (width === 390) {
            await page.goto(new URL(routes.home, base).href, { waitUntil: 'networkidle2', timeout: 45000 })
            const toggle = '.mobile-nav-toggler'
            await page.waitForSelector(toggle, { visible: true })
            await page.click(toggle)
            await page.waitForFunction(() => document.body.classList.contains('mobile-menu-visible'))
            assert.equal(await page.$eval(toggle, element => element.getAttribute('aria-expanded')), 'true', 'Menu toggle must announce expanded state')
            await page.keyboard.press('Escape')
            await page.waitForFunction(() => !document.body.classList.contains('mobile-menu-visible'))
            assert.equal(await page.$eval(toggle, element => element.getAttribute('aria-expanded')), 'false', 'Escape must announce collapsed state')
            assert(await page.$eval(toggle, element => document.activeElement === element), 'Escape must restore focus to the menu toggle')
            results.push('Mobile menu: opens, announces state, closes with Escape, restores focus')
        }
    }

    // Use an actual fixture title when available rather than inventing a listing.
    await page.setViewport({ width: 1440, height: 1000, deviceScaleFactor: 1 })
    await page.goto(new URL(routes.listing, base).href, { waitUntil: 'networkidle2', timeout: 45000 })
    const firstListing = await page.$eval('.editorial-card-title a', element => element.textContent.trim()).catch(() => '')
    const searchTerm = process.env.SEARCH_TERM || firstListing
    if (searchTerm) {
        const input = await page.$('form.filter-form input[name="k"]')
        assert(input, 'Listing keyword input must exist')
        assert(await input.evaluate(visible), 'Keyword input must be visible')
        await input.click({ clickCount: 3 })
        await input.type(searchTerm)
        const searchResponse = page.waitForResponse(response => {
            const url = new URL(response.url())
            return url.origin === base.origin && url.pathname === new URL(routes.listing, base).pathname && response.request().method() === 'POST'
        }, { timeout: 20000 })
        await input.evaluate(element => element.form.requestSubmit())
        const response = await searchResponse
        assert.equal(response.status(), 200, 'Keyword search must return HTTP 200')
        const requestBody = new URLSearchParams(response.request().postData() || '')
        assert.equal(requestBody.get('k'), searchTerm, 'Search request must preserve the typed keyword')
        assert(requestBody.get('_token'), 'Search request must retain CSRF token')
        await page.waitForFunction(term => new URL(location.href).searchParams.get('k') === term, { timeout: 20000 }, searchTerm)
        await page.waitForFunction(() => !document.querySelector('[data-bb-toggle="data-listing"] .loading-spinner'))
        if (!process.env.SEARCH_TERM) {
            assert(await page.$eval('[data-bb-toggle="data-listing"]', (element, title) => element.textContent.includes(title), firstListing), 'Matching fixture listing must remain in filtered results')
        }
        results.push('Listing keyword filter: real fixture query, HTTP 200, CSRF retained, URL/results updated')
    } else {
        results.push('SKIPPED keyword search: no property fixture or SEARCH_TERM supplied')
    }

    await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'reduce' }])
    await page.goto(new URL(routes.home, base).href, { waitUntil: 'networkidle2', timeout: 45000 })
    const motion = await page.evaluate(() => {
        const hero = document.querySelector('.editorial-hero')
        const heading = hero?.querySelector('h1')
        const word = heading?.querySelector('.is-visible')
        return {
            hero: !!hero,
            visible: !!heading && getComputedStyle(heading).visibility !== 'hidden' && Number(getComputedStyle(heading).opacity) > 0,
            pending: !!hero?.querySelector('.is-pending'),
            visibleWord: !word || (getComputedStyle(word).visibility !== 'hidden' && Number(getComputedStyle(word).opacity) > 0),
        }
    })
    assert(motion.hero && motion.visible && motion.visibleWord && !motion.pending, 'Reduced-motion hero must keep its heading and visible word readable without pending reveals')
    results.push('Reduced motion: hero content remains visible with no pending reveal')
    assert.deepEqual(pageErrors, [], 'Pages must not throw uncaught JavaScript errors')
    console.log(results.join('\n'))
    console.log(`PASS: ${results.length} browser smoke checks; no uncaught page errors`)
    if (consoleErrors.size) console.log(`Console messages requiring review (${consoleErrors.size}):\n${[...consoleErrors].join('\n')}`)
    if (failedRequests.size) console.log(`Failed asset requests requiring review (${failedRequests.size}):\n${[...failedRequests].join('\n')}`)
}

main().catch(error => {
    console.error(`FAIL: ${error.message}`)
    process.exitCode = 1
}).finally(async () => {
    if (browser) await browser.close()
})
