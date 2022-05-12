@props(['href'])

@php
$path = explode('/', $href);
$path = $path[count($path) - 1];
@endphp

<li class="nav-item col-6 col-md-auto">
    <a class="nav-link px-3 {{ request()->is('*' . $path) ? 'active' : '' }}"
        {{ request()->is('*' . $path) ? 'aria-current="page' : '' }}
        href="{{ $href }}">{{ $slot }}</a>
</li>
