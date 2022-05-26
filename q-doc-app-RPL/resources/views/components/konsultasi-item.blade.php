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
    </td>
</tr>
