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

<div class="header-property-detail editorial-detail-heading">
    <p class="editorial-eyebrow">{{ $isProperty ? __('The property') : __('The development') }} <span aria-hidden="true">/</span> {{ $model->unique_id ?: $model->getKey() }}</p>
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

@if ($isProperty && ($model->number_bedroom || $model->number_bathroom || $model->square))
    <dl class="editorial-detail-specs">
        @if ($model->number_bedroom)
            <div><dt>{{ __('Bedrooms') }}</dt><dd>{{ fmod($model->number_bedroom, 1) == 0 ? number_format($model->number_bedroom) : $model->number_bedroom }}</dd></div>
        @endif
        @if ($model->number_bathroom)
            <div><dt>{{ __('Bathrooms') }}</dt><dd>{{ fmod($model->number_bathroom, 1) == 0 ? number_format($model->number_bathroom) : $model->number_bathroom }}</dd></div>
        @endif
        @if ($model->square)
            <div><dt>{{ __('Area') }}</dt><dd>{{ $model->square_text }}</dd></div>
        @endif
    </dl>
@endif
