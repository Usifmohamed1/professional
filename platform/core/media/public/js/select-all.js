$(document)
    .off('click', '.js-media-select-all')
    .on('click', '.js-media-select-all', (event) => {
        event.preventDefault()

        const $files = $('.js-media-list-title[data-context="file"]')

        $files.find('input[type="checkbox"]').prop('checked', true)

        if ($files.length) {
            $(document).trigger($.Event('keydown', { ctrlKey: true }))
            $files.first().trigger('click')
            $(document).trigger($.Event('keyup', { ctrlKey: false }))
            $files.find('input[type="checkbox"]').prop('checked', true)
        }
    })