<div>
    <style>
        [x-cloak] {
            display: none !important;
        }
        .processing-animation {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .video-preview {
            position: relative;
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 8px;
            transition: transform 0.2s ease;
        }
        .video-preview video {
            width: 150px;
            height: 110px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .remove-video {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .video-preview:hover .remove-video {
            opacity: 1;
        }
        /* Estilos de overlay removidos */
        .input-container {
            position: sticky;
            bottom: 0;
            background-color: rgb(255, 255, 255);
            z-index: 10;
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            let pollingInterval = null;

            // Helpers de scroll (igual enfoque que en new-generador)
            function scrollToElement(element) {
                if (!element) return;
                const rect = element.getBoundingClientRect();
                const offset = rect.top + window.pageYOffset - 10; // pequeño offset
                window.scrollTo({ top: offset, behavior: 'smooth' });
            }

            function scrollToLastVideo() {
                const lastItem = document.querySelector('#videos-container > .flex:last-child');
                if (lastItem) {
                    scrollToElement(lastItem);
                    return true;
                }
                const container = document.getElementById('videos-container');
                if (container) {
                    scrollToElement(container);
                    return true;
                }
                return false;
            }

            function scrollContainerToBottom() {
                const container = document.querySelector('.flex-1.overflow-y-auto');
                if (container && container.scrollHeight > container.clientHeight) {
                    container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                } else {
                    // Si no hay contenedor con overflow, usar scroll de ventana al último video
                    scrollToLastVideo();
                }
            }
            
            // Escuchar el evento para iniciar polling
            Livewire.on('start-polling', () => {
                console.log('Iniciando polling de estados de video...');
                startPolling();
            });
            
            // Escuchar el evento para programar siguiente verificación
            Livewire.on('schedule-next-check', () => {
                console.log('Programando siguiente verificación en 15 segundos...');
                scheduleNextCheck();
            });
            
            // Escuchar el evento para iniciar subida con retraso
            Livewire.on('iniciar-subida-delayed', () => {
                console.log('Iniciando subida con retraso de 1 segundo...');
                setTimeout(() => {
                    @this.iniciarSubida();
                }, 1000); // Retraso de 1 segundo para que se vea el estado
            });
            
            // Escuchar el evento para hacer scroll hasta el final
            Livewire.on('scroll-to-bottom', () => {
                setTimeout(() => {
                    scrollContainerToBottom();
                }, 150);
            });

            // Observer: cuando se agregue un video nuevo al historial, bajar automáticamente
            setTimeout(() => {
                const videosContainer = document.getElementById('videos-container');
                if (!videosContainer) return;
                const observer = new MutationObserver((mutations) => {
                    for (const mutation of mutations) {
                        if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                            scrollContainerToBottom();
                            break;
                        }
                    }
                });
                observer.observe(videosContainer, { childList: true });
            }, 0);
            
            function startPolling() {
                // Limpiar intervalo existente si hay uno
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
                
                // Verificar inmediatamente
                checkVideoStatuses();
                
                // Programar verificación cada 15 segundos
                pollingInterval = setInterval(() => {
                    checkVideoStatuses();
                }, 15000);
            }
            
            function scheduleNextCheck() {
                // Programar una sola verificación en 15 segundos
                setTimeout(() => {
                    checkVideoStatuses();
                }, 15000);
            }
            
            function checkVideoStatuses() {
                console.log('Verificando estados de videos...');
                @this.checkVideoStatuses();
            }
            
            // Limpiar intervalo cuando se desmonte el componente
            Livewire.on('livewire:beforeDestroy', () => {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
            });
        });
    </script>

    <div class="bg-white text-gray-800 min-h-screen flex flex-col">
        <!-- Contenido principal con scroll -->
        <div class="flex-1 overflow-y-auto">
            <div class="w-full max-w-4xl px-4 mx-auto pb-4">
                <!-- Historial en formato chat, alineado a la izquierda -->
                @if(!empty($editedVideos))
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-3">
                            {{-- <h3 class="text-lg font-medium text-gray-900">Historial</h3> --}}
                        </div>

                        <div id="videos-container" class="space-y-4 max-w-md">
                            @foreach($editedVideos as $index => $video)
                                <div class="flex">
                                    <div class="w-full bg-white border border-gray-200 rounded-xl p-3 shadow-sm">

                                        @if(($video['status'] ?? '') === 'completed' && isset($video['finalUrl']))
                                            <div>
                                                <video controls class="w-full rounded-lg border border-gray-200 shadow-sm">
                                                    <source src="{{ $video['finalUrl'] }}" type="video/mp4">
                                                    Tu navegador no soporta la etiqueta de video.
                                                </video>
                                                {{-- <div class="mt-2 flex items-center justify-end">
                                                    <a href="{{ $video['finalUrl'] }}" download class="text-xs px-2 py-1 rounded bg-black hover:bg-gray-800 text-white">Descargar</a>
                                                </div> --}}
                                            </div>
                                        @elseif(($video['status'] ?? '') === 'processing')
                                            <div class="flex items-center">
                                                <svg class="animate-spin h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-600">Procesando...</span>
                                            </div>
                                        @elseif(($video['status'] ?? '') === 'failed')
                                            <div class="flex items-center justify-start">
                                                <span class="text-sm text-red-600">Falló: {{ $video['error'] ?? 'Error desconocido' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Debug removido --}}

                <!-- Mensaje de subida a S3 -->
                @if(false && $subiendo)
                    <div class="bg-yellow-100 border-2 border-yellow-400 rounded-lg p-6 mb-6 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-8 w-8 text-yellow-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <!-- Mensaje removido -->
                            <p class="text-sm text-yellow-700">Por favor espera mientras se procesa la subida del archivo</p>
                        </div>
                    </div>
                @endif

                <!-- Mensaje alternativo si no se muestra el de arriba -->
                @if(false && !$subiendo && !$videoUrl && $videoToEdit)
                    <div class="bg-red-100 border-2 border-red-400 rounded-lg p-4 mb-6 text-center">
                        <h3 class="text-lg font-bold text-red-800">⚠️ ESTADO: Video seleccionado pero no subiendo ni subido</h3>
                    </div>
                @endif

                <!-- Mensajes de error -->
                @if($errorMessage)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-800">{{ $errorMessage }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button wire:click="clearError" class="text-red-400 hover:text-red-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

      

        <!-- Caja de texto fija en la parte inferior -->
        <div class="input-container bg-black rounded-xl p-4 shadow-lg mx-4 mb-4">
            @if($videoToEdit)
            <div class="mx-4 mb-2">
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <video controls class="w-20 h-16 rounded border border-gray-300 object-cover">
                                <source src="{{ $videoToEdit->temporaryUrl() }}" type="video/mp4">
                            </video>
                        </div>
                        
                        <!-- Estado del video -->
                        <div class="flex-1">
                            @if($videoUrl)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-500 font-medium">Video listo</span>
                                </div>
                            @elseif($subiendo)
                                <div class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-500 font-medium">Procesando video...</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Video seleccionado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif



            <div class="relative bg-white rounded-lg">


                
                <textarea 
                    wire:model="transformSettings.promptText" 
                    class="w-full outline-none resize-none text-lg min-h-[80px] text-gray-700 no-border p-4" 
                    placeholder="Describe cómo quieres transformar el video ."
                    @keydown.enter.prevent="$event.shiftKey || $wire.procesarVideo()"
                ></textarea>
                
                <div class="flex items-center justify-between px-4 pb-3">
                    <!-- Opciones de configuración -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Botón para subir video -->
                        <div class="relative">
                            @if(!$videoToEdit)
                                <!-- Estado: No hay video -->
                                <input type="file" wire:model="videoFiles" accept="video/*" class="hidden" id="video-upload-inline" @if($subiendo) disabled @endif>
                                <label for="video-upload-inline" class="flex items-center space-x-1 {{ $subiendo ? 'bg-blue-100 cursor-not-allowed' : 'bg-gray-100 hover:bg-gray-200 cursor-pointer' }} rounded-full px-3 py-1 text-sm shadow-sm {{ $subiendo ? 'text-blue-700' : 'text-gray-700' }}">
                                    @if($subiendo)
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Subiendo...</span>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span>Subir Video</span>
                                    @endif
                                </label>
                            @else
                                <!-- Estado: Hay video - mostrar solo opción para quitar -->
                                <div class="flex items-center space-x-1">
                                    <!-- Indicador de video cargado -->
                                    <div class="flex items-center space-x-1 bg-black rounded-full px-3 py-1 text-sm shadow-sm text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Video cargado</span>
                                    </div>
                                    
                                    <!-- Botón para quitar video -->
                                    <button 
                                        wire:click="quitarVideoParaEditar"
                                        class="flex items-center justify-center w-8 h-8 bg-white hover:bg-gray-100 border border-gray-200 rounded-full text-gray-600 hover:text-gray-800"
                                        title="Quitar video para subir otro"
                                        @if($subiendo) disabled @endif
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button 
                                @click="open = !open"
                                class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                <span>{{ $transformSettings['ratio'] }}</span>
                            </button>
                            
                            <div 
                                x-show="open" 
                                x-cloak
                                @click.away="open = false"
                                class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                            >
                                <div class="text-center mb-2 text-gray-600 font-medium">Ratio de salida</div>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach($this->getAvailableRatios() as $ratio => $label)
                                        <button 
                                            wire:click="$set('transformSettings.ratio', '{{ $ratio }}')"
                                            @click="open = false"
                                            class="bg-{{ $transformSettings['ratio'] === $ratio ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                        >
                                            <span>{{ $ratio }}</span>
                                            <span class="text-xs text-gray-500">{{ $label }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button 
                                @click="open = !open"
                                class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $transformSettings['duration'] }}s</span>
                            </button>
                            
                            <div 
                                x-show="open" 
                                x-cloak
                                @click.away="open = false"
                                class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                            >
                                <div class="text-center mb-2 text-gray-600 font-medium">Duración</div>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach($this->getAvailableDurations() as $duration => $label)
                                        <button 
                                            wire:click="$set('transformSettings.duration', {{ $duration }})"
                                            @click="open = false"
                                            class="bg-{{ $transformSettings['duration'] == $duration ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                        >
                                            <span>{{ $duration }}s</span>
                                            <span class="text-xs text-gray-500">{{ $label }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            
                        </div>
                        <!-- Botón Limpiar (barra de herramientas) -->
                        <div class="flex items-center space-x-1">
                        <button 
                        x-data="{}"
                        @click="if(confirm('¿Limpiar historial?')) { $wire.limpiarHistorial() }"
                        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                        title="Limpiar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m-3 0h14" />
                        </svg>
                        <span>Limpiar</span>
                    </button>
                        </div>
                    </div>

                    <!-- Botón de procesar -->
                    {{-- @if(!$videoUrl)
                        <button disabled class="bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed text-sm">
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                No disponible
                            </span>
                        </button>
                    @else @endif--}}
                        <button 
                            wire:click="procesarVideo"
                            x-bind:disabled="$wire.isProcessing"
                            class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm flex items-center"
                        >
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Generar
                        </button>
                    
                </div>
            </div>
        </div>
    </div>

    
</div> 
