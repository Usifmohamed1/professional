@php
    Theme::set('pageTitle', __('My Wishlist'));
@endphp

<h1 class="d-none">{{ __('My Wishlist') }}</h1>

<div class="flat-section editorial-wishlist">
    @if($requiresLogin ?? false)
        <div class="text-center">
            <p class="body-2">{{ __('Please log in to view your wishlist.') }}</p>
            <a
                class="tf-btn primary"
                @if(theme_option('use_modal_for_authentication', true))
                    href="#modalLogin"
                    data-bs-toggle="modal"
                @else
                    href="{{ route('public.account.login') }}"
                @endif
            >
                {{ __('Login') }}
            </a>
        </div>
    @elseif($properties->isNotEmpty() || $projects->isNotEmpty())
        <section class="flat-recommended">
            @if($properties->isNotEmpty())
                <div class="box-title text-center">
                    <h2 class="section-title mt-4">{{ __('Your Favorite Properties') }}</h2>
                </div>

                @include(Theme::getThemeNamespace('views.real-estate.properties.index'))
            @endif
        </section>

        @if($projects->isNotEmpty())
            <section class="flat-section pb-0 flat-recommended">
                <div class="box-title text-center">
                    <h2 class="section-title mt-4">{{ __('Your Favorite Projects') }}</h2>
                </div>

                @include(Theme::getThemeNamespace('views.real-estate.projects.index'))
            </section>
        @endif
    @else
        <div class="editorial-empty-state">
            <x-core::icon name="ti ti-heart" />
            <h2>{{ __('A place for your favorites') }}</h2>
            <p>{{ __('You have not added any properties or projects to your wishlist.') }}</p>
            <a href="{{ RealEstateHelper::getPropertiesListPageUrl() }}" class="tf-btn primary">{{ __('Explore properties') }} <x-core::icon name="ti ti-arrow-up-right" /></a>
        </div>
    @endif
</div>
