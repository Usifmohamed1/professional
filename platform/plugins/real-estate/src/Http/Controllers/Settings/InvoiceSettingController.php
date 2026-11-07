<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\Base\Supports\Breadcrumb;
use Botble\RealEstate\Forms\Settings\InvoiceSettingForm;
use Botble\RealEstate\Http\Requests\Settings\InvoiceSettingRequest;

class InvoiceSettingController extends BaseSettingController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/real-estate::settings.invoice.name'));
    }

    public function edit()
    {
        $this->pageTitle(trans('plugins/real-estate::settings.invoice.name'));

        return InvoiceSettingForm::create()->renderForm();
    }

    public function update(InvoiceSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
