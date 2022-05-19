@props(['scheduleId', 'date', 'shiftStart', 'shiftEnd', 'scheduleStatus', 'consultations'])

@php
$scheduleDate = new Carbon\Carbon($date, 'Asia/Jakarta');
@endphp

<tr class="bg-brand" style="--bg-opacity: 0.3">
    <td>{{ $scheduleDate->locale('id')->dayName }}</td>
    <td>{{ $scheduleDate->day . ' ' . $scheduleDate->locale('id')->monthName . ' ' . $scheduleDate->year }}</td>
    <td>{{ $shiftStart . ' - ' . $shiftEnd }}</td>
    <td>{{ $consultations }}</td>
    <td>

        <button type="button" class="btn btn-secondary btn-sm px-2" data-bs-toggle="modal"
            data-bs-target="#modal-toggle-status" data-id-jadwal="{{ $scheduleId }}"
            data-action="{{ $scheduleStatus ? 'Nonaktifkan' : 'Aktifkan' }}">
            {{ $scheduleStatus ? 'Nonaktifkan' : 'Aktifkan' }}
        </button>

        <button type="button" class="btn btn-danger btn-sm px-2" data-bs-toggle="modal" data-bs-target="#modal-delete"
            data-id-jadwal="{{ $scheduleId }}" data-allowed="1">
            Hapus
        </button>
    </td>
</tr>
