<div @class([
    'form-search-payment-type',
    'form-group-3 form-style' => $inline ?? false,
    'box-select' => ! ($inline ?? false),
    $class ?? null,
])>
    <label for="payment_type" class="title-select fw-5">{{ trans('plugins/real-estate::project.form.payment_type') }}</label>
    <select name="payment_type" id="payment_type" class="select_js">
        <option value="">{{ __('All') }}</option>
        @foreach(\Botble\RealEstate\Enums\ProjectPaymentTypeEnum::labels() as $value => $label)
            <option value="{{ $value }}" @selected(request()->input('payment_type') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
