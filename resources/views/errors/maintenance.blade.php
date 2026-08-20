@php
    // Determine template: check query parameter ?template= or ?preview= or configuration in $details
    $requestedTemplate = request('template') ?? request('preview') ?? ($details['template'] ?? 'animated');
    $template = in_array($requestedTemplate, ['dark', 'light', 'animated']) ? $requestedTemplate : 'animated';
@endphp

@if ($template === 'dark')
    @include('errors.maintenance-dark', ['details' => $details])
@elseif ($template === 'light')
    @include('errors.maintenance-light', ['details' => $details])
@else
    @include('errors.maintenance-animated', ['details' => $details])
@endif
