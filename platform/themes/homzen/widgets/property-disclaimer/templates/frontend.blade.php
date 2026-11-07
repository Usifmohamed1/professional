@if($disclaimer)
    <div class="single-property-element" style="background-color: #fdf6e3; padding: 20px; border-left: 4px solid #d4a017; border-radius: 4px;">
        <div style="font-weight: 700; margin-bottom: 8px;">{{ __('Disclaimer') }}</div>
        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #555;">
            {!! BaseHelper::clean(str_replace($agentName, '<strong>' . e($agentName) . '</strong>', $disclaimer)) !!}
        </p>
    </div>
@endif
