@if (!is_null($value) && $value !== '')
    <div class="row mb-1">
        <div class="col-4 text-muted">{{ $label }}</div>
        <div class="col-8">: {{ $value }}</div>
    </div>
@endif
