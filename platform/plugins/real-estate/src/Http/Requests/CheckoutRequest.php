<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CheckoutRequest extends Request
{
    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|' . Rule::in(PaymentMethodEnum::values()),
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string'],
            'instapay_transfer_receipt' => ['required_if:payment_method,' . PaymentMethodEnum::INSTAPAY, 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
