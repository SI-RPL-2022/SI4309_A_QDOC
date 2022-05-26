@php
$user = request()->user();
@endphp
<x-layout-dokter>
    <x-slot name="title">Tabel Konsultasi</x-slot>

    <main class="container p-5 d-flex flex-column justify-content-center">
        <h3 class="text-center">Jadwal Konsultasi Pasien</h3>

        <div id="alert-container">
        </div>

        @if ($consultations->count() === 0)
            <h5 class="text-center my-4">
                Daftar konsultasi masih kosong.
            </h5>
        @else
            <table class="table table-bordered mt-3 text-center">
                <tr class="bg-brand text-light">
                    <th scope="col">Nama</th>
                    <th scope="col">Hari</th>
                    <th scope="col">Tanggal / Jam</th>
                    <th scope="col">No. Antrian</th>
                    <th scope="col">Aksi</th>
                </tr>

                @foreach ($consultations as $consultation)
                    <x-konsultasi-item :consultationId="$consultation->id" :patient="$consultation->patient" :date="$consultation->date" :shiftStart="$consultation->schedule->shift_start"
                        :shiftEnd="$consultation->schedule->shift_end" :queue="$consultation->queue" />
                @endforeach
            </table>
        @endif
    </main>

    <x-slot name="afterBsScript">

    </x-slot>

</x-layout-dokter>
