@php
    $style = in_array($shortcode->style, range(1, 3)) ? $shortcode->style : 1;
    $expandFirst = $shortcode->expand_first_time !== '0';
@endphp

<section class="flat-section">
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode]) !!}

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @include(Theme::getThemeNamespace("partials.shortcodes.faqs.styles.style-$style"))
            </div>
        </div>
    </div>
</section>
