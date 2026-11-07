<div class="editorial-listing-page">
    <section class="editorial-collection-intro container" aria-label="{{ Theme::get('pageTitle') }}">
        <p class="editorial-eyebrow">{{ __('RE/MAX Professional') }}</p>
        <div class="editorial-collection-heading">
            <h1>{{ Theme::get('pageTitle') }}</h1>
            <p>{{ __('Find the place that feels like you. Explore, refine, and make your next move.') }}</p>
        </div>
    </section>
    @include(Theme::getThemeNamespace("views.real-estate.listing-layouts.$layout"))
</div>

@php Theme::set('breadcrumbEnabled', 'no'); @endphp
