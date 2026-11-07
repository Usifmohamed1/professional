<?php

namespace Botble\RealEstate\Forms\Settings;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextField;
use Botble\RealEstate\Http\Requests\Settings\WebhookSettingRequest;
use Botble\Setting\Forms\SettingForm;

class WebhookSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/real-estate::settings.webhook.name'))
            ->setSectionDescription(trans('plugins/real-estate::settings.webhook.description'))
            ->setValidatorClass(WebhookSettingRequest::class)
            ->add(
                'real_estate_property_created_webhook_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.webhook.form.property_created_webhook_url'))
                    ->value(setting('real_estate_property_created_webhook_url'))
                    ->placeholder(trans('plugins/real-estate::settings.webhook.form.webhook_url_placeholder'))
                    ->helperText(trans('plugins/real-estate::settings.webhook.form.property_created_webhook_url_helper'))
            )
            ->add(
                'property_created_sample_data',
                HtmlField::class,
                [
                    'html' => $this->getSampleDataHtml(
                        trans('plugins/real-estate::settings.webhook.view_sample_data'),
                        $this->getPropertyCreatedSampleData()
                    ) . $this->getTestButtonHtml('property_created', 'real_estate_property_created_webhook_url'),
                ]
            )
            ->add(
                'real_estate_property_updated_webhook_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.webhook.form.property_updated_webhook_url'))
                    ->value(setting('real_estate_property_updated_webhook_url'))
                    ->placeholder(trans('plugins/real-estate::settings.webhook.form.webhook_url_placeholder'))
                    ->helperText(trans('plugins/real-estate::settings.webhook.form.property_updated_webhook_url_helper'))
            )
            ->add(
                'property_updated_sample_data',
                HtmlField::class,
                [
                    'html' => $this->getSampleDataHtml(
                        trans('plugins/real-estate::settings.webhook.view_sample_data'),
                        $this->getPropertyUpdatedSampleData()
                    ) . $this->getTestButtonHtml('property_updated', 'real_estate_property_updated_webhook_url'),
                ]
            )
            ->add(
                'real_estate_property_deleted_webhook_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.webhook.form.property_deleted_webhook_url'))
                    ->value(setting('real_estate_property_deleted_webhook_url'))
                    ->placeholder(trans('plugins/real-estate::settings.webhook.form.webhook_url_placeholder'))
                    ->helperText(trans('plugins/real-estate::settings.webhook.form.property_deleted_webhook_url_helper'))
            )
            ->add(
                'property_deleted_sample_data',
                HtmlField::class,
                [
                    'html' => $this->getSampleDataHtml(
                        trans('plugins/real-estate::settings.webhook.view_sample_data'),
                        $this->getPropertyDeletedSampleData()
                    ) . $this->getTestButtonHtml('property_deleted', 'real_estate_property_deleted_webhook_url'),
                ]
            )
            ->add(
                'real_estate_property_images_added_webhook_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.webhook.form.property_images_added_webhook_url'))
                    ->value(setting('real_estate_property_images_added_webhook_url'))
                    ->placeholder(trans('plugins/real-estate::settings.webhook.form.webhook_url_placeholder'))
                    ->helperText(trans('plugins/real-estate::settings.webhook.form.property_images_added_webhook_url_helper'))
            )
            ->add(
                'property_images_added_sample_data',
                HtmlField::class,
                [
                    'html' => $this->getSampleDataHtml(
                        trans('plugins/real-estate::settings.webhook.view_sample_data'),
                        $this->getPropertyImagesAddedSampleData()
                    ) . $this->getTestButtonHtml('property_images_added', 'real_estate_property_images_added_webhook_url'),
                ]
            )
            ->add(
                'webhook_test_script',
                HtmlField::class,
                [
                    'html' => $this->getWebhookTestScript(),
                ]
            );
    }

    protected function getSampleDataHtml(string $title, array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<HTML
            <div class="mb-3">
                <details class="border rounded p-3">
                    <summary class="cursor-pointer text-primary fw-bold">{$title}</summary>
                    <pre class="mt-3 p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{$json}</code></pre>
                </details>
            </div>
        HTML;
    }

    protected function getTestButtonHtml(string $webhookType, string $urlFieldId): string
    {
        $testUrl = route('real-estate.settings.webhook.test');
        $buttonText = trans('plugins/real-estate::settings.webhook.test_button');
        $icon = BaseHelper::renderIcon('ti ti-send-2');

        return <<<HTML
            <div class="mb-3">
                <button type="button"
                        class="btn btn-sm btn-primary test-webhook-btn"
                        data-webhook-type="{$webhookType}"
                        data-url-field="{$urlFieldId}"
                        data-test-url="{$testUrl}">
                    {$icon} {$buttonText}
                </button>
                <div class="webhook-test-result mt-2" id="test-result-{$webhookType}" style="display: none;"></div>
            </div>
        HTML;
    }

    protected function getPropertyCreatedSampleData(): array
    {
        return $this->getBaseSampleData('property_created');
    }

    protected function getPropertyUpdatedSampleData(): array
    {
        return $this->getBaseSampleData('property_updated');
    }

    protected function getPropertyDeletedSampleData(): array
    {
        return [
            'id' => 12345,
            'unique_id' => 'PR-ABC-123',
            'name' => 'Beautiful Modern Villa',
            'event' => 'property_deleted',
            'deleted_at' => '2025-01-15T10:30:00Z',
        ];
    }

    protected function getPropertyImagesAddedSampleData(): array
    {
        $data = $this->getBaseSampleData('property_images_added');
        $data['new_images'] = [
            'properties/property-4.jpg',
            'properties/property-5.jpg',
        ];
        $data['new_images_urls'] = [
            'https://example.com/storage/properties/property-4.jpg',
            'https://example.com/storage/properties/property-5.jpg',
        ];

        return $data;
    }

    protected function getBaseSampleData(string $event): array
    {
        return [
            'id' => 12345,
            'unique_id' => 'PR-ABC-123',
            'name' => 'Beautiful Modern Villa',
            'description' => 'A stunning modern villa with panoramic views.',
            'content' => '<p>This beautiful property features spacious rooms...</p>',
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
            ],
            'image_urls' => [
                'https://example.com/storage/properties/property-1.jpg',
                'https://example.com/storage/properties/property-2.jpg',
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
                ['id' => 1, 'name' => 'Villa'],
            ],
            'features' => [
                ['id' => 1, 'name' => 'Swimming Pool'],
                ['id' => 2, 'name' => 'Garage'],
            ],
            'author' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
            ],
            'is_featured' => true,
            'url' => 'https://example.com/properties/beautiful-modern-villa',
            'created_at' => '2025-01-15T10:30:00Z',
            'updated_at' => '2025-01-15T10:30:00Z',
            'expire_date' => '2025-02-15T10:30:00Z',
            'event' => $event,
        ];
    }

    protected function getWebhookTestScript(): string
    {
        $pleaseEnterUrlText = trans('plugins/real-estate::settings.webhook.please_enter_url');
        $testingText = trans('plugins/real-estate::settings.webhook.testing');
        $testFailedText = trans('plugins/real-estate::settings.webhook.test_failed_title');
        $testSuccessText = trans('plugins/real-estate::settings.webhook.test_success_title');
        $statusCodeText = trans('plugins/real-estate::settings.webhook.status_code');
        $errorOccurredText = trans('plugins/real-estate::settings.webhook.error_occurred');

        $loaderIcon = '<span style="display: inline-block; animation: spin 1s linear infinite;">' . BaseHelper::renderIcon('ti ti-loader-2', attributes: ['class' => 'm-0']) . '</span>';
        $loaderIcon = json_encode($loaderIcon);

        return <<<HTML
            <style>
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            </style>
            <script>
                $(document).ready(function() {
                    const loaderIcon = {$loaderIcon};

                    $('.test-webhook-btn').on('click', function() {
                        const button = $(this);
                        const webhookType = button.data('webhook-type');
                        const urlFieldId = button.data('url-field');
                        const testUrl = button.data('test-url');
                        const webhookUrl = $('#' + urlFieldId).val();
                        const resultDiv = $('#test-result-' + webhookType);
                        const originalButtonHtml = button.html();

                        if (!webhookUrl) {
                            Botble.showError('{$pleaseEnterUrlText}');
                            return;
                        }

                        button.prop('disabled', true);
                        button.html(loaderIcon + ' {$testingText}');
                        resultDiv.hide();

                        $.ajax({
                            url: testUrl,
                            type: 'POST',
                            data: {
                                webhook_url: webhookUrl,
                                webhook_type: webhookType,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.error) {
                                    resultDiv.html('<div class="alert alert-danger">' +
                                        '<p><strong>{$testFailedText}</strong> <br>' + response.message +
                                        (response.data ? '<br>{$statusCodeText}: ' + response.data.status_code : '') +
                                        '</p> </div>');
                                } else {
                                    resultDiv.html('<div class="alert alert-success">' +
                                        '<strong>{$testSuccessText}</strong> ' + response.message +
                                        '<br>{$statusCodeText}: ' + response.data.status_code +
                                        '</div>');
                                }
                                resultDiv.slideDown();
                            },
                            error: function(xhr) {
                                let errorMessage = '{$errorOccurredText}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                resultDiv.html('<div class="alert alert-danger">' +
                                    '<strong>{$testFailedText}</strong> ' + errorMessage +
                                    '</div>');
                                resultDiv.slideDown();
                            },
                            complete: function() {
                                button.prop('disabled', false);
                                button.html(originalButtonHtml);
                            }
                        });
                    });
                });
            </script>
        HTML;
    }
}
