@props(['label', 'content'])
<div class="mb-3">
    <h5 class="d-block mb-2">{{ $label }}</h5>
    <p class="d-block bg-gray p-2 rounded" style="--bg-opacity: 0.5">
        {{ $content }}
    </p>
</div>
