@php
    Theme::layout('full-width');
    Theme::set('breadcrumbEnabled', 'no');

    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->container('footer')->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.js');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');

    $style = theme_option('real_estate_property_detail_layout', 1);
    $style = in_array($style, range(1, 4)) ? $style : 1;
    Theme::set('pageTitle', $property->name);
    Theme::set('currentProperty', $property);

    $firstImage = $property->images[0] ?? null;
    $headerMeta = '';
    if ($firstImage) {
        $headerMeta .= '<link rel="preload" as="image" href="' . RvMedia::getImageUrl($firstImage) . '">';
    }
    $headerMeta .= '<link rel="stylesheet" href="' . Theme::asset()->url('plugins/fancybox/jquery.fancybox.min.css') . '" media="print" onload="this.media=\'all\'">';
    $headerMeta .= '<noscript><link rel="stylesheet" href="' . Theme::asset()->url('plugins/fancybox/jquery.fancybox.min.css') . '"></noscript>';
    Theme::set('headerMeta', $headerMeta);
@endphp



<div class="property-detail-page editorial-detail-page">
    @include(Theme::getThemeNamespace("views.real-estate.single-layouts.style-$style"), ['model' => $property])
</div>

<template id="map-popup-content">
    <div class="map-listing-item">
        <div class="inner-box">
            <div class="image-box">
                <a href="{{ $property->url }}">
                    {{ RvMedia::image($property->image_thumb, $property->name) }}
                </a>
                {!! BaseHelper::clean($property->status_html) !!}
            </div>
            <div class="content">
                @if($property->short_address)
                    <p class="location">
                        <x-core::icon name="ti ti-map-pin" />
                        {{ $property->short_address }}
                    </p>
                @endif
                <div class="title">
                    <a href="{{ $property->url }}" title="{{ $property->name }}">
                        {{ $property->name }}
                    </a>
                </div>
                @if (!setting('real_estate_hide_price', false))
                    <div class="price">@include(Theme::getThemeNamespace('views.real-estate.properties.price'), ['property' => $property, 'showPeriod' => true])</div>
                @endif
                <ul class="list-info">
                    @if ($property->number_bedroom)
                        <li>
                            <x-core::icon name="ti ti-bed" />
                            {{ fmod($property->number_bedroom, 1) == 0 ? number_format($property->number_bedroom) : $property->number_bedroom }}
                        </li>
                    @endif

                    @if ($property->number_bathroom)
                        <li>
                            <x-core::icon name="ti ti-bath" />
                            {{ fmod($property->number_bathroom, 1) == 0 ? number_format($property->number_bathroom) : $property->number_bathroom }}
                        </li>
                    @endif

                    @if ($property->square)
                        <li>
                            <x-core::icon name="ti ti-ruler" />
                            {{ $property->square_text }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</template>
