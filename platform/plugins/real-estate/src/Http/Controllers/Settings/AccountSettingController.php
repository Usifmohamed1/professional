<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\Base\Supports\Breadcrumb;
use Botble\RealEstate\Forms\Settings\AccountSettingForm;
use Botble\RealEstate\Http\Requests\Settings\AccountSettingRequest;

class AccountSettingController extends BaseSettingController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/real-estate::settings.account.name'));
    }

    public function edit()
    {
        $this->pageTitle(trans('plugins/real-estate::settings.account.name'));

        return AccountSettingForm::create()->renderForm();
    }

    public function update(AccountSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
