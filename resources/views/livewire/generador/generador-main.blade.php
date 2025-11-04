<div>
    <style>
       .type-selector-fixed {
                position: sticky;
                top: 0;
                background: white;
                z-index: 1;
                padding: 10px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 10px;
            }
            #generadores{
                z-index: 2;
            }
            
            /* Estilos para el grid de imágenes */
            .images-container {
                width: 100%;
            }
            
            .images-container .flex {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }
            
            .images-container .flex::-webkit-scrollbar {
                height: 6px;
            }
            
            .images-container .flex::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 3px;
            }
            
            .images-container .flex::-webkit-scrollbar-thumb {
                background-color: #cbd5e1;
                border-radius: 3px;
            }
            
            .image-card {
                position: relative;
                transition: transform 0.2s, box-shadow 0.2s;
                border-radius: 0.5rem;
                overflow: hidden;
            }
            
            .image-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            }

            .lightbox-overlay {
                backdrop-filter: blur(3px);
                animation: fadeIn 0.3s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            /* Responsive grid para diferentes tamaños de pantalla */
            @media (max-width: 768px) {
                            .image-card img,
            .image-card video {
                min-width: 150px !important;
                max-width: 250px !important;
            }
        }
        
        @media (min-width: 1024px) {
            .image-card img,
            .image-card video {
                min-width: 250px !important;
                max-width: 400px !important;
            }
        }

            /* Estilos para la sección de errores */
            .error-section {
                animation: slideDown 0.3s ease-out;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .error-item {
                transition: all 0.2s ease;
            }

            .error-item:hover {
                transform: translateX(2px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
            }
    </style>
<div class="w-full max-w-[1400px] mx-auto px-0 py-0 min-h-screen flex flex-col gap-4">
    <!-- ToolSelector -->
    <div class="type-selector-fixed">
    <section aria-label="Selector de herramientas" class="bg-white/70 dark:bg-zinc-900/70 backdrop-blur rounded-xl p-4">
        <div class="flex justify-center">
            <div class="flex bg-gray-100 rounded-full p-1 w-fit shadow-sm">
                {{-- 1. PROMPT GENERATOR (Primero) --}}
                @if(isset($tools['prompt-generator']))
                    @can('haveaccess','generador.prompt')
                    <button 
                        wire:click="setActiveTool('prompt-generator')"
                        type="button"
                        class="flex items-center justify-center rounded-full p-2 {{ $activeTool === 'prompt-generator' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                        title="{{ $tools['prompt-generator']['label'] }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tools['prompt-generator']['icon'] }}" />
                        </svg>
                    </button>
                    @endcan
                @endif

                {{-- 2. IMAGE GENERATOR --}}
                @if(isset($tools['image-generator']))
                    @can('haveaccess','generador.imagen')
                    <button 
                        wire:click="setActiveTool('image-generator')"
                        type="button"
                        class="flex items-center justify-center rounded-full p-2 {{ $activeTool === 'image-generator' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                        title="{{ $tools['image-generator']['label'] }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tools['image-generator']['icon'] }}" />
                        </svg>
                    </button>
                    @endcan
                @endif

                {{-- 3. IMAGE EDITOR --}}
                @if(isset($tools['image-editor']))
                    @can('haveaccess','edit.image')
                    <button 
                        wire:click="setActiveTool('image-editor')"
                        type="button"
                        class="flex items-center justify-center rounded-full p-2 {{ $activeTool === 'image-editor' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                        title="{{ $tools['image-editor']['label'] }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tools['image-editor']['icon'] }}" />
                        </svg>
                    </button>
                    @endcan
                @endif

                {{-- 4. VIDEO GENERATOR --}}
                @if(isset($tools['video-generator']))
                    @can('haveaccess','generador.video')
                    <button 
                        wire:click="setActiveTool('video-generator')"
                        type="button"
                        class="flex items-center justify-center rounded-full p-2 {{ $activeTool === 'video-generator' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                        title="{{ $tools['video-generator']['label'] }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tools['video-generator']['icon'] }}" />
                        </svg>
                    </button>
                    @endcan
                @endif

                {{-- 5. VIDEO EDITOR --}}
                @if(isset($tools['video-editor']))
                    @can('haveaccess','edit.video')
                    <button 
                        wire:click="setActiveTool('video-editor')"
                        type="button"
                        class="flex items-center justify-center rounded-full p-2 {{ $activeTool === 'video-editor' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                        title="{{ $tools['video-editor']['label'] }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tools['video-editor']['icon'] }}" />
                        </svg>
                    </button>
                    @endcan
                @endif

                {{-- 6. IMAGE EDITOR EXPAND (Comentado - No implementado) --}}
                {{-- @if(isset($tools['image-editor-expand']))
                    @can('haveaccess','edit.image')
                    <button 
                        wire:click="setActiveTool('image-editor-expand')"
                        type="button"
                        class="flex items-center justify-center rounded-full p-2 {{ $activeTool === 'image-editor-expand' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                        title="{{ $tools['image-editor-expand']['label'] }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tools['image-editor-expand']['icon'] }}" />
                        </svg>
                    </button>
                    @endcan
                @endif --}}

                {{-- 7. IMAGE EDITOR FILL (Comentado - No implementado) --}}
                {{-- @if(isset($tools['image-editor-fill']))
                    @can('haveaccess','edit.image')
                    <button 
                        wire:click="setActiveTool('image-editor-fill')"
                        type="button"
                        class="flex items-center justify-center rounded-full p-2 {{ $activeTool === 'image-editor-fill' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                        title="{{ $tools['image-editor-fill']['label'] }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tools['image-editor-fill']['icon'] }}" />
                        </svg>
                    </button>
                    @endcan
                @endif --}}
            </div>
        </div>
    </section>
     <!-- Sección de Errores -->
     @if(!empty($errors))
     <section aria-label="Errores recientes" class="error-section bg-gray-50 dark:bg-gray-800/20 backdrop-blur rounded-xl p-4 mb-4">
         <div class="flex items-center justify-between mb-3">
             <div class="flex items-center gap-2">
                 <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                 </svg>
                 <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Errores Recientes</h3>
             </div>
             <button wire:click="clearErrors" type="button" class="text-xs px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition-colors">
                 Limpiar Todo
             </button>
         </div>
 
         <div class="space-y-2">
             @foreach($errors as $error)
                 <div class="error-item bg-white dark:bg-gray-900/30 rounded-lg border border-gray-200 dark:border-gray-700 p-3 relative">
                     <div class="flex items-start justify-between gap-3">
                         <div class="flex-1 min-w-0">
                             <p class="text-sm text-gray-800 dark:text-gray-200 break-words">
                                 {{ $error['message'] }}
                             </p>
                             <div class="flex items-center gap-2 mt-1 text-xs text-gray-600 dark:text-gray-400">
                                 <span class="capitalize">{{ $error['tool'] ?? 'Sistema' }}</span>
                                 <span>•</span>
                                 <span>{{ \Carbon\Carbon::parse($error['date'])->format('H:i:s') }}</span>
                                 @if(isset($error['type']) && $error['type'] !== 'general')
                                     <span>•</span>
                                     <span class="capitalize">{{ $error['type'] }}</span>
                                 @endif
                             </div>
                         </div>
                         <button 
                             wire:click="dismissError('{{ $error['id'] }}')" 
                             type="button" 
                             class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                             title="Descartar error"
                         >
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                             </svg>
                         </button>
                     </div>
                 </div>
             @endforeach
         </div>
     </section>
     @endif
    </div>

   

 <!-- ResultsHistory -->
 <section id="historial"aria-label="Historial de resultados" class="flex-1 min-h-0 overflow-y-auto bg-white/70 dark:bg-zinc-900/70 backdrop-blur rounded-xl  px-4 p-4 pb-2">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Historial</h3>
        <div class="flex items-center gap-2">
            <button wire:click="clearHistory" type="button" class="text-xs px-3 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200">Limpiar</button>
        </div>
    </div>

    @if(empty($history))
        <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto text-zinc-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 002 2z" />
            </svg>
            <p class="text-sm text-zinc-500">Aún no hay resultados en el historial.</p>
            <p class="text-xs text-zinc-400 mt-1">Genera tu primera imagen para comenzar</p>
        </div>
    @else
        <div id="history-container" class="space-y-6">
            @foreach($history as $index => $item)
                <div id="history-item-{{ $index }}" class="bg-white rounded-xl border border-zinc-200 p-4 history-item">
                    @if(isset($item['images']) && is_array($item['images']))
                        {{-- Grupo de imágenes de una misma generación --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-0">
                                <h4 class="text-sm font-medium text-zinc-700 truncate">
                                    {{ $item['type'] === 'video/generate' ? 'Video generado' : 'Imagen generada' }}
                                </h4>
                                <span class="text-xs text-zinc-500">
                                    {{ \Carbon\Carbon::parse($item['date'])->format('H:i') }}
                                </span>
                            </div>
                            <div class="text-xs text-zinc-500 mb-3 flex gap-2">
                                <span>{{ $item['model'] ?? 'N/A' }}</span>
                                <span>•</span>
                                <span>{{ $item['ratio'] ?? 'N/A' }}</span>
                                <span>•</span>
                                <span>{{ count($item['images']) }} {{ $item['type'] === 'video/generate' ? 'video' : 'imagen' }}{{ count($item['images']) > 1 ? 'es' : '' }}</span>
                            </div>
                        </div>
                        
                        {{-- Grid de imágenes/videos de la misma generación --}}
                        <div class="images-container">
                            <div class="flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: thin;">
                                @foreach($item['images'] as $image)
                                    <div class="image-card flex-shrink-0 relative group">
                                        @if($item['type'] === 'video/generate')
                                            {{-- Renderizar como VIDEO --}}
                                            <video 
                                                src="{{ $image['url'] }}" 
                                                class="w-full h-auto max-h-[300px] object-contain rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                                style="min-width: 200px; max-width: 320px;"
                                                controls
                                                preload="metadata"
                                                @click="$dispatch('open-lightbox', { videoSrc: '{{ $image['url'] }}', type: 'video' })"
                                            >
                                                Tu navegador no soporta el elemento video.
                                            </video>
                                        @else
                                            {{-- Renderizar como IMAGEN --}}
                                            <img 
                                                src="{{ $image['url'] }}" 
                                                alt="Imagen generada" 
                                                class="w-full h-auto max-h-[300px] object-contain rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                                style="min-width: 200px; max-width: 320px;"
                                                @click="$dispatch('open-lightbox', { imgSrc: '{{ $image['url'] }}', type: 'image' })"
                                            />
                                        @endif
                                        
                                        <!-- Botones flotantes -->
                                        <div class="absolute bottom-2 right-2 flex gap-1">
                                            @if($item['type'] !== 'video/generate')
                                                @can('haveaccess','edit.image')
                                                <button 
                                                    @click.stop="editImageFromHistory('{{ $image['url'] }}', '{{ $item['generationId'] ?? 'unknown' }}', '{{ $item['model'] ?? '' }}', '{{ $item['ratio'] ?? '1:1' }}')"
                                                    class="bg-gray-600/80 hover:bg-gray-700 text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                                    title="Editar imagen"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span class="hidden sm:inline">Editar</span>
                                                </button>
                                                @endcan
                                                
                                                @can('haveaccess','generate.video')
                                                <button 
                                                    @click.stop="generateVideoFromHistory('{{ $image['url'] }}', '{{ $item['generationId'] ?? 'unknown' }}', '{{ $item['model'] ?? '' }}', '{{ $item['ratio'] ?? '1:1' }}')"
                                                    class="bg-blue-600/80 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                                    title="Generar video"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="hidden sm:inline">Video</span>
                                                </button>
                                                @endcan
                                            @else
                                                @can('haveaccess','edit.video')
                                                <button 
                                                    @click.stop="editVideoFromHistory('{{ $image['url'] }}', '{{ $item['generationId'] ?? 'unknown' }}', '{{ $item['model'] ?? '' }}', '{{ $item['ratio'] ?? '16:9' }}')"
                                                    class="bg-purple-600/80 hover:bg-purple-700 text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                                    title="Editar video"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span class="hidden sm:inline">Editar</span>
                                                </button>
                                                @endcan
                                            @endif
                                            <button 
                                                @click.stop="downloadFile('{{ $image['url'] }}', '{{ $item['type'] === 'video/generate' ? 'video' : 'image' }}')"
                                                class="bg-black/80 hover:bg-black text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                                title="Descargar"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="hidden sm:inline">Descargar</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($item['type'] === 'prompt/generate')
                        {{-- Prompt generado --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-0">
                                <h4 class="text-sm font-medium text-zinc-700 truncate">
                                    Prompt generado
                                </h4>
                                <span class="text-xs text-zinc-500">
                                    {{ \Carbon\Carbon::parse($item['date'])->format('H:i') }}
                                </span>
                            </div>
                            <div class="text-xs text-zinc-500 mb-3 flex gap-2">
                                <span>{{ $item['model'] ?? 'N/A' }}</span>
                                @if($item['documento'])
                                    <span>•</span>
                                    <span>Con documento Genesis</span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Contenido del prompt generado --}}
                        <div class="space-y-3">
                            {{-- Prompt original --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs text-gray-500 mb-1 font-medium">Prompt original:</div>
                                <div class="text-sm text-gray-700">{{ $item['prompt'] }}</div>
                            </div>
                            
                            {{-- Prompt generado --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <div class="text-xs text-gray-600 mb-1 font-medium">Prompt mejorado:</div>
                                <div class="text-sm text-gray-800 whitespace-pre-wrap">{{ $item['generatedPrompt'] }}</div>
                            </div>
                            
                            {{-- Botones de acción --}}
                            <div class="flex gap-2">
                                <button 
                                    @click="copyToClipboard('{{ addslashes($item['generatedPrompt']) }}')"
                                    class="bg-black hover:bg-gray-800 text-white px-3 py-1 rounded text-xs flex items-center gap-1"
                                    title="Copiar prompt mejorado"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Copiar</span>
                                </button>
                                
                                <button 
                                    @click.stop="loadPromptForImageGeneration('{{ addslashes($item['generatedPrompt']) }}')"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-xs flex items-center gap-1"
                                    title="Usar para generar imagen"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Usar para imagen</span>
                                </button>
                                
                                @can('haveaccess','generador.video')
                                <button 
                                    @click.stop="loadPromptForVideoGeneration('{{ addslashes($item['generatedPrompt']) }}')"
                                    class="bg-blue-600/80 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs flex items-center gap-1"
                                    title="Usar para generar video"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Usar para video</span>
                                </button>
                                @endcan
                            </div>
                        </div>
                    @elseif(isset($item['url']))
                        {{-- Imagen individual (compatibilidad hacia atrás) --}}
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-zinc-700">
                                {{ $item['type'] ?? 'Imagen generada' }}
                            </h4>
                            <span class="text-xs text-zinc-500">
                                {{ \Carbon\Carbon::parse($item['date'])->format('H:i') }}
                            </span>
                        </div>
                        <div class="max-w-md relative group">
                            <img 
                                src="{{ $item['url'] }}" 
                                alt="Imagen generada" 
                                class="w-full h-auto max-h-[300px] object-contain rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                @click="$dispatch('open-lightbox', { imgSrc: '{{ $item['url'] }}', type: 'image' })"
                            />
                            
                            <!-- Botones flotantes -->
                            <div class="absolute bottom-2 right-2 flex gap-1">
                                @if($item['type'] !== 'video/generate')
                                    @can('haveaccess','edit.image')
                                    <button 
                                        @click.stop="editImageFromHistory('{{ $item['url'] }}', '{{ $item['generationId'] ?? 'unknown' }}', '{{ $item['model'] ?? '' }}', '{{ $item['ratio'] ?? '1:1' }}')"
                                        class="bg-gray-600/80 hover:bg-gray-700 text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                        title="Editar imagen"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="hidden sm:inline">Editar</span>
                                    </button>
                                    @endcan
                                    
                                    @can('haveaccess','generate.video')
                                    <button 
                                        @click.stop="generateVideoFromHistory('{{ $item['url'] }}', '{{ $item['generationId'] ?? 'unknown' }}', '{{ $item['model'] ?? '' }}', '{{ $item['ratio'] ?? '1:1' }}')"
                                        class="bg-blue-600/80 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                        title="Generar video"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span class="hidden sm:inline">Video</span>
                                    </button>
                                    @endcan
                                @else
                                    @can('haveaccess','edit.video')
                                    <button 
                                        @click.stop="editVideoFromHistory('{{ $item['url'] }}', '{{ $item['generationId'] ?? 'unknown' }}', '{{ $item['model'] ?? '' }}', '{{ $item['ratio'] ?? '16:9' }}')"
                                        class="bg-purple-600/80 hover:bg-purple-700 text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                        title="Editar video"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="hidden sm:inline">Editar</span>
                                    </button>
                                    @endcan
                                @endif
                                <button 
                                    @click.stop="downloadFile('{{ $item['url'] }}', 'image')"
                                    class="bg-black/80 hover:bg-black text-white px-2 py-1 rounded text-xs backdrop-blur-sm flex items-center gap-1"
                                    title="Descargar"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Descargar</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
    
        <!-- Área de herramienta activa fija al fondo -->
<section id="generadores" aria-label="Herramienta activa" class="sticky bottom-0 bg-transparent left-0 right-0">
            @switch($activeTool)
                @case('image-generator')
                    <livewire:generador.herramientas.image-generator
            wire:key="image-generator-tool" />
                    @break

                @case('image-editor')
                    <livewire:generador.herramientas.image-editor
            wire:key="image-editor-tool" />
                    @break

                @case('video-generator')
                    <livewire:generador.herramientas.video-generator
            wire:key="video-generator-tool" />
                    @break

                @case('image-editor-expand')
                    <div class="text-sm text-zinc-600 dark:text-zinc-300">Editor de imágenes · Expand aún no conectado.</div>
                    @break

                @case('image-editor-fill')
                    <div class="text-sm text-zinc-600 dark:text-zinc-300">Editor de imágenes · Fill aún no conectado.</div>
                    @break

                @case('video-editor')
                    <livewire:generador.herramientas.video-editor
            wire:key="video-editor-tool" />
                    @break

                @case('prompt-generator')
                    <livewire:generador.herramientas.prompt-generator
            wire:key="prompt-generator-tool" />
                    @break
            @endswitch
</section>
</div>

   

   
<!-- Lightbox para Imágenes y Videos -->
<div 
id="mediaLightbox"
x-data="{ open: false, currentMedia: '', mediaType: 'image' }"
x-show="open"
x-cloak
@keydown.escape.window="open = false"
@open-lightbox.window="
    if ($event.detail.type === 'video') {
        currentMedia = $event.detail.videoSrc;
        mediaType = 'video';
    } else {
        currentMedia = $event.detail.imgSrc;
        mediaType = 'image';
    }
    open = true;
"
class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center lightbox-overlay"
style="display: none;"
>
<div class="relative max-w-4xl mx-auto p-4">
    <!-- Botón de cerrar -->
    <button 
        @click="open = false"
        class="absolute -top-4 -right-4 bg-black rounded-full p-2 text-white hover:bg-gray-800 z-10"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    
    <!-- Contenido multimedia -->
    <div class="max-h-[80vh] w-auto mx-auto">
        <template x-if="mediaType === 'video'">
            <video 
                :src="currentMedia" 
                class="max-h-[80vh] w-auto mx-auto object-contain rounded-lg"
                controls
                autoplay
                muted
            >
                Tu navegador no soporta el elemento video.
            </video>
        </template>
        
        <template x-if="mediaType === 'image'">
            <img 
                :src="currentMedia" 
                class="max-h-[80vh] w-auto mx-auto object-contain rounded-lg"
                alt="Imagen ampliada"
            >
        </template>
    </div>
    
    <!-- Botón de descarga -->
    <button 
        @click="downloadFile(currentMedia, mediaType)"
        class="mt-4 block w-full text-center bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-800 transition-colors"
    >
        Descargar <span x-text="mediaType === 'video' ? 'Video' : 'Imagen'"></span>
    </button>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    // Escuchar evento de scroll automático
    Livewire.on('scrollToLatest', () => {
    // Espera adicional para que Livewire termine de pintar todo
    setTimeout(() => {
        window.scrollTo({
            top: document.documentElement.scrollHeight,
            behavior: 'smooth'
        });
    }, 1500); 
});
});

// Función para descargar archivos (imágenes o videos) desde S3 usando fetch
async function downloadFile(fileUrl, fileType) {
    try {
        console.log(`🔽 Iniciando descarga de ${fileType}:`, fileUrl);
        
        // Mostrar indicador de descarga
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Descargando...';
        button.disabled = true;
        
        // Fetch del archivo desde S3
        const response = await fetch(fileUrl);
        if (!response.ok) {
            throw new Error(`Error al descargar el ${fileType}`);
        }
        
        // Convertir a blob
        const blob = await response.blob();
        
        // Crear URL temporal
        const blobUrl = window.URL.createObjectURL(blob);
        
        // Crear elemento <a> temporal para descarga
        const link = document.createElement('a');
        link.href = blobUrl;
        
        // Generar nombre de archivo basado en timestamp y tipo
        const timestamp = new Date().toISOString().slice(0, 19).replace(/[:-]/g, '');
        let extension = 'mp4'; // Por defecto para videos
        let prefix = 'video';
        
        if (fileType === 'image') {
            extension = fileUrl.includes('.png') ? 'png' : 'jpg';
            prefix = 'imagen';
        }
        
        link.download = `${prefix}_generado_${timestamp}.${extension}`;
        
        // Agregar al DOM temporalmente y hacer clic
        document.body.appendChild(link);
        link.click();
        
        // Limpiar
        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);
        
        console.log(`✅ ${fileType} descargado exitosamente`);
        
        // Restaurar botón
        button.textContent = originalText;
        button.disabled = false;
        
    } catch (error) {
        console.error(`❌ Error descargando ${fileType}:`, error);
        
        // Restaurar botón en caso de error
        const button = event.target;
        button.textContent = 'Error al descargar';
        button.disabled = false;
        
        // Restaurar texto después de 2 segundos
        setTimeout(() => {
            button.textContent = 'Descargar';
        }, 2000);
        
        // Fallback: intentar descarga directa
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = `${fileType}_${Date.now()}.${fileType === 'image' ? 'jpg' : 'mp4'}`;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Variable global para almacenar datos de imagen pendiente
window.pendingImageData = null;

// Variable global para almacenar datos de video pendiente
window.pendingVideoData = null;

// Variable global para almacenar datos de video pendiente para edición
window.pendingVideoEditData = null;

// Variable global para almacenar datos de prompt pendiente
window.pendingPromptData = null;

// Variable global para almacenar datos de prompt pendiente para video
window.pendingVideoPromptData = null;

// Función para editar imagen desde el historial
function editImageFromHistory(imageUrl, generationId, model, ratio) {
    console.log('🖼️ Editando imagen del historial:', {
        imageUrl,
        generationId,
        model,
        ratio
    });
    
    // Almacenar datos de imagen pendiente
    window.pendingImageData = {
        imageUrl: imageUrl,
        generationId: generationId,
        originalModel: model,
        originalRatio: ratio
    };
    
    // Cambiar a la herramienta de editor de imágenes
    Livewire.dispatch('toolChanged', { tool: 'image-editor' });
    
    // Intentar enviar inmediatamente (por si el componente ya está montado)
    setTimeout(() => {
        if (window.pendingImageData) {
            console.log('🔄 Intentando cargar imagen inmediatamente');
            Livewire.dispatch('loadImageFromHistory', window.pendingImageData);
        }
    }, 900);
}

// Función para generar video desde el historial
function generateVideoFromHistory(imageUrl, generationId, model, ratio) {
    console.log('🎬 Generando video desde imagen del historial:', {
        imageUrl,
        generationId,
        model,
        ratio
    });
    
    // Almacenar datos de video pendiente
    window.pendingVideoData = {
        imageUrl: imageUrl,
        generationId: generationId,
        originalModel: model,
        originalRatio: ratio
    };
    
    // Cambiar a la herramienta de generador de videos
    Livewire.dispatch('toolChanged', { tool: 'video-generator' });
    
    // Intentar enviar inmediatamente (por si el componente ya está montado)
    setTimeout(() => {
        if (window.pendingVideoData) {
            console.log('🔄 Intentando cargar imagen para video inmediatamente');
            Livewire.dispatch('loadImageForVideoFromHistory', window.pendingVideoData);
        }
    }, 900);
}

// Función para editar video desde el historial
function editVideoFromHistory(videoUrl, generationId, model, ratio) {
    console.log('🎬 Editando video del historial:', {
        videoUrl,
        generationId,
        model,
        ratio
    });
    
    // Almacenar datos de video pendiente para edición
    window.pendingVideoEditData = {
        videoUrl: videoUrl,
        generationId: generationId,
        originalModel: model,
        originalRatio: ratio
    };
    
    // Cambiar a la herramienta de editor de videos
    Livewire.dispatch('toolChanged', { tool: 'video-editor' });
    
    // Intentar enviar inmediatamente (por si el componente ya está montado)
    setTimeout(() => {
        if (window.pendingVideoEditData) {
            console.log('🔄 Intentando cargar video para edición inmediatamente');
            Livewire.dispatch('loadVideoFromHistory', [
                window.pendingVideoEditData.videoUrl,
                window.pendingVideoEditData.generationId,
                window.pendingVideoEditData.originalModel,
                window.pendingVideoEditData.originalRatio
            ]);
        }
    }, 900);
}

// Función para cargar prompt para generación de imagen
function loadPromptForImageGeneration(prompt) {
    console.log('🖼️ Cargando prompt para generación de imagen:', {
        prompt: prompt.substring(0, 50) + '...'
    });
    
    // Almacenar datos de prompt pendiente
    window.pendingPromptData = prompt;
    
    // Cambiar a la herramienta de generador de imágenes
    Livewire.dispatch('toolChanged', { tool: 'image-generator' });
    
    // Intentar enviar inmediatamente (por si el componente ya está montado)
    setTimeout(() => {
        if (window.pendingPromptData) {
            Livewire.dispatch('loadPromptForImageGeneration', [window.pendingPromptData]);
        }
    }, 900);
}

// Función para cargar prompt en el generador de video
function loadPromptForVideoGeneration(prompt) {
    // Guardar el prompt en una variable global para poder accederlo después
    window.pendingVideoPromptData = prompt;
    
    // Cambiar a la herramienta de generador de video
    Livewire.dispatch('toolChanged', { tool: 'video-generator' });
    
    // Intentar enviar inmediatamente (por si el componente ya está montado)
    setTimeout(() => {
        if (window.pendingVideoPromptData) {
            Livewire.dispatch('loadPromptForVideoGeneration', [window.pendingVideoPromptData]);
        }
    }, 900);
}

// Función para copiar texto al portapapeles
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Mostrar notificación temporal
        const button = event.target.closest('button');
        const originalText = button.textContent;
        button.textContent = '¡Copiado!';
        button.classList.add('bg-green-600');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-600');
        }, 2000);
    }).catch(err => {
        console.error('❌ Error copiando al portapapeles:', err);
        
        // Fallback: usar el método tradicional
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        const button = event.target.closest('button');
        const originalText = button.textContent;
        button.textContent = '¡Copiado!';
        button.classList.add('bg-green-600');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-600');
        }, 2000);
    });
}

// Listener para cuando el ImageGenerator esté listo
document.addEventListener('livewire:init', () => {
    Livewire.on('imageGeneratorReady', () => {
        // Si hay datos de prompt pendientes, enviarlos ahora
        if (window.pendingPromptData) {
            Livewire.dispatch('loadPromptForImageGeneration', [window.pendingPromptData]);
            
            // Limpiar datos pendientes
            window.pendingPromptData = null;
        }
    });
    
    Livewire.on('videoGeneratorReady', () => {
        // Si hay datos de prompt pendientes para video, enviarlos ahora
        if (window.pendingVideoPromptData) {
            Livewire.dispatch('loadPromptForVideoGeneration', [window.pendingVideoPromptData]);
            
            // Limpiar datos pendientes
            window.pendingVideoPromptData = null;
        }
    });
    
    Livewire.on('videoEditorReady', () => {
        // Si hay datos de video pendientes para edición, enviarlos ahora
        if (window.pendingVideoEditData) {
            Livewire.dispatch('loadVideoFromHistory', [
                window.pendingVideoEditData.videoUrl,
                window.pendingVideoEditData.generationId,
                window.pendingVideoEditData.originalModel,
                window.pendingVideoEditData.originalRatio
            ]);
            
            // Limpiar datos pendientes
            window.pendingVideoEditData = null;
        }
    });
});

</script>

</div>



