@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>
                {{ trans('plugins/career::career.application.applications') }}
            </x-core::card.title>
        </x-core::card.header>

        <form
            method="POST"
            action="{{ route('careers.applications.bulk-destroy') }}"
            onsubmit="return confirm('{{ trans('core/table::table.confirm_bulk_message') }}')"
        >
            @csrf
            @method('DELETE')

            <x-core::card.body class="border-bottom">
                <button type="submit" class="btn btn-danger">
                    <x-core::icon name="ti ti-trash" />
                    {{ trans('core/table::table.delete') }}
                </button>
            </x-core::card.body>

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input class="form-check-input" type="checkbox" id="career-applications-check-all">
                            </th>
                            <th>{{ trans('core/base::tables.id') }}</th>
                            <th>{{ trans('plugins/career::career.application.form_name') }}</th>
                            <th>{{ trans('plugins/career::career.application.form_phone') }}</th>
                            <th>{{ trans('plugins/career::career.application.form_email') }}</th>
                            <th>{{ trans('plugins/career::career.application.career') }}</th>
                            <th>{{ trans('plugins/career::career.application.cv') }}</th>
                            <th>{{ trans('plugins/career::career.application.cv_size') }}</th>
                            <th>{{ trans('core/base::tables.created_at') }}</th>
                            <th>{{ trans('core/base::tables.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr>
                                <td>
                                    <input class="form-check-input career-application-item" type="checkbox" name="ids[]" value="{{ $application->getKey() }}">
                                </td>
                                <td>{{ $application->getKey() }}</td>
                                <td>{{ $application->name }}</td>
                                <td>
                                    @if ($application->phone)
                                        @php
                                            $phoneLink = preg_replace('/[^0-9]/', '', $application->phone);
                                            if (str_starts_with($phoneLink, '0')) {
                                                $phoneLink = '2' . $phoneLink;
                                            }
                                        @endphp
                                        <a href="tel:+{{ $phoneLink }}">{{ $application->phone }}</a>
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td>
                                    @if ($application->email)
                                        <a href="mailto:{{ $application->email }}">{{ $application->email }}</a>
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td>{{ $application->career?->name ?: '—' }}</td>
                                <td>
                                    @if ($application->cv_path)
                                        <a
                                            href="{{ route('careers.applications.download', $application) }}"
                                            class="btn btn-sm btn-primary"
                                        >
                                            <x-core::icon name="ti ti-download" />
                                            {{ $application->cv_original_name ?: trans('plugins/career::career.application.download_cv') }}
                                        </a>
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td>
                                    @if ($application->cv_size)
                                        {{ number_format($application->cv_size / 1024, 1) }} KB
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td>{{ $application->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        form="delete-career-application-{{ $application->getKey() }}"
                                        onclick="return confirm('{{ trans('core/table::table.confirm_bulk_message') }}')"
                                    >
                                        <x-core::icon name="ti ti-trash" />
                                        {{ trans('core/table::table.delete') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    {{ trans('core/base::tables.no_record') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach ($applications as $application)
            <form
                id="delete-career-application-{{ $application->getKey() }}"
                method="POST"
                action="{{ route('careers.applications.destroy', $application) }}"
                class="d-none"
            >
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        @if ($applications->hasPages())
            <x-core::card.footer>
                {{ $applications->links() }}
            </x-core::card.footer>
        @endif
    </x-core::card>
@endsection

@push('footer')
    <script>
        document.getElementById('career-applications-check-all')?.addEventListener('change', function () {
            document.querySelectorAll('.career-application-item').forEach((checkbox) => {
                checkbox.checked = this.checked
            })
        })
    </script>
@endpush
