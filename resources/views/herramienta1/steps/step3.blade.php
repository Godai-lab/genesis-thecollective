<!-- step3.blade.php -->
<div id="step-3-form">
    <div id="step-3-form-content">
        <form id="step-3-form" method="POST" action="{{route('herramienta1.rellenariasave')}}" data-validate="true">
            @csrf
            <input type="hidden" name="extraccionIA" id="extraccionIA"> 
            <div id="editor-container"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="2" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btnCrearBriefIA" class="form-button">Aceptar</x-button-genesis>
                {{-- <button data-step="2" class="step-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Regresar
                </button>
                <button class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Aceptar
                </button> --}}
            </div>
        </form>
        {{-- <textarea class="block w-full rounded-md border-0 py-1.5 text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-700" name="extraccionIA" id="extraccionIA" cols="30" rows="10"></textarea> --}}
    </div>
</div>
