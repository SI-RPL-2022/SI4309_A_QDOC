@php
$user = request()->user();
@endphp
<x-layout-dokter>
    <x-slot name="title">Tabel Konsultasi</x-slot>

    <main class="container p-5 d-flex flex-column justify-content-center">
        <h3 class="text-center">Jadwal Konsultasi Pasien</h3>

        <div id="alert-container">
        </div>
        @if (session('success'))
            <div class="mt-3 alert alert-success alert-dismissible fade show" role="alert">
                Berhasil menambahkan resep pada konsultasi.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
        <!-- Modal resep -->
        <div class="modal fade" id="modal-resep" tabindex="-1" aria-labelledby="modal-resep-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-resep-label">Input Resep</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form id="form-resep" method="post" novalidate>
                            @csrf

                            <!-- Field id -->
                            <input type="hidden" name="id" id="id-konsultasi" />

                            <div class="container-fluid">
                                <!-- Field nama -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="nama-pasien" class="form-label fs-5">Nama Pasien</label>
                                        <input type="text" id="nama-pasien" class="form-control" aria-disabled="true"
                                            disabled />
                                    </div>
                                </div>

                                <!-- Field resep -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="resep" class="form-label fs-5">Resep</label>
                                        <textarea name="receipt" id="resep" class="form-control" rows="5" aria-required="true" required></textarea>
                                        <div class="invalid-feedback">Resep wajib diisi!</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="submit-modal-resep">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal konfirmasi resep -->
        <div class="modal fade" id="modal-konfirmasi-resep" tabindex="-1"
            aria-labelledby="modal-konfirmasi-resep-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-konfirmasi-resep-label">
                            Konfirmasi Input Resep
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <p>
                            Apakah anda yakin ingin menyimpan resep ini? Setelah menyimpan
                            anda tidak dapat lagi mengubah resep ini.
                        </p>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#modal-resep" data-batal-konfirmasi="true">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="konfirmasi-modal-resep"
                            data-bs-dismiss="modal">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-slot name="afterBsScript">
        <!-- Script modal -->
        <script>
            (function() {
                "use strict";

                // Modal resep

                // Ambil elemen modal resep
                let modalResepEl = document.getElementById("modal-resep");

                // Ambil elemen form resep
                let formResep = document.getElementById("form-resep"); // Elemen form resep
                let modalResepLabel = modalResepEl.querySelector("#modal-resep-label"); // Label modal resep
                let fieldNamaPasienModal = formResep.querySelector("#nama-pasien"); // Field nama pasien
                let fieldResep = formResep.querySelector("#resep"); // Field resep
                let fieldId = formResep.querySelector('#id-konsultasi'); // Field id konsultasi

                // deteksi event modal resep muncul
                modalResepEl.addEventListener("show.bs.modal", function(event) {
                    // Ambil elemen button yang ditekan
                    let relatedButton = event.relatedTarget;

                    // Atur ulang data form jika pemanggil modal bukan tombol batal konfirmasi
                    let isCancelConfirmation = relatedButton.getAttribute(
                        "data-batal-konfirmasi"
                    );

                    if (!isCancelConfirmation) {


                        let idKonsultasi = relatedButton.getAttribute('data-id-konsultasi');
                        let namaPasien = relatedButton.getAttribute("data-nama-pasien");
                        let dataResep = relatedButton.getAttribute("data-resep");

                        // atur value field resep dan nama pasien sesuai data resep yang ada
                        fieldId.value = idKonsultasi;
                        fieldResep.value = dataResep;
                        fieldNamaPasienModal.value = namaPasien;
                    }
                });

                // Tangani event submit resep

                // Elemen button submit resep
                let submitResepButton = modalResepEl.querySelector(
                    "#submit-modal-resep"
                );

                // Elemen modal konfirmasi
                let modalKonfirmasiResepEl = document.getElementById(
                    "modal-konfirmasi-resep"
                );

                let isConfirming = false; // status konfirmasi = false

                submitResepButton.addEventListener("click", function(event) {
                    let modalResep = bootstrap.Modal.getOrCreateInstance(modalResepEl);

                    // Tampilkan hasil validasi
                    formResep.classList.add("was-validated");

                    // Lakukan konfirmasi jika form valid
                    if (formResep.checkValidity()) {
                        isConfirming = true; // set status konfirmasi = true
                        modalResep.hide();
                    }
                });

                // tangani event ketika modal resep menghilang
                modalResepEl.addEventListener("hide.bs.modal", function(event) {
                    // Jika status konfirmasi = false, jangan lakukan apa-apa
                    if (!isConfirming) return;

                    let modalKonfirmasiResep = bootstrap.Modal.getOrCreateInstance(
                        modalKonfirmasiResepEl
                    );

                    // reset validasi
                    formResep.classList.remove("was-validated");

                    // Tampilkan modal konfirmasi
                    modalKonfirmasiResep.show();
                });

                // Tangani ketika resep di konfirmasi

                // Elemen button konfirmasi resep
                let konfirmasiResepButton = modalKonfirmasiResepEl.querySelector(
                    "#konfirmasi-modal-resep"
                );

                konfirmasiResepButton.addEventListener("click", function(event) {

                    let xhr = new XMLHttpRequest();

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            let result = JSON.parse(xhr.responseText);

                            if (xhr.status === 200) {
                                window.location.replace('{{ route('dokter.konsultasi.show') }}');
                            } else if (xhr.status === 422) {
                                let alertElem = document.createElement('div');
                                alertElem.classList.add('alert', 'alert-danger', 'alert-dismissible',
                                    'fade', 'show');
                                alertElem.setAttribute('role', 'alert');

                                let alertStrong = document.createElement('strong');
                                alertStrong.textContent = 'Error!';

                                let alertClose = document.createElement('button');
                                alertClose.type = 'button';
                                alertClose.classList.add('btn-close');
                                alertClose.setAttribute('data-bs-dismiss', 'alert');
                                alertClose.setAttribute('aria-label', 'Close');

                                alertElem.appendChild(alertStrong);
                                alertElem.append(' Data yang anda berikan tidak valid');
                                alertElem.appendChild(alertClose);

                                document.querySelector('#alert-container').appendChild(alertElem);
                            } else if (xhr.status === 500) {
                                let alertElem = document.createElement('div');
                                alertElem.classList.add('alert', 'alert-danger', 'alert-dismissible',
                                    'fade', 'show');
                                alertElem.setAttribute('role', 'alert');

                                let alertStrong = document.createElement('strong');
                                alertStrong.textContent = 'Error!';

                                let alertClose = document.createElement('button');
                                alertClose.type = 'button';
                                alertClose.classList.add('btn-close');
                                alertClose.setAttribute('data-bs-dismiss', 'alert');
                                alertClose.setAttribute('aria-label', 'Close');

                                alertElem.appendChild(alertStrong);
                                alertElem.append(' Terjadi kesalahan di latar belakang, mohon coba lagi.');
                                alertElem.appendChild(alertClose);

                                document.querySelector('#alert-container').appendChild(alertElem);
                            } else {
                                console.log(xhr.responseText);
                            }
                        }
                    }


                    let formData = new FormData(formResep);

                    let konsultasiData = {};

                    for (let pair of formData.entries()) {
                        konsultasiData[pair[0]] = pair[1];
                    }

                    xhr.open('PUT', '{{ route('dokter.konsultasi.show') }}/' + konsultasiData.id);

                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.send(JSON.stringify(konsultasiData));


                });

                // tangani event ketika modal konfirmasi menghilang
                modalKonfirmasiResepEl.addEventListener(
                    "hide.bs.modal",
                    function(event) {
                        // Atur status konfirmasi = false
                        isConfirming = false;
                    }
                );
            })();
        </script>
    </x-slot>

</x-layout-dokter>
