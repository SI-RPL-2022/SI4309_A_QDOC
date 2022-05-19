<x-layout-dokter>
    <x-slot name="title">Jadwal Dokter</x-slot>

    <main class="container p-5 d-flex flex-column justify-content-center">
        <h3 class="text-center">Jadwal Dokter</h3>

        @if ($errors->any())
            <div class="mt-3 alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="mt-3 alert alert-success alert-dismissible fade show" role="alert">
                Berhasil menambahkan jadwal baru!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('deleteSuccess'))
            <div class="mt-3 alert alert-success alert-dismissible fade show" role="alert">
                Berhasil menghapus jadwal!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('deleteFailed'))
            <div class="mt-3 alert alert-danger alert-dismissible fade show" role="alert">
                Gagal menghapus jadwal!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('toggleStateSuccess'))
            <div class="mt-3 alert alert-success alert-dismissible fade show" role="alert">
                {{ session('toggleStateMessage') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($pastScheduleCount > 0)
            <div class="mt-3 alert alert-warning alert-dismissible fade show" role="alert">
                Anda memiliki jadwal dengan tanggal di masa lalu. Anda bisa menghapusnya dengan menekan tombol di bawah.
                <br>
                <a href="{{ route('dokter.jadwal.delete.past') }}"><button type="button"
                        class="btn btn-sm btn-danger mt-2">Hapus</button></a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div id="alert-container">
        </div>

        <div class="text-center mt-4">
            <button type="button" class="btn bg-brand text-light px-5" data-bs-toggle="modal"
                data-bs-target="#modal-jadwal">
                Tambah
            </button>

            <button type="button" class="btn btn-secondary px-5" data-bs-toggle="modal"
                data-bs-target="#modal-add-weekly">Tambah Mingguan</button>
        </div>

        @if ($schedules->count() === 0)
            <h5 class="text-center my-4">
                Anda belum menambahkan jadwal. Tambahkan jadwal dengan menekan tombol di atas.
            </h5>
        @else
            <table class="table table-bordered my-4 text-center">
                <tr class="bg-brand text-light">
                    <th scope="col">Hari</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Jam</th>
                    <th scope="col">Jumlah Konsultasi</th>
                    <th scope="col">Aksi</th>
                </tr>
                @foreach ($schedules as $schedule)
                    <x-jadwal-item :scheduleId="$schedule->id" :scheduleStatus="$schedule->is_active" :date="$schedule->date" :shiftStart="$schedule->shift_start"
                        :shiftEnd="$schedule->shift_end" :consultations="$schedule->consultations_count" />
                @endforeach

            </table>
        @endif


        <!-- Modal Input -->
        <div class="modal fade" id="modal-jadwal" tabindex="-1" aria-labelledby="modal-jadwal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-jadwal-label">Tambah jadwal</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form id="form-jadwal" action="{{ route('dokter.jadwal.new') }}" method="post" novalidate>
                            @csrf
                            <!-- Field id -->
                            <input type="hidden" name="id" id="id-jadwal" />

                            <div class="container-fluid">
                                <!-- Field tanggal -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="tanggal" class="form-label fs-5">Tanggal</label>
                                        <input type="date" name="date" id="tanggal" class="form-control"
                                            aria-required="true" required />
                                        <div class="invalid-feedback">Tanggal wajib diisi!</div>
                                    </div>
                                </div>

                                <!-- Field jam mulai -->
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                                        <label for="jam-mulai" class="form-label fs-5">Jam Mulai</label>
                                        <input type="time" name="shift_start" id="jam-mulai" class="form-control"
                                            aria-required="true" required />
                                        <div class="invalid-feedback">Jam mulai wajib diisi!</div>
                                    </div>

                                    <!-- Field jam selesai -->
                                    <div class="col-12 col-md-6">
                                        <label for="jam-selesai" class="form-label fs-5">Jam Selesai</label>
                                        <input type="time" name="shift_end" id="jam-selesai" class="form-control"
                                            aria-required="true" required />
                                        <div class="invalid-feedback">
                                            Jam selesai wajib diisi!
                                        </div>
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
                        <button type="button" class="btn btn-primary" id="submit-modal" data-action="new">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal delete -->
        <div class="modal fade" id="modal-delete" tabindex="-1" aria-labelledby="modal-delete-label"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-delete-label">
                            Konfirmasi Penghapusan
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <p>
                            Apakah anda yakin ingin menghapus jadwal ini? Setelah dihapus
                            jadwal ini tidak dapat lagi dikembalikan.
                        </p>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="konfirmasi-modal-delete"
                            data-bs-dismiss="modal">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal toggle status -->
        <div class="modal fade" id="modal-toggle-status" tabindex="-1" aria-labelledby="modal-toggle-status-label"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-toggle-status-label">
                            Konfirmasi
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <p>
                            modal body
                        </p>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="konfirmasi-modal-toggle-status"
                            data-bs-dismiss="modal">
                            Non/Aktifkan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal tambah mingguan -->
        <div class="modal fade" id="modal-add-weekly" tabindex="-1" aria-labelledby="modal-add-weekly-label"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-weekly-label-label">
                            Konfirmasi
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <p>
                            Apakah anda yakin ingin menambahkan jadwal mingguan?
                        </p>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="konfirmasi-modal-weekly-label"
                            data-bs-dismiss="modal">
                            Konfirmasi
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

                // ambil elemen modal dan buat instance modal
                let modalJadwalEl = document.getElementById("modal-jadwal");

                // ambil elemen form
                let formJadwal = document.getElementById("form-jadwal"); // Form jadwal
                let modalLabel = document.getElementById("modal-jadwal-label"); // Label modal jadwal
                let fieldIdjadwal = formJadwal.querySelector("#id-jadwal"); // Field id jadwal
                let fieldTanggal = formJadwal.querySelector("#tanggal"); // Field tanggal jadwal
                let fieldJamMulai = formJadwal.querySelector("#jam-mulai"); // Field jam mulai
                let fieldJamSelesai = formJadwal.querySelector("#jam-selesai"); // Field jam selesai

                // deteksi event modal muncul
                modalJadwalEl.addEventListener("show.bs.modal", function(event) {
                    // Ambil tombol aktivasi modal
                    let relatedButton = event.relatedTarget;

                    // tentukan apakah tombol edit atau tambah yang ditekan
                    let isEdit = relatedButton.getAttribute("data-id-jadwal") && true;

                    if (isEdit) {
                        // dapatkan data jadwal
                        let dataJadwal = {
                            id: relatedButton.getAttribute("data-id-jadwal"),
                            tanggal: relatedButton.getAttribute("data-tanggal-jadwal"),
                            jamMulai: relatedButton.getAttribute("data-jam-mulai-jadwal"),
                            jamSelesai: relatedButton.getAttribute("data-jam-selesai-jadwal"),
                        };

                        let submitButton = modalJadwalEl.querySelector("#submit-modal");

                        // Set label modal menjadi edit jadwal
                        modalLabel.textContent = "Edit Jadwal";

                        // Set value pada form
                        fieldIdjadwal.value = dataJadwal.id;
                        fieldTanggal.value = dataJadwal.tanggal;
                        fieldJamMulai.value = dataJadwal.jamMulai;
                        fieldJamSelesai.value = dataJadwal.jamSelesai;
                        submitButton.setAttribute('data-action', 'update');
                    }
                });

                // Reset modal ketika ditutup
                modalJadwalEl.addEventListener("hidden.bs.modal", function(event) {
                    // Set form action ke route tambah
                    formJadwal.action = '{{ route('dokter.jadwal.new') }}';

                    // Reset validasi form
                    formJadwal.classList.remove("was-validated");
                    let submitButton = modalJadwalEl.querySelector("#submit-modal");

                    // Set label modal menjadi tambah jadwal
                    modalLabel.textContent = "Tambah Jadwal";

                    // Pastikan value pada form kosong
                    fieldIdjadwal.value = "";
                    fieldTanggal.value = "";
                    fieldJamMulai.value = "";
                    fieldJamSelesai.value = "";
                    submitButton.setAttribute('data-action', 'new');
                });

                // Tangani event submit form
                let submitButton = modalJadwalEl.querySelector("#submit-modal");

                submitButton.addEventListener("click", function(event) {
                    // Tampilkan hasil validasi
                    formJadwal.classList.add("was-validated");

                    // Submit form jika semua field sudah valid
                    if (formJadwal.checkValidity()) {
                        let formAction = submitButton.getAttribute('data-action');

                        if (formAction === 'new') {
                            formJadwal.submit();
                        }
                    }
                });

                // Tangani event modal delete
                let modalDeleteEl = document.getElementById("modal-delete");

                // Elemen button konfirmasi delete
                let konfirmasiDeleteButton = modalDeleteEl.querySelector(
                    "#konfirmasi-modal-delete"
                );

                modalDeleteEl.addEventListener("show.bs.modal", function(event) {
                    // Ambil id jadwal yang akan dihapus
                    let itemId = event.relatedTarget.getAttribute("data-id-jadwal");
                    let isAllowed = (event.relatedTarget.getAttribute("data-allowed") === '1') ? true : false;

                    if (isAllowed) {
                        modalDeleteEl.querySelector('.modal-body p').textContent =
                            'Apakah anda yakin ingin menghapus jadwal ini? Setelah dihapus jadwal ini tidak dapat lagi dikembalikan.';

                        konfirmasiDeleteButton.classList.remove('d-none');
                        konfirmasiDeleteButton.setAttribute("data-id-jadwal", itemId);
                    } else {
                        modalDeleteEl.querySelector('.modal-body p').textContent =
                            'Anda tidak diperbolehkan untuk menghapus jadwal ini, karena terdapat pasien yang telah melakukan booking terhadap jadwal ini.';

                        konfirmasiDeleteButton.classList.add('d-none');
                    }
                });

                konfirmasiDeleteButton.addEventListener("click", function(event) {
                    // Ambil id jadwal yang akan dihapus
                    let itemId = event.target.getAttribute("data-id-jadwal");

                    let xhr = new XMLHttpRequest();

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            let result = JSON.parse(xhr.responseText);

                            if (xhr.status === 200) {
                                window.location.replace('{{ route('dokter.jadwal.show') }}');
                            } else if (xhr.status === 409) {
                                let alertElem = document.createElement('div');
                                alertElem.classList.add('mt-3', 'alert', 'alert-danger', 'alert-dismissible',
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
                                alertElem.append(' ' + result.message);
                                alertElem.appendChild(alertClose);

                                document.querySelector('#alert-container').appendChild(alertElem);
                            } else if (xhr.status === 500) {
                                let alertElem = document.createElement('div');
                                alertElem.classList.add('mt-3', 'alert', 'alert-danger', 'alert-dismissible',
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
                                alertElem.append(' ' + result.message);
                                alertElem.appendChild(alertClose);

                                document.querySelector('#alert-container').appendChild(alertElem);
                            } else {
                                console.log(xhr.responseText);
                            }
                        }
                    }

                    xhr.open('DELETE', '{{ route('dokter.jadwal.show') }}/' + itemId);


                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.send();
                });

                // Event modal toggle status

                let modalToggleStatusEl = document.getElementById('modal-toggle-status'); // Elemen modal toggle status
                let konfirmToggleStatusBtn = modalToggleStatusEl.querySelector(
                    '#konfirmasi-modal-toggle-status'); // Tombol konfirmasi toggle status

                modalToggleStatusEl.addEventListener('show.bs.modal', function(event) {
                    let relatedButton = event.relatedTarget; // Tombol yang ditekan untuk memunuculkan modal

                    let jadwalId = relatedButton.getAttribute(
                        'data-id-jadwal'); // Data id jadwal yang tercantum pada tombol yang ditekan

                    // Berikan atribut data id jadwal berdasarkan data yang tercantum
                    konfirmToggleStatusBtn.setAttribute('data-id-jadwal', jadwalId);

                    let jadwalToggleStateTo = relatedButton.getAttribute(
                        'data-action'); // Data aksi yang tercantum pada tombol yang ditekan

                    // Berikan atribut data aksi berdasarkan data yang tercantum
                    konfirmToggleStatusBtn.setAttribute('data-action', jadwalToggleStateTo);

                    let modalToggleStatusLabel = modalToggleStatusEl.querySelector(
                        '#modal-toggle-status-label'); // Modal label
                    let modalToggleStatusBody = modalToggleStatusEl.querySelector('.modal-body p'); // Modal body

                    if (jadwalToggleStateTo === 'Aktifkan') {
                        // Ubah label modal
                        modalToggleStatusLabel.textContent = 'Konfirmasi Aktifkan Jadwal';

                        // Ubah pesan konfirmasi
                        modalToggleStatusBody.textContent =
                            'Apakah anda yakin ingin mengaktifkan jadwal ini? Setelah jadwal diaktifkan pasien akan dapat melakukan booking terhadap jadwal ini.';

                        // Atur tombol konfirmasi
                        konfirmToggleStatusBtn.textContent = 'Aktifkan';
                        konfirmToggleStatusBtn.classList.remove('btn-danger');
                        konfirmToggleStatusBtn.classList.add('btn-success');
                    } else if (jadwalToggleStateTo === 'Nonaktifkan') {
                        // Ubah label modal
                        modalToggleStatusLabel.textContent = 'Konfirmasi Nonaktifkan Jadwal';

                        // Ubah pesan konfirmasi
                        modalToggleStatusBody.textContent =
                            'Apakah anda yakin ingin menonaktifkan jadwal ini? Setelah jadwal dinonaktifkan pasien tidak akan dapat melakukan booking terhadap jadwal ini.';

                        // Atur tombol konfirmasi
                        konfirmToggleStatusBtn.textContent = 'Nonaktifkan';
                        konfirmToggleStatusBtn.classList.remove('btn-success');
                        konfirmToggleStatusBtn.classList.add('btn-danger');
                    }
                });

                konfirmToggleStatusBtn.addEventListener('click', function(event) {
                    let idJadwal = event.target.getAttribute('data-id-jadwal');

                    let xhr = new XMLHttpRequest();

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            let result = JSON.parse(xhr.responseText);

                            if (xhr.status === 200) {
                                window.location.replace('{{ route('dokter.jadwal.show') }}');
                            } else if (xhr.status === 500) {
                                let alertElem = document.createElement('div');
                                alertElem.classList.add('mt-3', 'alert', 'alert-danger', 'alert-dismissible',
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
                                alertElem.append(' ' + result.message);
                                alertElem.appendChild(alertClose);

                                document.querySelector('#alert-container').appendChild(alertElem);
                            }
                        }
                    }

                    xhr.open('PUT', '{{ route('dokter.jadwal.show') }}/' + idJadwal + '/toggle_status');

                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.send();
                });

                let modalAddWeeklyEl = document.getElementById('modal-add-weekly');
                let konfirmasiAddWeeklyBtn = modalAddWeeklyEl.querySelector('#konfirmasi-modal-weekly-label');

                konfirmasiAddWeeklyBtn.addEventListener('click', function() {
                    window.location.replace('{{ route('dokter.jadwal.new.weekly') }}');
                });
            })();
        </script>
    </x-slot>
</x-layout-dokter>
