<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>

        <link rel="preload" href="{{ Theme::asset()->url('fonts/icomoon.woff2') }}" as="font" type="font/woff2" crossorigin>

        <style>
            :root {
                --primary-color: {{ theme_option('primary_color', '#db1d23') }};
                --hover-color: {{ theme_option('hover_color', '#cd380f') }};
                --top-header-background-color: {{ theme_option('top_header_background_color', '#f7f7f7') }};
                --top-header-text-color: {{ theme_option('top_header_text_color', '#161e2d') }};
                --main-header-background-color: {{ theme_option('main_header_background_color', '#ffffff') }};
                --main-header-text-color: {{ theme_option('main_header_text_color', '#161e2d') }};
                --main-header-border-color: {{ theme_option('main_header_border_color', '#e4e4e4') }};
                --map-marker-icon-image: url({{ theme_option('map_marker_image') ? RvMedia::getImageUrl(theme_option('map_marker_image')) : Theme::asset()->url('images/map-icon.png') }});
            }
        </style>

        {!! Theme::header() !!}

        <link rel="stylesheet" href="{{ Theme::asset()->url('css/navbar-logo.css') }}">

        @if (theme_option('hotline'))
            <link rel="stylesheet" href="{{ Theme::asset()->url('css/floating-contact.css') }}">
        @endif

        @if (is_plugin_active('language') && preg_match('/^ar(?:[_-]|$)/i', Language::getCurrentLocale()))
            <link rel="stylesheet" href="{{ Theme::asset()->url('css/arabic-font.css') }}">
        @endif

        @if (is_plugin_active('language') && preg_match('/^en(?:[_-]|$)/i', Language::getCurrentLocale()))
            <link rel="stylesheet" href="{{ Theme::asset()->url('css/english-font.css') }}">
        @endif
    </head>

    <body
        {!! Theme::bodyAttributes() !!}
        data-account-authenticated="{{ auth('account')->check() ? 1 : 0 }}"
        data-use-auth-modal="{{ theme_option('use_modal_for_authentication', true) ? 1 : 0 }}"
        @if (is_plugin_active('real-estate'))
            data-login-url="{{ route('public.account.login') }}"
        @endif
        data-wishlist-login-message="{{ __('Please log in to view your wishlist.') }}"
    >
        <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" style="position: absolute; width: 0; height: 0; overflow: hidden">
            <defs>
                <filter id="navbar-logo-white-outline" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
                    <feMorphology in="SourceAlpha" operator="dilate" radius="1" result="expanded-alpha"/>
                    <feFlood flood-color="#fff" result="white"/>
                    <feComposite in="white" in2="expanded-alpha" operator="in" result="white-outline"/>
                    <feMerge>
                        <feMergeNode in="white-outline"/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>
        </svg>

        {!! apply_filters(THEME_FRONT_BODY, null) !!}

        <div id="wrapper">
            <main class="clearfix">
                @yield('content')
            </main>
        </div>

        {!! Theme::footer() !!}
    </body>
</html>
