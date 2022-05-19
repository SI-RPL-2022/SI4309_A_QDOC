@php
$user = request()->user();
@endphp
<x-layout-pasien>
    <x-slot name="title">Edit Profile</x-slot>

    <main class="mt-5">
        <h3 class="text-center">Edit Profile</h3>


        <div class="container my-3 px-5">
            <div class="px-md-5" id="alert-container">
                <div class="px-md-5">
                </div>
            </div>
            <form class="px-md-5 needs-validation" method="post" action="" novalidate>
                @csrf
                <div class="row px-md-5">
                    <div class="col-12 mb-3">
                        <label for="nama" class="form-label fs-5">Nama</label>
                        <input type="text" name="name" value="{{ $user->name }}" id="nama" class="form-control"
                            placeholder="Nama anda" aria-required="true" required />
                        <div class="invalid-feedback">Nama wajib diisi!</div>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label for="tempat-lahir" class="form-label fs-5">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ $user->birth_place }}" id="tempat-lahir"
                            class="form-control" placeholder="Tempat lahir anda" aria-required="true" required />
                        <div class="invalid-feedback">Tempat lahir wajib diisi!</div>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label for="tanggal-lahir" class="form-label fs-5">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ $user->birth_date }}" id="tanggal-lahir"
                            class="form-control" aria-required="true" required />
                        <div class="invalid-feedback">Tanggal lahir wajib diisi!</div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="jenis-kelamin" class="form-label fs-5">Jenis Kelamin</label>
                        <select name="gender" id="jenis-kelamin" class="form-select" aria-required="true" required>
                            <option>Pilih jenis kelamin anda</option>
                            <option id="Laki-laki" value="Laki-laki">Laki-laki</option>
                            <option id="Perempuan" value="Perempuan">Perempuan</option>
                        </select>
                        <script>
                            let selectedGender = document.querySelector('#{{ $user->gender }}');
                            selectedGender.setAttribute('selected', '');
                            selectedGender.setAttribute('aria-selected', 'true');
                        </script>
                        <div class="invalid-feedback">
                            Mohon pilih jenis kelamin anda!
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="no-hp" class="form-label fs-5">No. HP</label>
                        <input type="tel" name="phone_number" value="{{ $user->phone_number }}" id="no-hp"
                            class="form-control" placeholder="Nomor HP anda" aria-required="true" required />
                        <div class="invalid-feedback">No. HP wajib diisi!</div>
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
        <!-- Validation Script -->
        <script>
            (function() {
                "use strict";

                // Ambil form yang perlu di validasi
                let form = document.querySelector(".needs-validation");

                // Elemen field jenis kelamin
                let fieldGender = form.querySelector("#jenis-kelamin");

                // Validasi ketika submit
                form.addEventListener(
                    "submit",
                    function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        if (form.checkValidity() && fieldGender.value !== 'Pilih jenis kelamin anda') {
                            let xhr = new XMLHttpRequest();

                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === XMLHttpRequest.DONE) {
                                    let result = JSON.parse(xhr.responseText)
                                    if (xhr.status === 200) {
                                        window.location.replace('{{ route('pasien.profile.show') }}');
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

                                        document.querySelector('#alert-container div').appendChild(alertElem);
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

                                        document.querySelector('#alert-container div').appendChild(alertElem);
                                    } else {
                                        console.log(xhr.responseText);
                                    }
                                }
                            }

                            xhr.open('PUT', '{{ route('pasien.profile.update', ['id' => $user->id]) }}');

                            let myForm = document.querySelector('.needs-validation');
                            let formData = new FormData(myForm);

                            let profileData = {};

                            for (let pair of formData.entries()) {
                                profileData[pair[0]] = pair[1];
                            }

                            xhr.setRequestHeader('Content-Type', 'application/json');
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                            xhr.send(JSON.stringify(profileData));
                        }

                        form.classList.add("was-validated");

                        if (fieldGender.value === "Pilih jenis kelamin anda") {
                            fieldGender.setCustomValidity(" ");
                        }
                    },
                    false
                );

                // Validasi jenis kelamin
                fieldGender.addEventListener("change", function(event) {
                    if (fieldGender.value !== "Pilih jenis kelamin anda") {
                        fieldGender.setCustomValidity("");
                    } else {
                        fieldGender.setCustomValidity(" ");
                    }
                });
            })();
        </script>
    </x-slot>
</x-layout-pasien>
