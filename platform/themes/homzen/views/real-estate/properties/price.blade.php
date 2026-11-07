@php
    $currency = $property->currency ?: get_application_currency();
    $currencyCode = $currency?->title;
    $useSuffixCurrency = $currencyCode
        && strtoupper((string) $currencyCode) === 'EGP'
        && (str_starts_with(app()->getLocale(), 'ar') || str_starts_with(app()->getLocale(), 'en'));
    $priceAmount = null;

    if ($useSuffixCurrency && $property->price) {
        $formattedPrice = format_price($property->price, $currency, false, false);

        if (str_ends_with($formattedPrice, $currencyCode)) {
            $priceAmount = rtrim(substr($formattedPrice, 0, -strlen($currencyCode)));
        }
    }

    $showPeriod ??= false;
@endphp

@if ($priceAmount !== null)
    <span class="d-inline-flex align-items-baseline gap-1" dir="ltr">
        <span>{{ $priceAmount }}</span>
        <span>{{ $currencyCode }}</span>
        @if ($showPeriod && $property->type == \Botble\RealEstate\Enums\PropertyTypeEnum::RENT)
            <span>/ {{ Str::lower($property->period->shortLabel()) }}</span>
        @endif
    </span>
@else
    {{ $showPeriod ? $property->price_html : $property->price_format }}
@endif