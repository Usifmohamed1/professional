<?php

namespace ArchiElite\Career\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Support\Http\Requests\Request;

class CareerApplicationRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:60'],
            'email' => ['required', new EmailRule(), 'max:120'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('plugins/career::career.application.form_name'),
            'phone' => trans('plugins/career::career.application.form_phone'),
            'email' => trans('plugins/career::career.application.form_email'),
            'cv' => trans('plugins/career::career.application.form_cv'),
        ];
    }
}
