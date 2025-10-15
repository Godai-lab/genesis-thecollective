
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
                wire:model.live="promptText" 
                class="w-full outline-none resize-none text-sm min-h-[60px] text-gray-700 no-border" 
                placeholder="Describe una imagen para generar..."
                @keydown.enter.prevent="$event.shiftKey || $wire.generate()"
            ></textarea>

            <div class="flex items-center justify-between px-4 pb-3">
                <div class="flex flex-wrap items-center gap-2">
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
                            <div class="text-center mb-2 text-gray-600 font-medium">Calidad de imagen</div>
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
                    wire:click="generate"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors text-sm flex items-center"
                >
                    
                    Generar
                </button>
            </div>
        </div>
    </div>
    </div>

    
    <livewire:generador.components.generating-status
    :show="$isGenerating"
    message="Generando imagen..."
    :subtitle="'Espere por favor...'"
    {{-- wire:key="generating-status-{{ uniqid() }}"  --}}
    />
<!-- Selector de modelos flotante, reutilizable -->
   
<livewire:generador.components.model-selector
    :models="$availableModels"
    :selected="$model"
    :eventName="'image-generator-model-selected'"
    title="Modelo de IA"
    wire:key="model-selector-island" />
 
</div>




{{-- image-generator.blade.php --}}
@script
<script>
(function(){
    /* ---------- helpers (sin arrow, sin const/let en raíz) ---------- */
    var $ = function(id) { return document.getElementById(id); };
    var q = function(sel) { return document.querySelector(sel); };

    function toggleGenSpinner(show) {
        var s = $('generation-spinner');
        if (s) s.style.display = show ? 'flex' : 'none';
    }
    function toggleGenClass(add) {
        var box = q('.results-container');
        if (box) box.classList.toggle('generating', add);
    }

    /* ---------- listeners ---------- */
    Livewire.on('generationStarted', function() {
        console.log('🚀 Iniciando generación...');
        toggleGenSpinner(true);
        toggleGenClass(true);
    });

    Livewire.on('generationCompleted', function() {
        console.log('✅ Generación completada');
        setTimeout(function() {
            toggleGenSpinner(false);
            toggleGenClass(false);
        }, 500);
    });

    Livewire.on('generationError', function() {
        console.log('❌ Error en generación');
        toggleGenSpinner(false);
        toggleGenClass(false);
    });

    /* ---------- Flux polling ---------- */
    function startFluxPolling(data) {
        console.log('⏰ Iniciando polling Flux:', data.generationId);
        setTimeout(function() { checkFluxStatus(data); }, 10_000);
    }

    function checkFluxStatus(data) {
        console.log('🔍 Verificando Flux:', data.generationId);
        Livewire.dispatch('verificarEstadoFluxKontext', data);
    }

    Livewire.on('fluxTaskStarted', function(e) { startFluxPolling(e); });
    Livewire.on('fluxStillPending', function(e) {
        console.log('⏳ Flux aún pendiente');
        setTimeout(function() { checkFluxStatus(e); }, 10_000);
    });

    console.log('📜 ImageGenerator: listeners registrados (sin Alpine errors)');
})();
</script>
@endscript
</div>


