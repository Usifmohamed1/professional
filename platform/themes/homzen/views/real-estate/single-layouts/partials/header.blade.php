@php
    $model = $model ?? $property ?? null;
    $isProperty = $model instanceof \Botble\RealEstate\Models\Property;
    $isArabicProperty = $isProperty && str_starts_with(app()->getLocale(), 'ar');
    $currency = $isProperty ? ($model->currency ?: get_application_currency()) : null;
    $priceCurrency = $currency?->title;
    $isEnglishEgpProperty = $isProperty
        && str_starts_with(app()->getLocale(), 'en')
        && strtoupper((string) $priceCurrency) === 'EGP';
    $priceAmount = null;

    if (($isArabicProperty || $isEnglishEgpProperty) && $model->price) {
        $formattedPrice = format_price($model->price, $currency, false, false);

        if ($priceCurrency && str_ends_with($formattedPrice, $priceCurrency)) {
            $priceAmount = rtrim(substr($formattedPrice, 0, -strlen($priceCurrency)));
        }
    }
@endphp

<div class="header-property-detail">
    <div class="content-top d-flex justify-content-between align-items-center">
        <div class="box-name">
            {!! BaseHelper::clean($model->status_html) !!}
            <h1 class="h4 title">
                {!! BaseHelper::clean($model->name) !!}
            </h1>
        </div>

        @if (!setting('real_estate_hide_price', false) && (($model->price_html ?? null) || ($model->formatted_price ?? null)))
            <div class="box-price d-flex align-items-center">
                @if ($isArabicProperty && $priceAmount !== null)
                    <span class="h3 d-inline-flex align-items-baseline gap-2" dir="ltr">
                        <strong class="fw-bolder" style="font-weight: 900 !important;">{{ $priceAmount }}</strong>
                        <span class="fs-6 fw-semibold">{{ $priceCurrency }}</span>
                        @if ($model->type == \Botble\RealEstate\Enums\PropertyTypeEnum::RENT)
                            <span class="fs-6">/ {{ Str::lower($model->period->shortLabel()) }}</span>
                        @endif
                    </span>
                @elseif ($isEnglishEgpProperty && $priceAmount !== null)
                    <span class="h3 d-inline-flex align-items-baseline gap-2" dir="ltr">
                        <strong class="fw-bolder" style="font-weight: 900 !important;">{{ $priceAmount }}</strong>
                        <span class="fs-6 fw-semibold">{{ $priceCurrency }}</span>
                        @if ($model->type == \Botble\RealEstate\Enums\PropertyTypeEnum::RENT)
                            <span class="fs-6">/ {{ Str::lower($model->period->shortLabel()) }}</span>
                        @endif
                    </span>
                @else
                    <span @class(['h3' => $isProperty, 'h4' => ! $isProperty, 'fw-bolder' => $isProperty]) @style(['font-weight: 900 !important' => $isProperty])>{{ $model->price_html ?? $model->formatted_price }}</span>
                @endif
            </div>
        @endif
    </div>
    @include(Theme::getThemeNamespace('views.real-estate.partials.meta'), ['model' => $model])
</div>
