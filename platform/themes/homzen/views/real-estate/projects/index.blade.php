@php
    $itemLayout ??= request()->input('layout', 'grid');
    $itemLayout = in_array($itemLayout, ['grid', 'list']) ? $itemLayout : 'grid';

    $layout ??= theme_option('real_estate_project_listing_layout', 'top-map');

    if (! isset($itemsPerRow)) {
        $itemsPerRow = $itemLayout === 'grid' ? 3 : 2;
        if (! in_array($layout, ['top-map', 'without-map'])) {
            $itemsPerRow = $itemLayout === 'grid' ? 2 : 1;
        }
    }
@endphp

@if ($projects->isNotEmpty())
    @include(Theme::getThemeNamespace("views.real-estate.projects.$itemLayout"), compact('itemsPerRow'))
@else
    <div class="editorial-empty-state" role="status">
        <x-core::icon name="ti ti-search" />
        <h3>{{ __('No projects found.') }}</h3>
        <p>{{ __('Try a different location or adjust your filters to discover more options.') }}</p>
    </div>
@endif

@if ($projects instanceof \Illuminate\Pagination\LengthAwarePaginator && $projects->hasPages())
    <div class="justify-content-center wd-navigation">
        {{ $projects->onEachSide(1)->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
    </div>
@endif
