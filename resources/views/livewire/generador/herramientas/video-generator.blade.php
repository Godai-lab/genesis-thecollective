<div>
    
        
    <div class="w-full mx-auto space-y-1">
    <style>
        textarea.no-border { border: none !important; box-shadow: none !important; }
        .input-container {
             position: sticky; bottom: 0; 
             background-color: #ffffff;
              z-index: 10; }
        @media (max-width: 768px) {
            .input-container { bottom: 0; }
        }
        
        /* Estilos para previsualizaciones de imágenes */
        .image-preview {
            position: relative;
            display: inline-block;
        }
        
        .image-preview img {
            border-radius: 0.5rem;
            border: 2px solid #e5e7eb;
            transition: border-color 0.2s;
        }
        
        .image-preview:hover img {
            border-color: #9ca3af;
        }
        
        .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .remove-image:hover {
            background-color: #dc2626;
        }
        .image-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>

    <!-- Vista previa de imágenes para video (similar al ImageEditor) -->
    @if(!empty($imageFilesStart) || !empty($imageFilesEnd) || ($fromHistory && $imageUrl))
    <div class="w-full max-w-4xl px-0 mx-auto mb-3">
        <div class="bg-white rounded-lg border border-gray-200 p-3">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xs font-medium text-gray-600">
                        @if($fromHistory && $imageUrl)
                            Imagen del historial (inicio)
                        @elseif(!empty($imageFilesStart) && !empty($imageFilesEnd))
                            Imágenes de inicio y fin
                        @elseif(!empty($imageFilesStart))
                            Imagen de inicio
                        @else
                            Imagen de fin
                        @endif
                    </h3>
                    <span class="text-xs px-2 py-0.5 bg-gray-200 rounded-full text-gray-600">
                        {{ $this->getModelDisplayName($model) }}
                    </span>
                </div>
                <button 
                    wire:click="limpiarTodasLasImagenes"
                    class="text-gray-600 hover:text-black text-xs flex items-center gap-1"
                    title="Limpiar todas las imágenes"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="hidden sm:inline">Limpiar</span>
                </button>
            </div>
            
            <!-- Grid de imágenes con etiquetas -->
            <div class="flex flex-wrap gap-4">
                @if($fromHistory && $imageUrl)
                    <!-- Imagen del historial -->
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            <img 
                                src="{{ $imageUrl }}" 
                                alt="Imagen del historial" 
                                class="image-preview border-2 border-gray-300 shadow-sm"
                            >
                            <!-- Indicador de que viene del historial -->
                            <div class="absolute top-1 left-1 bg-black text-white rounded-full p-1">
                                <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-xs text-gray-700 font-medium mt-1">Historial (Inicio)</span>
                    </div>
                @elseif(!empty($imageFilesStart))
                    <!-- Imagen de inicio -->
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            <img 
                                src="{{ $imageFilesStart[0]->temporaryUrl() }}" 
                                alt="Imagen de inicio" 
                                class="image-preview border-2 border-gray-300 shadow-sm"
                            >
                            <!-- Etiqueta de inicio -->
                            <div class="absolute top-1 left-1 bg-black text-white rounded-full p-1">
                                <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <!-- Botón para eliminar -->
                            <button 
                                wire:click="quitarImagenInicio(0)"
                                class="absolute -top-1 -right-1 bg-gray-600 hover:bg-gray-700 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Eliminar imagen de inicio"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-gray-700 font-medium mt-1">Inicio</span>
                    </div>
                @endif
                
                <!-- Imagen de fin -->
                @if(!empty($imageFilesEnd))
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            <img 
                                src="{{ $imageFilesEnd[0]->temporaryUrl() }}" 
                                alt="Imagen de fin" 
                                class="image-preview border-2 border-gray-300 shadow-sm"
                            >
                            <!-- Etiqueta de fin -->
                            <div class="absolute top-1 left-1 bg-black text-white rounded-full p-1">
                                <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <!-- Botón para eliminar -->
                            <button 
                                wire:click="quitarImagenFin(0)"
                                class="absolute -top-1 -right-1 bg-gray-600 hover:bg-gray-700 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Eliminar imagen de fin"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-gray-700 font-medium mt-1">Fin</span>
                    </div>
                @endif
            </div>
            
            <!-- Información adicional -->
            <div class="mt-2 text-xs text-gray-500 flex gap-3">
                <span>Modelo: {{ $this->getModelDisplayName($model) }}</span>
                @if($fromHistory && $imageUrl)
                    <span>Imagen del historial</span>
                    @if(!empty($historyMetadata['originalModel']))
                        <span>Origen: {{ $historyMetadata['originalModel'] }}</span>
                    @endif
                @elseif(!empty($imageFilesStart) && !empty($imageFilesEnd))
                    <span>2 imágenes cargadas</span>
                @elseif(!empty($imageFilesStart) || !empty($imageFilesEnd))
                    <span>1 imagen cargada</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Caja de herramienta  -->
    <div class="w-full max-w-4xl px-0 mx-auto">
    <div class="input-container bg-black rounded-xl p-2 shadow-lg ">
        <div class="relative bg-white rounded-lg">
            <textarea 
                wire:model.live="promptText" 
                class="w-full outline-none resize-none text-sm min-h-[60px] text-gray-700 no-border" 
                placeholder="Describe un video para generar..."
                @keydown.enter.prevent="$event.shiftKey || $wire.generate()"
            ></textarea>

            <div class="flex items-center justify-between px-4 pb-3">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Ratio dropdown -->
                    <div x-data="{ open: false }" class="relative group">
                        <button 
                            @click="@if(!$ratioLocked) open = !open @endif"
                            class="flex items-center space-x-1 {{ $ratioLocked ? 'bg-gray-300 cursor-not-allowed' : 'bg-gray-100 hover:bg-gray-200 cursor-pointer' }} rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                            @if($ratioLocked) disabled @endif
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>{{ $ratio }}</span>
                            @if($ratioLocked)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            @endif
                        </button>
                        @if(!$ratioLocked)
                        <div 
                            x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                        >
                            <div class="text-center mb-2 text-gray-600 font-medium">Relación de aspecto</div>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($this->getAvailableRatiosForModel() as $value => $label)
                                    <button 
                                        wire:click="$set('ratio', '{{ $value }}')"
                                        @click="open = false"
                                        class="bg-{{ $ratio === $value ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                    >
                                        <span>{{ $value }}</span>
                                        <span class="text-xs text-gray-500">{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="absolute bottom-full left-0 mb-1 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 w-[220px] z-20 shadow-lg hidden group-hover:block">
                            Ratio bloqueado por imagen subida. Elimina la imagen para cambiar el ratio.
                        </div>
                        @endif
                    </div>



                    <!-- Duración dropdown - Solo para modelos Sora -->
                    @if(in_array($model, ['sora-2', 'sora-2-pro']))
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $durationSeconds }}s</span>
                        </button>
                        <div 
                            x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                        >
                            <div class="text-center mb-2 text-gray-600 font-medium">Duración del video</div>
                            <div class="grid grid-cols-1 gap-2">
                                <button 
                                    wire:click="$set('durationSeconds', 4)"
                                    @click="open = false"
                                    class="bg-{{ $durationSeconds === 4 ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                >
                                    <span>4 segundos</span>
                                    <span class="text-xs text-gray-500">Rápido</span>
                                </button>
                                <button 
                                    wire:click="$set('durationSeconds', 8)"
                                    @click="open = false"
                                    class="bg-{{ $durationSeconds === 8 ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                >
                                    <span>8 segundos</span>
                                    <span class="text-xs text-gray-500">Medio</span>
                                </button>
                                <button 
                                    wire:click="$set('durationSeconds', 12)"
                                    @click="open = false"
                                    class="bg-{{ $durationSeconds === 12 ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                >
                                    <span>12 segundos</span>
                                    <span class="text-xs text-gray-500">Largo</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Cantidad dropdown - Para videos siempre es 1 por ahora -->
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>{{ $count }} video</span>
                        </button>
                        <div 
                            x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                        >
                            <div class="text-center mb-2 text-gray-600 font-medium">Cantidad de videos</div>
                            <div class="grid grid-cols-1 gap-2">
                                <button 
                                    wire:click="$set('count', 1)"
                                    @click="open = false"
                                    class="bg-black text-white rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                >
                                    <span>1</span>
                                    <span class="text-xs text-gray-500">Por defecto</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de subida de imágenes para modelos que soportan imágenes -->
                    @if(in_array($model, ['veo2', 'gen4_turbo', 'gen3a_turbo', 'ray2', 'ray2-flash', 'sora-2', 'sora-2-pro']))
                    <div class="flex items-center gap-2">
                        <!-- Botón de imagen de inicio (Veo2, Runway, Luma, Sora) -->
                        <button type="button" 
                                onclick="document.getElementById('imageUploadStart').click()" 
                                class="flex items-center gap-2 bg-{{ !empty($imageFilesStart) ? 'black hover:bg-black text-white' : 'gray-100 hover:bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-sm shadow-sm cursor-pointer transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Imagen de inicio</span>
                            @if(!empty($imageFilesStart))
                                <span class="bg-white text-black text-xs rounded-full w-4 h-4 flex items-center justify-center">✓</span>
                            @endif
                        </button>
                        <input id="imageUploadStart" type="file" class="hidden" wire:model.live="temporaryImagesStart" accept="image/*">
                        
                        <!-- Botón de imagen de fin (para Gen3 y Luma) -->
                        @if(in_array($model, ['gen3a_turbo', 'ray2', 'ray2-flash']))
                        <button type="button" 
                                onclick="document.getElementById('imageUploadEnd').click()" 
                                class="flex items-center gap-2 bg-{{ !empty($imageFilesEnd) ? 'black hover:bg-black text-white' : 'gray-100 hover:bg-gray-200 text-gray-700' }} rounded-full px-3 py-1 text-sm shadow-sm cursor-pointer transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Imagen de fin</span>
                            @if(!empty($imageFilesEnd))
                                <span class="bg-white text-black text-xs rounded-full w-4 h-4 flex items-center justify-center">✓</span>
                            @endif
                        </button>
                        <input id="imageUploadEnd" type="file" class="hidden" wire:model.live="temporaryImagesEnd" accept="image/*">
                        @endif
                    </div>
                    @endif
                </div>

                <button 
                    wire:click="generate"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors text-sm flex items-center"
                >
                    Generar Video
                </button>
            </div>
        </div>
    </div>
    </div>

    
    <livewire:generador.components.generating-status
    :show="$isGenerating"
    message="Generando video..."
    :subtitle="'Espere por favor...'"
    {{-- wire:key="generating-status-{{ uniqid() }}"  --}}
    />
<!-- Selector de modelos flotante, reutilizable -->
   
<livewire:generador.components.model-selector
    :models="$availableModels"
    :selected="$model"
    :eventName="'video-generator-model-selected'"
    title="Modelo de Video"
    wire:key="video-model-selector-island" />
 
</div>
@script
<script>
    console.log('📜 VideoGenerator: script ejecutado – registrando listeners');

    /* ---------- tus listeners ---------- */
    Livewire.on('videoGenerationStarted', () => {
        console.log('🎬 Iniciando generación de video...');
        const spinner = document.getElementById('generation-spinner');
        if (spinner) spinner.style.display = 'flex';

        const resultsContainer = document.querySelector('.results-container');
        if (resultsContainer) resultsContainer.classList.add('generating');
    });

    Livewire.on('videoGenerationCompleted', () => {
        console.log('✅ Generación de video completada');
        setTimeout(() => {
            const spinner = document.getElementById('generation-spinner');
            if (spinner) spinner.style.display = 'none';

            const resultsContainer = document.querySelector('.results-container');
            if (resultsContainer) resultsContainer.classList.remove('generating');
        }, 500);
    });

    Livewire.on('videoGenerationError', () => {
        console.log('❌ Error en generación de video');
        const spinner = document.getElementById('generation-spinner');
        if (spinner) spinner.style.display = 'none';

        const resultsContainer = document.querySelector('.results-container');
        if (resultsContainer) resultsContainer.classList.remove('generating');
    });

    Livewire.on('videoTaskStarted', (event) => {
        console.log('🚀 Video task iniciada:', event.generationId);
        startVideoPolling(event);
    });

    Livewire.on('videoStillPending', (event) => {
        console.log('⏳ Video aún pendiente, esperando 10s más...');
        setTimeout(() => checkVideoStatus(event), 10000);
    });

    /* ---------- funciones auxiliares ---------- */
    function startVideoPolling(data) {
        console.log('⏰ Iniciando polling para:', data.generationId);
        setTimeout(() => checkVideoStatus(data), 10000);
    }

    function checkVideoStatus(data) {
        console.log('🔍 Verificando estado de video:', data.generationId);
        Livewire.dispatch('verificarEstadoVideo', data);
    }
</script>
@endscript
</div>
