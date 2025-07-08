<!-- step8.blade.php -->
<div id="step-8-form">
    <div id="step-8-form-content">
        <form id="step-9-form" method="POST" action="{{route('herramienta1.saveBrief')}}" data-validate="true">
            @csrf
            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Brief</h2>
            <p class="mt-1 m-b-2 text-sm leading-6 text-black dark:text-gray-400">Revisa la información detalladamente. Puedes editar, corregir y añadir. </p>
            <input type="hidden" name="Brief" id="Brief"> 
            <div id="contentBrief"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-form="btngenerarBrief" class="form-button-step">Volver a generar</x-button-genesis>
                <x-button-genesis type="button" class="form-button">Aceptar</x-button-genesis>
                {{-- <button data-form="btngenerarBrief" class="form-button-step inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Volver a generar
                </button>
                <button class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Aceptar
                </button> --}}
            </div>
        </form>
    </div>
</div>
