@if ($model->images && is_array($model->images))
    <section class="flat-location flat-slider-detail-v1">
        <div class="swiper tf-sw-location" data-preview-lg="2.03" data-preview-md="2" data-preview-sm="2" data-space="{{ $model instanceof \Botble\RealEstate\Models\Property ? 0 : 20 }}" data-centered="true" data-loop="true">
            <div class="swiper-wrapper">
                @foreach ($model->images as $image)
                    <div class="swiper-slide">
                        <a href="{{ RvMedia::getImageUrl($image) }}" data-fancybox="gallery" class="box-imgage-detail d-block">
                            @if ($loop->first)
                                {{ RvMedia::image($image, $model->name, attributes: ['width' => '100%', 'fetchpriority' => 'high'], lazy: false) }}
                            @else
                                {{ RvMedia::image($image, $model->name, attributes: ['width' => '100%', 'loading' => 'lazy']) }}
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="box-navigation">
                <button type="button" class="navigation swiper-nav-next nav-next-location" aria-label="{{ __('Previous photo') }}">
                    <x-core::icon name="ti ti-chevron-left" class="icon" />
                </button>
                <button type="button" class="navigation swiper-nav-prev nav-prev-location" aria-label="{{ __('Next photo') }}">
                    <x-core::icon name="ti ti-chevron-right" class="icon" />
                </button>
            </div>
        </div>
    </section>
@endif
