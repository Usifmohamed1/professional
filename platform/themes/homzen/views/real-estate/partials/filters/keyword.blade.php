@php $keywordFieldId = 'keyword-' . Str::uuid(); @endphp
<div class="form-group-1 form-search-form form-style form-search-keyword-input" data-bb-toggle="search-suggestion">
    <label for="{{ $keywordFieldId }}">{{ __('Keyword') }}</label>
    <div class="position-relative">
        <input id="{{ $keywordFieldId }}" type="text" class="form-control" placeholder="{{ __('Search for Keyword') }}" value="{{ BaseHelper::stringify(request()->query('k')) }}" name="k" />
        <div data-bb-toggle="data-suggestion"></div>
    </div>
</div>
