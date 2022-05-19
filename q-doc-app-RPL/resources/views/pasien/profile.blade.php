@php
$user = request()->user();
@endphp

<x-layout-pasien>
    <x-slot name="title">Profile</x-slot>

    <main class="mt-5">
        <div class="container">
            <h3 class="text-center">Profile</h3>

            <div class="px-5">
                <x-profile-field :label="'Nama'" :content="$user->name" />

                @php
                    $birthDate = new Carbon\Carbon($user->birth_date, 'Asia/Jakarta');
                    $birthDate = $birthDate->locale('id');
                    $birthDateString = $birthDate->day;
                    $birthDateString .= ' ' . $birthDate->monthName;
                    $birthDateString .= ' ' . $birthDate->year;
                @endphp

                <x-profile-field :label="'Tempat Tanggal Lahir'" :content="$user->birth_place . ', ' . $birthDateString" />
                <x-profile-field :label="'Jenis Kelamin'" :content="$user->gender" />
                <x-profile-field :label="'No. Hp'" :content="$user->phone_number" />
            </div>

            <div class="d-flex justify-content-center mt-5">
                <a href="{{ route('pasien.profile.edit') }}">
                    <button type="button" class="btn btn-warning px-4 text-white">
                        Edit
                    </button>
                </a>
            </div>
        </div>
    </main>
</x-layout-pasien>
