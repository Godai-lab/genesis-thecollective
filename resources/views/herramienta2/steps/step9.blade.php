<!-- step9.blade.php -->
<style>

    #fuentes-lista-escenario ul {
        list-style-type: disc; /* Muestra los puntos de la lista */
        padding-left: 20px; /* Agrega espacio a la izquierda */
        font-size: 13px;
    }
    
    #fuentes-lista-escenario li {
        margin-bottom: 10px; /* Espacio entre elementos de la lista */
        word-break: break-all; /* Permite que las URLs largas se rompan */
    }
    
    #fuentes-lista-escenario a {
        color: #3490dc; /* Color de enlace */
        text-decoration: underline;
    }
    
    #fuentes-lista-escenario a:hover {
        color: #2779bd; /* Color al pasar el mouse */
    }
    </style>
    <div id="step-9-form">
        <div id="step-9-form-content">
            <form id="step9Form" method="POST" action="{{route('herramienta2.saveconstruccionescenariomejorado')}}" data-validate="true">
                @csrf
                <input type="hidden" name="construccionescenariomejorado" id="construccionescenariomejorado"> 
                {{-- <input type="hidden" name="genesisgenerado" id="genesisgenerado"> --}}
                <!--nuevos campos -->
                {{-- <div style="display: none;" id="ResultadoAnterior" class="mb-4">
                    <h2 class="text-lg font-bold mb-4">Resultado anterior:</h2>
                    <div class="container-edit" id="RegenerateEscenarioOld"></div>
                    <x-button-genesis style="" type="button" id="" class="btn-approve approve-old mt-4">Seleccionar</x-button-genesis>
                
                </div> --}}
    
                <div id="ResultadoEscenarioConceptoMejorado" class="mb-4">
                    <h2 class="text-lg font-bold mb-4">Resultado Escenario Concepto Mejorado:</h2>
                    
                    <div class="container-edit" id="editor-container-construccionescenario-concepto-mejorado"></div>
                    
                    <p style="font-size: 12px; font-weight: bold;" class="text-blue-600 dark:text-blue-400 cursor-pointer underline mt-2" x-data="" x-on:click="$dispatch('open-modal', 'fuentes-modal-escenario-concepto-mejorado')">
                        Ver fuentes
                    </p>
                   <!-- Contenedor flex para alinear los botones -->
                   <div class="flex items-center gap-4 mt-4">
                    <x-button-genesis style="display: none;" type="button" id="" class="btn-approve approve-new">Seleccionar</x-button-genesis>
                    {{-- <x-button-genesis type="button" x-data="" x-on:click="$dispatch('open-modal', 'fuentes-modal')">
                        Ver Fuentes
                    </x-button-genesis> --}}
                   </div>
                    <!-- Modal para las fuentes -->
                    <x-modal name="fuentes-modal-escenario-concepto-mejorado" :show="false" maxWidth="lg">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Fuentes de Información
                            </h2>
                            <div id="fuentes-lista-escenario-concepto-mejorado" class="text-gray-600 dark:text-gray-400">
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
                
                <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                    {{-- <x-button-genesis type="button" data-route="{{route('herramienta2.regenerarConstruccionEscenario')}}" data-form="btnconstruccionescenario" class="form-button-regenerate">Volver a generar</x-button-genesis> --}}
                    <x-button-genesis type="button" id="btnsaveconstruccionescenario-concepto-mejorado" class="" data-btnForm="btnsaveconstruccionescenariomejorado">Aceptar</x-button-genesis>
                    <x-button-genesis type="button" data-step="6" class="step-button">Omitir</x-button-genesis>
                </div>
            </form>
        </div>
    </div>
    
        
        
        
        
        
        