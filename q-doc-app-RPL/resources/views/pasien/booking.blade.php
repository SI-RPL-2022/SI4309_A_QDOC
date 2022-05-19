<x-layout-pasien>
    <x-slot name="title">Booking</x-slot>

    <main class="mt-5">
        <h3 class="text-center">Booking</h3>

        <div class="container my-3 px-5">
            <div class="px-md-5">
                <div class="px-md-5">
                    @if ($errors->any())
                        <div class="mt-3 alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>
            <form class="px-md-5 needs-validation" method="post" action="{{ route('pasien.booking.new') }}" novalidate>
                <div class="row px-md-5">
                    @csrf

                    <div class="col-12 mb-3">
                        <label for="nama-dokter" class="form-label fs-5">Nama Dokter</label>
                        <input type="text" name="nama-dokter" id="nama-dokter" class="form-control"
                            value="dr. Maibillisa" aria-readonly="true" aria-required="true" readonly required />
                        <div class="invalid-feedback">Nama wajib diisi!</div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="hari-tanggal" class="form-label fs-5">Hari / Tanggal</label>
                        <select name="schedule" id="hari-tanggal" class="form-select" aria-required="true" required>
                            <option aria-selected="true" selected>
                                Pilih hari/tanggal konsultasi
                            </option>
                            @foreach ($schedules as $schedule)
                                @php
                                    $date = new \Carbon\Carbon($schedule->date);
                                    $date = $date->locale('id');
                                    $dateString = $date->isoFormat('dddd, DD MMMM G');
                                @endphp
                                <option value="{{ $schedule->id }}">
                                    {{ $dateString }}. {{ $schedule->shift_start }} - {{ $schedule->shift_end }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Mohon pilih tanggal konsultasi!
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center mt-3 px-md-5">
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary px-5">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <x-slot name="afterBsScript">
        <script>
            (function() {
                "use strict";

                // Ambil form yang perlu di validasi
                let form = document.querySelector(".needs-validation");
                // Hari/tanggal input
                let hariTanggal = document.querySelector("#hari-tanggal");
                // Jam input
                let jam = document.querySelector("#jam");

                // Validasi ketika submit
                form.addEventListener(
                    "submit",
                    function(event) {
                        if (hariTanggal.value === "Pilih hari/tanggal konsultasi") {
                            hariTanggal.setCustomValidity(" ");
                            event.preventDefault();
                            event.stopPropagation();
                        }

                        form.classList.add("was-validated");
                    },
                    false
                );

                hariTanggal.addEventListener(
                    "change",
                    function(event) {
                        if (hariTanggal.value !== "Pilih hari/tanggal konsultasi") {
                            hariTanggal.setCustomValidity("");
                        } else {
                            hariTanggal.setCustomValidity(" ");
                        }
                    },
                    false
                );
            })();
        </script>
    </x-slot>
</x-layout-pasien>
