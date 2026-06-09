@php
    $complete = $complete ?? false;
@endphp

<div class="booking-stepper {{ $complete ? 'booking-stepper--success' : '' }}">
    @foreach ($steps as $index => $label)
        <div class="booking-step {{ $complete ? 'is-complete' : '' }}" @unless ($complete) data-step-indicator @endunless>
            <span>{{ $index + 1 }}</span>
            <strong>{{ $label }}</strong>
        </div>
    @endforeach
</div>
