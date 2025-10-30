<style>

</style>
<!-- step3.blade.php -->
<div id="step-3-form">
    <div id="step-3-form-content">
        <form id="step3Form" method="POST" action="{{route('herramienta2.construccionescenario')}}" data-validate="true">
            @csrf
            <input type="hidden" name="genesisgenerado" id="genesisgenerado"> 
            <div style="display: none;" id="ResultadoAnterior" class="mb-4">
                <h2 class="text-lg font-bold mb-4">Resultado anterior:</h2>
                <div class="container-edit" id="RegenerateGenesisOld"></div>
                <x-button-genesis style="" type="button" id="" class="btn-approve approve-old mt-4">Seleccionar</x-button-genesis>
            </div>
            <div id="ResultadoGenesis" class="mb-4">
                <h2 class="text-lg font-bold mb-4">Resultado Génesis:</h2>
                <!-- Enlace en lugar del botón -->

                <div class="container-edit" id="editorGenesis"></div>
                
                <p style="font-size: 12px; font-weight: bold;" class="text-blue-600 dark:text-blue-400 cursor-pointer underline mt-2" x-data="" x-on:click="$dispatch('open-modal', 'fuentes-modal')">
                    Ver fuentes
                </p>
                
                
                
                <!-- Contenedor flex para alinear los botones -->
                <div class="flex items-center gap-4 mt-4">
                    <x-button-genesis style="display: none;" type="button" id="btnSeleccionar" class="btn-approve approve-new">
                        Seleccionar
                    </x-button-genesis>
            
                    {{-- <x-button-genesis type="button" x-data="" x-on:click="$dispatch('open-modal', 'fuentes-modal')">
                        Ver Fuentes
                    </x-button-genesis> --}}
                </div>
            
                <!-- Modal para las fuentes -->
                <x-modal name="fuentes-modal" :show="false" maxWidth="lg">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Fuentes de Información
                        </h2>
                        <div id="fuentes-lista" class="text-gray-600 dark:text-gray-400">
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
            
             
            <div class="message mt-4 text-sm space-y-1 text-red-600 font-medium"></div>
            
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="2" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" data-route="{{route('herramienta2.regenerateGenesis')}}" class="" data-btnForm="btnRegenerarEstrategia">Volver a generar</x-button-genesis>
                <x-button-genesis type="button" id="btnconstruccionescenario" data-btnForm="btnConstruccionEscenario">Continuar</x-button-genesis>
            </div>
        </form>
    </div> 
    </div>

