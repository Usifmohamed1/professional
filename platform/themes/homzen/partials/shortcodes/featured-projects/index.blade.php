<section class="editorial-section editorial-featured-projects flat-section-v5 bg-surface flat-recommended flat-recommended-v2" {!! $shortcode->htmlAttributes() !!}>
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'hasButton' => false, 'class' => 'style-2']) !!}

        @if ($projects->isNotEmpty())
            <div class="editorial-development-grid wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                @foreach($projects as $project)
                    <div @class(['editorial-development-grid__item', 'editorial-development-grid__item--lead' => $loop->first])>
                        @include(Theme::getThemeNamespace('views.real-estate.projects.item-grid'))
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
