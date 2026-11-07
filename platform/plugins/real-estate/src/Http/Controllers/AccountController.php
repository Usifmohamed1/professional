<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Media\Models\MediaFile;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Forms\AccountForm;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Http\Requests\AccountEditRequest;
use Botble\RealEstate\Http\Resources\AccountResource;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Tables\AccountTable;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Botble\Slug\Services\SlugService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends BaseController
{
    protected string $accountType = Account::TYPE_USER;

    protected string $routePrefix = 'account';

    protected string $translationPrefix = 'plugins/real-estate::account';

    protected string $tableClass = AccountTable::class;

    public function __construct()
    {
        OptimizerHelper::disable();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::account.name'), route('account.index'));
    }

    public function index()
    {
        $this->pageTitle(trans($this->translationPrefix . '.name'));

        $dataTable = app($this->tableClass);

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans($this->translationPrefix . '.create'));

        $form = AccountForm::create()
            ->remove('is_change_password')
            ->remove('social_links');

        if ($this->accountType !== Account::TYPE_AGENT) {
            $form->remove([
                'slug',
                'contact_email',
                'is_featured',
                'is_public_profile',
                'hide_phone',
                'hide_email',
            ]);
        }

        return $form->renderForm();
    }

    public function store(AccountCreateRequest $request)
    {
        $form = AccountForm::create();

        $form
            ->saving(function (AccountForm $form) use ($request): void {
                $account = $form->getModel();

                $data = $request->except('password');
                if ($this->accountType !== Account::TYPE_AGENT) {
                    unset($data['contact_email']);
                }

                $account->fill($data);
                $account->account_type = $this->accountType;

                $account->is_featured = $request->input('is_featured');
                $account->is_public_profile = $this->accountType === Account::TYPE_AGENT
                    ? true
                    : $request->input('is_public_profile', 0);
                $account->confirmed_at = Carbon::now();
                $account->approved_at = Carbon::now();

                $account->password = Hash::make($request->input('password'));
                $account->dob = Carbon::parse($request->input('dob'))->toDateString();

                if ($avatarImage = $request->input('avatar_image')) {
                    $account->avatar_id = MediaFile::query()
                        ->where('url', $avatarImage)
                        ->value('id');
                } else {
                    $account->avatar_id = null;
                }

                $account->save();
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route($this->routePrefix . '.index'))
            ->setNextUrl(route($this->routePrefix . '.edit', $form->getModel()->id))
            ->withCreatedSuccessMessage();
    }

    public function edit(Account $account)
    {
        $this->ensureAccountType($account);

        $this->pageTitle(trans($this->translationPrefix . '.edit', ['name' => $account->name]));

        $account->password = null;

        $form = AccountForm::createFromModel($account)
            ->remove('social_links');

        if ($this->accountType !== Account::TYPE_AGENT) {
            $form->remove([
                'slug',
                'contact_email',
                'is_featured',
                'is_public_profile',
                'hide_phone',
                'hide_email',
            ]);
        }

        return $form->renderForm();
    }

    public function update(Account $account, AccountEditRequest $request)
    {
        $this->ensureAccountType($account);

        $form = AccountForm::createFromModel($account)->setRequest($request);

        $form
            ->saving(function (AccountForm $form) use ($request): void {
                $account = $form->getModel();

                $data = $request->except('password');
                if ($this->accountType !== Account::TYPE_AGENT) {
                    unset($data['contact_email']);
                }

                $account->fill($data);

                if ($request->input('is_change_password') == 1) {
                    $account->password = Hash::make($request->input('password'));
                }

                $account->dob = Carbon::parse($request->input('dob'))->toDateString();

                if ($avatarImage = $request->input('avatar_image')) {
                    $account->avatar_id = MediaFile::query()
                        ->where('url', $avatarImage)
                        ->value('id');
                } else {
                    $account->avatar_id = null;
                }

                $account->is_featured = $request->input('is_featured');
                $account->is_public_profile = $request->input('is_public_profile', 0);

                // Handle blocking/unblocking
                $isBlocked = $request->input('is_blocked', 0);
                if ($isBlocked) {
                    $account->blocked_at = now();
                    $account->blocked_reason = $request->input('blocked_reason');
                } else {
                    $account->blocked_at = null;
                    $account->blocked_reason = null;
                }

                $account->save();

                if ($request->filled('slug')) {
                    $slug = Slug::query()->firstOrNew([
                        'reference_type' => Account::class,
                        'reference_id' => $account->getKey(),
                    ]);

                    $slug->key = (new SlugService())->create(
                        $request->input('slug'),
                        $slug->exists ? $slug->getKey() : 0
                    );
                    $slug->prefix = SlugHelper::getPrefix(Account::class, '', false);
                    $slug->save();
                }

                event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route($this->routePrefix . '.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Account $account)
    {
        $this->ensureAccountType($account);

        return DeleteResourceAction::make($account);
    }

    public function getList(Request $request)
    {
        $keyword = BaseHelper::stringify($request->input('q'));

        if (! $keyword) {
            return $this
                ->httpResponse()
                ->setData([]);
        }

        $data = Account::query()
            ->agents()
            ->where(function ($query) use ($keyword): void {
                $query
                    ->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
            })
            ->select(['id', 'first_name', 'last_name'])
            ->take(10)
            ->get();

        return $this
            ->httpResponse()
            ->setData(AccountResource::collection($data));
    }

    public function verifyEmail(int|string $id, Request $request)
    {
        $account = Account::query()
            ->where([
                'id' => $id,
                'confirmed_at' => null,
            ])->firstOrFail();

        $account->confirmed_at = Carbon::now();
        $account->save();

        event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route($this->routePrefix . '.index'))
            ->withUpdatedSuccessMessage();
    }

    public function show(Account $account)
    {
        $this->ensureAccountType($account);

        $this->pageTitle(trans($this->translationPrefix . '.view', ['name' => $account->name]));

        return view('plugins/real-estate::accounts.view', compact('account'));
    }

    public function verify(Account $account, Request $request)
    {
        $this->ensureAccountType($account);

        $account->is_verified = true;
        $account->verified_at = Carbon::now();
        $account->verified_by = auth()->id();
        $account->verification_note = $request->input('verification_note');
        $account->save();

        event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route($this->routePrefix . '.index'))
            ->setMessage(trans('plugins/real-estate::account.verified_successfully'))
            ->withUpdatedSuccessMessage();
    }

    public function unverify(Account $account, Request $request)
    {
        $this->ensureAccountType($account);

        $account->is_verified = false;
        $account->verified_at = null;
        $account->verified_by = null;
        $account->verification_note = null;
        $account->save();

        event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route($this->routePrefix . '.index'))
            ->setMessage(trans('plugins/real-estate::account.unverified_successfully'))
            ->withUpdatedSuccessMessage();
    }

    protected function ensureAccountType(Account $account): void
    {
        abort_unless($account->account_type === $this->accountType, 404);
    }
}
