<!-- step3.blade.php -->
<div id="step-3-container">
    <div id="step-3-form-content">
        {{-- <form id="step-3-form" method="POST" action="{{route('herramienta1.rellenariasave')}}" data-validate="true"> --}}
            {{-- @csrf --}}
            <form id="step-3-form" method="POST" action="{{route('asistenteCreativo.guardar')}}" data-validate="true">
            @csrf
             <input type="hidden" name="account" id="account_id_step3" value="">
            <div id="asistenteCreativoGenerateContainer"></div>
            <input type="hidden" name="asistenteCreativoGenerateInput" id="asistenteCreativoGenerateInput"> 
            <x-dynamic-form 
                :fields="[

                    ['label'=>'Nombre del archivo','placeholder'=>'Escribe el nombre del archivo','type'=>'text', 'name'=>'file_name', 'id'=>'file_name', 'col'=>'sm:col-span-3', 'value'=>old('file_name'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=nombre_archivo'],

                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Guardar archivo</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">aquí puedes guardar tu archivo</p>
            </x-dynamic-form>
            <div class="mt-4">
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valoración</label>
                    <div class="rating mt-1" id="rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-2xl cursor-pointer" data-rating="{{ $i }}"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-value" data-validation-rules="required" data-field-name="valoración">
                </div>
            </div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="2" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btnGuardar">Guardar</x-button-genesis>
                <x-button-genesis type="button" data-form="btngenerarCreatividad" class="form-button-step">Volver a generar</x-button-genesis>
                <x-button-genesis type="button" id="btnGenerarPDF" class="">Descargar</x-button-genesis>
            </div>
        </form>
    </div>
</div>
