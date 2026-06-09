<form
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('rooms.booking.store', ['room' => $room->nomor_kamar]) }}"
    class="booking-form"
    data-booking-stepper
    data-initial-step="{{ $initialStep }}"
    data-daily="{{ (float) $room->harga_harian }}"
    data-monthly="{{ (float) $room->harga_bulanan }}"
    data-deposit="{{ (float) $room->deposit }}"
    data-booking-conflicts='@json($bookingConflicts, JSON_HEX_APOS)'
>
    @csrf

    @include('pages.rooms.partials.booking.stepper')

    @if ($errors->any())
        <div class="booking-alert">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($needsDataStep)
        @include('pages.rooms.partials.booking.panels.data')
    @endif

    @include('pages.rooms.partials.booking.panels.reservation')
    @include('pages.rooms.partials.booking.panels.notice')

    <div class="booking-actions">
        <button type="button" class="btn-outline-rooms" data-step-prev>Kembali</button>
        <button type="button" class="btn-wa-card" data-step-next>Lanjut</button>
        <button type="submit" class="btn-wa-card" data-step-submit>Kirim Reservasi & Bukti TF</button>
    </div>
</form>
