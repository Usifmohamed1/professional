<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Models\Account;
use Botble\Support\Http\Requests\Request;

class AccountEditRequest extends Request
{
    public function rules(): array
    {
        $account = $this->route('account') ?: $this->route('agent');
        $accountId = $account instanceof Account ? $account->getKey() : $account;

        $rules = [
            'first_name' => 'required|string|max:120|min:2',
            'last_name' => 'required|string|max:120|min:2',
            'email' => 'required|max:60|min:6|email|unique:re_accounts,email,' . $accountId,
            'contact_email' => 'nullable|max:60|email',
            'phone' => 'required|' . BaseHelper::getPhoneValidationRule(),
        ];

        $rules['is_blocked'] = 'nullable|boolean';
        $rules['blocked_reason'] = 'nullable|string|max:500|required_if:is_blocked,1';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'blocked_reason.required_if' => trans('plugins/real-estate::account.blocked_reason_required'),
        ];
    }
}
