<div style="position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;">
    <!-- Botón flotante -->
    <button
        wire:click="toggleChat"
        class="bg-white p-2 shadow-lg rounded-full border hover:scale-105 transition-transform"
    >
        <x-application-logo class="block h-6 w-auto fill-current text-white" />
    </button>

   
    @if ($abierto)
    <div
    x-data="{ show: false, showDocumentos: false }"
    x-init="setTimeout(() => {
        show = true;
        setTimeout(() => {
            const chatScroll = document.getElementById('chatScroll');
            if (chatScroll) {
                chatScroll.scrollTop = chatScroll.scrollHeight;
            }
        }, 100);
    }, 10)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
    style="width: 400px;"
>

        <div class="mt-4 shadow-xl rounded-xl overflow-hidden bg-white border border-gray-200 flex flex-col h-[500px]">
            <!-- Encabezado -->
            <div class="bg-purple-100 text-black font-semibold px-4 py-3 flex justify-between items-center">
                <div class="flex items-center">
                    Godai
                    {{-- @if($documentoInfo)
                        <span class="ml-2 text-xs bg-purple-200 py-1 px-2 rounded-full flex items-center">
                            {{ $documentoInfo['tipo'] }}
                            <button 
                                wire:click="quitarDocumento" 
                                class="ml-1 text-gray-500 hover:text-red-500"
                                title="Quitar documento"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif --}}
                </div>
                <div class="flex items-center">
                    <button wire:click="toggleChat" class="hover:text-purple-700 text-xl">&times;</button>
                </div>
            </div>
            
            <!-- Selector de documentos (aparece/desaparece) -->
            <div x-show="showDocumentos" x-transition class="bg-gray-50 border-b p-3">
                <div class="mb-2">
                    <label for="documentoSelect" class="block text-sm font-medium text-gray-700 mb-1">
                        Seleccionar documento para consultar:
                    </label>
                    <select 
                        wire:model.live="documentoSeleccionado" 
                        wire:change="seleccionarDocumento"
                        id="documentoSelect" 
                        class="w-full rounded-md border-gray-300 text-sm py-2"
                    >
                        <option value="">Seleccione un documento</option>
                        @foreach($documentos as $doc)
                            <option value="{{ $doc['id'] }}">{{ $doc['texto'] }} ({{ $doc['fecha'] }})</option>
                        @endforeach
                    </select>
                </div>
                
                @if($documentoInfo)
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-700">Documento seleccionado:</div>
                    <div class="bg-purple-100 text-sm py-1 px-3 rounded-full flex items-center">
                        {{ $documentoInfo['tipo'] }}
                        <button 
                            wire:click="quitarDocumento"
                            class="ml-2 text-gray-500 hover:text-red-500" 
                            title="Quitar documento"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Mensajes -->
            <div 
            id="chatScroll"
            class="flex-1 overflow-y-auto px-4 py-2 space-y-2"
            style="max-height: 380px;"
            wire:ignore.self
            >
                @foreach ($mensajes as $msg)
                    <div class="text-sm {{ $msg['tipo'] === 'usuario' ? 'text-right' : 'text-left' }}">
                        <div class="inline-block px-3 py-2 rounded-lg
                            {{ $msg['tipo'] === 'usuario' ? 'bg-black text-white' : ($msg['tipo'] === 'sistema' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-800') }}">
                            {{ $msg['texto'] }}
                        </div>
                    </div>
                @endforeach

                <!-- Indicador de escritura -->
                @if ($isTyping)
                    <div class="text-sm text-left">
                        <div class="inline-block px-3 py-2 rounded-lg bg-gray-100">
                            <div class="flex items-center">
                                <div class="typing-indicator">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <span class="ml-2 text-gray-600">Godai está escribiendo...</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            <!-- Entrada -->
            <div class="flex items-center px-4 py-3 border-t bg-white gap-2">
                <input
                    wire:model="mensaje"
                    type="text"
                    placeholder="{{ $documentoInfo ? 'Pregunta sobre este documento...' : 'Escribe tu mensaje...' }}"
                    class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-300"
                    wire:keydown.enter="enviarMensaje"
                />
                <button 
                        @click="showDocumentos = !showDocumentos" 
                        class="mr-3 text-gray-600 hover:text-purple-700"
                        title="Seleccionar documento"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </button>
                <button
                    wire:click="enviarMensaje"
                    class="text-white p-2 rounded-full shadow-md transition-all duration-200 flex items-center justify-center"
                    title="Enviar mensaje"
                >
                    <img class="w-[30px] h-auto max-w-[30px] block" src="{{ asset('images/god-ai-icon-right.png') }}" alt="">
                </button>
                
                
            </div>
        </div>
    </div>
    @endif

    <!-- Estilos -->
    <style>
        .typing-indicator {
            display: inline-flex;
            align-items: center;
        }
        
        .typing-indicator span {
            height: 8px;
            width: 8px;
            background: #606060;
            border-radius: 50%;
            display: inline-block;
            margin: 0 1px;
            animation: bounce 1.3s linear infinite;
        }
        
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.15s;
        }
        
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.3s;
        }
        
        @keyframes bounce {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-4px);
            }
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            function scrollToBottom() {
                const chatScroll = document.getElementById('chatScroll');
                if (chatScroll) {
                    chatScroll.scrollTop = chatScroll.scrollHeight;
                }
            }

            Livewire.on('toggleChat', () => {
                setTimeout(scrollToBottom, 100);
            });
            
            Livewire.hook('message.processed', (message, component) => {
                scrollToBottom();
            });
            
            Livewire.on('mensajeEnviado', () => {
                setTimeout(scrollToBottom, 50);
            });
            
            Livewire.on('respuestaRecibida', scrollToBottom);
            
            const chatContainer = document.getElementById('chatScroll');
            if (chatContainer) {
                const observer = new MutationObserver(() => {
                    scrollToBottom();
                });
                
                observer.observe(chatContainer, {
                    childList: true,
                    subtree: true
                });
            }
        });
    </script>
    
</div>
