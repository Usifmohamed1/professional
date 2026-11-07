<section class="editorial-section editorial-pricing-plan flat-section flat-pricing">
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        @if ($packages->isEmpty())
            <div class="free-pricing-wrap">
                <div class="free-pricing-card">
                    <div class="free-pricing-main">
                        <div class="free-pricing-badge">{{ __('Free') }}</div>

                        <div class="free-pricing-content">
                            <h3>{{ __('Publishing is free now') }}</h3>
                            <p>{{ __('You can add properties without buying a package.') }}</p>
                        </div>

                        <ul class="free-pricing-list">
                            <li>
                                <span class="check-icon icon-tick"></span>
                                {{ __('No package is required') }}
                            </li>
                            <li>
                                <span class="check-icon icon-tick"></span>
                                {{ __('No credits are deducted') }}
                            </li>
                        </ul>

                        <a href="{{ auth('account')->check() ? route('public.account.properties.create') : route('public.account.login') }}" class="tf-btn primary free-pricing-action">
                            {{ auth('account')->check() ? __('Submit Property') : __('Sign in') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($packages as $package)
                    <div class="box col-lg-{{ max(round(12 / $packages->count()), 3) }} col-md-6 g-4">
                        <div @class(['box-pricing', 'active' => $package->is_default])>
                            <div class="price d-flex align-items-end">
                                <span class="h4">{{ $package->price == 0 ? __('Free') : format_price($package->price) }}</span>
                                <span class="body-2 text-variant-1">
                                    /
                                    @if ($package->number_of_listings === 1)
                                        {{ __('1 post') }}
                                    @else
                                        {{ __(':number posts', ['number' => number_format($package->number_of_listings)]) }}
                                    @endif
                                </span>
                            </div>
                            <div class="box-title-price">
                                <h6 class="title">{!! BaseHelper::clean($package->name) !!}</h6>
                                @if ($package->description)
                                    <p class="desc">{{ $package->description }}</p>
                                @endif
                            </div>
                            @if ($package->formatted_features)
                                <ul class="list-price">
                                    @foreach ($package->formatted_features as $feature)
                                        <li class="item">
                                            <span class="check-icon icon-tick"></span>
                                            {!! BaseHelper::clean($feature) !!}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <a
                                href="{{ route('public.account.packages') }}"
                                class="tf-btn"
                            >
                                {{ __('Choose The Package') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
