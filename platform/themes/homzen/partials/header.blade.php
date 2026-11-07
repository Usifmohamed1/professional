@php
    $mobileContactItems = collect();

    if (Schema::hasTable('widgets')) {
        $themeName = Theme::getThemeName();
        $widgetThemeName = $themeName;

        if (is_plugin_active('language')) {
            $currentLocale = Language::getCurrentLocale();
            $defaultLocale = Language::getDefaultLocale();
            $supportedLocales = Language::getSupportedLocales();

            if ($currentLocale !== $defaultLocale && isset($supportedLocales[$currentLocale]['lang_code'])) {
                $widgetThemeName = $themeName . '-' . $supportedLocales[$currentLocale]['lang_code'];
            }
        }

        $siteInfoWidget = DB::table('widgets')
            ->where('theme', $widgetThemeName)
            ->where('sidebar_id', 'inner_footer_sidebar')
            ->where('widget_id', 'SiteInformationWidget')
            ->orderBy('position')
            ->first();

        if (! $siteInfoWidget && $widgetThemeName !== $themeName) {
            $siteInfoWidget = DB::table('widgets')
                ->where('theme', $themeName)
                ->where('sidebar_id', 'inner_footer_sidebar')
                ->where('widget_id', 'SiteInformationWidget')
                ->orderBy('position')
                ->first();
        }

        $siteInfoWidgetData = $siteInfoWidget ? json_decode($siteInfoWidget->data, true) : [];

        $mobileContactItems = collect($siteInfoWidgetData['items'] ?? [])
            ->map(function ($item) {
                return collect($item)->pluck('value', 'key')->all();
            })
            ->filter(fn ($item) => in_array($item['icon'] ?? null, ['ti ti-phone-call', 'ti ti-phone', 'ti ti-mail']) && filled($item['text'] ?? null))
            ->values();
    }

    if ($mobileContactItems->isEmpty()) {
        if ($hotline = theme_option('hotline')) {
            $mobileContactItems->push(['icon' => 'ti ti-phone', 'text' => $hotline]);
        }

        if ($email = theme_option('email')) {
            $mobileContactItems->push(['icon' => 'ti ti-mail', 'text' => $email]);
        }
    }

    $mobileMenuDirection = is_plugin_active('language')
        ? (Language::getCurrentLocaleRTL() ? 'rtl' : 'ltr')
        : BaseHelper::siteLanguageDirection();
@endphp

<header
    id="header"
    @class(['main-header', 'editorial-header', 'fixed-header' => theme_option('sticky_header_enabled', true), Theme::get('headerClass')])
>
    <div class="header-lower">
        <div class="row">
            <div class="col-lg-12">
                <div class="inner-container d-flex justify-content-between align-items-center">
                    <div class="logo-box">
                        <div class="logo">
                            <a href="{{ BaseHelper::getHomepageUrl() }}">
                                @if (theme_option('logo'))
                                    {{ Theme::getLogoImage(maxHeight: 44) }}
                                @else
                                    <span class="editorial-wordmark"><strong>RE<span>/</span>MAX</strong><small>PROFESSIONAL</small></span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="nav-outer">
                        <nav class="main-menu show navbar-expand-md" aria-label="{{ __('Main navigation') }}">
                            <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                {!! Menu::renderMenuLocation('main-menu', [
                                    'options' => ['class' => 'navigation clearfix'],
                                    'view' => 'main-menu',
                                ]) !!}
                            </div>
                        </nav>
                    </div>
                    <div class="header-account">
                        @if (is_plugin_active('real-estate') && RealEstateHelper::isLoginEnabled())
                            <div class="flat-bt-top">
                                <a class="tf-btn primary" href="{{ route('public.account.properties.index') }}">{{ __('Submit Property') }}</a>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="mobile-nav-toggler mobile-button" aria-label="{{ __('Open menu') }}" aria-expanded="false" aria-controls="editorial-mobile-menu">
                        <x-core::icon name="ti ti-menu-2" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="close-btn" aria-label="{{ __('Close menu') }}">
        <x-core::icon name="ti ti-x" />
    </button>

    <div id="editorial-mobile-menu" class="mobile-menu" dir="{{ $mobileMenuDirection }}">
        <div class="menu-backdrop"></div>
        <nav class="menu-box">
            <div class="nav-logo">
                <a href="{{ BaseHelper::getHomepageUrl() }}">
                    @if (theme_option('logo'))
                                    {{ Theme::getLogoImage(maxHeight: 44) }}
                                @else
                                    <span class="editorial-wordmark"><strong>RE<span>/</span>MAX</strong><small>PROFESSIONAL</small></span>
                                @endif
                </a>
            </div>
            <div class="bottom-canvas">
                @if (is_plugin_active('real-estate') && RealEstateHelper::isLoginEnabled())
                    <div class="mobile-add-listing-wrapper">
                        <a href="{{ route('public.account.properties.index') }}" class="mobile-add-listing-btn">
                            <div class="add-listing-icon-wrapper">
                                <x-core::icon name="ti ti-plus" />
                            </div>
                            <div class="add-listing-text-wrapper">
                                <span class="add-listing-title">{{ __('Submit Property') }}</span>
                            </div>
                            <x-core::icon name="ti ti-arrow-right" class="add-listing-arrow" />
                        </a>
                    </div>

                    @auth('account')
                        @php $customer = auth('account')->user(); @endphp
                        <div class="mobile-user-wrapper">
                            <a href="{{ route('public.account.dashboard') }}" class="mobile-user-btn">
                                <div class="user-avatar-wrapper">
                                    {{ RvMedia::image($customer->avatar_url, $customer->name, attributes: ['class' => 'user-avatar']) }}
                                </div>
                                <div class="user-text-wrapper">
                                    <span class="user-name">{{ $customer->name }}</span>
                                    <span class="user-subtitle">{{ __('View dashboard') }}</span>
                                </div>
                                <x-core::icon name="ti ti-chevron-right" class="user-arrow" />
                            </a>
                        </div>
                    @else
                        <div class="mobile-signin-wrapper">
                            <a
                                @if (theme_option('use_modal_for_authentication', true))
                                    href="#modalLogin"
                                    data-bs-toggle="modal"
                                @else
                                    href="{{ route('public.account.login') }}"
                                @endif
                                class="mobile-signin-btn"
                            >
                                <div class="signin-icon-wrapper">
                                    <x-core::icon name="ti ti-user" />
                                </div>
                                <div class="signin-text-wrapper">
                                    <span class="signin-title">{{ __('Sign in') }}</span>
                                    <span class="signin-subtitle">{{ __('Access your account') }}</span>
                                </div>
                                <x-core::icon name="ti ti-chevron-right" class="signin-arrow" />
                            </a>
                        </div>
                    @endauth
                @endif

                <div class="menu-outer"></div>

                @php $mobileSwitcherStyle = theme_option('mobile_menu_switcher_style', 'inline'); @endphp

                <div class="mobi-icon-box">
                    @if (is_plugin_active('real-estate'))
                        @if (RealEstateHelper::isEnabledWishlist())
                            <div class="box">
                                <a href="{{ route('public.wishlist') }}">
                                    {{ __('My Wishlist') }}
                                    (<span data-bb-toggle="wishlist-count" class="fw-medium">0</span>)
                                </a>
                            </div>
                        @endif
                    @endif

                    @if ($mobileSwitcherStyle === 'dropdown')
                        @if (is_plugin_active('real-estate'))
                            <div class="box">
                                {!! Theme::partial('currency-switcher') !!}
                            </div>
                        @endif

                        @if ($languageSwitcher = Theme::partial('language-switcher'))
                            <div class="box">
                                {!! $languageSwitcher !!}
                            </div>
                        @endif
                    @endif

                    @foreach($mobileContactItems as $contactItem)
                        <div class="box d-flex align-items-center gap-2">
                            @php
                                $isEmail = ($contactItem['icon'] ?? null) === 'ti ti-mail' || filter_var($contactItem['text'], FILTER_VALIDATE_EMAIL);
                                $contactHref = $isEmail ? 'mailto:' . $contactItem['text'] : 'tel:' . preg_replace('/[^0-9+]/', '', $contactItem['text']);
                            @endphp

                            <x-core::icon :name="$isEmail ? 'ti ti-mail' : 'ti ti-phone'" style="width: 1.25rem; height: 1.25rem" />
                            <a href="{{ $contactHref }}" title="{{ $isEmail ? __('Email') : __('Phone') }}" dir="ltr">{{ $contactItem['text'] }}</a>
                        </div>
                    @endforeach
                </div>

                @if ($mobileSwitcherStyle === 'inline')
                    <div class="mobile-switchers">
                        @if (is_plugin_active('real-estate'))
                            {!! Theme::partial('currency-switcher-mobile') !!}
                        @endif
                        {!! Theme::partial('language-switcher-mobile') !!}
                    </div>
                @endif
            </div>
        </nav>
    </div>
</header>
