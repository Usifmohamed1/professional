@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>
                {{ trans('plugins/real-estate::property.pending_properties') }}
            </x-core::card.title>
        </x-core::card.header>

        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>{{ trans('core/base::tables.id') }}</th>
                        <th>{{ trans('plugins/real-estate::property.form.images') }}</th>
                        <th>{{ trans('plugins/real-estate::property.form.name') }}</th>
                        <th>{{ trans('plugins/real-estate::property.account') }}</th>
                        <th>{{ trans('plugins/real-estate::property.unique_id') }}</th>
                        <th>{{ trans('plugins/real-estate::property.moderation_status') }}</th>
                        <th>{{ trans('core/base::tables.created_at') }}</th>
                        <th class="text-end">{{ trans('core/base::tables.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($properties as $property)
                        <tr>
                            <td>{{ $property->getKey() }}</td>
                            <td>
                                <img
                                    src="{{ RvMedia::getImageUrl($property->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                    alt="{{ $property->name }}"
                                    class="rounded"
                                    width="50"
                                    height="50"
                                >
                            </td>
                            <td>
                                <a href="{{ route('property.edit', $property->getKey()) }}">
                                    {{ $property->name }}
                                </a>
                            </td>
                            <td>
                                @if($property->author?->getKey())
                                    <a href="{{ route('account.edit', $property->author->getKey()) }}">
                                        {{ $property->author->name }}
                                    </a>
                                    @if($property->author->email)
                                        <div class="text-muted small">{{ $property->author->email }}</div>
                                    @endif
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td>{{ $property->unique_id ?: '—' }}</td>
                            <td>{!! BaseHelper::clean($property->moderation_status->toHtml()) !!}</td>
                            <td>{{ BaseHelper::formatDateTime($property->created_at) }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <x-core::button
                                        tag="a"
                                        :href="route('property.edit', $property->getKey())"
                                        size="sm"
                                        color="primary"
                                        icon="ti ti-check"
                                    >
                                        {{ trans('plugins/real-estate::property.status_moderation.approve') }}
                                    </x-core::button>

                                    <x-core::form
                                        :url="route('property.destroy', $property->getKey())"
                                        method="DELETE"
                                        onsubmit="return confirm('{{ trans('core/base::notices.confirm_delete') }}')"
                                    >
                                        <x-core::button
                                            type="submit"
                                            size="sm"
                                            color="danger"
                                            icon="ti ti-trash"
                                        >
                                            {{ trans('core/base::tables.delete') }}
                                        </x-core::button>
                                    </x-core::form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                {{ trans('core/base::tables.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($properties->hasPages())
            <x-core::card.footer>
                {{ $properties->links() }}
            </x-core::card.footer>
        @endif
    </x-core::card>
@endsection
