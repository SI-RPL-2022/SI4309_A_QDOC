<x-layout-dokter>
    <x-slot name="title">Profile Pasien</x-slot>

    <main class="mt-5">
        <div class="container">
            <h3 class="text-center">Profile Pasien</h3>

            <div class="px-5">
                <x-profile-field :label="'Nama'" :content="$user->name" />

                @php
                    $birthDate = new Carbon\Carbon($user->birth_date, 'Asia/Jakarta');
                    $birthDate = $birthDate->locale('id');
                    $birthDateString = $birthDate->day;
                    $birthDateString .= ' ' . $birthDate->monthName;
                    $birthDateString .= ' ' . $birthDate->year;
                @endphp

                <x-profile-field :label="'Tempat Tanggal Lahir'"
                    :content="$user->birth_place . ', ' . $birthDateString" />
                <x-profile-field :label="'Jenis Kelamin'" :content="$user->gender" />
                <x-profile-field :label="'No. Hp'" :content="$user->phone_number" />
            </div>
        </div>
    </main>
</x-layout-dokter>
