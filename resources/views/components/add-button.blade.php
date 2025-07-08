@props(['route'])
@if ($route)
    <a {{$attributes->merge(['class' => 
        'inline-block middle none center rounded-lg bg-green-600 py-3 px-6 text-xs font-bold text-white transition-all hover:bg-green-700 focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none'])}}
        href="{{ $route }}">
        <i class="fa-solid fa-plus"></i> Agregar Nuevo
    </a>
@endif