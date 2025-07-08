<x-app-layout>
    <x-slot name="title">Génesis - Asistentes</x-slot>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg flex flex-col gap-20">
                <h1 class="sm:text-5xl text-2xl mt-20 px-16">Produce más rápido.</h1>
                <div class="p-6 text-black dark:text-gray-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 space-x-4 gap-32 justify-stretch items-stretch">
                    @can('haveaccess','asistentecreativo.index')
                    <div class="basis-1/3 flex flex-row gap-6">
                        <div class="text-base mt-1.5">
                            1.0
                        </div>
                        <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">Asistente creativo. <a class="inline-block ms-2" href="{{route('asistenteCreativo.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Usa nuestros asistentes IA especializados para generar bajadas creativas, estrategias,  contenidos etc. Integra tu pensamiento y deja que el milagro suceda.</p>
                            <x-button-genesis class="mt-4" href="{{route('asistenteCreativo.index')}}" >Iniciar</x-button-genesis>
                        </div>
                    </div>
                    @endcan
                    @can('haveaccess','asistentesocialmedia.index')
                    <div class="basis-1/3 flex flex-row gap-6">
                        <div class="text-base mt-1.5">
                            2.0
                        </div>
                        <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">Asistente social media. <a class="inline-block ms-2" href="{{route('asistenteSocialMedia.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Usa nuestros asistentes IA especializados para generar bajadas creativas, estrategias,  contenidos etc. Integra tu pensamiento y deja que el milagro suceda.</p>
                            <x-button-genesis class="mt-4" href="{{route('asistenteSocialMedia.index')}}" >Iniciar</x-button-genesis>
                        </div>
                    </div>
                    @endcan
                    
                    {{-- <div class="basis-1/3 flex flex-row gap-6">
                        <div class="text-base mt-1.5">
                            3.0
                        </div>
                        <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="text-3xl flex items-end">Asistente Innovación. <a class="inline-block ms-2" href="{{route('asistenteInnovacion.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Usa nuestros asistentes IA especializados para generar bajadas creativas, estrategias,  contenidos etc. Integra tu pensamiento y deja que el milagro suceda.</p>
                            <x-button-genesis class="mt-4" href="{{route('asistenteInnovacion.index')}}" >Iniciar</x-button-genesis>
                        </div>
                    </div> --}}
                    @can('haveaccess','asistentesocialmedia.index')
                    <div class="basis-1/3 flex flex-row gap-6">
                        <div class="text-base mt-1.5">
                            3.0
                        </div>
                        {{-- <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">Asistente gráfica. <a class="inline-block ms-2" href="{{route('asistenteGrafica.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Usa nuestros asistentes IA especializados para generar bajadas creativas, estrategias,  contenidos etc. Integra tu pensamiento y deja que el milagro suceda.</p>
                            <x-button-genesis class="mt-4" href="{{route('asistenteGrafica.index')}}" >Iniciar</x-button-genesis>
                        </div> --}}
                        <div class="flex flex-col gap-5 items-start justify-between">
                            <h2 class="sm:text-3xl text-xl flex items-end">Asistente gráfica. <a class="inline-block ms-2" href="{{route('asistenteGenerador.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                            <p class="text-base text-[#737373] h-full">Usa nuestros asistentes IA especializados para generar bajadas creativas, estrategias,  contenidos etc. Integra tu pensamiento y deja que el milagro suceda.</p>
                            <x-button-genesis class="mt-4" href="{{route('asistenteGenerador.index')}}" >Iniciar</x-button-genesis>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
