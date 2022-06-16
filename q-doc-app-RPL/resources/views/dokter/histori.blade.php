{{-- @dd($consultations) --}}
<x-layout-dokter>
    <x-slot name="title">Histori Konsultasi</x-slot>

    <main class="container p-5 d-flex flex-column justify-content-center">
        <h3 class="text-center">Tabel Histori Konsultasi</h3>

        @if ($consultations->count() === 0)
            <h5 class="text-center my-4">
                Daftar konsultasi masih kosong.
            </h5>
        @else
            <table class="table table-bordered my-4 text-center">
                <tr class="bg-brand text-light">
                    <th scope="col">Nama</th>
                    <th scope="col">Hari</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Aksi</th>
                </tr>

                @foreach ($consultations as $consultation)
                    <x-histori-item :consultation="$consultation" />
                @endforeach
            </table>
            {{ $consultations->links() }}
        @endif
    </main>
</x-layout-dokter>
