<x-app-layout>
    <x-slot name="title">Génesis - Generador de Videos</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Generador de Videos') }}
        </h2>
    </x-slot>
    <livewire:video-generador />
</x-app-layout> 