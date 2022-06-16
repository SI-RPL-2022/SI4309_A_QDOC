@props(['consultation'])
@php
$date = new \Carbon\Carbon($consultation->date, 'Asia/Jakarta');
@endphp

<tr class="bg-brand" style="--bg-opacity: 0.3">
    <td><a
            href="{{ route('dokter.profile.pasien', ['user' => $consultation->patient->id]) }}">{{ $consultation->patient->name }}</a>
    </td>
    <td>{{ $date->isoFormat('dddd') }}</td>
    <td>{{ $date->isoFormat('DD MMMM G') }}</td>
    <td>
        <a href="{{ route('dokter.konsultasi.detail', ['consultation' => $consultation->id]) }}">
            <button type="button" class="btn btn-sm btn-primary">Detail</button>
        </a>
    </td>
</tr>
