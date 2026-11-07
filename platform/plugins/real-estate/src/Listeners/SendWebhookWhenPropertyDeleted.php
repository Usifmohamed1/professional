<?php

namespace Botble\RealEstate\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Events\PropertyDeleted;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class SendWebhookWhenPropertyDeleted
{
    public function handle(PropertyDeleted $event): void
    {
        $webhookURL = setting('real_estate_property_deleted_webhook_url');

        if (! $webhookURL || ! URL::isValidUrl($webhookURL) || BaseHelper::hasDemoModeEnabled()) {
            return;
        }

        try {
            $data = $event->propertyData;

            $data = apply_filters('real_estate_property_deleted_webhook_data', $data);

            Http::withoutVerifying()
                ->acceptJson()
                ->post($webhookURL, $data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
