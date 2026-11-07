<?php

namespace Botble\RealEstate\Listeners;

use Botble\Base\Facades\BaseHelper;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Events\PropertyUpdated;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class SendWebhookWhenPropertyImagesAdded
{
    public function handle(PropertyUpdated $event): void
    {
        $webhookURL = setting('real_estate_property_images_added_webhook_url');

        if (! $webhookURL || ! URL::isValidUrl($webhookURL) || BaseHelper::hasDemoModeEnabled()) {
            return;
        }

        $property = $event->property;
        $originalImages = $event->originalImages;
        $currentImages = $property->images ?? [];

        $newImages = array_diff($currentImages, $originalImages);

        if (empty($newImages)) {
            return;
        }

        try {
            $data = $property->toWebhookData();
            $data['new_images'] = array_values($newImages);
            $data['new_images_urls'] = array_map(
                fn ($image) => RvMedia::getImageUrl($image),
                array_values($newImages)
            );

            $data = apply_filters('real_estate_property_images_added_webhook_data', $data, $property, $newImages);

            Http::withoutVerifying()
                ->acceptJson()
                ->post($webhookURL, $data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
