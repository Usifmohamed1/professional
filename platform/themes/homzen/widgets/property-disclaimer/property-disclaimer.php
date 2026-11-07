<?php

use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Theme\Facades\Theme;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Collection;

class PropertyDisclaimerWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Property Disclaimer'),
            'description' => __('Display a disclaimer on property detail pages'),
            'disclaimer_text' => '{site_name} does not offer any guarantees relating to the information contained on this website. All information is provided in good faith, however, {site_name} does not make any representation or provide any warranty, whether express or implied, of adequacy, accuracy, completeness, reasonableness, or use of the information for any particular purpose. {site_name} accepts no responsibility or liability whatsoever regarding any issues arising from the use of the information on this website, for any purpose. {agent_name} is responsible for the accuracy and completeness of property listings.',
        ]);
    }

    protected function data(): array|Collection
    {
        $property = Theme::get('currentProperty');

        if (! $property) {
            return ['disclaimer' => null];
        }

        $siteName = Theme::getSiteTitle() ?: config('app.name');
        $agentName = $property->author?->name ?: __('the advertiser');

        $disclaimer = str_replace(
            ['{site_name}', '{agent_name}'],
            [$siteName, $agentName],
            $this->getConfig()['disclaimer_text']
        );

        return compact('disclaimer', 'agentName');
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add(
                'disclaimer_text',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Disclaimer Text'))
                    ->helperText(__('Use {site_name} and {agent_name} as placeholders.'))
                    ->attributes(['rows' => 6])
                    ->toArray(),
            );
    }

    protected function requiredPlugins(): array
    {
        return ['real-estate'];
    }
}
