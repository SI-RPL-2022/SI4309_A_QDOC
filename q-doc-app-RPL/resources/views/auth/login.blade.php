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

    <title>Q-Doc - Login</title>
</head>

<body>
    <section class="vh-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 px-0 d-none d-sm-block">
                    <img src="{{ url('login_illust.png') }}" alt="Login image" class="w-100 vh-100"
                        style="object-fit: cover; object-position: left;">
                </div>

                <div class="col-md-8 col-lg-7 col-xl-6 d-flex align-items-center text-light">

                    <div class="px-5 ms-5 me-5 mt-5 py-3 bg-other border border-1 border-primary rounded-3 flex-fill">

                        <h1 class="text-center mb-3"><em>Q-Doc</em></h1>

                        @error('loginError')
                            <div class="mb-3">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @enderror

                        <form action="{{ route('auth') }}" method="post" class="needs-validation" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="no-hp" class="form-label fs-5">No. HP</label>
                                <input type="tel" name="phone_number" id="no-hp" class="form-control ph-light bg-gray"
                                    style="--bg-opacity: 0.5" placeholder="Nomor HP anda"
                                    value="{{ old('phone_number') }}" aria-required="true" required />
                                <div class="invalid-feedback">Nomor HP wajib diisi!</div>
                                @error('phoneNumber')
                                    <script>
                                        document.querySelector('#no-hp+.invalid-feedback').textContent = '{{ $message }}';
                                        document.querySelector('#no-hp').classList.add('is-invalid');
                                    </script>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fs-5">Password</label>
                                <input type="password" name="password" id="password" class="form-control ph-light bg-gray"
                                    style="--bg-opacity: 0.5" placeholder="Password anda"
                                    value="{{ old('password') }}" aria-required="true" required />
                                <div class="invalid-feedback">Password wajib diisi!</div>
                                @error('password')
                                    <script>
                                        document.querySelector('#password+.invalid-feedback').textContent = '{{ $message }}';
                                        document.querySelector('#password').classList.add('is-invalid');
                                    </script>
                                @enderror
                            </div>

                            <div class="mb-3 d-flex justify-content-center">
                                <button type="submit" class="btn bg-brand text-white rounded-3 px-4">
                                    Login
                                </button>
                            </div>
                        </form>

                        <p class="text-center">
                            Belum punya akun? <a href="{{ route('register') }}" class="text-light">Buat
                                Akun</a>
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Script validasi -->
    <script>
        (function() {
            "use strict";

            // Ambil form
            let form = document.querySelector(".needs-validation");

            // validasi ketika disubmit
            form.addEventListener(
                "submit",
                function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add("was-validated");
                },
                false
            );
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
</body>

</html>
