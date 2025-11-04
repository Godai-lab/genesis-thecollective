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
    </style>
    <!-- Caja de herramienta  -->
    <div class="w-full max-w-4xl px-0 mx-auto">
    <div class="input-container bg-black rounded-xl p-2 shadow-lg ">
        <div class="relative bg-white rounded-lg">
            <textarea 
                wire:model.defer="promptText" 
                class="w-full outline-none resize-none text-sm min-h-[60px] text-gray-700 no-border" 
                placeholder="Escribe una instrucción o selecciona un documento Genesis..."
                @keydown.enter.prevent="$event.shiftKey || $wire.generate()"
            ></textarea>

            <div class="flex items-center justify-between px-4 pb-3">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Selector de documentos Genesis -->
                    @if(!empty($documentos))
                    <div class="bg-gray-50 rounded-lg p-3">
                        <select 
                            wire:model.live="documentoSeleccionado" 
                            wire:change="seleccionarDocumentoGenesis"
                            class="w-full rounded-md border-gray-300 text-sm py-2"
                        >
                            <option value="">Seleccione un Genesis</option>
                            @foreach($documentos as $doc)
                                <option value="{{ $doc['id'] }}">{{ $doc['texto'] }} ({{ $doc['fecha'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <!-- Botón para limpiar historial -->
                    @if(!empty($chatHistory))
                    <button 
                        wire:click="limpiarHistorial"
                        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-3 py-1 text-sm shadow-sm"
                        title="Limpiar historial de chat"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Limpiar</span>
                    </button>
                    @endif
                </div>

                <button 
                    wire:click="generate"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors text-sm flex items-center"
                >
                    Generar Prompt
                </button>
            </div>
        </div>
    </div>
    </div>

    
    <livewire:generador.components.generating-status
    :show="$isGenerating"
    message="Generando prompt..."
    :subtitle="'Mejorando tu idea con IA...'"
    />
 
</div>

<script>
document.addEventListener('livewire:init', () => {
    // Escuchar evento de inicio de generación
    Livewire.on('generationStarted', () => {
        console.log('🚀 Iniciando generación de prompt...');
    });
    
    // Escuchar evento de generación completada
    Livewire.on('generationCompleted', () => {
        console.log('✅ Generación de prompt completada');
        
        // Scroll automático al final del chat
        setTimeout(() => {
            const chatContainer = document.querySelector('.chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }, 100);
    });
    
    // Escuchar evento de error
    Livewire.on('generationError', () => {
        console.log('❌ Error en generación de prompt');
    });
    
    // Escuchar evento de actualización de historial
    Livewire.on('historialActualizado', () => {
        console.log('📝 Historial actualizado');
        
        // Scroll automático al final del chat
        setTimeout(() => {
            const chatContainer = document.querySelector('.chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }, 100);
    });
});
</script>
</div>
