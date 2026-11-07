@php
    $categoryFieldId = 'category-' . Str::uuid();
    $categories = collect(get_property_categories_for_select());
    $selectedCategoryId = request()->query('category_id');
    if (isset($category)) {
        $selectedCategoryId = $category->id;
    }

    $currentSearchType = $searchType ?? request()->query('type');
@endphp

<div class="form-group-3 form-style form-search-category" @if (theme_option('real_estate_enable_advanced_search', 'yes') != 'yes') style="border: none" @endif>
    <label for="{{ $categoryFieldId }}">{{ __('Category') }}</label>
    <div class="group-select">
        <select name="category_id" id="{{ $categoryFieldId }}" class="select_js" data-bb-toggle="property-category-filter" data-current-search-type="{{ $currentSearchType }}">
            <option value="">{{ __('All') }}</option>
            @foreach($categories as $item)
                @php
                    $categoryName = \Illuminate\Support\Str::lower(str_replace([' ', 'أ'], ['', 'ا'], (string) $item->name));
                    $categoryKey = match (true) {
                        in_array($categoryName, ['land', 'ارض']) => 'land',
                        in_array($categoryName, ['villa', 'فيلا']) => 'villa',
                        default => '',
                    };
                @endphp
                <option value="{{ $item->id }}" data-category-key="{{ $categoryKey }}" @selected($selectedCategoryId == $item->id)>{!! BaseHelper::clean($item->indent_text) !!} {!! BaseHelper::clean($item->name) !!}</option>
            @endforeach
        </select>
    </div>
</div>
