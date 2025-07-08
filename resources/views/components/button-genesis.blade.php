@props(['href'])
@if (isset($href))
    <a {{$attributes->merge(['class' => 
        'border border-black hover:bg-transparent bg-black text-white hover:text-black rounded-xl px-5 text-base text-center'])}}
        href="{{ $href }}">
        {{ $slot }}
    </a>
@else
    <button {{$attributes->merge(['class' => 
        'border border-black hover:bg-transparent bg-black text-white hover:text-black rounded-xl px-5 text-base text-center'])}}>
        {{ $slot }}
    </button>
@endif