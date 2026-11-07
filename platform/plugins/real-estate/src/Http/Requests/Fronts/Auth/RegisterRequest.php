<?php

namespace Botble\RealEstate\Http\Requests\Fronts\Auth;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\EmailRule;
use Botble\RealEstate\Models\Account;
use Botble\Support\Http\Requests\Request;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120', 'min:2'],
            'last_name' => ['required', 'string', 'max:120', 'min:2'],
            'email' => ['required', 'max:60', 'min:6', new EmailRule(), 'unique:re_accounts'],
            'phone' => [
                'required',
                ...explode('|', BaseHelper::getPhoneValidationRule()),
                'unique:' . (new Account())->getTable() . ',phone',
            ],
            'password' => ['required', 'min:6', 'confirmed'],
            'agree_terms_and_policy' => ['sometimes', 'accepted:1'],
        ];
    }
}
