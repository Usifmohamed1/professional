@php
    $topFooterSidebar = dynamic_sidebar('top_footer_sidebar');
    $innerFooterSidebar = dynamic_sidebar('inner_footer_sidebar');
    $bottomFooterSidebar = dynamic_sidebar('bottom_footer_sidebar');
    $footerBackgroundColor = theme_option('footer_background_color', '#161e2d');
    $footerBackgroundImage = RvMedia::getImageUrl(theme_option('footer_background_image'));
@endphp


    <footer class="footer editorial-footer" @style(["background-color: $footerBackgroundColor" => $footerBackgroundColor, "background-image: url('$footerBackgroundImage') !important" => theme_option('footer_background_image')])>
        <div class="container editorial-footer__invitation">
            <div>
                <p class="editorial-eyebrow">RE/MAX PROFESSIONAL</p>
                <h2>{{ __('A place for your next chapter.') }}</h2>
            </div>
            @if ($hotline = theme_option('hotline'))
                <a class="editorial-circle-link" href="tel:{{ preg_replace('/[^0-9+]/', '', $hotline) }}" aria-label="{{ __('Call our team') }}"><x-core::icon name="ti ti-arrow-up-right" /></a>
            @elseif (is_plugin_active('real-estate'))
                <a class="editorial-circle-link" href="{{ RealEstateHelper::getPropertiesListPageUrl() }}" aria-label="{{ __('Explore properties') }}"><x-core::icon name="ti ti-arrow-up-right" /></a>
            @endif
        </div>
        @if($topFooterSidebar)
            <div class="top-footer">
                <div class="container">
                    <div class="content-footer-top">
                        {!! $topFooterSidebar !!}
                    </div>
                </div>
            </div>
        @endif

        @if($innerFooterSidebar)
            <div class="inner-footer">
                <div class="container">
                    <div class="row">
                        {!! $innerFooterSidebar !!}
                    </div>
                </div>
            </div>
        @endif

        @if($bottomFooterSidebar)
            <div class="bottom-footer">
                <div class="container">
                    <div class="content-footer-bottom">
                        {!! $bottomFooterSidebar !!}
                    </div>
                </div>
            </div>
        @endif
    </footer>
