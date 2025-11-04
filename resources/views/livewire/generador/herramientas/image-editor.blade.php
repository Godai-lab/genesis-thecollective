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
        .image-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>

    <!-- Vista previa de imágenes (subidas o del historial) -->
    @if(!empty($imageFiles) || ($fromHistory && $imageUrl))
    <div class="w-full max-w-4xl px-0 mx-auto mb-3">
        <div class="bg-white rounded-lg border border-gray-200 p-3">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    @if($fromHistory)
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xs font-medium text-gray-700">Imagen del historial</h3>
                        @if(!empty($historyMetadata['originalModel']))
                            <span class="text-xs px-2 py-0.5 bg-gray-200 rounded-full text-gray-600">
                                {{ $historyMetadata['originalModel'] }}
                            </span>
                        @endif
                    @else
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <h3 class="text-xs font-medium text-gray-600">
                            {{ count($imageFiles) == 1 ? 'Imagen subida' : count($imageFiles) . ' imágenes subidas' }}
                        </h3>
                    @endif
                </div>
                <button 
                    wire:click="clearImage"
                    class="text-gray-600 hover:text-black text-xs flex items-center gap-1"
                    title="Limpiar imagen"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="hidden sm:inline">Limpiar</span>
                </button>
            </div>
            
            <!-- Grid de imágenes - Unificado -->
            <div class="flex flex-wrap gap-2">
                @if($fromHistory && $imageUrl)
                    <!-- Imagen del historial -->
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
                @else
                    <!-- Imágenes subidas -->
                    @foreach($imageFiles as $index => $image)
                        <div class="relative group">
                            <img 
                                src="{{ $this->getTemporaryUrl($image) }}" 
                                alt="Imagen {{ $index + 1 }}" 
                                class="image-preview border border-gray-200"
                            >
                            <!-- Botón para eliminar imagen individual -->
                            <button 
                                wire:click="quitarImagen({{ $index }})"
                                class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Eliminar esta imagen"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                @endif
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

    <!-- Caja de herramienta principal -->
    <div class="w-full max-w-4xl px-0 mx-auto">
    <div class="input-container bg-black rounded-xl p-2 shadow-lg">
        <div class="relative bg-white rounded-lg">
            <textarea 
                wire:model.defer="promptText" 
                class="w-full outline-none resize-none text-sm min-h-[60px] text-gray-700 no-border" 
                placeholder="Describe cómo quieres editar la imagen..."
                @keydown.enter.prevent="$event.shiftKey || $wire.editImage()"
            ></textarea>

            <div class="flex items-center justify-between px-4 pb-3">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Botón de subir imagen -->
                    <div class="relative">
                        <label for="image-upload-btn" class="{{ count($imageFiles) >= 4 ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                            <div class="flex items-center space-x-1 bg-{{ count($imageFiles) >= 4 ? 'gray-300 text-gray-500' : (!empty($imageFiles) ? 'black hover:bg-black text-white' : 'gray-100 hover:bg-gray-200 text-gray-700') }} rounded-full px-3 py-1 text-sm shadow-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ !empty($imageFiles) ? count($imageFiles) . ' imagen' . (count($imageFiles) > 1 ? 'es' : '') . ' cargada' . (count($imageFiles) > 1 ? 's' : '') . (count($imageFiles) < 4 ? '' : '') : 'Subir imagen' }}</span>
                            </div>
                        </label>
                        <input 
                            id="image-upload-btn" 
                            type="file" 
                            wire:model.live="temporaryImages" 
                            accept="image/*" 
                            class="hidden"
                            multiple
                            max="4"
                            {{ count($imageFiles) >= 4 ? 'disabled' : '' }}
                        >
                    </div>

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
                            <div class="text-center mb-2 text-gray-600 font-medium">Relación de aspecto</div>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($availableRatios as $value => $label)
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
                    </div>

                    <!-- Cantidad dropdown - Solo para modelos que soportan múltiples imágenes -->
                    @if($this->supportsMultipleImages)
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>{{ $count }} imagen{{ $count > 1 ? 'es' : '' }}</span>
                        </button>
                        <div 
                            x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                        >
                            <div class="text-center mb-2 text-gray-600 font-medium">Cantidad de imágenes</div>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach([1,2,3,4] as $n)
                                    <button 
                                        wire:click="$set('count', {{ $n }})"
                                        @click="open = false"
                                        class="bg-{{ $count === $n ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                    >
                                        <span>{{ $n }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Calidad dropdown - Solo para modelos OpenAI -->
                    @if($model === 'gpt-image-1')
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $calidadesDisponibles[$calidadImagen] }}</span>
                        </button>
                        <div 
                            x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                        >
                            <div class="text-center mb-2 text-gray-600 font-medium">Calidad de edición</div>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($calidadesDisponibles as $value => $label)
                                    <button 
                                        wire:click="$set('calidadImagen', '{{ $value }}')"
                                        @click="open = false"
                                        class="bg-{{ $calidadImagen === $value ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                    >
                                        <span>{{ $label }}</span>
                                        @if($value === 'auto')
                                            <span class="text-xs text-gray-500">Por defecto</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <button 
                    wire:click="editImage"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors text-sm flex items-center"
                    
                >
                    @if($isProcessing)
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Editando...
                    @else
                       
                        Editar Imagen
                    @endif
                </button>
            </div>
        </div>
    </div>
    </div>

    <!-- Estado de procesamiento -->
    <livewire:generador.components.generating-status
        :show="$isProcessing"
        message="Editando imagen..."
        :subtitle="'Aplicando cambios con IA...'"
    />

    <!-- Selector de modelos flotante -->
    <livewire:generador.components.model-selector
        :models="$availableModels"
        :selected="$model"
        :eventName="'image-generator-model-selected'"
        title="Modelo de IA para Edición"
        wire:key="image-editor-model-selector" />
 
</div>

@script
<script>
(function(){
    /* ---------- helpers ---------- */
    var $ = function(id) { return document.getElementById(id); };
    var q = function(sel) { return document.querySelector(sel); };

    function toggleEditingSpinner(show) {
        var spinner = $('editing-spinner');
        if (spinner) spinner.style.display = show ? 'flex' : 'none';
    }

    function toggleEditingClass(add) {
        var box = q('.results-container');
        if (box) box.classList.toggle('editing', add);
    }

    /* ---------- listeners ---------- */
    Livewire.on('editingStarted', function() {
        console.log('🚀 Iniciando edición de imagen...');
        toggleEditingSpinner(true);
        toggleEditingClass(true);
    });

    Livewire.on('editingCompleted', function() {
        console.log('✅ Edición completada');
        setTimeout(function() {
            toggleEditingSpinner(false);
            toggleEditingClass(false);
        }, 500);
    });

    Livewire.on('editingError', function() {
        console.log('❌ Error en edición');
        toggleEditingSpinner(false);
        toggleEditingClass(false);
    });

    Livewire.on('startImageEditing', function(data) {
        console.log('🔄 Ejecutando edición con datos:', data);
    });

    /* ---------- Flux polling ---------- */
    function startFluxPolling(data) {
        console.log('⏰ Iniciando polling Flux edición:', data.generationId);
        setTimeout(function() { checkFluxStatus(data); }, 10_000);
    }

    function checkFluxStatus(data) {
        console.log('🔍 Verificando Flux edición:', data.generationId);
        Livewire.dispatch('verificarEstadoFluxKontext', data);
    }

    Livewire.on('fluxTaskStarted', function(e) { startFluxPolling(e); });
    Livewire.on('fluxStillPending', function(e) {
        console.log('⏳ Flux edición aún pendiente');
        setTimeout(function() { checkFluxStatus(e); }, 10_000);
    });

    /* ---------- utilidades ---------- */
    Livewire.on('imageUploaded', function(e) {
        console.log('📷 Imagen cargada:', e.url);
        if (!window.pendingImageData) {
            setTimeout(function() {
                var el = q('[class*="bg-gradient-to-r"], .bg-white');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    });

    Livewire.on('imageCleared', function() {
        console.log('🗑️ Imagen eliminada');
    });

    /* ---------- imagen pendiente ---------- */
    if (window.pendingImageData) {
        console.log('🔄 Procesando imagen pendiente:', window.pendingImageData);
        Livewire.dispatch('loadImageFromHistory', window.pendingImageData);
        window.pendingImageData = null;
    }

    console.log('📜 ImageEditor: listeners registrados');
})();
</script>
@endscript
</div>
