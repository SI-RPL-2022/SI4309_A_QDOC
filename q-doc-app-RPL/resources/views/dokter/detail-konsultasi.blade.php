<x-layout-dokter>
    <x-slot name="title">Detail Konsultasi</x-slot>

    <main class="mt-5">
        <div class="container">
            <h3 class="text-center">Detail Konsultasi</h3>

            <div class="px-5">
                <x-profile-field :label="'ID'" :content="$consultation->id" />
                <x-profile-field :label="'Nama Pasien'" :content="$consultation->patient->name" />

                @php
                    $date = new \Carbon\Carbon($consultation->date, 'Asia/Jakarta');
                @endphp

                <x-profile-field :label="'Tanggal'" :content="$date->isoFormat('dddd, DD MMMM G')" />
                <x-profile-field :label="'Status'" :content="$consultation->is_done ? 'Selesai' : 'Belum selesai'" />

                <div class="mb-3">
                    <h5 class="d-block mb-2">Resep</h5>
                    <textarea class="form-control" rows="6" readonly aria-readonly="true">{{ $consultation->receipt }}</textarea>
                </div>
            </div>
        </div>
    </main>
</x-layout-dokter>
