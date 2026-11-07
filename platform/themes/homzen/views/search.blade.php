@php
    $searchQuery = e(BaseHelper::stringify(request()->input('q')));
    Theme::set('pageTitle', __('Search result for: ":query"', ['query' => $searchQuery]))
@endphp

<h1 class="d-none">{{ __('Search result for: ":query"', ['query' => $searchQuery]) }}</h1>

@include(Theme::getThemeNamespace('views.loop'))
