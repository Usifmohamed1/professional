@php
    $class ??= null;
    $itemsPerRow ??= 3;

    $selectedCategoryId = isset($category) ? $category->getKey() : request()->integer('category_id');
    $displayCategory = $selectedCategoryId
        ? $property->categories->firstWhere('id', $selectedCategoryId)
        : $property->category;
@endphp

<div @class(['property-item homeya-box editorial-property-card', $class]) @if ($property->latitude && $property->longitude) data-lat="{{ $property->latitude }}" data-lng="{{ $property->longitude }}" @endif>
    <div class="archive-top">
        <div class="editorial-card-media">
        <a href="{{ $property->url }}" class="images-group">
            <div class="images-style">
                {{ RvMedia::image($property->image, $property->name, 'medium-rectangle') }}
            </div>
        </a>
            <div class="top">
                <div class="d-flex gap-8">
                    @if($property->is_featured)
                        <span class="flag-tag success">{{ __('Featured') }}</span>
                    @endif
                    {!! BaseHelper::clean($property->status_html) !!}
                </div>
                @if (RealEstateHelper::isEnabledWishlist())
                    <div class="d-flex gap-4">
                        <button type="button" class="box-icon w-32"
                                data-type="property"
                                data-bb-toggle="add-to-wishlist"
                                data-id="{{ $property->getKey() }}"
                                data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $property->name]) }}"
                                data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $property->name]) }}"
                                aria-label="{{ __('Save :name', ['name' => $property->name]) }}" aria-pressed="false"
                        >
                            <x-core::icon name="ti ti-heart" />
                        </button>
                    </div>
                @endif
            </div>
            @if($displayCategory)
                <div class="bottom">
                    <span class="flag-tag style-2">{{ $displayCategory->name }}</span>
                </div>
            @endif
        </div>
        <div class="content">
<h3 class="editorial-card-title">
                <a href="{{ $property->url }}" class="link line-clamp-2" title="{{ $property->name }}">{!! BaseHelper::clean($property->name) !!}</a>
            </h3>
            @if($property->short_address)
                <div class="desc">
                    <i class="icon icon-mapPin"></i>
                    <p class="line-clamp-1">{{ $property->short_address }}</p>
                </div>
            @endif
            @if($class === 'lg' && $property->description)
                <p class="note">{!! Str::limit(BaseHelper::clean($property->description)) !!}</p>
            @endif
            <ul class="meta-list">
                @if($property->number_bedroom)
                    <li class="item">
                        <i class="icon icon-bed"></i>
                        <span>{{ fmod($property->number_bedroom, 1) == 0 ? number_format($property->number_bedroom) : $property->number_bedroom }} <span class="editorial-spec-label">{{ __('Beds') }}</span></span>
                    </li>
                @endif
                @if($property->number_bathroom)
                    <li class="item">
                        <i class="icon icon-bathtub"></i>
                        <span>{{ fmod($property->number_bathroom, 1) == 0 ? number_format($property->number_bathroom) : $property->number_bathroom }} <span class="editorial-spec-label">{{ __('Baths') }}</span></span>
                    </li>
                @endif
                @if($property->number_reception)
                    <li class="item">
                        <x-core::icon name="ti ti-armchair" />
                        <span>{{ number_format($property->number_reception) }}</span>
                    </li>
                @endif
                @if($property->square)
                    <li class="item">
                        <i class="icon icon-ruler"></i>
                        <span>{{ $property->square_text }}</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <div class="archive-bottom d-flex justify-content-between align-items-center">
        @if (! \Botble\RealEstate\Facades\RealEstateHelper::isDisabledPublicProfile() && ($author = $property->author) && $author->exists)
            <div class="d-flex gap-8 align-items-center">
                @if ($itemsPerRow < 4)
                    <div class="avatar avt-40 round">
                        {{ RvMedia::image($author->avatar_url, $author->name) }}
                    </div>
                @endif
                <span>{{ $author->name }} {!! $author->badge !!}</span>
            </div>
        @endif
        @if (!setting('real_estate_hide_price', false))
            <div class="d-flex align-items-center">
                <span class="h6 editorial-card-price">@include(Theme::getThemeNamespace('views.real-estate.properties.price'), ['property' => $property])</span>
            </div>
        @endif
        <a class="editorial-card-link" href="{{ $property->url }}" aria-label="{{ __('View :name', ['name' => $property->name]) }}"><x-core::icon name="ti ti-arrow-up-right" /></a>
    </div>
</div>
