@php
    Theme::asset()->container('footer')->usePath()->add('nouislider', 'js/nouislider.min.js');
    Theme::asset()->container('footer')->usePath()->add('wnumb', 'js/wNumb.min.js');
    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');

    $style ??= 1;
@endphp

@if($style === 'sidebar')
    <div class="flat-tab flat-tab-form widget-filter-search widget-box bg-surface">
        <div class="h7 title fw-7">{{ __('Search') }}</div>
        <div class="form-sl">
            <div class="wd-filter-select">
                <div class="inner-group inner-filter">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.keyword'))
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.location'), ['style' => 3])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.categories'), ['searchType' => 'property'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bathroom'), ['class' => 'form-style property-filter-land-hidden'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bedroom'), ['class' => 'form-style property-filter-land-hidden'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.floor'), ['class' => 'form-style property-filter-land-hidden property-filter-villa-only'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.types'), ['class' => 'form-style property-filter-land-hidden'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.projects'), ['class' => 'form-style property-filter-land-hidden'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['class' => 'form-style'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.square'), ['class' => 'form-style wd-price-2'])

                    <div class="form-style btn-show-advanced">
                        <a class="filter-advanced pull-right" href="#">
                            <x-core::icon name="ti ti-adjustments-alt" />
                            <span class="text-advanced">{{ __('Advanced') }}</span>
                        </a>
                    </div>
                    <div class="form-style wd-amenities property-filter-land-hidden">
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.features'), ['asGrid' => false])
                    </div>
                    <div class="form-style btn-hide-advanced">
                        <a class="filter-advanced pull-right" href="#">
                            <x-core::icon name="ti ti-adjustments-alt" />
                            <span class="text-advanced">{{ __('Hide Advanced') }}</span>
                        </a>
                    </div>
                    <div class="form-style mt-5">
                        <button type="submit" class="tf-btn primary">{{ __('Find Properties') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div @class(['flat-tab flat-tab-form', $class ?? null])>
        <div class="form-sl">
            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.base'))
            <div class="wd-search-form" data-bb-toggle="property-search-filters">
                <div class="grid-2 group-box group-price">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'))
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.square'))
                </div>
                <div class="group-box">
                    <div class="group-select grid-3">
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bathroom'), ['class' => 'property-filter-land-hidden'])
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bedroom'), ['class' => 'property-filter-land-hidden'])
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.floor'), ['class' => 'property-filter-land-hidden property-filter-villa-only'])
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.types'), ['class' => 'property-filter-land-hidden'])
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.projects'), ['class' => 'property-filter-land-hidden'])
                    </div>
                </div>
                <div class="property-filter-land-hidden">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.features'))
                </div>

                <button type="submit" class="tf-btn primary form-search-box-offcanvas-button mt-5">{{ __('Find Properties') }}</button>
            </div>
        </div>
    </div>
@endif
