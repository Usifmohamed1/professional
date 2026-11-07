<section
    class="editorial-section editorial-properties flat-section flat-property"
    @style(["background-color: $shortcode->background_color" => $shortcode->background_color])
>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'centered' => false]) !!}

        <div class="editorial-property-showcase">
            @forelse ($properties as $property)
                <div @class(['editorial-property-showcase__item', 'editorial-property-showcase__item--lead' => $loop->first])>
                    @include(Theme::getThemeNamespace('views.real-estate.properties.item-grid'), ['property' => $property])
                </div>
            @empty
                <p class="editorial-empty">{{ __('No properties found.') }}</p>
            @endforelse
        </div>
    </div>
</section>
