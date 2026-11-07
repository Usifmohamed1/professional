@php
    use Botble\RealEstate\Facades\RealEstateHelper;

    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $backgroundImage = $backgroundImage ?: asset('storage/hero-2.webp');
    $titleColor = $shortcode->title_color ?: '#ffffff';
    $descriptionColor = $shortcode->description_color ?: '#ffffff';

    $sliderImages = [];
    for ($i = 1; $i <= 4; $i++) {
        $imageKey = "slider_image_{$i}";
        if ($shortcode->$imageKey) {
            $sliderImages[] = RvMedia::getImageUrl($shortcode->$imageKey);
        }
    }

    if ($backgroundImage) {
        array_unshift($sliderImages, $backgroundImage);
    }

    $sliderImages = array_filter($sliderImages);
    $rotationInterval = (int) ($shortcode->rotation_interval ?: 5);
    $enableRotation = $shortcode->enable_rotation ?: 'yes';

    $propertiesUrl = RealEstateHelper::getPropertiesListPageUrl();
    $selectedSearchType = request()->query('type') === 'rent' ? 'rent' : 'sale';

    Theme::asset()->container('footer')->usePath()->add('nouislider', 'js/nouislider.min.js');
    Theme::asset()->container('footer')->usePath()->add('wnumb', 'js/wNumb.min.js');
    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');
@endphp

<section class="flat-slider home-1 hero-banner-slider remax-home-hero"
         @style(["background-image: url('$backgroundImage') !important" => $backgroundImage])
         data-slider-images="{{ json_encode($sliderImages) }}"
         data-rotation-interval="{{ $rotationInterval }}"
         data-enable-rotation="{{ $enableRotation }}"
         style="transition: background-image 1s ease-in-out;">
    <div class="container relative remax-hero__container">
        <div class="remax-hero__inner">
            <div class="remax-hero__copy">
                <p class="remax-hero__eyebrow">
                    <span aria-hidden="true"></span>
                    {{ app()->getLocale() === 'ar' ? 'عقارات مختارة في الإسكندرية' : 'ALEXANDRIA, EGYPT · RE/MAX PROFESSIONAL' }}
                </p>
                <div class="heading">
                    <h1 class="animationtext slide" style="color: {{ $titleColor }} !important;">
                        {!! BaseHelper::clean($shortcode->title) !!}
                        {!! Theme::partial('shortcodes.hero-banner.partials.animation-text', compact('shortcode')) !!}
                    </h1>
                    @if ($shortcode->description)
                        <p class="subtitle body-1" style="color: {{ $descriptionColor }} !important;">
                            {!! BaseHelper::clean($shortcode->description) !!}
                        </p>
                    @endif
                </div>
                {!! Theme::partial('shortcodes.hero-banner.partials.action-button', ['shortcode' => $shortcode, 'class' => 'remax-hero__actions']) !!}
            </div>

            @if(is_plugin_active('real-estate') && $shortcode->search_box_enabled)
                <div class="remax-home-search flat-tab flat-tab-form" role="search" aria-label="{{ __('Search properties') }}">
                    <ul class="remax-home-search__tabs nav-tab-form style-1" role="tablist" aria-label="{{ __('Buy or rent') }}">
                        <li class="nav-tab-item" role="presentation">
                            <a
                                href="javascript:void(0);"
                                @class(['nav-link-item', 'active' => $selectedSearchType === 'sale'])
                                data-bs-toggle="tab"
                                data-bb-toggle="change-search-type"
                                data-value="sale"
                                data-url="{{ $propertiesUrl }}"
                                role="tab"
                                aria-selected="{{ $selectedSearchType === 'sale' ? 'true' : 'false' }}"
                            >{{ __('Buy') }}</a>
                        </li>
                        <li class="nav-tab-item" role="presentation">
                            <a
                                href="javascript:void(0);"
                                @class(['nav-link-item', 'active' => $selectedSearchType === 'rent'])
                                data-bs-toggle="tab"
                                data-bb-toggle="change-search-type"
                                data-value="rent"
                                data-url="{{ $propertiesUrl }}"
                                role="tab"
                                aria-selected="{{ $selectedSearchType === 'rent' ? 'true' : 'false' }}"
                            >{{ __('Rent') }}</a>
                        </li>
                    </ul>

                    <form action="{{ $propertiesUrl }}" method="get" id="hero-search-form" class="remax-home-search__form">
                        <input type="hidden" name="type" value="{{ $selectedSearchType }}">

                        <div class="remax-home-search__fields">
                            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.location'), ['style' => 1])
                            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.categories'), ['label' => __('Property type'), 'searchType' => $selectedSearchType])
                            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bedroom'))
                            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['maxPrice' => get_max_properties_price()])
                        </div>

                        <button type="submit" class="remax-home-search__submit tf-btn primary">
                            <x-core::icon name="ti ti-search" />
                            <span>{{ __('Search') }}</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
    <div class="overlay"></div>
</section>
