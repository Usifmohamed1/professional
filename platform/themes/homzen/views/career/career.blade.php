<section class="flat-section career-single">
    <div class="career-single-content">
        <h2 class="career-name">{{ $career->name }}</h2>
        <div class="career-meta">
            <div class="career-meta-item">
                <x-core::icon name="ti ti-clock" />
                {{ Theme::formatDate($career->created_at) }}
            </div>
            <div class="career-meta-item">
                <x-core::icon name="ti ti-map-pin" />
                {{ $career->location }}
            </div>
        </div>
        <div class="career-apply-action">
            <a class="tf-btn primary" href="#career-application">
                {{ trans('plugins/career::career.application.title') }}
            </a>
        </div>
        <div class="ck-content single-detail career-content">
            {!! BaseHelper::clean($career->content) !!}
        </div>

        <div class="career-application" id="career-application">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div>
                    <h3 class="career-application-title">{{ trans('plugins/career::career.application.title') }}</h3>
                    <p class="career-application-description mb-0">{{ trans('plugins/career::career.application.description') }}</p>
                </div>
            </div>

            @if (session('career_application_success'))
                <div class="alert alert-success" role="alert">
                    {{ session('career_application_success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    {{ trans('plugins/career::career.application.validation_error') }}
                </div>
            @endif

            <form
                id="career-application-form"
                class="career-application-form"
                action="{{ route('public.careers.apply', $career) }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="career_application_name">{{ trans('plugins/career::career.application.form_name') }}</label>
                        <input
                            id="career_application_name"
                            class="form-control @error('name') is-invalid @enderror"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="career_application_phone">{{ trans('plugins/career::career.application.form_phone') }}</label>
                        <input
                            id="career_application_phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                        >
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="career_application_email">{{ trans('plugins/career::career.application.form_email') }}</label>
                        <input
                            id="career_application_email"
                            class="form-control @error('email') is-invalid @enderror"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="career_application_cv">{{ trans('plugins/career::career.application.form_cv') }}</label>
                        <input
                            id="career_application_cv"
                            class="form-control @error('cv') is-invalid @enderror"
                            type="file"
                            name="cv"
                            accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            required
                        >
                        <small class="form-text text-muted">{{ trans('plugins/career::career.application.form_cv_help') }}</small>
                        @error('cv')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button class="tf-btn primary" type="submit">
                    {{ trans('plugins/career::career.application.submit') }}
                </button>
            </form>
        </div>
    </div>

    <div class="career-related">
        <h3 class="career-related-title">{{ __('Related Careers') }}</h3>
        @include(Theme::getThemeNamespace('views.career.partials.list'), ['careers' => $relatedCareers])
    </div>
</section>
