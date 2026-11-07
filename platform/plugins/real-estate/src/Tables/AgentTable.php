<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Account;
use Botble\Table\Actions\Action;
use Botble\Table\BulkChanges\EmailBulkChange;
use Botble\Table\BulkChanges\PhoneBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;

class AgentTable extends AccountTable
{
    protected string $accountType = Account::TYPE_AGENT;

    protected string $routePrefix = 'agent';

    protected string $permissionPrefix = 'agent';

    public function setup(): void
    {
        parent::setup();

        $this->addBulkChanges([
            EmailBulkChange::make()
                ->title(trans('plugins/real-estate::account.login_email')),
            TextBulkChange::make()
                ->name('contact_email')
                ->title(trans('plugins/real-estate::account.public_contact_email'))
                ->validate(['nullable', 'email', 'max:60']),
            PhoneBulkChange::make(),
            PhoneBulkChange::make()
                ->name('whatsapp')
                ->title(trans('plugins/real-estate::account.whatsapp')),
        ]);

        $this->addAction(
            Action::make('verify')
                ->route('agent.verify')
                ->action('PUT')
                ->icon('ti ti-shield-check')
                ->color('success')
                ->label(__('Verify'))
                ->permission('agent.edit')
                ->renderUsing(fn (Action $action) => $action->getItem()->is_verified ? '' : null)
        );
    }
}
