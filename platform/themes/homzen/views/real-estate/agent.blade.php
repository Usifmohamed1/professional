@php
    Theme::set('breadcrumbEnabled', 'no');
    Theme::set('pageTitle', $account->name);
@endphp

<section class="agent-detail-section">
    <div class="agent-header">
        <div class="agent-avatar">
            {{ RvMedia::image($account->avatar_url, $account->name, 'medium-square') }}
        </div>
        <div class="agent-info">
            <h1 class="h2 agent-name">{{ $account->name }} {!! $account->badge !!}</h1>
            @if($account->company)
                <p class="agent-company">{!! BaseHelper::clean(__('Company Agent at :company', ['company' => "<strong>$account->company</strong>"])) !!}</p>
            @endif
<div class="agent-contact-info">
    @if($account->phone && ! setting('real_estate_hide_agency_phone', false) && ! $account->hide_phone)
                @php
                    $normalizedPhone = preg_replace('/[^0-9]/', '', $account->phone);
                    if (str_starts_with($normalizedPhone, '0')) {
                        $normalizedPhone = '20' . substr($normalizedPhone, 1);
                    }
                @endphp
                <a href="tel:+{{ $normalizedPhone }}" class="agent-info-item">
                    <x-core::icon name="ti ti-phone" />
                    {{ preg_replace('/\s+/', '', $account->phone) }}
                </a>

                @endif
                @if($account->public_email && ! setting('real_estate_hide_agency_email', false) && ! $account->hide_email)
                    <a href="mailto:{{ $account->public_email }}" class="agent-info-item">
                        <x-core::icon name="ti ti-mail" />
                        {{ $account->public_email }}
                    </a>
                @endif
            </div>

            @if($account->whatsapp)
                @php
                    $normalizedWhatsapp = preg_replace('/[^0-9]/', '', $account->whatsapp);
                    if (str_starts_with($normalizedWhatsapp, '0')) {
                        $normalizedWhatsapp = '20' . substr($normalizedWhatsapp, 1);
                    } elseif (! str_starts_with($normalizedWhatsapp, '2')) {
                        $normalizedWhatsapp = '2' . $normalizedWhatsapp;
                    }
                @endphp
                <div class="agent-whatsapp-section mt-3">
                    <a href="https://wa.me/+{{ $normalizedWhatsapp }}" target="_blank" class="contact-whatsapp-btn justify-content-center">
                        <x-core::icon name="ti ti-brand-whatsapp" />
                        {{ __('Chat on WhatsApp') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($account->description)
        <div class="agent-about-section">
            <h5>{{ __('About Agent') }}</h5>
            <p class="agent-description">{!! BaseHelper::clean($account->description) !!}</p>
        </div>
    @endif

    @if ($properties->isNotEmpty())
        <div class="agent-properties-section">
            <h5>{{ __('Properties by this agent') }}</h5>
            @include(Theme::getThemeNamespace('views.real-estate.properties.index'))
        </div>
    @endif

    {!! apply_filters('real_estate_agent_details', null, $account) !!}
</section>
