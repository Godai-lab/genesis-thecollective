@php

@endphp

<style>
#container-result-genesis ul {
    list-style-type: disc !important;
    padding-left: 40px !important;
    margin-top: 5px;
    font-size: 14px;
}

</style>
<!-- step8.blade.php -->
<div id="step-8-form">
    <div id="step-8-form-content">
        <form id="step8Form" method="POST" action="{{route('herramienta2.saveEstrategiaCreatividadInnovacion')}}" data-validate="true">
            @csrf
            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">RESULTADO</h2>
            <div id="container-result-genesis"></div>
            <x-dynamic-form 
                :fields="[

                    ['label'=>'Nombre del archivo','placeholder'=>'Escribe el nombre del archivo','type'=>'text', 'name'=>'file_name', 'id'=>'file_name', 'col'=>'sm:col-span-3', 'value'=>old('file_name'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=nombre_archivo'],

                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Guardar archivo</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">aquí puedes guardar tu archivo</p>
            </x-dynamic-form>

            <div class="mt-4">
                <div class="content-rating">
                    <label for="rating-validar-concepto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valoración</label>
                    <div class="rating mt-1" id="rating-validar-concepto">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-2xl cursor-pointer" data-rating="{{ $i }}"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-value-validar-concepto" data-validation-rules="required" data-field-name="valoración">
                </div>
            </div>

            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" id="btnguardarresult" data-btnForm="guardarConstruccionEstrategiaCreatividadInnovacion" >Guardar</x-button-genesis>
            </div>
        </form>
    </div>
</div>
