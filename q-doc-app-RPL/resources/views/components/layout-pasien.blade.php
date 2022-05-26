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

    <title>Q-Doc - {{ $title ?? 'App' }}</title>
</head>

<body>
    <header class="navbar navbar-expand-md navbar-dark bd-navbar bg-brand">
        <nav class="container-fluid px-3 flex-wrap flex-md-nowrap" aria-label="Main navigation">
            <a class="navbar-brand p-0 me-3 me-lg-5 fs-3" href="{{ route('pasien.home') }}"
                aria-label="Q-Doc">Q-Doc</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse collapse" id="navbarNav" style="">
                <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav pt-2 py-md-0">
                    <x-nav-item :href="route('pasien.profile.show')">Profile</x-nav-item>
                    <x-nav-item :href="route('pasien.booking')">Booking</x-nav-item>
                    <x-nav-item :href="route('home')">Resep</x-nav-item>
                </ul>

                <hr class="d-md-none text-white-50">

                <ul class="navbar-nav flex-row flex-wrap ms-md-auto">
                    <li class="nav-item col-6 col-md-auto text-md-end">
                        <p class="nav-link m-0">
                            {{ request()->user()->name }}
                            <button class="btn btn-sm btn-secondary ms-2"><a href="{{ route('logout') }}"
                                class="nav-link p-0 m-0">Log
                                out</a></button>
                        </p>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    {{ $slot }}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>

    {{ $afterBsScript ?? '' }}

</body>

</html>
