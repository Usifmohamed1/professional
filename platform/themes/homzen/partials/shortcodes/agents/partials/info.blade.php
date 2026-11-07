<ul class="list-info">
    @if ($account->properties_count)
        <li>
            <x-core::icon name="ti ti-home" />
            @if ($account->properties_count === 1)
                {{ __('1 Property') }}
            @else
                {{ __(':count Properties', ['count' => $account->properties_count]) }}
            @endif
        </li>
    @endif

@if ($account->phone && ! setting('real_estate_hide_agency_phone', 0) && ! $account->hide_phone)
    @php
        $normalizedPhone = preg_replace('/[^0-9]/', '', $account->phone);
        if (str_starts_with($normalizedPhone, '0')) {
            $normalizedPhone = '20' . substr($normalizedPhone, 1);
        }
    @endphp
    <li>
        <a href="tel:+{{ $normalizedPhone }}">
            <x-core::icon name="ti ti-phone" />
            {{ preg_replace('/\s+/', '', $account->phone) }}
        </a>
    </li>

    @endif

        @if ($account->public_email && ! setting('real_estate_hide_agency_email', 0) && ! $account->hide_email)
        <li>
            <a href="mailto:{{ $account->public_email }}"><x-core::icon name="ti ti-mail" /> {{ $account->public_email }}</a>
        </li>
    @endif

        @if ($account->address)
        <li><x-core::icon name="ti ti-map-pin" /> {{ $account->address }}</li>
    @endif

    @if ($account->whatsapp)
        @php
            $normalizedWhatsapp = preg_replace('/[^0-9]/', '', $account->whatsapp);
            if (str_starts_with($normalizedWhatsapp, '0')) {
                $normalizedWhatsapp = '20' . substr($normalizedWhatsapp, 1);
            } elseif (! str_starts_with($normalizedWhatsapp, '2')) {
                $normalizedWhatsapp = '2' . $normalizedWhatsapp;
            }
        @endphp
        <li>
            <a href="https://wa.me/+{{ $normalizedWhatsapp }}" target="_blank" rel="noopener noreferrer">
                <x-core::icon name="ti ti-brand-whatsapp" /> {{ __('WhatsApp') }}
            </a>
        </li>
    @endif
</ul>
