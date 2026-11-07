<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\InputFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\PasswordField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Models\Account;
use Botble\Slug\Forms\Fields\PermalinkField;
use Carbon\Carbon;

class AccountForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/account-admin.css')
            ->addScriptsDirectly(['/vendor/core/plugins/real-estate/js/account-admin.js']);

        $this
            ->model(Account::class)
            ->setValidatorClass(AccountCreateRequest::class)
            ->disablePermalinkField()
            ->template('plugins/real-estate::account.admin.form')
            ->add(
                'first_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::account.first_name'))
                    ->required()
                    ->placeholder(trans('plugins/real-estate::account.first_name'))
                    ->maxLength(120)
                    ->toArray()
            )
            ->add(
                'last_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::account.last_name'))
                    ->required()
                    ->placeholder(trans('plugins/real-estate::account.last_name'))
                    ->maxLength(120)
                    ->toArray()
            )
            ->add(
                'phone',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::account.phone'))
                    ->required()
                    ->placeholder(trans('plugins/real-estate::account.phone_placeholder'))
                    ->maxLength(20)
                    ->toArray()
            )
            ->add(
                'whatsapp',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::account.whatsapp'))
                    ->required()
                    ->placeholder(trans('plugins/real-estate::account.whatsapp_placeholder'))
                    ->maxLength(25)
                    ->toArray()
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(trans('plugins/real-estate::account.form.email'))
                    ->required()
                    ->placeholder(trans('plugins/real-estate::account.email_placeholder'))
                    ->maxLength(60)
                    ->toArray()
            )
            ->add(
                'contact_email',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(trans('plugins/real-estate::account.public_contact_email'))
                    ->placeholder(trans('plugins/real-estate::account.email_placeholder'))
                    ->maxLength(60)
                    ->toArray()
            )
            ->addAfter('email', 'slug', PermalinkField::class, [
                'model' => $this->getModel(),
                'colspan' => 'full',
            ])
            ->add(
                'avatar_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('plugins/real-estate::dashboard.profile-picture'))
                    ->value($this->getModel()->avatar->url)
            )
            ->when(AdminHelper::isInAdmin(true), function (FormAbstract $form): void {
                $form
                    ->add(
                        'is_featured',
                        OnOffField::class,
                        OnOffFieldOption::make()
                        ->label(trans('core/base::forms.is_featured'))
                        ->defaultValue(false)
                        ->toArray()
                    )
                    ->add(
                        'is_blocked',
                        OnOffField::class,
                        OnOffFieldOption::make()
                            ->label(trans('plugins/real-estate::account.form.is_blocked'))
                            ->defaultValue($this->getModel()->is_blocked ?? false)
                            ->attributes([
                                'data-bb-toggle' => 'collapse',
                                'data-bb-target' => '#blocked-reason-section',
                            ])
                            ->toArray()
                    )
                    ->add(
                        'blocked_reason',
                        TextareaField::class,
                        DescriptionFieldOption::make()
                            ->label(trans('plugins/real-estate::account.form.blocked_reason'))
                            ->placeholder(trans('plugins/real-estate::account.form.blocked_reason_placeholder'))
                            ->addAttribute('rows', 3)
                            ->addAttribute('data-counter', 500)
                            ->toArray()
                    );
            })
            ->when(! RealEstateHelper::isDisabledPublicProfile(), function (FormAbstract $form): void {
                $form->add(
                    'is_public_profile',
                    OnOffField::class,
                    OnOffFieldOption::make()
                        ->label(trans('plugins/real-estate::account.form.is_public_profile'))
                        ->helperText(trans('plugins/real-estate::account.form.is_public_profile_helper'))
                        ->defaultValue(false)
                        ->toArray()
                );
            })
            ->add(
                'hide_phone',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::account.form.hide_phone'))
                    ->helperText(trans('plugins/real-estate::account.form.hide_phone_helper'))
                    ->defaultValue(false)
                    ->toArray()
            )
            ->add(
                'hide_email',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::account.form.hide_email'))
                    ->helperText(trans('plugins/real-estate::account.form.hide_email_helper'))
                    ->defaultValue(false)
                    ->toArray()
            )
            ->when($this->getModel()->id && RealEstateHelper::isEnabledCreditsSystem(), function (FormAbstract $form): void {
                /**
                 * @var Account $account
                 */
                $account = $this->getModel();

                $form->addMetaBoxes([
                    'credits' => [
                        'attributes' => [
                            'id' => 'credit-histories',
                        ],
                        'title' => trans('plugins/real-estate::account.transactions'),
                        'subtitle' => trans('plugins/real-estate::account.credits_count', ['count' => number_format($this->getModel()->credits)]),
                        'header_actions' => view(
                            'plugins/real-estate::partials.forms.header-actions.button',
                            [
                                'className' => 'btn-trigger-add-credit',
                                'label' => trans('plugins/real-estate::account.manual_transaction'),
                            ]
                        )->render(),
                        'content' => view('plugins/real-estate::account.admin.credits', [
                            'account' => $account,
                            'transactions' => $account->transactions()->latest()->get(),
                        ])->render(),
                    ],
                ]);
            })
            ->remove('social_links')
            ->setBreakFieldPoint('avatar_image');
    }
}
