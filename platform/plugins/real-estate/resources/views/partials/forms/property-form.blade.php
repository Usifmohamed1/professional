@extends('core/base::forms.form')

@section('form_end')
    @if($form->getModel()?->is_pending_moderation)
        <x-core::modal.action
            id="approve-property-modal"
            type="success"
            :title="trans('plugins/real-estate::property.status_moderation.approve_title')"
            :form-action="route('property.approve', $form->getModel())"
        >
            <div class="text-muted text-break mb-3">
                {{ trans('plugins/real-estate::property.status_moderation.approve_message') }}
            </div>

            @php
                $agents = \Botble\RealEstate\Models\Account::query()
                    ->select(['id', 'first_name', 'last_name', 'email'])
                    ->agents()
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get();

                $selectedAgentId = old('author_id');
            @endphp

            <x-core::form.select
                name="author_id"
                :label="trans('plugins/real-estate::property.status_moderation.approve_agent')"
                :required="true"
            >
                <option value="">{{ trans('plugins/real-estate::property.select_account') }}</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" @selected((string) $selectedAgentId === (string) $agent->id)>
                        {{ trim($agent->first_name . ' ' . $agent->last_name) }}{{ $agent->email ? ' - ' . $agent->email : '' }}
                    </option>
                @endforeach
            </x-core::form.select>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <x-core::button type="submit" color="success" class="w-100">
                                {{ trans('core/base::tables.submit') }}
                            </x-core::button>
                        </div>
                        <div class="col">
                            <x-core::button type="button" class="w-100" data-bs-dismiss="modal">
                                {{ trans('core/base::base.close') }}
                            </x-core::button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-core::modal.action>

        <x-core::modal.action
            id="reject-property-modal"
            type="danger"
            :title="trans('plugins/real-estate::property.status_moderation.reject_title')"
            :form-action="route('property.reject', $form->getModel())"
        >
            <div class="text-muted">{{ trans('plugins/real-estate::property.status_moderation.reject_message') }}</div>

            <textarea
                name="reason"
                class="form-control mt-3"
                placeholder="{{ trans('plugins/real-estate::property.status_moderation.reject_reason') }}"
            ></textarea>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <x-core::button type="submit" color="danger" class="w-100">
                                {{ trans('core/base::tables.submit') }}
                            </x-core::button>
                        </div>
                        <div class="col">
                            <x-core::button type="button" class="w-100" data-bs-dismiss="modal">
                                {{ trans('core/base::base.close') }}
                            </x-core::button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-core::modal.action>
    @endif
@endsection
