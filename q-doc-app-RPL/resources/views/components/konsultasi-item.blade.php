@props(['consultationId', 'patient', 'date', 'shiftStart', 'shiftEnd', 'queue'])
<?php
$scheduleDate = new Carbon\Carbon($date, 'Asia/Jakarta');
?>
<tr class="bg-brand" style="--bg-opacity: 0.3">
    <td>{{ $patient->name }}</td>
    <td>{{ $scheduleDate->locale('id')->dayName }}</td>
    <td>{{ $scheduleDate->day . ' ' . $scheduleDate->locale('id')->monthName . ' ' . $scheduleDate->year . ', ' . $shiftStart . ' - ' . $shiftEnd }}
    </td>
    <td>{{ $queue }}</td>
    <td>
        <button type="button" class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-resep"
            data-id-konsultasi="{{ $consultationId }}" data-nama-pasien="{{ $patient->name }}" data-resep="">
            Resep
        </button>
    </td>
</tr>
