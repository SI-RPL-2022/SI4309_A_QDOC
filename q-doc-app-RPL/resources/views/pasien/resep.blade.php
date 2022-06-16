<x-layout-pasien>
    <x-slot name="title">Resep</x-slot>

    <main class="mt-5">
        <div class="container px-5">
            <div class="row px-lg-5">
                @if ($consultations->count() === 0)
                <h4 class="text-center">Anda belum memiliki resep. Klik <a
                        href="{{ route('pasien.booking') }}">link</a> ini untuk membuat booking.</h4>
            @else
                @foreach ($consultations as $consultation)
                    @php
                        $date = new \Carbon\Carbon($consultation->date, 'Asia/Jakarta');
                        $date = $date->locale('id');
                        $dateString = $date->isoFormat('dddd, DD MMMM G');
                    @endphp
                    <x-resep-item :date="$dateString">
                        {{ $consultation->receipt }}
                    </x-resep-item>
                @endforeach
            @endif  
            </div>
        </div>
    </main>
</x-layout-pasien>
