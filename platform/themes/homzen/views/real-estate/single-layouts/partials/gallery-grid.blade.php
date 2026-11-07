@php
    $model = $model ?? $property ?? null;
@endphp

@if ($model->images)
    <section class="flat-gallery-single">
        @foreach((array) $model->images as $image)
            @if($loop->first)
                <a href="{{ RvMedia::getImageUrl($image) }}" data-fancybox="gallery" class="item1 box-img">
                    {{ RvMedia::image($image, $model->name, attributes: ['fetchpriority' => 'high'], lazy: false) }}
                    <div class="box-btn">
                        <span class="tf-btn primary">{{ __('View All Photos (:count)', ['count' => count($model->images)]) }}</span>
                    </div>
                </a>
            @else
                <a href="{{ RvMedia::getImageUrl($image) }}" class="item-{{ $loop->iteration }} box-img" data-fancybox="gallery" @style(['display: none' => $loop->iteration > 5])>
                    {{ RvMedia::image($image, $model->name) }}
                </a>
            @endif
        @endforeach
    </section>
@endif
