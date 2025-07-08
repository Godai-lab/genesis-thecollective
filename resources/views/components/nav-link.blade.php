@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-black dark:border-indigo-600 text-sm font-medium leading-5 text-black dark:text-gray-100 focus:outline-none focus:border-black transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-black dark:text-gray-400 hover:text-black dark:hover:text-gray-300 hover:border-black dark:hover:border-gray-700 focus:outline-none focus:text-black dark:focus:text-gray-300 focus:border-black dark:focus:border-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
