<style>

#visual-lista-fuentes-genesis ul,
#visual-lista-fuentes-escenario ul {
    list-style-type: disc !important;
    padding-left: 40px !important;
    margin-top: 5px;
    font-size: 14px;
}


</style>
<!-- step7.1.blade.php -->
<div id="step-7-1-form">
    <div id="step-7-1-form-content">
        <form id="step7-1Form" method="POST" action="{{route('herramienta2.setGenerarIdeasContenido')}}" data-validate="true">
            @csrf
            <input type="hidden" name="construccionestrategia" id="construccionestrategia"> 
            {{-- <input type="hidden" name="genesisgenerado" id="genesisgenerado"> --}}
            <!--nuevos campos -->
            <div style="display: none;" id="ResultadoAnterior" class="mb-4">
                <h2 class="text-lg font-bold mb-4">Resultado anterior:</h2>
                <div class="container-edit" id="RegenerateEstrategiaOld"></div>
                <x-button-genesis style="" type="button" id="" class="btn-approve approve-old mt-4">Seleccionar</x-button-genesis>
            
            </div>

            <div id="ResultadoEstrategia" class="mb-4">
                <h2 class="text-lg font-bold mb-4">Resultado Estrategia:</h2>

                <div class="container-edit" id="editor-container-estrategia"></div>
                
                <p style="font-size: 12px; font-weight: bold;" class="text-blue-600 dark:text-blue-400 cursor-pointer underline mt-2" x-data="" x-on:click="$dispatch('open-modal', 'fuentes-modal-estrategia')">
                    Ver fuentes
                </p>
               <!-- Contenedor flex para alinear los botones -->
               <div class="flex items-center gap-4 mt-4">
                <x-button-genesis style="display: none;" type="button" id="" class="btn-approve approve-new">Seleccionar</x-button-genesis>
               </div>
                <!-- Modal para las fuentes -->
                <x-modal name="fuentes-modal-estrategia" :show="false" maxWidth="lg">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Fuentes de Información
                        </h2>
                        <div id="fuentes-lista-estrategia" class="text-gray-600 dark:text-gray-400">
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
                <x-button-genesis type="button" data-step="7" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" data-route="{{route('herramienta2.setGenerarEstrategia')}}" data-btnForm="btnRegenerarConstruccionEstrategia" class="">Volver a generar</x-button-genesis>
                <x-button-genesis type="button" id="btnsaveconstruccionestrategia" class="" data-btnForm="btnSaveConstruccionEstrategia">Aceptar</x-button-genesis>
            </div>
        </form>
    </div>
</div>
