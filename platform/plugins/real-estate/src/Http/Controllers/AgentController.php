<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Http\Requests\AccountEditRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Tables\AgentTable;
use Illuminate\Http\Request;

class AgentController extends AccountController
{
    protected string $accountType = Account::TYPE_AGENT;

    protected string $routePrefix = 'agent';

    protected string $translationPrefix = 'plugins/real-estate::agent';

    protected string $tableClass = AgentTable::class;

    public function __construct()
    {
        OptimizerHelper::disable();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::agent.name'), route('agent.index'));
    }

    public function edit(Account $agent)
    {
        return parent::edit($agent);
    }

    public function update(Account $agent, AccountEditRequest $request)
    {
        return parent::update($agent, $request);
    }

    public function destroy(Account $agent)
    {
        return parent::destroy($agent);
    }

    public function show(Account $agent)
    {
        return parent::show($agent);
    }

    public function verify(Account $agent, Request $request)
    {
        return parent::verify($agent, $request);
    }

    public function unverify(Account $agent, Request $request)
    {
        return parent::unverify($agent, $request);
    }
}
