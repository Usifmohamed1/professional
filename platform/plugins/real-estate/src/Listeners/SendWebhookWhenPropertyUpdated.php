<?php

namespace Botble\RealEstate\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Events\PropertyUpdated;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class SendWebhookWhenPropertyUpdated
{
    public function handle(PropertyUpdated $event): void
    {
        $webhookURL = setting('real_estate_property_updated_webhook_url');

        if (! $webhookURL || ! URL::isValidUrl($webhookURL) || BaseHelper::hasDemoModeEnabled()) {
            return;
        }

        try {
            $property = $event->property;

            $data = $property->toWebhookData();

            $data = apply_filters('real_estate_property_updated_webhook_data', $data, $property);

            Http::withoutVerifying()
                ->acceptJson()
                ->post($webhookURL, $data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
