<x-app-layout>
    <x-slot name="title">Génesis - Asistente Generativo</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Asistente Generativo') }}
        </h2>
    </x-slot>
{{-- <livewire:new-generador /> --}}
<livewire:generador.generador-main />
</x-app-layout>