<!-- step1.blade.php -->
<div id="step-1-form">
    <div id="step-1-form-content">
         @if(Auth::user()->can('haveaccess', 'generador.imagen') || Auth::user()->can('haveaccess', 'generador.video'))
        
         <h1 class="text-2xl mt-20 px-16">¿Qué quieres hacer?</h1>
         @else
         <h1 class="text-2xl mt-20 px-16">No tienes permsisos suficientes para esta herramienta</h1>
         @endif
         <div class="p-6 text-black dark:text-gray-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 space-x-4 gap-32 justify-stretch items-stretch">
            {{-- <div class="basis-1/3 flex flex-row gap-6">
                <div class="text-base mt-1.5">
                    1.0
                </div>
                <div class="flex flex-col gap-5 items-start justify-between">
                    <h2 class="text-3xl flex items-end">Logos. <a data-step="2" class="step-button inline-block ms-2" href="#"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                    <x-button-genesis class="step-button mt-4" data-step="2"  >Iniciar</x-button-genesis>
                </div>
            </div> --}}
          @if(Auth::user()->can('haveaccess', 'generador.imagen') || Auth::user()->can('haveaccess', 'generador.video'))
                
                 <div class="basis-1/3 flex flex-row gap-6">
                <div class="text-base mt-1.5">
                    1.0
                </div>
                <div class="flex flex-col gap-5 items-start justify-between">
                    <h2 class="text-3xl flex items-end">Artes conceptuales. <a class="step-button inline-block ms-2" href="{{route('asistenteGenerador.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                    <x-button-genesis class="step-button mt-4" href="{{route('asistenteGenerador.index')}}" >Iniciar</x-button-genesis>
                </div>
            </div>
            @endif

           
           
          
            @can('haveaccess','user.index')
            <div class="basis-1/3 flex flex-row gap-6">
                <div class="text-base mt-1.5">
                    2.0
                </div>
                <div class="flex flex-col gap-5 items-start justify-between">
                    <h2 class="text-3xl flex items-end">Experimental. <a class="step-button inline-block ms-2" href="{{route('asistenteExperimental.index')}}"><img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt=""></a></h2>
                    <x-button-genesis class="step-button mt-4" href="{{route('asistenteExperimental.index')}}" >Iniciar</x-button-genesis>
                </div>
            </div>
            @endcan
            
        </div>
        {{-- <form method="POST" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    ['label'=>'Selecciona','type'=>'select', 'name'=>'type', 'id'=>'type', 'col'=>'sm:col-span-4', 'value'=>old('type'), 'attr'=>'validate-required=required validate-name=marca', 'list'=>$tipos],
                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Tipo</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
            </x-dynamic-form> --}}
            
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" href="{{route('asistentes')}}" class="">Regresar</x-button-genesis>
                {{-- <x-button-genesis type="button" data-step="2" class="step-button">Artes conceptuales</x-button-genesis> --}}
            </div>
        {{-- </form> --}}
    </div>
</div>
