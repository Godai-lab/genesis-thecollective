<div>
    <style>
        /* Estilos completos para que coincidan con el generador de imágenes */
        [x-cloak] {
            display: none !important;
        }
        .generating-animation {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .selected-option {
            background-color: black;
            color: white;
        }
        .image-preview {
            position: relative;
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        .image-preview img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .remove-image {
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
        }
        .generating-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        .gallery-container {
            padding: 20px;
            border-radius: 10px;
            background-color: #f8fafc;
            margin-bottom: 20px;
            min-height: 300px;
            max-height: 60vh;
            overflow-y: auto;
        }
        .text-typing {
            border-right: 2px solid;
            animation: blinkCursor 0.7s infinite;
        }
        @keyframes blinkCursor {
            0%, 100% { border-color: transparent; }
            50% { border-color: black; }
        }
        .message-item {
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .user-message {
            background-color: #f1f5f9;
            padding: 12px 16px;
            border-radius: 18px 18px 18px 4px;
            align-self: flex-start;
            display: inline-block;
            max-width: 80%;
        }
        .system-message {
            border-radius: 18px 18px 4px 18px;
            padding: 12px 16px;
            align-self: flex-end;
            display: inline-block;
            max-width: 80%;
        }
        .input-container {
            position: sticky;
            bottom: 0;
            background-color: white;
            padding-top: 10px;
        }
        .no-border {
            border: none !important;
            outline: none !important;
            resize: none !important;
            padding: 10px 15px !important;
        }
        .error-message {
            display: block !important; /* Forzar visualización */
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            background-color: #FEE2E2;
            color: #B91C1C;
            border: 1px solid #F87171;
        }
        .generated-image, .gallery-image, .message-image, .result-image {
            cursor: pointer;
            transition: transform 0.2s ease, filter 0.2s ease;
        }
        .generated-image:hover, .gallery-image:hover, .message-image:hover, .result-image:hover {
            transform: scale(1.02);
            filter: brightness(1.05);
        }
        .lightbox-overlay {
            backdrop-filter: blur(3px);
            animation: fadeIn 0.3s ease;
        }
    </style>
    
    <div class="bg-white text-gray-800 min-h-screen">
        <!-- Navegación entre componentes -->
        <div class="flex justify-center my-4 mb-8">
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <a href="{{ route('asistenteGenerador.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 focus:z-10 focus:ring-2 focus:ring-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Generar Imágenes
                </a>
                <a href="{{ route('generar-videos') }}" 
                   class="px-4 py-2 text-sm font-medium text-white bg-black border border-gray-700 rounded-r-lg hover:bg-gray-800 focus:z-10 focus:ring-2 focus:ring-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Generar Videos
                </a>
            </div>
        </div>
        
        <!-- Encabezado para videos -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex items-center bg-gray-100 px-4 py-2 rounded-lg shadow-sm min-w-[200px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span class="font-semibold text-xl text-gray-800">Generador de Videos</span>
            </div>
        </div>
        
        <div class="w-full max-w-4xl px-4 mx-auto main-content-container">
            @if(empty($chatHistory))
                <div class="mb-4">
                    <div class="input-container bg-black rounded-xl p-4 shadow-lg">
                        <div class="relative bg-white rounded-lg">
                            <textarea 
                                wire:model="prompt" 
                                class="w-full outline-none resize-none text-lg min-h-[80px] text-gray-700 no-border" 
                                placeholder="Describe una secuencia de video para generar..."
                                @keydown.enter.prevent="$event.shiftKey || $wire.generar()"
                            ></textarea>
                            
                            <div class="p-2 flex flex-wrap gap-2 items-center">
                                <!-- Botón para subir imágenes -->
                                <div class="flex items-center">
                                    <label for="file-upload" class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Subir imagen</span>
                                    </label>
                                    <input id="file-upload" wire:model="temporaryImage" type="file" accept="image/*" class="hidden" />
                                </div>
                                
                                <!-- Previsualización de la imagen subida -->
                                @if($imagePreview)
                                    <div class="image-preview">
                                        <img src="{{ $imagePreview }}" alt="Imagen previa" />
                                        <button class="remove-image" wire:click="quitarImagen">×</button>
                                    </div>
                                @endif
                                
                                <!-- Mensaje informativo -->
                                @if($imageFile)
                                    <div class="text-xs text-gray-500 ml-2">
                                        <span>La imagen se usará como primer fotograma del video</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Mensajes de error para la carga de imágenes -->
                            @error('temporaryImage')
                                <div class="text-red-500 text-sm p-2">{{ $message }}</div>
                            @enderror
                            
                            <!-- Botón de generar -->
                            <div class="flex justify-end mt-3 px-3 pb-3">
                                <button 
                                    wire:click="generar"
                                    class="bg-black text-white rounded-lg px-6 py-2 font-medium hover:bg-gray-900 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $isGenerating ? 'disabled' : '' }}
                                >
                                    @if($isGenerating)
                                        <span class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Generando...
                                        </span>
                                    @else
                                        Generar
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Mensaje de error -->
                @if(session()->has('error'))
                <div class="error-message flex items-center my-4 sticky top-[70px] z-20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium">Error en la generación</p>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
                @endif
                
                <!-- Contenido del historial de videos -->
                <div class="gallery-container bg-gray-50 rounded-lg shadow-sm">
                    @foreach($chatHistory as $index => $mensaje)
                        <div class="message-item p-3" wire:key="mensaje-{{ $index }}">
                            @if($mensaje['tipo'] === 'usuario')
                                <!-- Mensaje del usuario -->
                                <div class="flex items-start mb-4">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="user-message">
                                            <p>{{ $mensaje['contenido'] }}</p>
                                            
                                            <!-- Si hay imágenes, mostrarlas -->
                                            @if(!empty($mensaje['imagenes']))
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach($mensaje['imagenes'] as $imagen)
                                                        <div class="w-full max-w-[200px]">
                                                            <img src="{{ $imagen['url'] }}" alt="Imagen para video" class="w-full h-auto rounded-lg border border-gray-200 shadow-sm">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 mt-1">{{ $mensaje['tiempo'] }}</span>
                                    </div>
                                </div>
                            @else
                                <!-- Respuesta del sistema: videos generados -->
                                <div class="flex items-start mb-4 justify-end">
                                    <div class="flex flex-col items-end">
                                        <div class="system-message bg-gray-100">
                                            <p>{{ $mensaje['contenido'] }}</p>
                                        </div>
                                        <span class="text-xs text-gray-500 mt-1">{{ $mensaje['tiempo'] }}</span>
                                    </div>
                                    <div class="flex-shrink-0 ml-3">
                                        <div class="w-8 h-8 rounded-full bg-black flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(!empty($mensaje['url']))
                                    <div class="w-full my-4 flex justify-center">
                                        <div class="relative" style="width: 100%; max-width: 800px;">
                                            <video 
                                                src="{{ $mensaje['url'] }}" 
                                                controls 
                                                class="w-full h-auto rounded-lg shadow-md"
                                            ></video>
                                            <a href="{{ $mensaje['url'] }}" download="video_generado_{{ $index }}.mp4" class="absolute bottom-2 right-2 bg-black bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Descargar
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                    
                    <!-- Indicador de generación -->
                    @if($videoGenerating || $isGenerating)
                    <div class="rounded-lg shadow my-4 bg-white p-4 border border-gray-200">
                        <div class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-3 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-medium">Generando video...</span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-black h-2 rounded-full animate-pulse" style="width: 70%"></div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Área de entrada para generar nuevo video -->
                <div class="input-container bg-black rounded-b-xl p-4 shadow-lg">
                    <div class="relative bg-white rounded-lg">
                        <textarea 
                            wire:model="prompt" 
                            class="w-full outline-none resize-none text-lg min-h-[80px] text-gray-700 no-border" 
                            placeholder="Describe una secuencia de video para generar..."
                            @keydown.enter.prevent="$event.shiftKey || $wire.generar()"
                        ></textarea>
                        
                        <div class="p-2 flex flex-wrap gap-2 items-center">
                            <!-- Botón para subir imágenes -->
                            <div class="flex items-center">
                                <label for="file-upload" class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Subir imagen</span>
                                </label>
                                <input id="file-upload" wire:model="temporaryImage" type="file" accept="image/*" class="hidden" />
                            </div>
                            
                            <!-- Previsualización de la imagen subida -->
                            @if($imagePreview)
                                <div class="image-preview">
                                    <img src="{{ $imagePreview }}" alt="Imagen previa" />
                                    <button class="remove-image" wire:click="quitarImagen">×</button>
                                </div>
                            @endif
                            
                            <!-- Mensaje informativo -->
                            @if($imageFile)
                                <div class="text-xs text-gray-500 ml-2">
                                    <span>La imagen se usará como primer fotograma del video</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Mensajes de error para la carga de imágenes -->
                        @error('temporaryImage')
                            <div class="text-red-500 text-sm p-2">{{ $message }}</div>
                        @enderror
                        
                        <!-- Botón de limpiar -->
                        <button 
                            x-data="{}"
                            @click="if(confirm('¿Estás seguro de que quieres limpiar todo el historial?')) { $wire.limpiarHistorial() }"
                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Limpiar</span>
                        </button>
                    </div>
                    
                    <!-- Botón de generar -->
                    <div class="flex justify-end mt-3 px-3 pb-3">
                        <button 
                            wire:click="generar"
                            class="bg-black text-white rounded-lg px-6 py-2 font-medium hover:bg-gray-900 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $isGenerating ? 'disabled' : '' }}
                        >
                            @if($isGenerating)
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generando...
                                </span>
                            @else
                                Generar
                            @endif
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Scripts para gestionar la UI de videos -->
    <script>
        document.addEventListener('livewire:init', () => {
            const scrollToBottom = () => {
                const galleryContainer = document.querySelector('.gallery-container');
                if (galleryContainer && galleryContainer.children.length > 0) {
                    galleryContainer.scrollTop = galleryContainer.scrollHeight;
                    setTimeout(() => {
                        galleryContainer.scrollTop = galleryContainer.scrollHeight;
                    }, 100);
                    window.scrollTo(0, document.body.scrollHeight);
                }
            };
            
            const scrollToTop = () => {
                window.scrollTo(0, 0);
                setTimeout(() => {
                    window.scrollTo(0, 0);
                }, 100);
            };
            
            window.addEventListener('load', () => {
                const galleryContainer = document.querySelector('.gallery-container');
                if (galleryContainer && galleryContainer.children.length > 0) {
                    setTimeout(scrollToBottom, 300);
                    setTimeout(scrollToBottom, 1000);
                }
            });
            
            Livewire.on('historialActualizado', () => {
                setTimeout(scrollToBottom, 50);
                setTimeout(scrollToBottom, 500);
            });
            
            Livewire.on('historialLimpiado', () => {
                scrollToTop();
            });
            
            Livewire.on('videoGenerado', () => {
                setTimeout(scrollToBottom, 50);
                setTimeout(scrollToBottom, 300);
            });
            
            Livewire.on('errorOcurrido', () => {
                // Forzar actualización de la UI
                setTimeout(() => {
                    const errorMessages = document.querySelectorAll('.error-message');
                    errorMessages.forEach(msg => {
                        msg.style.display = 'block';
                    });
                }, 100);
            });
            
            Livewire.hook('message.processed', (message, component) => {
                if (component.name === 'video-generador') {
                    // Verificar si el mensaje contiene la acción de limpiar historial
                    if (message.updateQueue && message.updateQueue.some(update => 
                        update.payload && update.payload.event === 'limpiarHistorial')) {
                        scrollToTop();
                        return;
                    }
                    
                    const galleryContainer = document.querySelector('.gallery-container');
                    if (galleryContainer && galleryContainer.children.length > 0) {
                        setTimeout(scrollToBottom, 50);
                        setTimeout(scrollToBottom, 500);
                    }
                }
            });
            
            // También modificar el botón de limpiar para hacer scroll arriba
            document.addEventListener('click', (e) => {
                if (e.target.closest('button') && 
                    e.target.closest('button').textContent.includes('Limpiar')) {
                    setTimeout(scrollToTop, 300);
                }
            });
            
            const galleryContainer = document.querySelector('.gallery-container');
            if (galleryContainer) {
                const observer = new MutationObserver(() => {
                    if (galleryContainer.children.length > 0) {
                        scrollToBottom();
                    } else {
                        scrollToTop();
                    }
                });
                
                observer.observe(galleryContainer, {
                    childList: true,
                    subtree: true
                });
            }
            
            Livewire.on('verificarEstadoVideo', () => {
                console.log('Verificando estado de video...');
                
                const allIndicators = document.querySelectorAll('.animate-pulse');
                allIndicators.forEach(indicator => {
                    indicator.classList.remove('animate-pulse');
                    setTimeout(() => {
                        indicator.classList.add('animate-pulse');
                    }, 10);
                });
                
                setTimeout(scrollToBottom, 100);
            });
        });
    </script>
</div> 