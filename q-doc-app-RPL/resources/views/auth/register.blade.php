<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('style.css') }}" />

    <title>Q-Doc - Register</title>
</head>

<body>
    <main class="container p-5 d-flex align-items-center">
        <div class="row justify-content-center flex-grow-1">
            <h3 class="text-center">Registrasi</h3>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <p><strong>Error!</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }} </li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="container my-3 px-5">
                <form class="px-md-5 needs-validation" method="post" action="{{ route('register') }}" novalidate>
                    @csrf

                    <div class="row px-md-5">
                        <div class="col-12 mb-3">
                            <label for="nama" class="form-label fs-5">Nama</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="nama" class="form-control" placeholder="Nama anda"
                                aria-required="true" required />
                            <div class="invalid-feedback">Nama wajib diisi!</div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="tempat-lahir" class="form-label fs-5">Tempat Lahir</label>
                            <input type="text" name="birth_place" value="{{ old('birth_place') }}" id="tempat-lahir" class="form-control"
                                placeholder="Tempat lahir anda" aria-required="true" required />
                            <div class="invalid-feedback">Tempat lahir wajib diisi!</div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="tanggal-lahir" class="form-label fs-5">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" id="tanggal-lahir" class="form-control"
                                aria-required="true" required />
                            <div class="invalid-feedback">Tanggal lahir wajib diisi!</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="jenis-kelamin" class="form-label fs-5">Jenis Kelamin</label>
                            <select name="gender" id="jenis-kelamin" class="form-select" aria-required="true"
                                required>
                                <option aria-selected="true" selected>
                                    Pilih jenis kelamin anda
                                </option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback">
                                Mohon pilih jenis kelamin anda!
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="no-hp" class="form-label fs-5">No. HP</label>
                            <input type="tel" name="phone_number" value="{{ old('phone_number') }}" id="no-hp" class="form-control"
                                placeholder="Nomor HP anda" aria-required="true" required />
                            <div class="invalid-feedback">No. HP wajib diisi!</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="password" class="form-label fs-5">Password</label>
                            <input type="password" name="password" value="{{ old('password') }}" id="password" class="form-control"
                                placeholder="Password anda" aria-required="true" required />
                            <div class="invalid-feedback">Password wajib diisi!</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="konfirmasi-password" class="form-label fs-5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" id="konfirmasi-password"
                                class="form-control" placeholder="Konfirmasi password anda" aria-required="true"
                                required />
                            <div class="invalid-feedback invalid-password-confirmation"></div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-3">
                        <div class="col-auto d-flex flex-column justify-content-center">
                            <button type="submit" class="btn btn-primary mb-2">
                                Submit
                            </button>
                            <p class="text-center">
                                Sudah punya akun? <a href="{{ route('login') }}">Login</a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Script validasi -->
    <script>
        (function() {
            "use strict";

            // Ambil form yang perlu di validasi
            let form = document.querySelector(".needs-validation");
            // invalid feedback untuk konfirm password
            let invalidPasswordFeedback = document.querySelector(
                ".invalid-password-confirmation"
            );

            // Elemen field konfirmasi password
            let passwordConfirmation = document.querySelector(
                "#konfirmasi-password"
            );

            // Elemen field jenis kelamin
            let fieldGender = form.querySelector("#jenis-kelamin");

            // Validasi ketika submit
            form.addEventListener(
                "submit",
                function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // Set feedback konfirm password
                    invalidPasswordFeedback.innerHTML =
                        "Mohon konfirmasi password anda!";

                    form.classList.add("was-validated");

                    if (fieldGender.value === "Pilih jenis kelamin anda") {
                        fieldGender.setCustomValidity(" ");
                    }
                },
                false
            );

            // validasi konfirmasi password
            passwordConfirmation.addEventListener(
                "input",
                function(event) {
                    passwordConfirmation.setCustomValidity(" ");

                    invalidPasswordFeedback.innerHTML =
                        "Konfirmasi password tidak valid!";

                    if (
                        document.querySelector("#password").value ===
                        passwordConfirmation.value
                    ) {
                        passwordConfirmation.setCustomValidity("");
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
</body>

</html>
