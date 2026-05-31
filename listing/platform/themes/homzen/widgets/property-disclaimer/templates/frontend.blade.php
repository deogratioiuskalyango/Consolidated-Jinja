@if($disclaimer)
    <div class="single-property-element property-disclaimer">
        <h4 class="property-disclaimer-title">{{ __('Disclaimer') }}</h4>
        <p class="property-disclaimer-body">
            @php
                $decoded = html_entity_decode($disclaimer);
                $safe = strip_tags($decoded, '<strong><b><em><i>');
                $agentHtml = $agentUrl
                    ? '<a href="' . e($agentUrl) . '" class="property-disclaimer-agent">' . e($agentName) . '</a>'
                    : '<strong>' . e($agentName) . '</strong>';
            @endphp
            {!! str_replace(e($agentName), $agentHtml, $safe) !!}
        </p>
    </div>
@endif
