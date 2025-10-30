<x-app-layout>
    <x-slot name="title">Génesis - Dashboard</x-slot>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg flex flex-col gap-20">
                <h1 class="sm:text-5xl text-2xl mt-20 px-16">Resuelve una campaña 360 <br>
                    en minutos con Génesis.</h1>
                    <div class="p-6 text-black dark:text-gray-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 space-x-4 gap-32 justify-stretch items-stretch">
                    @can('haveaccess','brief.index')
                    <div class="sm:basis-1/3 flex flex-row gap-6">
                        <div class="text-base mt-1.5">
                            1.0
                        </div>
                        <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">La data es lo más importante. <a class="inline-block ms-2" href="{{route('dashboard.herramienta1')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Recopila todas las fuentes de información que veas conveniente para armar un buen brief y deja que nuestra metodología impulsada con IA lo resuelva todo por ti.</p>
                            <x-button-genesis class="mt-4" href="{{route('dashboard.herramienta1')}}" >Iniciar</x-button-genesis>
                        </div>
                    </div>
                    @endcan
                    @can('haveaccess','genesis.index')
                    <div class="sm:basis-1/3 flex flex-row gap-6">
                        <div class="text-base mt-1.5">
                            2.0
                        </div>
                        <!-- <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">Potencia tus habilidades. <a class="inline-block ms-2" href="{{route('herramienta2.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Una vez armado el brief, genera lineamientos estratégicos creativos 360 en pocos minutos. Añade tus conocimientos y deja que nuestra poderosa herramienta con IA lo eleve.</p>
                            <x-button-genesis class="mt-4" href="{{route('herramienta2.index')}}" >Iniciar</x-button-genesis>
                        </div> -->
                        <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">Potencia tus habilidades. <a class="inline-block ms-2" href="{{route('creatividad')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Una vez armado el brief, genera lineamientos estratégicos creativos 360 en pocos minutos. Añade tus conocimientos y deja que nuestra poderosa herramienta con IA lo eleve.</p>
                            <x-button-genesis class="mt-4" href="{{route('creatividad')}}" >Iniciar</x-button-genesis>
                        </div>
                    </div>
                    @endcan
                    @if(Auth::user()->can('haveaccess', 'asistentegrafica.index') || Auth::user()->can('haveaccess', 'asistentecreativo.index') || Auth::user()->can('haveaccess', 'asistentesocialmedia.index'))
                    <div class="sm:basis-1/3 flex flex-row gap-6">
                        <div class="text-base mt-1.5">
                            3.0
                        </div>
                        <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">Produce más rápido. <a class="inline-block ms-2" href="{{route('asistentes')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Usa nuestros asistentes IA especializados para generar bajadas creativas, estrategias,  contenidos etc. Integra tu pensamiento y deja que el milagro suceda.</p>
                            <x-button-genesis class="mt-4" href="{{route('asistentes')}}" >Iniciar</x-button-genesis>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
