@props(['date'])

<div class="col-12 mb-4">
    <h6><strong>Hari/Tanggal:</strong> {{ $date }}</h6>
    <p class="border border-1 border-dark rounded p-3">
        {{ $slot }}
    </p>
    <hr />
</div>
