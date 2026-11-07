@php
    $currencies = get_all_currencies();
@endphp

@if (count($currencies) > 1)
    <div>{{ __('Currency') }}</div>
    <div class="currency-list-mobile">
        @php
            $currentCurrency = get_application_currency();
        @endphp
        @foreach ($currencies as $currency)
            <a @class(['currency-item', 'active' => get_application_currency_id() == $currency->id]) href="{{ route('public.change-currency', $currency->title) }}">
                <span class="currency-symbol">{{ $currency->symbol }}</span>
                {{ $currency->title }}
            </a>
        @endforeach
    </div>
@endif
