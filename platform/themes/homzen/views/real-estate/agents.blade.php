@php
    Theme::set('pageTitle', __('Agents'));

    if (theme_option('breadcrumb_background_image_agents')) {
        Theme::set('breadcrumbBackgroundImage', theme_option('breadcrumb_background_image_agents'));
    }
@endphp

<h1 class="d-none">{{ __('Agents') }}</h1>

<section class="flat-section flat-agents editorial-agents-page">
    <div class="container">
        <div class="editorial-collection-heading mb-5"><h2>{{ __('People behind your next move') }}</h2></div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
            @forelse($accounts as $account)
                <div class="box col mb-4">
                    <div class="box-agent hover-img wow fadeIn" data-wow-delay=".2s" data-wow-duration="2000ms">
                        <a href="{{ $account->url }}" class="box-img img-style mb-2" aria-label="{{ $account->name }}">
                            {{ RvMedia::image($account->avatar_url, $account->name) }}
                        </a>
                        <div class="content">
                            <div class="info">
                                <a href="{{ $account->url }}">
                                    <h6 class="link">{{ $account->name }} {!! $account->badge !!}</h6>
                                </a>
                                {!! Theme::partial('shortcodes.agents.partials.info', compact('account')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="editorial-empty-state">{{ __('No agents found.') }}</p>
            @endforelse
        </div>

    </div>
</section>
