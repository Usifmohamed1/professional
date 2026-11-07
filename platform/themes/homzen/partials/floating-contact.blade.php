@php
    $phoneDigits = '201020622289';
    $whatsappDigits = '+201003039070';
@endphp

@if ($phoneDigits && $whatsappDigits)
    <div class="floating-contact" data-floating-contact>
        <button
            class="floating-contact__toggle"
            type="button"
            aria-label="{{ __('Open contact options') }}"
            aria-expanded="false"
            aria-controls="floating-contact-actions"
            data-open-label="{{ __('Open contact options') }}"
            data-close-label="{{ __('Close contact options') }}"
        >
            <svg class="floating-contact__chat-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                <path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H5l1.3-3.25A7.5 7.5 0 1 1 20 11.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 11.5h.01M12.5 11.5h.01M16 11.5h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
            </svg>
            <svg class="floating-contact__close-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="floating-contact__actions" id="floating-contact-actions">
            <a
                class="floating-contact__action floating-contact__action--whatsapp"
                href="https://wa.me/{{ $whatsappDigits }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="{{ __('WhatsApp') }}"
                aria-hidden="true"
                tabindex="-1"
            >
                <span class="floating-contact__label">{{ __('WhatsApp') }}</span>
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.04 2a9.9 9.9 0 0 0-8.45 15.07L2.3 21.7l4.75-1.25A9.9 9.9 0 1 0 12.04 2Zm0 18.05a8.1 8.1 0 0 1-4.13-1.13l-.3-.18-2.82.74.75-2.75-.2-.31a8.14 8.14 0 1 1 6.7 3.63Zm4.47-6.1c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.55.12-.16.24-.63.78-.77.94-.14.16-.28.18-.52.06-.24-.12-1.01-.37-1.93-1.19-.72-.64-1.2-1.42-1.34-1.66-.14-.24-.02-.37.1-.49.11-.1.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.55-1.32-.75-1.81-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.69 2.58 4.1 3.62.57.25 1.01.4 1.35.51.57.18 1.09.16 1.5.1.46-.07 1.42-.58 1.62-1.14.2-.57.2-1.05.14-1.15-.06-.1-.22-.16-.46-.28Z"/>
                </svg>
            </a>

            <a
                class="floating-contact__action floating-contact__action--phone"
                href="tel:+{{ $phoneDigits }}"
                aria-label="{{ __('Phone Call') }}"
                aria-hidden="true"
                tabindex="-1"
            >
                <span class="floating-contact__label">{{ __('Phone Call') }}</span>
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path d="M5 4.75A1.75 1.75 0 0 1 6.75 3h1.1a1.75 1.75 0 0 1 1.7 1.34l.54 2.16a1.75 1.75 0 0 1-.5 1.7l-1.1 1.1a14.1 14.1 0 0 0 4.01 4.01l1.1-1.1a1.75 1.75 0 0 1 1.7-.5l2.16.54A1.75 1.75 0 0 1 18.8 14v1.1A1.75 1.75 0 0 1 17.05 16.85h-.8C9.48 16.85 4 11.37 4 4.6v-.8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>

    <script src="{{ Theme::asset()->url('js/floating-contact.js') }}" defer></script>
@endif
