<?php

use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Faq\Models\Faq;
use Botble\Faq\Models\FaqCategory;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Theme\Homzen\Forms\ShortcodeForm;

if (! is_plugin_active('faq')) {
    return;
}

Event::listen(RouteMatched::class, function (): void {
    Shortcode::register('faqs', __('FAQs'), __('FAQs'), function (ShortcodeCompiler $shortcode): ?string {
        if (! $categoryIds = Shortcode::fields()->parseIds($shortcode->category_ids)) {
            return null;
        }

        $limit = (int) $shortcode->limit ?: 5;
        $faqs = collect();
        $categories = collect();

        if ($shortcode->display_type === 'list') {
            $faqs = Faq::query()
                ->whereIn('category_id', $categoryIds)
                ->wherePublished()
                ->take($limit)
                ->get();
        } else {
            $categories = FaqCategory::query()
                ->whereIn('id', $categoryIds)
                ->with('faqs')
                ->get();
        }

        return Theme::partial('shortcodes.faqs.index', compact('shortcode', 'faqs', 'categories'));
    });

    Shortcode::setPreviewImage('faqs', Theme::asset()->url('images/shortcodes/faqs/style-1.png'));

    Shortcode::setAdminConfig('faqs', function (array $attributes): ShortcodeForm {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->add(
                'style',
                UiSelectorField::class,
                UiSelectorFieldOption::make()
                    ->choices(
                        collect(range(1, 3))
                            ->mapWithKeys(fn ($number) => [
                                $number => [
                                    'label' => __('Style :number', ['number' => $number]),
                                    'image' => Theme::asset()->url("images/shortcodes/faqs/style-$number.png"),
                                ],
                            ])
                            ->all()
                    )
                    ->selected(Arr::get($attributes, 'style', 1))
            )
            ->addSectionHeadingFields()
            ->add(
                'category_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('FAQ categories'))
                    ->choices(
                        FaqCategory::query()
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->selected(is_array($categoryIds = Arr::get($attributes, 'category_ids')) ? $categoryIds : explode(',', (string) $categoryIds))
                    ->searchable()
                    ->multiple()
                    ->toArray()
            )
            ->add(
                'display_type',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(__('Display type'))
                    ->choices([
                        'list' => __('List'),
                        'group' => __('Group by category'),
                    ])
            )
            ->addLimitField(defaultValue: 5)
            ->add(
                'expand_first_time',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(__('Expand the content of the first FAQ'))
                    ->defaultValue(true)
            );
    });
});
