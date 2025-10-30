<style>

#fuentes-lista-concepto ul {
    list-style-type: disc; /* Muestra los puntos de la lista */
    padding-left: 20px; /* Agrega espacio a la izquierda */
    font-size: 13px;
}

#fuentes-lista-concepto li {
    margin-bottom: 10px; /* Espacio entre elementos de la lista */
    word-break: break-all; /* Permite que las URLs largas se rompan */
}

#fuentes-lista-concepto a {
    color: #3490dc; /* Color de enlace */
    text-decoration: underline;
}

#fuentes-lista-concepto a:hover {
    color: #2779bd; /* Color al pasar el mouse */
}
</style>
<!-- step5.blade.php -->
<div id="step-5-form">
    <div id="step-5-form-content">
        <form id="step5Form" method="POST" action="{{route('herramienta2.validarconcepto')}}" data-validate="true">
            @csrf
            {{-- <input type="hidden" name="construccionescenario" id="construccionescenario"> 
            <input type="hidden" name="genesisgenerado" id="genesisgenerado"> --}}

            <div id="form-concepto" class="space-y-12">
                <div class="border-b border-gray-700 pb-12 mb-6">
                    <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Validar Concepto</h2>
                    <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 items-start">
                        {{-- <div class="sm:col-span-4">
                            <label for="concepto_pais" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">País</label>
                            <div class="mt-2">
                                <input type="text" name="concepto_pais" id="concepto_pais" data-validation-rules="required|max:100" data-field-name="país" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div>
                        <div class="sm:col-span-4">
                            <label for="concepto_nombre_marca" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Nombre de la marca</label>
                            <div class="mt-2">
                                <input type="text" name="concepto_nombre_marca" id="concepto_nombre_marca" data-validation-rules="required|max:100" data-field-name="nombre_de_la_marca" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div> --}}
                        
                        <div class="sm:col-span-4">
                            <label for="concepto_categoria" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Categoría</label>
                            <div class="mt-2">
                                <input type="text" name="concepto_categoria" id="concepto_categoria" data-validation-rules="required|max:100" data-field-name="categoría" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-4">
                            <label for="concepto_periodo_campania" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Periodo de la campaña</label>
                            <div class="mt-2">
                                <input type="text" name="concepto_periodo_campania" id="concepto_periodo_campania" data-validation-rules="required|max:100" data-field-name="periodo_de_la_campaña" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            {{-- <!--nuevos campos -->
            <div style="display: none;" id="ResultadoAnterior" class="mb-4">
                <h2 class="text-lg font-bold mb-4">Resultado anterior:</h2>
                <div class="container-edit" id="RegenerateConceptoOld"></div>
                <x-button-genesis style="" type="button" id="" class="btn-approve approve-old mt-4">Seleccionar</x-button-genesis>
            
            </div>

            <div style="display: none;" id="ResultadoConcepto" class="mb-4">
                <h2 class="text-lg font-bold mb-4">Validar Concepto:</h2>
                
                <div class="container-edit" id="editor-container-concepto2"></div>
                
                <p style="font-size: 12px; font-weight: bold;" class="text-blue-600 dark:text-blue-400 cursor-pointer underline mt-2" x-data="" x-on:click="$dispatch('open-modal', 'fuentes-modal-concepto')">
                    Ver fuentes
                </p>
               <!-- Contenedor flex para alinear los botones -->
               <div class="flex items-center gap-4 mt-4">
                <x-button-genesis style="display: none;" type="button" id="" class="btn-approve approve-new">Seleccionar</x-button-genesis>
               </div>
                <!-- Modal para las fuentes -->
                <x-modal name="fuentes-modal-concepto" :show="false" maxWidth="lg">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Fuentes de Información
                        </h2>
                        <div id="fuentes-lista-concepto" class="text-gray-600 dark:text-gray-400">
                            <!-- Las fuentes se insertarán aquí dinámicamente -->
                        </div>
                        <div class="mt-6 flex justify-end">
                            <x-button-genesis type="button" x-on:click="$dispatch('close')">
                                Cerrar
                            </x-button-genesis>
                        </div>
                    </div>
                </x-modal>
            </div> --}}
            <!--fin nuevos campos -->
            
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="4" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btnvalidarconcepto" data-btnForm="btnValidarConcepto" class="">Validar</x-button-genesis>
                <x-button-genesis type="button" id="btnomitirconcepto" data-step="6" class="step-button">Omitir</x-button-genesis>
            </div>
        </form>
    </div>
</div>

    
    
    
    
    
    