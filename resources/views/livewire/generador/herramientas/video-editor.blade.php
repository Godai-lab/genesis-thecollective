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
        
        /* Estilos para preview de video */
        .video-preview {
            position: relative;
            display: inline-block;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            transition: border-color 0.2s;
        }
        
        .video-preview:hover {
            border-color: #9ca3af;
        }
        
        .video-preview video {
            width: 120px;
            height: 80px;
            object-fit: cover;
        }
        
        .remove-video {
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
        
        .remove-video:hover {
            background-color: #dc2626;
        }



    </style>



    <!-- Caja de herramienta principal -->
    <div class="w-full max-w-4xl px-0 mx-auto">
    <div class="input-container bg-black rounded-xl p-2 shadow-lg">
        
        <!-- Preview del video seleccionado -->
        @if($videoFile || $videoUrl)
            <div class="mb-3 px-4 pt-2">
                <div class="bg-white rounded-lg p-3 border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            @if($fromHistory && $videoUrl)
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-xs font-medium text-gray-700">Video del historial</h3>
                                @if(!empty($historyMetadata['originalModel']))
                                    <span class="text-xs px-2 py-0.5 bg-gray-200 rounded-full text-gray-600">
                                        {{ $historyMetadata['originalModel'] }}
                                    </span>
                                @endif
                            @else
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-xs font-medium text-gray-600">Video subido</h3>
                            @endif
                        </div>
                        <button 
                            wire:click="quitarVideo"
                            class="text-gray-600 hover:text-black text-xs flex items-center gap-1"
                            title="Limpiar video"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="hidden sm:inline">Limpiar</span>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="video-preview">
                            @if($videoFile)
                                <video controls class="w-20 h-16">
                                    <source src="{{ $videoFile->temporaryUrl() }}" type="video/mp4">
                                    Tu navegador no soporta el elemento video.
                                </video>
                            @elseif($videoUrl)
                                <video controls class="w-20 h-16">
                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                    Tu navegador no soporta el elemento video.
                                </video>
                                @if($fromHistory)
                                    <!-- Indicador de que viene del historial -->
                                    <div class="absolute top-1 left-1 bg-black text-white rounded-full p-1">
                                        <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center">
                                @if($isUploading)
                                    <svg class="animate-spin h-4 w-4 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600 font-medium">Subiendo video...</span>
                                @elseif($videoUrl)
                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-600 font-medium">Video listo para editar</span>
                                @else
                                    <span class="text-sm text-gray-600">Video seleccionado</span>
                                @endif
                            </div>
                        </div>
                        
                        <button 
                            wire:click="quitarVideo"
                            class="flex-shrink-0 text-gray-400 hover:text-red-600 transition-colors"
                            title="Quitar video"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Información adicional del historial -->
                    @if($fromHistory && !empty($historyMetadata))
                    <div class="mt-2 text-xs text-gray-500 flex gap-3">
                        @if(!empty($historyMetadata['originalRatio']))
                            <span>Ratio: {{ $historyMetadata['originalRatio'] }}</span>
                        @endif
                        @if(!empty($historyMetadata['generationId']))
                            <span>ID: {{ substr($historyMetadata['generationId'], -8) }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="relative bg-white rounded-lg">
            <textarea 
                wire:model.defer="promptText" 
                class="w-full outline-none resize-none text-sm min-h-[60px] text-gray-700 no-border" 
                placeholder="Describe cómo quieres transformar el video..."
                @keydown.enter.prevent="$event.shiftKey || $wire.processVideo()"
            ></textarea>

            <div class="flex items-center justify-between px-4 pb-3">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Botón para subir video -->
                    @if(!$videoFile && !$videoUrl)
                        <div class="relative">
                            <input type="file" wire:model.live="videoFile" accept="video/*" class="hidden" id="video-upload">
                            <label for="video-upload" class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700 cursor-pointer transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Subir Video</span>
                            </label>
                        </div>
                    @endif

                    <!-- Ratio dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>{{ $ratio }}</span>
                        </button>
                        <div 
                            x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                        >
                            <div class="text-center mb-2 text-gray-600 font-medium">Ratio de salida</div>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($this->getAvailableRatiosForModel() as $ratioValue => $label)
                                    <button 
                                        wire:click="$set('ratio', '{{ $ratioValue }}')"
                                        @click="open = false"
                                        class="bg-{{ $ratio === $ratioValue ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                    >
                                        <span>{{ $ratioValue }}</span>
                                        <span class="text-xs text-gray-500">{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Duración dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $duration }}s</span>
                        </button>
                        <div 
                            x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                        >
                            <div class="text-center mb-2 text-gray-600 font-medium">Duración</div>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($this->getAvailableDurationsForModel() as $durationValue => $label)
                                    <button 
                                        wire:click="$set('duration', {{ $durationValue }})"
                                        @click="open = false"
                                        class="bg-{{ $duration == $durationValue ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                    >
                                        <span>{{ $durationValue }}s</span>
                                        <span class="text-xs text-gray-500">{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <button 
                    wire:click="processVideo"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors text-sm flex items-center"
                    @if(!$videoFile && !$videoUrl) disabled @endif
                >
                    
                    Editar Video
                </button>
            </div>
        </div>
    </div>
    </div>

    <!-- Componente de estado de generación -->
    <livewire:generador.components.generating-status
        :show="$isGenerating"
        message="Editando video..."
        :subtitle="'Aplicando transformaciones...'"
    />

    <!-- Selector de modelos -->
    <livewire:generador.components.model-selector
        :models="$availableModels"
        :selected="$model"
        :eventName="'video-editor-model-selected'"
        title="Modelo de Edición"
        wire:key="video-editor-model-selector" />
</div>

@script
<script>
   
    const $ = id => document.getElementById(id);
    const q   = sel => document.querySelector(sel);

    function toggleSpinner(show = true) {
        const spinner = $('generation-spinner');
        if (spinner) spinner.style.display = show ? 'flex' : 'none';
    }

    function startVideoEditPolling(data) {
        console.log('🚀 Iniciando polling edición:', data.generationId);
        setTimeout(() => checkVideoEditStatus(data), 10_000);
    }

    function checkVideoEditStatus(data) {
        console.log('🔍 Verificando edición:', data.generationId);
        Livewire.dispatch('verificarEstadoVideoEditor', data);
    }

    /* ---------- listeners ---------- */
    Livewire.on('videoEditStarted', () => {
        console.log('🚀 Iniciando edición de video...');
        toggleSpinner(true);
    });

    Livewire.on('videoEditCompleted', () => {
        console.log('✅ Edición completada');
        setTimeout(() => toggleSpinner(false), 500);
    });

    Livewire.on('videoEditError', () => {
        console.log('❌ Error en edición');
        toggleSpinner(false);
    });

    Livewire.on('videoEditTaskStarted', (e) => startVideoEditPolling(e));

    Livewire.on('videoEditStillPending', (e) => {
        console.log('⏳ Edición aún pendiente');
        setTimeout(() => checkVideoEditStatus(e), 10_000);
    });

    Livewire.on('videoLoadedForEditing', (e) => {
        console.log('📹 Video cargado para edición:', e.url);
    });

    /* ---------- montaje: procesar posible video pendiente ---------- */
    if (window.pendingVideoEditData) {
        console.log('🔄 Procesando video pendiente al montar:', window.pendingVideoEditData);
        Livewire.dispatch('loadVideoFromHistory', [
            window.pendingVideoEditData.videoUrl,
            window.pendingVideoEditData.generationId,
            window.pendingVideoEditData.originalModel,
            window.pendingVideoEditData.originalRatio
        ]);
        window.pendingVideoEditData = null;
    }

    console.log('📜 VideoEditor: listeners registrados');
</script>
@endscript
</div>
