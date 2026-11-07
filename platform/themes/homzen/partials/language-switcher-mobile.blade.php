@if (is_plugin_active('language'))
    @php
        $supportedLocales = Language::getSupportedLocales();
        $languageDisplay = setting('language_display', 'all');
        $showRelated = setting('language_show_default_item_if_current_version_not_existed', true);
        $currentLocale = Language::getCurrentLocale();
        $currentLocaleProperties = $supportedLocales[$currentLocale] ?? null;
    @endphp

    @if ($supportedLocales && count($supportedLocales) > 1 && $currentLocaleProperties)
        <div class="mobile-switcher-heading">{{ __('Language') }}</div>
        <div class="language-current-mobile">
            @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                <span class="language-flag">{!! language_flag($currentLocaleProperties['lang_flag'], $currentLocaleProperties['lang_name']) !!}</span>
            @endif
            @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                <span class="language-name">{{ $currentLocaleProperties['lang_name'] }}</span>
            @endif
        </div>
        <div class="language-list-mobile">
            @foreach ($supportedLocales as $localeCode => $properties)
                @if ($localeCode !== $currentLocale)
                    <a class="language-item" href="{{ $showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode) }}" aria-label="{{ __('Switch to :language', ['language' => $properties['lang_name']]) }}">
                        @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                            <span class="language-flag">{!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}</span>
                        @endif
                        @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                            <span class="language-name">{{ $properties['lang_name'] }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    @endif
@endif
