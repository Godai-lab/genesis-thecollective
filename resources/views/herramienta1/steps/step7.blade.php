<!-- step7.blade.php -->
<div id="step-7-form">
    <div id="step-7-form-content">
        <form id="step-3-form" method="POST" action="{{route('herramienta1.datosextrassave')}}" data-validate="true">
            @csrf
            <input type="hidden" name="extraMarca" id="extraMarca"> 
            <input type="hidden" name="extraProductos" id="extraProductos"> 
            <input type="hidden" name="extraCompetencia" id="extraCompetencia"> 
            <input type="hidden" name="extraEstudiosMercado" id="extraEstudiosMercado"> 
            <input type="hidden" name="extraCiudadPaisEconomia" id="extraCiudadPaisEconomia"> 
            <input type="hidden" name="extraNecesidades" id="extraNecesidades">
            <label>Sobre tu marca que desees compartir</label>
            <div id="editor-extraMarca"></div>
            <label for="">Sobre tus productos</label>
            <div id="editor-extraProductos"></div>
            <label for="">Sobre tu competencia</label>
            <div id="editor-extraCompetencia"></div>
            <label for="">Estudios de mercado</label>
            <div id="editor-extraEstudiosMercado"></div>
            <label for="">Sobre tu ciudad, país, situación económica</label>
            <div id="editor-extraCiudadPaisEconomia"></div>
            <label for="">Para conocer las necesidades del cliente actual</label>
            <div id="editor-extraNecesidades"></div>

            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="6" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btngenerarBrief" class="form-button">Aceptar</x-button-genesis>
                {{-- <button data-step="6" class="step-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Regresar
                </button>
                <button id="btngenerarBrief" class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Aceptar
                </button> --}}
            </div>
        </form>
    </div>
</div>
