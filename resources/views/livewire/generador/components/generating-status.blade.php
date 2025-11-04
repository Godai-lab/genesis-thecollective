<div>
    @if($show)
        <div 
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            wire:transition.opacity
        >
            <div class="bg-white rounded-2xl shadow-2xl p-8 mx-4 max-w-sm w-full text-center">
                <!-- Icono animado -->
                <div class="mb-6">
                    @if($icon)
                        <div class="w-16 h-16 mx-auto text-gray-700 animate-pulse">
                            {!! $icon !!}
                        </div>
                    @else
                        <!-- Icono por defecto: spinner elegante -->
                        <div class="relative w-16 h-16 mx-auto">
                            <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-black border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    @endif
                </div>

                <!-- Mensaje principal -->
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ $message }}
                </h3>

                <!-- Subtítulo opcional -->
                @if($subtitle)
                    <p class="text-sm text-gray-600 mb-4">
                        {{ $subtitle }}
                    </p>
                @endif

                <!-- Puntos animados -->
                <div class="flex justify-center space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
        </div>
    @endif
</div>