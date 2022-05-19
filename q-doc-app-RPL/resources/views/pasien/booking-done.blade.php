<x-layout-pasien>
    <x-slot name="title">Booking</x-slot>

    <main class="mt-5">
        <h3 class="text-center">Booking</h3>

        <div class="container my-3 px-5">
            <form class="px-md-5">
                <div class="row px-md-5">
                    <div class="col-12 mb-3">
                        <label for="nama-dokter" class="form-label fs-5">Nama Dokter</label>
                        <input type="text" name="nama-dokter" id="nama-dokter" class="form-control"
                            value="Dr. {{ $consultation->doctor->name }}" aria-disabled="true" disabled />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="hari-tanggal" class="form-label fs-5">Hari / Tanggal</label>
                        <select name="hari-tanggal" id="hari-tanggal" class="form-select" aria-disabled="true"
                            disabled>
                            @php
                                $date = new \Carbon\Carbon($consultation->date);
                                $date = $date->locale('id');
                                $dateString = $date->isoFormat('dddd, DD MMMM G');
                            @endphp
                            <option value="2021-12-19" aria-selected="true" selected>
                                {{ $dateString }}.
                            </option>
                        </select>
                    </div>

                    <div class="col-12 mb-1">
                        <h5>Jam</h5>
                        <p class="ps-1">{{ $consultation->schedule->shift_start }} -
                            {{ $consultation->schedule->shift_end }}</p>
                    </div>

                    <div class="col-12 mb-1">
                        <h5>No. Antrian Anda</h5>
                        <p class="p-2 bg-warning d-inline-block no-antrian">{{ $consultation->queue }}</p>
                    </div>

                    <div class="col-12 mb-1">
                        <h5>Antrian sekarang</h5>
                        <p class="ps-1">{{ $currentQueue }}</p>
                    </div>
                </div>
            </form>
        </div>
    </main>
</x-layout-pasien>
