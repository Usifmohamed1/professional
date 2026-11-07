<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebhookTestController extends BaseController
{
    public function test(Request $request, BaseHttpResponse $response)
    {
        $request->validate([
            'webhook_url' => ['required', 'url'],
            'webhook_type' => ['required', 'in:property_created,property_updated,property_deleted,property_images_added'],
        ]);

        $webhookUrl = $request->input('webhook_url');
        $webhookType = $request->input('webhook_type');

        try {
            $sampleData = $this->getSampleDataForType($webhookType);

            $httpResponse = Http::timeout(10)
                ->withoutVerifying()
                ->acceptJson()
                ->post($webhookUrl, $sampleData);

            if ($httpResponse->successful()) {
                return $response
                    ->setMessage(trans('plugins/real-estate::settings.webhook.test_success'))
                    ->setData([
                        'status_code' => $httpResponse->status(),
                        'response_body' => $httpResponse->body(),
                    ]);
            }

            return $response
                ->setError()
                ->setMessage(trans('plugins/real-estate::settings.webhook.test_failed'))
                ->setData([
                    'status_code' => $httpResponse->status(),
                    'response_body' => $httpResponse->body(),
                ]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/real-estate::settings.webhook.test_error', ['error' => $exception->getMessage()]));
        }
    }

    protected function getSampleDataForType(string $type): array
    {
        $baseData = [
            'id' => 12345,
            'unique_id' => 'PR-ABC-123',
            'name' => 'Beautiful Modern Villa',
            'description' => 'A stunning modern villa with panoramic views.',
            'content' => '<p>This beautiful property features spacious rooms, modern amenities, and a prime location...</p>',
            'type' => [
                'value' => 'sale',
                'text' => 'Sale',
            ],
            'status' => [
                'value' => 'active',
                'text' => 'Active',
            ],
            'moderation_status' => [
                'value' => 'approved',
                'text' => 'Approved',
            ],
            'price' => 850000,
            'price_formatted' => '$850,000',
            'currency' => [
                'id' => 1,
                'title' => 'USD',
                'symbol' => '$',
            ],
            'period' => null,
            'images' => [
                'properties/property-1.jpg',
                'properties/property-2.jpg',
                'properties/property-3.jpg',
            ],
            'image_urls' => [
                'https://example.com/storage/properties/property-1.jpg',
                'https://example.com/storage/properties/property-2.jpg',
                'https://example.com/storage/properties/property-3.jpg',
            ],
            'primary_image' => 'properties/property-1.jpg',
            'primary_image_url' => 'https://example.com/storage/properties/property-1.jpg',
            'location' => [
                'address' => '123 Main Street',
                'city' => 'Los Angeles',
                'state' => 'California',
                'country' => 'United States',
                'zip_code' => '90001',
                'latitude' => 34.0522,
                'longitude' => -118.2437,
                'full_address' => '123 Main Street, Los Angeles, California, United States, 90001',
            ],
            'specifications' => [
                'square' => 2500,
                'square_text' => '2,500 m²',
                'number_bedroom' => 4,
                'number_bathroom' => 3,
                'number_floor' => 2,
            ],
            'categories' => [
                [
                    'id' => 1,
                    'name' => 'Villa',
                ],
            ],
            'features' => [
                [
                    'id' => 1,
                    'name' => 'Swimming Pool',
                ],
                [
                    'id' => 2,
                    'name' => 'Garage',
                ],
                [
                    'id' => 3,
                    'name' => 'Garden',
                ],
            ],
            'author' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
            ],
            'is_featured' => true,
            'url' => 'https://example.com/properties/beautiful-modern-villa',
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
            'expire_date' => now()->addDays(30)->toIso8601String(),
            'test_webhook' => true,
        ];

        return match ($type) {
            'property_created' => array_merge($baseData, [
                'event' => 'property_created',
            ]),
            'property_updated' => array_merge($baseData, [
                'event' => 'property_updated',
            ]),
            'property_deleted' => [
                'id' => 12345,
                'unique_id' => 'PR-ABC-123',
                'name' => 'Beautiful Modern Villa',
                'event' => 'property_deleted',
                'deleted_at' => now()->toIso8601String(),
                'test_webhook' => true,
            ],
            'property_images_added' => array_merge($baseData, [
                'event' => 'property_images_added',
                'new_images' => [
                    'properties/property-4.jpg',
                    'properties/property-5.jpg',
                ],
                'new_images_urls' => [
                    'https://example.com/storage/properties/property-4.jpg',
                    'https://example.com/storage/properties/property-5.jpg',
                ],
            ]),
            default => $baseData,
        };
    }
}
