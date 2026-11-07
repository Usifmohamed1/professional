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
        let _self = $(event.currentTarget)

        _self.addClass('button-loading')

        $.ajax({
            type: 'POST',
            cache: false,
            url: _self.closest('.modal-content').find('form').prop('action'),
            data: _self.closest('.modal-content').find('form').serialize(),
            success: (res) => {
                if (!res.error) {
                    Botble.showNotice('success', res.message)
                    $('#add-credit-modal').modal('hide')

                    $('#credit-histories').load(`${$('.page-body form').prop('action')} #credit-histories > *`)
                } else {
                    Botble.showNotice('error', res.message)
                }
                _self.removeClass('button-loading')
            },
            error: (res) => {
                Botble.handleError(res)
                _self.removeClass('button-loading')
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
        let _self = $(event.currentTarget)

        _self.addClass('button-loading')

        $.ajax({
            type: 'POST',
            cache: false,
            url: _self.data('action'),
            success: (res) => {
                if (!res.error) {
                    Botble.showSuccess(res.message)
                    setTimeout(() => {
                        window.location.reload()
                    }, 2000)
                } else {
                    Botble.showError(res.message)
                }
                _self.removeClass('button-loading')
                _self.closest('.modal').modal('hide')
            },
            error: (res) => {
                Botble.handleError(res)
                _self.removeClass('button-loading')
            },
        })
    })
})
