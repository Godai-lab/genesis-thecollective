<!-- step10.blade.php -->
<div id="step-10-form">
    <div id="step-10-form-content">
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Brief guardado exitosamente.</h2>
        <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
            <x-button-genesis type="button" href="{{route('dashboard')}}" class="">Inicio</x-button-genesis>
            <x-button-genesis type="button" href="{{route('herramienta2.index')}}" class="">Génesis</x-button-genesis>
            {{-- <a class="inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700" href="{{route('dashboard')}}">
                Inicio
            </a>
            <a class="inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700" href="{{route('herramienta2.index')}}">
                Génesis
            </a> --}}
        </div>
        
    </div>
</div>