$(document).ready(() => {
    const normalizePhoneValue = (value) => (value || '').replace(/\s+/g, '')

    const syncPermalinkFromNames = () => {
        const firstName = $('input[name="first_name"]').val() || ''
        const lastName = $('input[name="last_name"]').val() || ''
        const permalink = `${firstName}-${lastName}`
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')

        const $slugInput = $('input[name="slug"]')

        if ($slugInput.length && permalink) {
            $slugInput.val(permalink)
            $('.slug-current').val(permalink)
        }
    }

    const normalizePhoneInputs = () => {
        $('input[name="phone"], input[name="whatsapp"]').each(function () {
            const $input = $(this)
            const value = normalizePhoneValue($input.val())

            if (value !== $input.val()) {
                $input.val(value)
            }
        })
    }

    $(document).on('input change blur', 'input[name="phone"], input[name="whatsapp"]', function () {
        const $input = $(this)
        const value = normalizePhoneValue($input.val())

        if (value !== $input.val()) {
            $input.val(value)
        }
    })

    $(document).on('submit', 'form', normalizePhoneInputs)
    normalizePhoneInputs()

    $(document).on('input change keyup blur', 'input[name="first_name"], input[name="last_name"]', () => {
        syncPermalinkFromNames()
        setTimeout(syncPermalinkFromNames, 100)
        setTimeout(syncPermalinkFromNames, 1000)
    })
    syncPermalinkFromNames()

    $(document).on('click', '#is_change_password', (event) => {
        if ($(event.currentTarget).is(':checked')) {
            $('input[type=password]').closest('.form-group').removeClass('hidden').fadeIn()
        } else {
            $('input[type=password]').closest('.form-group').addClass('hidden').fadeOut()
        }
    })

    $(document).on('click', '.btn-trigger-add-credit', (event) => {
        event.preventDefault()
        $('#add-credit-modal').modal('show')
    })

    $(document).on('click', '#confirm-add-credit-button', (event) => {
        event.preventDefault()
        const button = $(event.currentTarget)

        button.addClass('button-loading')

        $.ajax({
            type: 'POST',
            cache: false,
            url: button.closest('.modal-content').find('form').prop('action'),
            data: button.closest('.modal-content').find('form').serialize(),
            success: (response) => {
                if (!response.error) {
                    Botble.showNotice('success', response.message)
                    $('#add-credit-modal').modal('hide')
                    $('#credit-histories').load(`${$('.page-body form').prop('action')} #credit-histories > *`)
                } else {
                    Botble.showNotice('error', response.message)
                }
                button.removeClass('button-loading')
            },
            error: (response) => {
                Botble.handleError(response)
                button.removeClass('button-loading')
            },
        })
    })

    $(document).on('click', '.show-timeline-dropdown', (event) => {
        event.preventDefault()
        $($(event.currentTarget).data('target')).slideToggle()
        $(event.currentTarget).closest('.comment-log-item').toggleClass('bg-white')
    })

    $(document).on('click', '.verify-account-email-button', (event) => {
        event.preventDefault()
        $('#confirm-verify-account-email-button').data('action', $(event.currentTarget).prop('href'))
        $('#verify-account-email-modal').modal('show')
    })

    $(document).on('click', '#confirm-verify-account-email-button', (event) => {
        event.preventDefault()
        const button = $(event.currentTarget)

        button.addClass('button-loading')

        $.ajax({
            type: 'POST',
            cache: false,
            url: button.data('action'),
            success: (response) => {
                if (!response.error) {
                    Botble.showSuccess(response.message)
                    setTimeout(() => {
                        window.location.reload()
                    }, 2000)
                } else {
                    Botble.showError(response.message)
                }
                button.removeClass('button-loading')
                button.closest('.modal').modal('hide')
            },
            error: (response) => {
                Botble.handleError(response)
                button.removeClass('button-loading')
            },
        })
    })
})
