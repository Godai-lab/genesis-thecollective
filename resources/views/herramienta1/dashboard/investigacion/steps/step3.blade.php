<!-- step3.blade.php -->
<div id="step-3-form">
    <div id="step-3-form-content">
        <form id="step-3-form" method="POST" action="{{route('investigacion.store')}}" data-validate="true">
            @csrf
            <input type="hidden" name="account" id="account_id_step3" value="">
            <input type="hidden" name="investigaciongenerada" id="investigaciongenerada">
            <div id="ResultadoInvestigacion" class="mb-4">
                <h2 class="text-lg font-bold mb-4">Resultado de Investigación:</h2>
                
                <div class="container-edit" id="editorinvestigacion"></div>
                
                {{-- <p style="font-size: 12px; font-weight: bold;" class="text-blue-600 dark:text-blue-400 cursor-pointer underline mt-2" x-data="" x-on:click="$dispatch('open-modal', 'fuentes-modal')">
                    Ver fuentes
                </p> --}}
                
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
                {{-- <x-button-genesis type="button" data-route="{{route('herramienta2.regenerateGenesis')}}" class="form-button-regenerate">Volver a generar</x-button-genesis> --}}
                <x-button-genesis type="button" class="form-button">Guardar</x-button-genesis>
            </div>
        </form>
    </div> 
    </div>

