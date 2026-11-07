<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\Base\Supports\Breadcrumb;
use Botble\Setting\Http\Controllers\SettingController;

abstract class BaseSettingController extends SettingController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/real-estate::real-estate.name'));
    }
}
