<div>
    <style>
/* Ajustes responsive como en new-generador */
@media (max-width: 768px) {
  .model-selector-container {
    bottom: auto !important;
    top: 70px !important;
    left: 6px !important;
    z-index: 40 !important;
  }
  .model-dropdown-direction {
    bottom: auto !important;
    top: 100% !important;
    margin-bottom: 0 !important;
    margin-top: 4px !important;
  }
}

/* Estilos para tooltip - VERSIÓN CORREGIDA */
.tooltip {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    z-index: 60;
    background-color: #000000;
    color: white;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #404040;
    font-size: 14px;
    line-height: 1.4;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    transition: opacity 0.3s, visibility 0.3s;
    width: 280px;
    /* CAMBIO: Posicionar hacia la derecha */
    left: 100%;
    top: -150px;
    margin-left: 8px;
}

.tooltip::after {
    content: "";
    position: absolute;
    top: 15px;
    /* CAMBIO: Flecha hacia la izquierda */
    right: 100%;
    border: 6px solid transparent;
    border-right-color: #000000;
}

.model-item:hover .tooltip {
    visibility: visible;
    opacity: 1;
}

/* Para mostrar tooltip en móviles con Alpine.js */
.tooltip.show {
    visibility: visible !important;
    opacity: 1 !important;
}

/* Responsive: En móviles, mostrar arriba */
@media (max-width: 768px) {
  .tooltip {
    left: 50%;
    top: auto;
    bottom: 100%;
    margin-left: -140px; /* Centrar (width/2) */
    margin-bottom: 8px;
    transform: none;
  }
  .tooltip::after {
    right: auto;
    left: 50%;
    top: 100%;
    bottom: auto;
    margin-left: -6px;
    border-right-color: transparent;
    border-top-color: #000000;
  }
}

/* Si hay poco espacio a la derecha, mostrar arriba en desktop también */
@media (min-width: 769px) {
  .model-selector-container.near-right .tooltip {
    left: 50%;
    top: auto;
    bottom: 100%;
    margin-left: -140px;
    margin-bottom: 8px;
  }
  .model-selector-container.near-right .tooltip::after {
    right: auto;
    left: 50%;
    top: 100%;
    bottom: auto;
    margin-left: -6px;
    border-right-color: transparent;
    border-top-color: #1f2937;
  }
}
</style>

<div class="fixed bottom-6 left-6 z-30 model-selector-container">
    <div class="flex flex-col space-y-2 bg-white rounded-xl p-3 shadow-lg border border-gray-200">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center justify-between gap-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-800 shadow-sm min-w-[150px]">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>{{ $this->getModelName($selected) }}</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-cloak @click.away="open = false" class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[320px] z-50 shadow-lg model-dropdown-direction">
                <div class="text-center mb-3 text-gray-600 font-medium">{{ $title }}</div>
                <div class="grid grid-cols-1 gap-2">
                    @foreach($models as $key => $modelInfo)
                        <div class="model-item relative" x-data="{ showTooltip: false }">
                            <div class="flex items-center">
                                <button wire:click="cambiarModelo('{{ $key }}')" @click="open = false" class="flex-1 flex items-center justify-between px-3 py-2 rounded-lg {{ $selected === $key ? 'bg-black text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">{{ $this->getModelName($key) }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        @if($selected === $key)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </div>
                                </button>
                                @if($this->hasDetailedInfo($key))
                                    <!-- Botón de información separado para móviles -->
                                    <button 
                                        @click="showTooltip = !showTooltip" 
                                        class="ml-1 p-2 rounded-lg text-black hover:text-gray-600 hover:bg-gray-50 md:hidden"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    <!-- Icono de información para desktop (solo hover) -->
                                    <div class="hidden md:block ml-1 p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            @if($this->hasDetailedInfo($key))
                                <!-- Tooltip con información detallada -->
                                <div 
                                    class="tooltip"
                                    :class="showTooltip ? 'show' : ''"
                                    @click.away="showTooltip = false"
                                >
                                    <div class="space-y-2">
                                        <div class="border-b border-gray-500 pb-2">
                                            <h4 class="font-semibold text-white">{{ $modelInfo['name'] }}</h4>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-white font-medium">{{ $modelInfo['price'] }} {{ $modelInfo['priceUnit'] }}</span>
                                                <span class="text-xs px-2 py-1 bg-gray-700 text-white rounded-full">{{ $modelInfo['speed'] }}</span>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <p class="text-gray-300 text-sm mb-2">{{ $modelInfo['description'] }}</p>
                                            
                                            <div class="mb-2">
                                                <span class="text-xs font-medium text-gray-400">Mejor para:</span>
                                                <p class="text-xs text-gray-300">{{ $modelInfo['bestFor'] }}</p>
                                            </div>

                                            <div class="mb-2">
                                                <span class="text-xs font-medium text-gray-400">Calidad:</span>
                                                <span class="text-xs text-gray-300">{{ $modelInfo['quality'] }}</span>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

</div>
