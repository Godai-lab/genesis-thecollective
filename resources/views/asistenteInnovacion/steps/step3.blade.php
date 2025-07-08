<!-- step3.blade.php -->
<div id="step-3-form">
    <div id="step-3-form-content">
        {{-- <form id="step-3-form" method="POST" action="{{route('herramienta1.rellenariasave')}}" data-validate="true"> --}}
            {{-- @csrf --}}
            <div id="asistenteInnovacionGenerateContainer"></div>
            <input type="hidden" name="asistenteInnovacionGenerateInput" id="asistenteInnovacionGenerateInput"> 
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="2" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" data-form="btngenerarCreatividad" class="form-button-step">Volver a generar</x-button-genesis>
                <x-button-genesis type="button" id="btnGenerarPDF" class="">Descargar</x-button-genesis>
            </div>
        {{-- </form> --}}
    </div>
</div>
