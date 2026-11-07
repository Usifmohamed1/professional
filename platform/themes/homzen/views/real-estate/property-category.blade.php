@php
    Theme::layout('full-width');
    Theme::set('pageTitle', $category->name);

    $mapQuery = ['category_id' => $category->id];

    if (in_array(request()->input('type'), array_keys(\Botble\RealEstate\Enums\PropertyTypeEnum::labels()), true)) {
        $mapQuery['type'] = request()->input('type');
    }
@endphp


@include(Theme::getThemeNamespace('views.real-estate.partials.listing'), [
    'actionUrl' => RealEstateHelper::getPropertiesListPageUrl(),
    'ajaxUrl' => route('public.properties'),
    'mapUrl' => route('public.ajax.properties.map') . '?' . http_build_query($mapQuery),
    'itemLayout' => request()->input('layout', 'grid'),
    'layout' => theme_option('real_estate_property_listing_layout', 'top-map'),
    'perPages' => RealEstateHelper::getPropertiesPerPageList(),
    'filterViewPath' => Theme::getThemeNamespace('views.real-estate.partials.filters.property-search-box'),
    'itemsViewPath' => Theme::getThemeNamespace('views.real-estate.properties.index'),
    'category' => $category,
])

@include(Theme::getThemeNamespace('views.real-estate.partials.property-map-content'))
