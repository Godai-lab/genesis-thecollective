<style>

#visual-lista-fuentes-genesis ul,
#visual-lista-fuentes-escenario ul {
    list-style-type: disc !important;
    padding-left: 40px !important;
    margin-top: 5px;
    font-size: 14px;
}


</style>
<!-- step6.blade.php -->
<div id="step-6-form">
    <div id="step-6-form-content">
        <form id="step-6-form" method="POST" action="{{route('herramienta2.saveEstrategiaCreatividadInnovacion')}}" data-validate="true">
            @csrf
            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">RESULTADO GÉNESIS</h2>
            <h3 class="text-base font-semibold leading-7 text-black dark:text-gray-100">ESTRATEGIA</h3>
            <div id="editor-container-genesis"></div>
        
            {{-- <h4 class="text-base font-semibold leading-7 text-black dark:text-gray-100">FUENTE GÉNESIS</h4> --}}
            <div id="visual-lista-fuentes-genesis" class="mt-2 list-disc pl-5"></div>
            <br>
            <h3 class="text-base font-semibold leading-7 text-black dark:text-gray-100">CREATIVIDAD</h3>
            <div id="editor-container-escenario" ></div>
            {{-- <h4 class="text-base font-semibold leading-7 text-black dark:text-gray-100">FUENTE ESCENARIO</h4> --}}
            <div id="visual-lista-fuentes-escenario"  class="mt-2 list-disc pl-5"></div>
            <br>
            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">BAJADAS CREATIVAS</h2>
            <input type="hidden" name="construccionCreatividad" id="construccionCreatividad"> 
            <div class="mb-2" id="editor-container-construccionCreatividad"></div>
            <x-button-genesis type="button" data-type="Creatividad" class="generarNewCEI mt-3 mb-6">Volver a generar</x-button-genesis>

            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">ESTRATEGIA DIGITAL</h2>
            <input type="hidden" name="construccionEstrategia" id="construccionEstrategia"> 
            <div class="mb-2" id="editor-container-construccionEstrategia"></div>
            <x-button-genesis type="button" data-type="Estrategia" class="generarNewCEI mt-3 mb-6">Volver a generar</x-button-genesis>

            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">IDEAS DE CONTENIDO</h2>
            <input type="hidden" name="construccionIdeasContenido" id="construccionIdeasContenido"> 
            <div class="mb-2" id="editor-container-construccionIdeasContenido"></div>
            <x-button-genesis type="button" data-type="Contenido" class="generarNewCEI mt-3 mb-6">Volver a generar</x-button-genesis>
            
            <!-- <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">INNOVACIONES</h2>
            <input type="hidden" name="construccionInnovacion" id="construccionInnovacion"> 
            <div class="mb-2" id="editor-container-construccionInnovacion"></div> 
            <x-button-genesis type="button" data-type="Innovacion" class="generarNewCEI mt-3 mb-6">Volver a generar</x-button-genesis>-->

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
                <x-button-genesis type="button" data-step="5" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" data-form="btnsaveeleccioncampania" class="form-button-step">Volver a generar</x-button-genesis>
                <x-button-genesis type="button" class="form-button-save">Guardar</x-button-genesis>
                
            </div>
        </form>
    </div>
</div>
