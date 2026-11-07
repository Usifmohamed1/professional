<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\Base\Supports\Breadcrumb;
use Botble\RealEstate\Forms\Settings\WebhookSettingForm;
use Botble\RealEstate\Http\Requests\Settings\WebhookSettingRequest;

class WebhookSettingController extends BaseSettingController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/real-estate::settings.webhook.name'));
    }

    public function edit()
    {
        $this->pageTitle(trans('plugins/real-estate::settings.webhook.name'));

        return WebhookSettingForm::create()->renderForm();
    }

    public function update(WebhookSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
