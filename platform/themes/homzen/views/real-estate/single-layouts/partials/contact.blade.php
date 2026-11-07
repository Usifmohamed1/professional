@php
    $model = $model ?? $property ?? null;
    $isProject = $model instanceof \Botble\RealEstate\Models\Project;
    $account = $model->author;
    $hasAssignedAgent = ! $isProject
        && $account instanceof \Botble\RealEstate\Models\Account
        && $account->exists;
    $assignedAgents = $hasAssignedAgent ? $model->agents : collect();

    if ($hasAssignedAgent && ! $assignedAgents->contains('id', $account->getKey())) {
        $assignedAgents->prepend($account);
    }

    $hasMultipleAssignedAgents = $assignedAgents->count() > 1;
    $whatsappAgentModalId = 'whatsapp-agent-choice-' . $model->getKey();
    $callAgentModalId = 'call-agent-choice-' . $model->getKey();
@endphp

<div @class(['widget-box single-property-contact', $class ?? null])>
    @if ($hasAssignedAgent)
        <div class="h7 title fw-7">{{ __('Contact Agent') }}</div>

        <div class="box-avatar">
            <div class="avatar avt-100 round">
                <a href="{{ $account->url }}" class="d-block">
                    {{ RvMedia::image($account->avatar_url, $account->name) }}
                </a>
            </div>
            <div class="info line-clamp-1">
                <div class="text-1 name">
                    <a href="{{ $account->url }}">{{ $account->name }} {!! $account->badge !!}</a>
                </div>
                @if ($account->phone && ! setting('real_estate_hide_agency_phone', false) && ! $account->hide_phone)
                    @php
                        $agentPhone = preg_replace('/\s+/', '', $account->phone);
                        if (str_starts_with($agentPhone, '0')) {
                            $agentPhone = '2' . $agentPhone;
                        }
                    @endphp
                    <a href="tel:+{{ $agentPhone }}" class="info-item">{{ preg_replace('/\s+/', '', $account->phone) }}</a>
                @endif
                @if ($account->public_email && ! setting('real_estate_hide_agency_email', false) && ! $account->hide_email)
                    <a href="mailto:{{ $account->public_email }}" class="info-item">{{ $account->public_email }}</a>
                @endif
            </div>
        </div>

        @foreach ($assignedAgents->where('id', '!=', $account->getKey()) as $additionalAgent)
            <div class="box-avatar mt-3">
                <div class="avatar avt-100 round">
                    <a href="{{ $additionalAgent->url }}" class="d-block">
                        {{ RvMedia::image($additionalAgent->avatar_url, $additionalAgent->name) }}
                    </a>
                </div>
                <div class="info line-clamp-1">
                    <div class="text-1 name">
                        <a href="{{ $additionalAgent->url }}">{{ $additionalAgent->name }} {!! $additionalAgent->badge !!}</a>
                    </div>
                    @if ($additionalAgent->phone && ! setting('real_estate_hide_agency_phone', false) && ! $additionalAgent->hide_phone)
                        @php
                            $additionalAgentPhone = preg_replace('/\s+/', '', $additionalAgent->phone);
                            if (str_starts_with($additionalAgentPhone, '0')) {
                                $additionalAgentPhone = '2' . $additionalAgentPhone;
                            }
                        @endphp
                        <a href="tel:+{{ $additionalAgentPhone }}" class="info-item">{{ preg_replace('/\s+/', '', $additionalAgent->phone) }}</a>
                    @endif
                    @if ($additionalAgent->public_email && ! setting('real_estate_hide_agency_email', false) && ! $additionalAgent->hide_email)
                        <a href="mailto:{{ $additionalAgent->public_email }}" class="info-item">{{ $additionalAgent->public_email }}</a>
                    @endif
                </div>
            </div>
        @endforeach
    @elseif ($isProject && ! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && $account && $account->exists)
        <div class="h7 title fw-7">{{ __('Project Contact Agency') }}</div>

        <div class="box-avatar">
            <div class="avatar avt-100 round">
                <a href="{{ $account->url }}" class="d-block">
                    {{ RvMedia::image($account->avatar_url, $account->name) }}
                </a>
            </div>
            <div class="info line-clamp-1">
                <div class="text-1 name">
                    <a href="{{ $account->url }}">{{ $account->name }} {!! $account->badge !!}</a>
                </div>
                @if ($account->phone && ! setting('real_estate_hide_agency_phone', false) && ! $account->hide_phone)
                    @php
                        $agentPhone = $isProject ? preg_replace('/\s+/', '', $account->phone) : preg_replace('/\s+/', '', $account->phone);
                        if (str_starts_with($agentPhone, '0')) {
                            $agentPhone = '2' . $agentPhone;
                        }
                    @endphp
                    <a href="tel:+{{ $agentPhone }}" class="info-item">{{ preg_replace('/\s+/', '', $account->phone) }}</a>
                @elseif($hotline = theme_option('hotline'))
                    @php
                        $hotlineLink = preg_replace('/[^0-9]/', '', $hotline);
                        if (str_starts_with($hotlineLink, '0')) {
                            $hotlineLink = '2' . $hotlineLink;
                        }
                    @endphp
                    <a href="tel:+{{ $hotlineLink }}" class="info-item">{{ $hotline }}</a>
                @endif
                @if ($account->public_email && ! setting('real_estate_hide_agency_email', false) && ! $account->hide_email)
                    <a href="mailto:{{ $account->public_email }}" class="info-item">{{ $account->public_email }}</a>
                @endif
            </div>
        </div>
    @elseif (! $isProject)
        <div class="h7 title fw-7">{{ __('Contact Agency') }}</div>
        <div class="box-avatar">
            @if ($agencyLogo = Theme::getLogoImage(['class' => 'img-fluid'], 'property_contact_agency_logo', 80) ?: Theme::getLogoImage(['class' => 'img-fluid'], maxHeight: 80))
                <div class="avatar avt-100 round agency-contact-logo">
                    {!! $agencyLogo !!}
                </div>
            @endif
            <div class="info line-clamp-1">
                <a href="tel:+201020622289" class="info-item">01020622289</a>
                <a href="mailto:info@professional-re.com" class="info-item">info@professional-re.com</a>
            </div>
        </div>
    @else
        <div class="h7 title fw-7">{{ __('Contact') }}</div>
    @endif

    @if ($isProject)
        {!! apply_filters('project_right_details_info', null, $model) !!}
    @else
        {!! apply_filters('property_right_details_info', null, $model) !!}
    @endif

    @if (RealEstateHelper::isEnabledConsultForm())
        {!! apply_filters('before_consult_form', null, $model) !!}

        {!! \Botble\RealEstate\Forms\Fronts\ConsultForm::create()
            ->formClass('contact-form')
            ->setFormInputWrapperClass('ip-group')
            ->modify('content', 'textarea', ['attr' => ['class' => 'form-control']])
            ->modify('submit', 'submit', ['attr' => ['class' => 'tf-btn primary w-100']])
            ->add('type', 'hidden', ['attr' => ['value' => $isProject ? 'project' : 'property']])
            ->add('data_id', 'hidden', ['attr' => ['value' => $model->getKey()]])
            ->addBefore('content', 'data_name', 'text', ['label' => false, 'attr' => ['value' => $model->name, 'disabled' => true, 'aria-label' => $isProject ? __('Project name') : __('Property name')]])
            ->renderForm()
        !!}

        {!! apply_filters('after_consult_form', null, $model) !!}
    @endif

    @php
        $whatsappNumber = null;

        if ($hasAssignedAgent) {
            $whatsappNumber = $account->whatsapp ?: ($hasMultipleAssignedAgents
                ? $assignedAgents->first(fn ($agent) => $agent->whatsapp)?->whatsapp
                : null);
        } elseif (! $isProject) {
            $whatsappNumber = '01003039070';
        } elseif ($isProject && ! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && $account && $account->exists && $account->whatsapp) {
            $whatsappNumber = $account->whatsapp;
        } elseif ($isProject && ($globalWhatsapp = theme_option('whatsapp_phone_number'))) {
            $whatsappNumber = $globalWhatsapp;
        }
    @endphp

    @if ($whatsappNumber && (theme_option('enable_whatsapp_button', 'yes') === 'yes' || (! $isProject && ! $hasAssignedAgent)))
        @php
            $whatsappMessage = theme_option('whatsapp_inquiry_message', __('Hi, I have an inquiry about this property: [property_url]'));
            $whatsappMessage = str_replace('[property_url]', $model->url, $whatsappMessage);
            $whatsappDigits = preg_replace('/[^0-9]/', '', $whatsappNumber);
            if (str_starts_with($whatsappDigits, '0')) {
                $whatsappDigits = '20' . substr($whatsappDigits, 1);
            } elseif (! str_starts_with($whatsappDigits, '2')) {
                $whatsappDigits = '2' . $whatsappDigits;
            }

            $callNumber = null;
            if ($hasAssignedAgent && $account->phone) {
                $callNumber = preg_replace('/\s+/', '', $account->phone);
            } elseif ($isProject && ! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && $account && $account->exists && $account->phone) {
                $callNumber = preg_replace('/\s+/', '', $account->phone);
            } elseif (! $isProject) {
                $callNumber = '01020622289';
            }
        @endphp
        <div class="whatsapp-contact-section mt-4 pt-4" style="border-top: 1px solid #e4e4e4;">
            <div class="text-center mb-3">
                <p class="mb-2" style="color: #666; font-size: 14px;">{{ __('Or get instant response via WhatsApp') }}</p>
            </div>
            <div class="text-center">
                @if ($hasMultipleAssignedAgents)
                    <a href="#{{ $whatsappAgentModalId }}" data-bs-toggle="modal" class="contact-whatsapp-btn justify-content-center">
                @else
                    <a href="https://wa.me/+{{ $whatsappDigits }}?text={{ urlencode($whatsappMessage) }}" target="_blank" class="contact-whatsapp-btn justify-content-center">
                @endif
                    <x-core::icon name="ti ti-brand-whatsapp" />
                    <span>{{ __('Chat on WhatsApp') }}</span>
                </a>
            </div>

            @if ($hasMultipleAssignedAgents || $callNumber)
                <div class="text-center mt-3">
                    @if ($hasMultipleAssignedAgents)
                        <a href="#{{ $callAgentModalId }}" data-bs-toggle="modal" class="contact-whatsapp-btn justify-content-center" style="background: #dc2626 !important; border-color: #dc2626 !important;">
                    @else
                        @php
                            $callNumberLink = preg_replace('/[^0-9]/', '', $callNumber);
                            if (str_starts_with($callNumberLink, '0')) {
                                $callNumberLink = '2' . $callNumberLink;
                            }
                        @endphp
                        <a href="tel:+{{ $callNumberLink }}" class="contact-whatsapp-btn justify-content-center" style="background: #dc2626 !important; border-color: #dc2626 !important;">
                    @endif
                        <x-core::icon name="ti ti-phone" />
                        <span>{{ __('Make a call') }}</span>
                    </a>
                </div>
            @endif
        </div>

        @if ($hasMultipleAssignedAgents)
            <x-core::modal :id="$whatsappAgentModalId" :title="__('Choose an agent for WhatsApp')">
                <div class="list-group">
                    @foreach ($assignedAgents as $choiceAgent)
                        @if ($choiceAgent->whatsapp)
                            @php
                                $choiceWhatsapp = preg_replace('/[^0-9]/', '', $choiceAgent->whatsapp);
                                if (str_starts_with($choiceWhatsapp, '0')) {
                                    $choiceWhatsapp = '20' . substr($choiceWhatsapp, 1);
                                } elseif (! str_starts_with($choiceWhatsapp, '2')) {
                                    $choiceWhatsapp = '2' . $choiceWhatsapp;
                                }
                            @endphp
                            <a href="https://wa.me/+{{ $choiceWhatsapp }}?text={{ urlencode($whatsappMessage) }}" target="_blank" rel="noopener noreferrer" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>{{ $choiceAgent->name }}</span>
                                <span dir="ltr">{{ $choiceAgent->whatsapp }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </x-core::modal>

            <x-core::modal :id="$callAgentModalId" :title="__('Choose an agent to call')">
                <div class="list-group">
                    @foreach ($assignedAgents as $choiceAgent)
                        @if ($choiceAgent->phone)
                            @php
                                $choicePhone = preg_replace('/[^0-9]/', '', $choiceAgent->phone);
                                if (str_starts_with($choicePhone, '0')) {
                                    $choicePhone = '2' . $choicePhone;
                                } elseif (! str_starts_with($choicePhone, '2')) {
                                    $choicePhone = '2' . $choicePhone;
                                }
                            @endphp
                            <a href="tel:+{{ $choicePhone }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>{{ $choiceAgent->name }}</span>
                                <span dir="ltr">{{ $choiceAgent->phone }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </x-core::modal>
        @endif
    @endif
</div>
