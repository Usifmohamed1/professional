@php
    Theme::set('breadcrumbEnabled', 'no');
    if (theme_option('breadcrumb_background_image_forgot_password')) {
        Theme::set('breadcrumbBackgroundImage', theme_option('breadcrumb_background_image_forgot_password'));
    }
@endphp

<section class="editorial-auth" aria-label="{{ __('Your account') }}">
    <div class="editorial-auth-intro">
        <a class="editorial-auth-home" href="{{ BaseHelper::getHomepageUrl() }}" aria-label="{{ __('Home') }}">@if ($accountLogo = Theme::getLogoImage(maxHeight: 42))
                {{ $accountLogo }}
            @else
                <span class="editorial-wordmark"><strong>RE<span>/</span>MAX</strong><small>PROFESSIONAL</small></span>
            @endif</a>
        <div>
            <span class="editorial-eyebrow">{{ __('RE/MAX Professional') }}</span>
            <h1>{{ __('A fresh start.') }}</h1>
            <p>{{ __('Choose a new password to securely access your account.') }}</p>
        </div>
        <a class="editorial-text-link" href="{{ BaseHelper::getHomepageUrl() }}">{{ __('Explore the website') }} <span aria-hidden="true">↗</span></a>
    </div>
    <div class="editorial-auth-form">{!! $form->renderForm() !!}</div>
</section>
