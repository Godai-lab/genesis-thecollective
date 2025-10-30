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
    <!-- step10.blade.php -->
    <div id="step-10-form">
        <div id="step-10-form-content">
            <form id="step10Form" method="POST" action="{{route('herramienta2.saveValidarConcepto')}}" data-validate="true">
                @csrf
                <!--nuevos campos -->
                <div style="display: none;" id="ResultadoAnterior" class="mb-4">
                    <h2 class="text-lg font-bold mb-4">Resultado anterior:</h2>
                    <div class="container-edit" id="RegenerateConceptoOld"></div>
                    <x-button-genesis style="" type="button" id="" class="btn-approve approve-old mt-4">Seleccionar</x-button-genesis>
                
                </div>
    
                <div id="ResultadoConcepto" class="mb-4">
                    <h2 class="text-lg font-bold mb-4">Validar Concepto:</h2>
                    
                    <div class="container-edit" id="editor-container-concepto"></div>
                    
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
                </div>
                <!--fin nuevos campos -->
                <input type="hidden" name="validarConcepto" id="validarConcepto">

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
                    <x-button-genesis type="button" data-step="5" class="step-button">Regresar</x-button-genesis>
                    <x-button-genesis type="button" id="btnguardarconcepto" data-btnForm="btnguardarconcepto" class="">Guardar</x-button-genesis>
                    <x-button-genesis type="button" data-route="{{route('herramienta2.mejorarConcepto')}}" class="" data-btnForm="mejorarConceptoForm">Mejorar concepto</x-button-genesis>
                    <x-button-genesis type="button" data-step="6" class="step-button">Omitir</x-button-genesis>
                </div>
            </form>
        </div>
    </div>    