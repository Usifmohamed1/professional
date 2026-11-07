<?php

namespace Botble\RealEstate\Http\Requests\Settings;

use Botble\Support\Http\Requests\Request;

class WebhookSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'real_estate_property_created_webhook_url' => ['nullable', 'url'],
            'real_estate_property_updated_webhook_url' => ['nullable', 'url'],
            'real_estate_property_deleted_webhook_url' => ['nullable', 'url'],
            'real_estate_property_images_added_webhook_url' => ['nullable', 'url'],
        ];
    }
}
