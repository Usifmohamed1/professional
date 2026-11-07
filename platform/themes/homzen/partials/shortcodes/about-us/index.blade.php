<section class="editorial-section editorial-about flat-section flat-banner-about">
    <div class="container">
        <div class="row editorial-about__intro">
            <div class="col-md-6">
                @if ($shortcode->title)
                    <h2 class="editorial-about__title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                @endif
            </div>
            <div class="col-md-6 hover-btn-view editorial-about__story">
                @if ($shortcode->description)
                    <p class="body-2 text-variant-1">
                        {!! BaseHelper::clean(nl2br($shortcode->description)) !!}
                    </p>
                @endif
                @if ($shortcode->button_label)
                    <a
                        href="{{ $shortcode->button_url }}"
                        class="btn-view style-1"
                    >
                        <span class="text">{{ $shortcode->button_label }}</span>
                        <span class="icon icon-arrow-right2"></span>
                    </a>
                @endif
            </div>
        </div>
        <div class="banner-video editorial-about__image">
            @if ($shortcode->image)
                {{ RvMedia::image($shortcode->image, $shortcode->title) }}
            @endif

            @if ($shortcode->video_url)
                <a
                    href="{{ $shortcode->video_url }}"
                    data-fancybox="gallery2"
                    class="btn-video"
                    aria-label="{{ __('Play video') }}"
                >
                    <span class="icon icon-play"></span>
                </a>
            @endif
        </div>
    </div>
</section>
