<div>
    
        <style>
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
                transition: transform 0.2s ease;
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
                opacity: 0;
                transition: opacity 0.2s ease;
            }
            .image-preview:hover .remove-image {
                opacity: 1;
            }
            .generating-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 50;
            }
            .generating-card {
                background-color: white;
                border-radius: 12px;
                padding: 2rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                text-align: center;
                max-width: 90%;
                width: 400px;
            }
            .chat-layout {
                display: flex;
                flex-direction: column;
                height: calc(100vh - 100px);
                max-height: 800px;
                overflow: hidden;
            }
            .chat-container {
                flex: 1;
                overflow-y: auto;
                padding: 1rem;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }
            .input-container {
                /* max-height: 10px; */
                position: sticky;
                bottom: 0;
                background-color: rgb(255, 255, 255);
                /* padding: 1rem; */
                /* border-top: 1px solid #e2e8f0; */
                z-index: 10;
            }
            .chat-container::-webkit-scrollbar {
                width: 6px;
            }
            .chat-container::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            .chat-container::-webkit-scrollbar-thumb {
                background-color: #cbd5e1;
                border-radius: 3px;
            }
            .chat-message {
                margin-bottom: 1rem;
                max-width: 80%;
            }
            .user-message {
                background-color: #000000;
                color: #ffffff;
                border-radius: 0.75rem;
                margin: 0.5rem 1rem 0.5rem auto;
                max-width: 80%;
                width: fit-content;
            }
            .system-message {
                background-color: #ffffff;
                border-radius: 0.75rem;
                margin: 0.5rem auto 0.5rem 1rem;
                max-width: 80%;
                width: fit-content;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .message-time {
                font-size: 0.75rem;
                color: #94a3b8;
                text-align: right;
                margin-top: 0.25rem;
            }
            .message-images-container {
                display: flex;
                width: 150px;
                flex-wrap: wrap;
                margin-top: 0.5rem;
                gap: 0.5rem;
            }
            .message-image {
                max-width: 150px;
                max-height: 150px;
                border-radius: 8px;
                object-fit: cover;
            }
            .result-image {
                margin-top: 0.5rem;
                max-width: 100%;
                max-height: 400px;
                border-radius: 8px;
                object-fit: contain;
            }
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
            
            textarea.no-border {
                border: none !important;
                box-shadow: none !important;
                background-color: transparent !important;
                outline: none !important;
                padding: 12px !important;
            }
            
            .input-wrapper {
                background-color: black;
                border-radius: 0.75rem;
                padding: 0.75rem;
            }
            
            .input-inner {
                background-color: white;
                border-radius: 0.75rem;
                box-shadow: none;
            }
            
            /* Estilos adicionales para la nueva estructura de galería */
            .gallery-layout {
                display: flex;
                flex-direction: column;
                height: calc(100vh - 100px);
                max-height: 800px;
                overflow: hidden;
            }
            
            .gallery-container {
                flex: 1;
                overflow-y: auto;
                padding: 1rem;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }
            
            .message-item {
                display: flex;
                flex-direction: column;
                margin-bottom: 2rem;
                position: relative;
            }
            
            .user-prompt {
                background-color: #000000;
                color: #ffffff;
                border-radius: 0.75rem;
                padding: 0.75rem 1rem;
                margin-left: auto;
                margin-right: 0;
                max-width: 80%;
                margin-bottom: 1rem;
            }
            
            .generated-content {
                display: flex;
                justify-content: flex-start;
                flex-wrap: wrap;
                margin-top: 1rem;
                gap: 1rem;
            }
            
            .generated-image {
                max-width: 100%;
                height: auto;
                max-height: 300px;
                object-fit: contain;
                border-radius: 0.5rem;
            }
            
            .single-image {
                max-width: 600px;
            }
            
            .multi-images .generated-image {
                max-width: calc(50% - 0.5rem);
            }
            
            .message-timestamp {
                font-size: 0.75rem;
                color: #94a3b8;
                margin-left: auto;
                margin-right: 0;
                margin-top: 0.25rem;
            }
            
            /* Estilos adicionales para mejorar la visualización de imágenes en fila */
            .gallery-container {
                scroll-behavior: smooth;
            }
            
            .message-item {
                margin-bottom: 1.5rem;
            }
            
            .message-item:last-child {
                margin-bottom: 0.5rem;
            }
            
            /* Estilos para el contenedor de imágenes en fila */
            .images-row {
                display: flex;
                overflow-x: auto;
                gap: 8px;
                padding-bottom: 10px;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }
            
            .images-row::-webkit-scrollbar {
                height: 6px;
            }
            
            .images-row::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            
            .images-row::-webkit-scrollbar-thumb {
                background-color: #cbd5e1;
                border-radius: 3px;
            }
            
            /* Estilos para el contenedor de prompt */
            .prompt-container {
                background-color: #000;
                color: white;
                padding: 12px 16px;
                border-radius: 12px;
                max-width: 250px;
                font-size: 0.9rem;
                line-height: 1.4;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            /* Estilos para las imágenes en la fila */
            .gallery-image-wrapper {
                position: relative;
                min-width: 150px;
                max-width: 220px;
                transition: transform 0.2s;
            }
            
            .gallery-image-wrapper:hover {
                transform: translateY(-2px);
            }
            
            .gallery-image {
                height: 180px;
                object-fit: cover;
                border-radius: 8px;
                box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            }
            
            .download-button {
                position: absolute;
                bottom: 8px;
                right: 8px;
                background-color: rgba(0,0,0,0.7);
                color: white;
                border-radius: 6px;
                padding: 4px 8px;
                font-size: 0.7rem;
                display: flex;
                align-items: center;
                transition: background-color 0.2s;
            }
            
            .download-button:hover {
                background-color: #000;
            }
            
            /* Estilos para el contenedor de instrucción actual */
            .instruction-container {
                background-color: #f8f9fa;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .instruction-text {
                font-weight: 500;
                color: #1a202c;
            }
            
            /* Mejoras para la visualización de imágenes */
            .images-container {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 0.75rem;
                padding: 0.5rem 0;
                width: 100%;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }
            
            .image-card {
                position: relative;
                min-width: 200px;
                flex: 1;
                max-width: 320px;
                transition: transform 0.2s, box-shadow 0.2s;
                border-radius: 0.5rem;
                overflow: hidden;
            }
            
            .image-card img {
                width: 100%;
                height: auto;
                object-fit: contain;
                max-height: 70vh;
                background-color: #f3f4f6;
            }
            
            /* Estilos adicionales para mejorar la visualización */
            .flex.justify-center.w-full.my-4 {
                width: 100%;
                padding: 0;
            }
            
            /* Para imágenes únicas */
            .relative.max-w-md {
                width: 100%;
                max-width: 100%;
            }
            
            .relative.max-w-md img {
                width: 100%;
                height: auto;
                max-height: 80vh;
                object-fit: contain;
            }
            
            /* Asegurarse que el contenedor de galería use todo el ancho disponible */
            .gallery-container {
                width: 100%;
                padding: 0.5rem;
                background-color: white;
                border-radius: 0.5rem;
            }
            
            /* Ocultar todos los elementos de instrucción */
            .instruction-block, 
            .message-timestamp {
                display: none;
            }

            /* Estilos para el selector de modelos */
            @media (max-width: 768px) {
                .model-selector-container {
                    bottom: auto !important;
                    top: 70px !important; /* Ajustado para estar debajo del selector de tipo */
                    left: 6px !important;
                    z-index: 40 !important;
                }
                /* Para que el dropdown se abra hacia abajo en móviles */
                .model-dropdown-direction {
                    bottom: auto !important;
                    top: full !important;
                    margin-bottom: 0 !important;
                    margin-top: 1px !important;
                }
            }

            /* Estilos para el selector de tipo fijo */
            .type-selector-fixed {
                position: sticky;
                top: 0;
                background: white;
               z-index: 30;
                padding: 10px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 20px;
            }
            
            /* Añadir padding-top al contenedor principal para compensar el espacio del selector fijo */
            .main-content-container {
                padding-top: 10px;
            }

            /* Añadir un estilo específico para los mensajes de error */
            .error-message {
                display: flex !important; /* Cambiar a flex para permitir el layout correcto */
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

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            /* Animación para la transición entre imágenes */
            @keyframes fadeInOut {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }
            
            .transition-preview {
                animation: fadeInOut 2s infinite;
            }

            .image-preview-runway {
                position: relative;
                display: inline-block;
                margin-top: 4px;
            }
            
            .remove-image-runway {
                position: absolute;
                top: -6px;
                right: -6px;
                background: #ef4444;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                cursor: pointer;
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            }
            
            /* Espacio adicional debajo de los botones para las miniaturas */
            .runway-buttons-container {
                margin-bottom: 20px;
            }
            
            /* Asegurar que el spinner sea visible con buen contraste */
            .animate-spin {
                display: inline-block;
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }

            /* Ajustes para el contenedor de videos */
            .generated-content {
                max-width: 600px; /* Reducir el ancho máximo del contenedor */
                margin: 0 auto; /* Centrar el contenedor */
            }

            video {
                max-height: 400px !important; /* Ajustar altura máxima del video */
                width: 100% !important;
                max-width: 600px !important; /* Ajustar ancho máximo del video */
                margin: 0.5rem auto !important; /* Centrar y reducir márgenes */
            }

            /* Ajustar el contenedor del video */
            .w-full.my-4.flex.justify-center {
                margin: 0.5rem 0 !important; /* Reducir márgenes verticales */
            }

            .relative[style*="width: 100%"] {
                max-width: 500px !important; /* Ajustar ancho máximo del contenedor relativo */
                margin: 0 auto !important; /* Centrar el contenedor */
            }
            /* Añadir un estilo específico para los mensajes de error */
            
/* Estilos para los handles de redimensionamiento */
.resize-handle {
    position: absolute;
    background-color: #3b82f6;
    border: 2px solid white;
    border-radius: 4px;
    cursor: pointer;
    opacity: 0.8;
    transition: all 0.2s ease;
    z-index: 10;
}

.resize-handle:hover {
    opacity: 1;
    background-color: #2563eb;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
}

.resize-handle.dragging {
    opacity: 1;
    background-color: #1d4ed8;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.6);
}

/* Handles específicos para cada lado - CORREGIDO */
.handle-top, .handle-bottom {
    width: 60px;
    height: 8px;
    cursor: ns-resize;
    left: 50%;
    transform: translateX(-50%);
}

.handle-top:hover, .handle-bottom:hover {
    transform: translateX(-50%) scale(1.1);
}

.handle-left, .handle-right {
    width: 8px;
    height: 60px;
    cursor: ew-resize;
    top: 50%;
    transform: translateY(-50%);
}

.handle-left:hover, .handle-right:hover {
    transform: translateY(-50%) scale(1.1);
}

.handle-top {
    top: -4px;
}

.handle-bottom {
    bottom: -4px;
}

.handle-left {
    left: -4px;
}

.handle-right {
    right: -4px;
}

/* Indicador visual durante el arrastre */
.overlay-resizing {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Cursor personalizado durante el redimensionamiento */
.resizing-cursor {
    cursor: grabbing !important;
}

    /* Estilos para sliders con paleta blanco/negro/gris */
    .slider-thumb::-webkit-slider-thumb {
        appearance: none;
        height: 18px;
        width: 18px;
        background: #000000;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
    }
    
    .slider-thumb::-webkit-slider-thumb:hover {
        background: #374151;
        transform: scale(1.1);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
    }
    
    .slider-thumb::-moz-range-thumb {
        height: 18px;
        width: 18px;
        background: #000000;
        border-radius: 50%;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
    }
    
    .slider-thumb::-moz-range-thumb:hover {
        background: #374151;
        transform: scale(1.1);
    }
    
    .slider-thumb::-webkit-slider-track {
        background: #e5e7eb;
        height: 8px;
        border-radius: 4px;
    }
    
    .slider-thumb::-moz-range-track {
        background: #e5e7eb;
        height: 8px;
        border-radius: 4px;
        border: none;
    }

    
        </style>
        <div class="bg-white text-gray-800 min-h-screen">
            
                      
            <!-- Selector de tipo (imagen/video) - ahora fijo -->
            {{-- <div class="type-selector {{ !empty($chatHistory) ? 'type-selector-fixed' : '' }}"> --}}
            <div class="type-selector-fixed">

                <div class="flex justify-center bg-gray-100 rounded-full p-1 w-fit mx-auto shadow-sm">
                     @can('haveaccess','generador.imagen')
                    <button 
                        wire:click="cambiarTipo('gprompt')"
                        class="flex items-center justify-center rounded-full p-2 {{ $tipo === 'gprompt' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>

                    </button>
                    @endcan
                    @can('haveaccess','generador.imagen')
                    <button 
                        wire:click="cambiarTipo('imagen')"
                        class="flex items-center justify-center rounded-full p-2 {{ $tipo === 'imagen' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </button>
                    @endcan
                     @can('haveaccess','edit.image')
                    <button 
                        wire:click="cambiarTipo('editimagen')"
                        class="flex items-center justify-center rounded-full p-2 {{ $tipo === 'editimagen' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 16H9v-2.828zM5 20h14a2 2 0 002-2V7a2 2 0 00-2-2h-7l-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                        </svg>

                    </button>
       
                    @endcan
                    @can('haveaccess','generador.video')
                    <button 
                        wire:click="cambiarTipo('video')"
                        class="flex items-center justify-center rounded-full p-2 {{ $tipo === 'video' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                    @endcan
                    @can('haveaccess','generador.video')
                    <button 
                        wire:click="cambiarTipo('editvideo')"
                        class="flex items-center justify-center rounded-full p-2 {{ $tipo === 'editvideo' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </button>
                    @endcan
                </div>
                             @if($tipo === 'editimagen')
        <div class="flex justify-center mt-3">
            <div class="flex bg-gray-100 rounded-full p-1 w-fit shadow-sm">
                @can('haveaccess','edit.expand.image')
                <button 
                    wire:click="cambiarModoEdicion('expand')"
                    class="flex items-center justify-center rounded-full p-2 {{ $modoEdicion === 'expand' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                    <span class="text-sm">Expandir</span>
                </button>
                @endcan
                @can('haveaccess','edit.fill.image')
                <button 
                    wire:click="cambiarModoEdicion('fill')"
                    class="flex items-center justify-center rounded-full p-2 {{ $modoEdicion === 'fill' ? 'bg-black text-white' : 'text-gray-500' }} mx-1 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19c-1-5 3-9 7-10 3-1 6-4 7-6" />
  <circle cx="5" cy="19" r="1" />
</svg>




                    <span class="text-sm">Rellenar</span>
                </button>
                @endcan
            </div>
        </div>
    @endif
            </div>
            
            <!-- Contenido principal con clase para compensar el espacio del selector fijo -->
            <div class="w-full max-w-4xl px-4 mx-auto main-content-container">
                <!-- Indicador de herramienta actual - ahora en una sección separada y centrada -->
                @if(empty($chatHistory))
                <div class="flex justify-center my-4 mb-12 mt-14">
                    <div class="bg-gray-100 bg-opacity-20 hover:bg-opacity-100 inline-flex items-center px-4 py-2 rounded-lg shadow-sm min-w-[200px]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mr-2 text-black-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        @switch($tipo)
            @case('imagen')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                @break

            @case('video')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                @break

            @case('gprompt')
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" /> <!-- Ejemplo de icono para prompt -->
                @break
            @case('editvideo')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                @break
                

            @default
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 16H9v-2.828zM5 20h14a2 2 0 002-2V7a2 2 0 00-2-2h-7l-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
        @endswitch
    </svg>
    <span class="font-semibold text-xl text-gray-800">
       
        @switch($tipo)
            @case('imagen')
              Generador de Imágenes
                @break

            @case('video')
               Generador de Videos
                @break

            @case('gprompt')
               Generador de Prompts
                @break
            @case('editimagen')
               Editor de imagenes
                @break
            @case('editvideo')
               Editor de Videos
                @break

            @default
               Generador de contenido
        @endswitch
    

    </span>
</div>
                </div>
                @endif
                

<!--Incio Área de expandir imagen -->  
<div @wheel.prevent="onWheelZoom($event)" x-data="expandImage()" class="max-w-xl mx-auto mt-8" x-show="$wire.tipo ==='editimagen' && $wire.modoEdicion==='expand'">
     
    <!-- Drag & Drop -->
    <div 
        x-show="!imageSrc"
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="handleDrop($event)"
        :class="{'bg-blue-50 border-blue-400': dragOver, 'bg-gray-100 border-gray-300': !dragOver}"
        class="border-2 border-dashed rounded-lg p-8 text-center transition-colors duration-200 cursor-pointer"
        @click="$refs.fileInput.click()"
    >
        <input type="file" x-ref="fileInput" wire:model="temporaryImages" class="hidden" @change="handleFile($event)" accept="image/*">
        <span class="text-gray-500">Arrastra una imagen aquí o haz click para seleccionar</span>
    </div>

    <!-- Previsualización y controles -->
    <div x-show="imageSrc" class="mt-4">
        <!-- Canvas de imagen con overlay y handles -->
        <div class="relative bg-gray-50 border rounded-lg flex justify-center items-center" 
        style="min-height: 400px;"
        :style="`transform: scale(${zoom}); transition: transform 0.2s;`"
        :class="{ 'resizing-cursor': isResizing }"
        >
            <div 
                class="relative"
                x-show="imageSrc"
                :style="`width: ${overlayWidth}px; height: ${overlayHeight}px; background: repeating-linear-gradient(45deg, #e5e7eb 0 10px, #f3f4f6 10px 20px);`"
                :class="{ 'overlay-resizing': isResizing }"
                >
                <!-- Overlay: cubre el área a expandir, deja hueco para la imagen -->
                <div 
                    x-show="imageLoaded"
                    class="absolute inset-0 pointer-events-none"
                    :style="maskStyle"
                    style="z-index: 1;"
                ></div>
                
                <!-- Imagen original centrada, encima del overlay -->
                <img 
                    :src="imageSrc" 
                    x-ref="img"
                    :style="`position: absolute; left: ${imgOffsetX}px; top: ${imgOffsetY}px; width: ${imgDisplayWidth}px; height: ${imgDisplayHeight}px; object-fit: contain; z-index: 2;`"
                    class="block"
                >
                
                <!-- Handles de redimensionamiento -->
                <template x-if="imageLoaded && !isResizing">
                    <div>
                        <!-- Handle superior -->
                        <div 
                            class="resize-handle handle-top"
                            @mousedown="startResize($event, 'top')"
                            title="Redimensionar altura"
                        ></div>
                        
                        <!-- Handle inferior -->
                        <div 
                            class="resize-handle handle-bottom"
                            @mousedown="startResize($event, 'bottom')"
                            title="Redimensionar altura"
                        ></div>
                        
                        <!-- Handle izquierdo -->
                        <div 
                            class="resize-handle handle-left"
                            @mousedown="startResize($event, 'left')"
                            title="Redimensionar ancho"
                        ></div>
                        
                        <!-- Handle derecho -->
                        <div 
                            class="resize-handle handle-right"
                            @mousedown="startResize($event, 'right')"
                            title="Redimensionar ancho"
                        ></div>
                    </div>
                </template>
                
                <!-- Botón de eliminar imagen -->
                <button 
                    @click="reset()"
                    class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors flex items-center justify-center z-10"
                    title="Quitar imagen"
                    style="box-shadow: 0 2px 8px rgba(0,0,0,0.15);"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
      
 
<!-- Mensaje de error específico para expansión -->
@if(session()->has('expand_error') || !empty($expandError))
    <div class="error-message items-start mb-4 p-3 rounded-lg bg-red-50 border border-red-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="text-red-700 flex-1">
            <p class="font-medium">Error en la expansión</p>
            <p class="text-sm">{{ session('expand_error') ?? $expandError }}</p>
        </div>
        <button 
            wire:click="clearExpandError" 
            class="ml-auto text-red-400 hover:text-red-600 flex-shrink-0"
            title="Cerrar error"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif
         </div>

<!-- Sección de imágenes expandidas -->
<div class="expanded-images-section mb-8 pb-8" x-show="$wire.tipo ==='editimagen' && $wire.modoEdicion==='expand'">
    <h3 class="text-lg font-semibold mb-4" x-show="imageSrc">Imágenes Expandidas</h3>
    <div x-data="{
        scrollLeft() { this.$refs.carrusel.scrollBy({ left: -180, behavior: 'smooth' }); },
        scrollRight() { this.$refs.carrusel.scrollBy({ left: 180, behavior: 'smooth' }); }
    }" class="relative my-4" style="min-height: 130px;">
        <!-- Flecha izquierda -->
        <button @click="scrollLeft"
            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-gray-200 hover:bg-gray-300 shadow"
            style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <svg class="w-6 h-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <!-- Carrusel -->
        <div class="flex overflow-x-auto gap-4 py-2" style="scrollbar-width: thin; margin: 0 40px;" x-ref="carrusel">
            @php
                $reversedImages = array_reverse($expandedImages);
            @endphp
            @foreach($reversedImages as $revIndex => $image)
                @php
                    $realIndex = count($expandedImages) - 1 - $revIndex;
                @endphp
                <div class="relative min-w-[120px] max-w-[160px] h-32 mb-12 flex items-center justify-center bg-gray-100 rounded-sm">
                    
                    <!-- Badge para imagen nueva -->
                    @if($revIndex === 0)
                       <div class="rounded-md absolute -top-2 -left-2 z-20 bg-black text-white text-xs font-bold px-2 py-1 shadow-lg">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Nuevo
                            </div>
                        </div>
                    @endif
                    
                    <img 
                        src="{{ $image }}" 
                        alt="Imagen expandida {{ $realIndex + 1 }}" 
                        class="w-full h-28 object-contain cursor-pointer border-2 border-transparent hover:border-blue-500 transition bg-gray-100"
                        {{-- class="w-full h-28 object-contain rounded-lg cursor-pointer border-2 border-transparent hover:border-blue-500 transition bg-gray-100" --}}
                        style="background: #f3f4f6; object-fit: contain;"
                        @click="$dispatch('open-lightbox', { imgSrc: '{{ $image }}', images: @js($reversedImages), index: {{ $revIndex }} })"
                    >
                    <button wire:click="quitarImagenExpandida({{ $realIndex }})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
        <!-- Flecha derecha -->
        <button @click="scrollRight"
            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-gray-200 hover:bg-gray-300 shadow"
            style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>
          <!-- Controles fijos en la parte inferior -->
<div 
    class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-2xl z-50 bg-white shadow-2xl border-t border-gray-200 px-4 py-3"
>
<div class="flex gap-2 w-full md:w-auto">
<!-- Select de aspecto -->
        <div x-data="{ open: false }" class="relative min-w-[100px] mb-2">
            <button 
                @click="open = !open"
                class="w-full flex items-center justify-between bg-gray-100 hover:bg-gray-200 rounded-lg px-3 py-2 text-sm shadow-sm"
            >
                <span x-text="{
                    'landscape': 'Landscape',
                    'portrait': 'Portrait',
                    'square': 'Square',
                    'custom': 'Custom'
                }[aspect]"></span>
                {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg> --}}
            </button>

            <!-- Menú de opciones -->
            <div 
                x-show="open" 
                x-cloak
                @click.away="open = false"
                class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-xl p-3 w-[220px] z-20 shadow-lg"
            >
                <div class="text-center mb-2 text-gray-600 font-medium">Selecciona el aspecto</div>
                <div class="grid grid-cols-1 gap-2">
                    <template x-for="option in [
                        { key: 'landscape', label: 'Landscape', icon: 'M4 6h16M4 12h16M4 18h16' },
                        { key: 'portrait', label: 'Portrait', icon: 'M12 4v16m8-8H4' },
                        { key: 'square', label: 'Square', icon: 'M4 6h16M4 12h16M4 18h16' },
                        { key: 'custom', label: 'Custom', icon: 'M10.325 4.317c...Z' }
                    ]" :key="option.key">
                        <button
                             @click="setAspect(option.key); open = false"
                            :class="aspect === option.key ? 'bg-black text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800'"
                            class="rounded flex items-center gap-2 px-3 py-2 text-sm w-full"
                        >
                            {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path :d="option.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg> --}}
                            <span x-text="option.label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
     
<!-- Dropdown de tamaño -->
<div class="relative min-w-[100px]">
                <button 
                    @click="sizeDropdownOpen = !sizeDropdownOpen"
                    class="flex items-center bg-gray-100 hover:bg-gray-200 rounded-lg px-3 py-2 text-sm shadow-sm w-full"
                >
                    <span x-text="overlayRealWidth + ' × ' + overlayRealHeight"></span>
                </button>
                
                <!-- Menú de tamaño -->
                <div 
                    x-show="sizeDropdownOpen" 
                    x-cloak
                    @click.away="sizeDropdownOpen = false"
                    class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-xl p-4 w-[260px] z-20 shadow-lg"
                >
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm mb-1">Width</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="range" 
                                :min="imgWidth || 256" 
                                max="2048" 
                                step="1" 
                                :value="overlayRealWidth"
                                @input="updateCustomSize('width', parseInt($event.target.value))"
                                class="w-full"
                            >
                            <span class="w-12 text-right text-xs" x-text="overlayRealWidth"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm mb-1">Height</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="range" 
                                :min="imgHeight || 256"
                                max="2048" 
                                step="1" 
                                :value="overlayRealHeight"
                                @input="updateCustomSize('height', parseInt($event.target.value))"
                                class="w-full"
                            >
                            <span class="w-12 text-right text-xs" x-text="overlayRealHeight"></span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 text-center">
                        <span x-show="aspect === 'custom'" class="text-blue-600 font-medium">Modo personalizado</span>
                        <span x-show="aspect !== 'custom'" x-text="'Modo: ' + aspect"></span>
                    </div>
                </div>
            </div>
        

</div>
    <div class="flex flex-wrap md:flex-nowrap items-stretch gap-2 justify-center">
        <!-- Input de prompt -->
        <input 
            type="text" 
            wire:model="promptExpansion" 
            placeholder="Describe cómo quieres expandir la imagen..." 
            class="flex-1 min-w-[180px] px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
        >
        <!-- Botón expandir -->
        <button 
            x-data="{ expandiendo: false }"
            @click="
                expandiendo = true; 
                $wire.expandirImagenFlux({
                    imgWidth: imgWidth,
                    imgHeight: imgHeight,
                    overlayRealWidth: overlayRealWidth,
                    overlayRealHeight: overlayRealHeight
                })
            "
            @expansion-completed.window="expandiendo = false"
            @expansion-error.window="expandiendo = false"
            class="flex items-center justify-center px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors min-w-[100px]"
            x-show="imageLoaded"
            :disabled="expandiendo"
        >
            <div 
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" 
                x-show="expandiendo" 
                style="display: none;"
            ></div>
            <span x-show="!expandiendo">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 16V8M12 8l-4 4M12 8l4 4"  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span x-show="expandiendo" x-text="'Expandiendo...'"></span>
        </button>
    </div>

    <!-- Dimensiones -->
    {{-- <div class="mt-2 flex flex-col sm:flex-row justify-between text-sm text-gray-500 text-center gap-1 sm:gap-0">
        <div>
            Original: <span x-text="imgWidth"></span> x <span x-text="imgHeight"></span> px
        </div>
        <div>
            Expandido: <span x-text="overlayRealWidth"></span> x <span x-text="overlayRealHeight"></span> px
        </div>
    </div> --}}
</div>

  

</div>
<!--Fin Área de expandir imagen -->

<!--Inicio Área de rellenar imagen -->  
<div x-data="fillImage()" class="max-w-xl mx-auto mt-8" x-show="$wire.tipo ==='editimagen' && $wire.modoEdicion==='fill'">
     
    <!-- Drag & Drop -->
    <div 
        x-show="!imageSrc"
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="handleDrop($event)"
        :class="{'bg-blue-50 border-blue-400': dragOver, 'bg-gray-100 border-gray-300': !dragOver}"
        class="border-2 border-dashed rounded-lg p-8 text-center transition-colors duration-200 cursor-pointer"
        @click="$refs.fileInputFill.click()"
    >
        <input type="file" x-ref="fileInputFill"  class="hidden" @change="handleFileFill($event)" accept="image/*">
        <span class="text-gray-500">Arrastra una imagen aquí o haz click para seleccionar</span>
    </div>

    <!-- Editor de Canvas -->
    <div x-show="imageSrc" class="mt-4">
        <!-- Controles del pincel -->
        
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-800">Herramientas de Pincel</h4>
                
                <!-- Botones en la misma fila -->
                <div class="flex items-center gap-3">
                    <!-- Botón vista previa -->
                    {{-- <button 
                        @click="togglePreview()" 
                        :class="showPreview ? 'bg-black text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    >
                        <span x-text="showPreview ? 'Ocultar Vista Previa' : 'Vista Previa'"></span>
                    </button> --}}
                    
                    <!-- Botón limpiar - en la misma fila -->
                    <button 
                        @click="clearMask()" 
                        class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-black transition-colors duration-200 shadow-sm"
                        x-show="paintedPixels.size > 0"
                    >
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Limpiar
                        </div>
                    </button>
                </div>
            </div>
            
            <!-- Controles de tamaño y opacidad -->
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Tamaño:</label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="range" 
                            x-model="brushSize" 
                            min="5" 
                            max="50" 
                            class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb"
                        >
                        <span class="text-sm font-medium text-gray-600 min-w-[35px]" x-text="brushSize + 'px'"></span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Opacidad:</label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="range" 
                            x-model="brushOpacity" 
                            min="0.1" 
                            max="1" 
                            step="0.1"
                            class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb"
                        >
                        <span class="text-sm font-medium text-gray-600 min-w-[35px]" x-text="Math.round(brushOpacity * 100) + '%'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas de imagen con overlay para pincel -->
        <div class="relative bg-gray-100 border-2 border-gray-200 rounded-lg overflow-hidden shadow-sm">
          
            <canvas 
                x-ref="canvas"
                class="border-0 rounded cursor-crosshair block mx-auto bg-white"
                @mousedown="startDrawing($event)"
                @mousemove="draw($event)"  
                @mouseup="stopDrawing()"
                @mouseleave="stopDrawing()"
                style="max-width: 100%; height: auto;"
            ></canvas>
            
            <!-- Botón de eliminar imagen -->
            <button 
                @click="reset()"
                class="absolute top-3 right-3 bg-gray-800 text-white p-2 rounded-full hover:bg-black transition-colors duration-200 flex items-center justify-center z-10 shadow-lg"
                title="Quitar imagen"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        <!-- Mensaje de error específico para fill -->
        @if(session()->has('fill_error') || !empty($fillError))
            <div class="error-message items-start mb-4 p-3 rounded-lg bg-red-50 border border-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-red-700 flex-1">
                    <p class="font-medium">Error en el rellenado</p>
                    <p class="text-sm">{{ session('fill_error') ?? $fillError }}</p>
                </div>
                <button 
                    wire:click="clearFillError" 
                    class="ml-auto text-red-400 hover:text-red-600 flex-shrink-0"
                    title="Cerrar error"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Controles fijos en la parte inferior -->
        <div class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-2xl z-50 bg-white shadow-2xl border-t border-gray-200 px-4 py-3">
            <div class="flex flex-wrap md:flex-nowrap items-stretch gap-2 justify-center">
                <!-- Input de prompt -->
                <input 
                    type="text" 
                    wire:model="promptFill" 
                    placeholder="Describe qué quieres rellenar en las áreas marcadas..." 
                    class="flex-1 min-w-[180px] px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
                >
                <!-- Botón rellenar -->
                               <!-- Botón rellenar -->
                <button 
                    x-data="{ rellenando: false }"
                    @click="
                        if (processFill()) {
                            rellenando = true;
                        }
                    "
                    @fill-completed.window="rellenando = false"
                    @fill-error.window="rellenando = false"
                    class="flex items-center justify-center px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors min-w-[100px]"
                    x-show="imageLoaded"
                    :disabled="rellenando"
                >
                    <div 
                        class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" 
                        x-show="rellenando" 
                        style="display: none;"
                    ></div>
                    <span x-show="!rellenando">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </span>
                    <span x-show="rellenando" x-text="'Rellenando...'"></span>
                    <span x-show="!rellenando" x-text="'Rellenar'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sección de imágenes rellenadas -->
<div class="filled-images-section mb-8 pb-8" x-show="$wire.tipo ==='editimagen' && $wire.modoEdicion==='fill'">
    <h3 class="text-lg font-semibold mb-4" x-show="$wire.filledImages.length > 0">Imágenes Rellenadas</h3>
    <div x-data="{
        scrollLeft() { this.$refs.carruselFill.scrollBy({ left: -180, behavior: 'smooth' }); },
        scrollRight() { this.$refs.carruselFill.scrollBy({ left: 180, behavior: 'smooth' }); }
    }" class="relative my-4" style="min-height: 130px;">
        <!-- Flecha izquierda -->
        <button @click="scrollLeft"
            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-gray-200 hover:bg-gray-300 shadow"
            x-show="$wire.filledImages.length > 0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <!-- Carrusel -->
        <div class="flex overflow-x-auto gap-4 py-2" style="scrollbar-width: thin; margin: 0 40px;" x-ref="carruselFill" x-show="$wire.filledImages.length > 0">
            @php
                $reversedFilledImages = array_reverse($filledImages);
            @endphp
            @foreach($reversedFilledImages as $revIndex => $image)
                @php
                    $realIndex = count($filledImages) - 1 - $revIndex;
                @endphp
                <div class="relative min-w-[120px] max-w-[160px] h-32 mb-12 flex items-center justify-center bg-gray-100 rounded-sm">
                    
                    <!-- Badge para imagen nueva -->
                    @if($revIndex === 0)
                       <div class="rounded-md absolute -top-2 -left-2 z-20 bg-black text-white text-xs font-bold px-2 py-1 shadow-lg">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Nuevo
                            </div>
                        </div>
                    @endif
                    
                    <img 
                        src="{{ $image }}" 
                        alt="Imagen rellenada {{ $realIndex + 1 }}" 
                        class="w-full h-28 object-contain cursor-pointer border-2 border-transparent hover:border-blue-500 transition bg-gray-100"
                        style="background: #f3f4f6; object-fit: contain;"
                        @click="$dispatch('open-lightbox', { imgSrc: '{{ $image }}', images: @js($reversedFilledImages), index: {{ $revIndex }} })"
                    >
                    <button wire:click="quitarImagenRellenada({{ $realIndex }})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
        <!-- Flecha derecha -->
        <button @click="scrollRight"
            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-gray-200 hover:bg-gray-300 shadow"
            x-show="$wire.filledImages.length > 0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>
<!--Fin Área de rellenar imagen -->

<!--Incio Área de editor de videos -->  
<div x-show="$wire.tipo === 'editvideo'">
    @livewire('video-editor')
</div>
<!--Fin Área de editor de videos -->
                @if(empty($chatHistory))
                @if(session()->has('error'))
                        {{-- <div class="error-message flex items-center my-4 sticky top-[70px] z-20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-medium">Error en la generación</p>
                                <p>{{ session('error') }}</p>
                            </div>
                        </div> --}}
                        <div class="error-message flex items-center my-4 p-4 bg-red-50 border border-red-200 text-red-700 sticky top-[70px] z-20 shadow-md rounded-lg overflow-hidden relative">
                        <div class="flex items-start space-x-3 w-full pr-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-red-800 mb-0.5">Error en la generación</p>
                                <p class="text-red-700">{{ session('error') }}</p>
                            </div>
                            </div>
                             <button onclick="this.parentElement.remove()" class="absolute top-3 right-3 text-red-400 hover:text-red-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />Add commentMore actions
                            </svg>
                        </button>
                        </div>
                    @endif
                    <div class="mb-4" x-show="$wire.modoEdicion !== 'expand' && $wire.modoEdicion !== 'fill' && $wire.tipo !== 'editvideo' ">

                        <div  id="cajaherramienta" class="input-container bg-black rounded-xl p-4 shadow-lg" x-show="$wire.modoEdicion !== 'expand' && $wire.tipo !== 'editvideo'">
                            
                            <div class="relative bg-white rounded-lg">
                                <textarea 
                                    wire:model="prompt" 
                                    class="w-full outline-none resize-none text-lg min-h-[80px] text-gray-700 no-border" 
                                    {{-- placeholder="{{ $tipo === 'imagen' ? 'Describe una imagen para generar...' : 'Describe una secuencia de video para generar...' }}" --}}
                                     @switch($tipo)
                                        @case('imagen')
                                            placeholder="Describe una imagen para generar..."
                                            @break

                                        @case('video')
                                            placeholder="Describe una secuencia de video para generar..."
                                            @break

                                        @case('gprompt')
                                            placeholder="Describe un prompt para generar..."
                                            @break

                                        @case('editvideo')
                                            placeholder="Editor de videos..."
                                            @break

                                        @default
                                            placeholder="Describe el contenido para generar..."
                                    @endswitch
                                    @keydown.enter.prevent="$event.shiftKey || $wire.generar()"
                                ></textarea>
                                <!--vista previa de imagen seleccionada-->
                                @if(!empty($imageFiles))
                                    <div class="mb-4">
                                        <div class="flex flex-wrap">
                                            @foreach($imageFiles as $index => $image)
                                                <div class="image-preview">
                                                    rr
                                                    <img src="{{ $image->temporaryUrl() }}" alt="Imagen de referencia">
                                                    <div class="remove-image" wire:click="quitarImagen({{ $index }})">×</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif(!empty($imagenBaseParaVideo))
                                    <!-- Vista previa de la imagen seleccionada para video -->
                                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="text-sm font-medium text-gray-700 mb-2">Imagen base para video:</div>
                                        <div class="flex flex-wrap">
                                            <div style="position: relative; width: 120px; height: 120px; margin: 0.5rem;">
                                                <img src="{{ $imagenBaseParaVideo }}" alt="Imagen base para video" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem; border: 1px solid #d1d5db;">
                                                <div style="position: absolute; top: -8px; right: -8px; background-color: #ef4444; color: white; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" wire:click="quitarImagenBaseVideo()">×</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                 <!-- Incio Contenido de selectores chat vacio-->
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Selector de tamaño (solo visible cuando OpenAI está seleccionado) -->
                                    <div x-data="{ open: false }" class="relative" x-show="$wire.servicioImagen === 'openai' && $wire.tipo === 'imagen'">
                                        <button 
                                            @click="open = !open"
                                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                            <span>{{ $tamanoOpenAI }}</span>
                                        </button>
                                        
                                        <!-- Menú desplegable de tamaño para OpenAI -->
                                        <div 
                                            x-show="open" 
                                            x-cloak
                                            @click.away="open = false"
                                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                        >
                                            <div class="text-center mb-2 text-gray-600 font-medium">Tamaño de imagen</div>
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach($tamanosOpenAI as $tamano => $descripcion)
                                                    <button 
                                                        wire:click="seleccionarTamanoOpenAI('{{ $tamano }}')"
                                                        @click="open = false"
                                                        class="bg-{{ $tamanoOpenAI === $tamano ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                                    >
                                                        <span>{{ $tamano }}</span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $descripcion }}
                                                        </span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                <!-- Selector de relación de aspecto para videos -->
<div x-data="{ open: false }" class="relative" x-show="$wire.tipo === 'video'">
    <button 
        @click="open = !open"
        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
        </svg>
        <span>{{ $ratioVideo }}</span>
    </button>
    
    <!-- Menú desplegable de ratio -->
    <div 
        x-show="open" 
        x-cloak
        @click.away="open = false"
        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
    >
        <div class="text-center mb-2 text-gray-600 font-medium">Relación de aspecto</div>
        <div class="grid grid-cols-1 gap-2">
            @foreach($ratiosVideoDisponibles[$servicioImagen] ?? [] as $ratioOption => $label)
                <button 
                    wire:click="seleccionarRatioVideo('{{ $ratioOption }}')"
                    @click="open = false"
                    class="bg-{{ $ratioVideo === $ratioOption ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                >
                    <span>{{ $ratioOption }}</span>
                    <span class="text-xs text-gray-500">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>
<!-- fin Selector de relación de aspecto para videos -->
                                    <!-- Selector de relación de aspecto (no mostrar cuando OpenAI está seleccionado) -->
                                    <div x-data="{ open: false }" class="relative" x-show="($wire.tipo === 'imagen' || $wire.tipo === 'editimagen') && $wire.servicioImagen !== 'openai'">
                                        <button 
                                            @click="open = !open"
                                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                            <span>{{ $ratio }}</span>
                                        </button>
                                        
                                        <!-- Menú desplegable de ratio -->
                                        <div 
                                            x-show="open" 
                                            x-cloak
                                            @click.away="open = false"
                                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                        >
                                            <div class="text-center mb-2 text-gray-600 font-medium">Relación de aspecto</div>
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach(['1:1', '4:3', '3:4', '16:9', '9:16'] as $ratioOption)
                                                    <button 
                                                        wire:click="seleccionarRatio('{{ $ratioOption }}')"
                                                        @click="open = false"
                                                        class="bg-{{ $ratio === $ratioOption ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                                    >
                                                        <span>{{ $ratioOption }}</span>
                                                        <span class="text-xs text-gray-500">
                                                            @if($ratioOption == '1:1')
                                                                Cuadrado
                                                            @elseif($ratioOption == '4:3')
                                                                Horizontal
                                                            @elseif($ratioOption == '3:4')
                                                                Vertical
                                                            @elseif($ratioOption == '16:9')
                                                                Panorámico
                                                            @elseif($ratioOption == '9:16')
                                                                Vertical móvil
                                                            @endif
                                                        </span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Selector de cantidad de imágenes (mostrar para Gemini3, Gemini4 y OpenAI) -->
                                    <div x-data="{ open: false }" class="relative" x-show="($wire.tipo === 'imagen') && ($wire.servicioImagen === 'gemini' || $wire.servicioImagen === 'gemini4' || $wire.servicioImagen === 'openai')">
                                        <button 
                                            @click="open = !open"
                                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $cantidadImagenes }} {{ $cantidadImagenes === 1 ? 'imagen' : 'imágenes' }}</span>
                                        </button>
                                        
                                        <!-- Menú desplegable de cantidad -->
                                        <div 
                                            x-show="open" 
                                            x-cloak
                                            @click.away="open = false"
                                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                        >
                                            <div class="text-center mb-2 text-gray-600 font-medium">Cantidad de imágenes</div>
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach([1, 2, 3, 4] as $cantidad)
                                                    <button 
                                                        wire:click="seleccionarCantidad({{ $cantidad }})"
                                                        @click="open = false"
                                                        class="bg-{{ $cantidadImagenes === $cantidad ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                                    >
                                                        <span>{{ $cantidad }}</span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $cantidad === 1 ? 'imagen' : 'imágenes' }}
                                                        </span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                     <!-- Selector de subida de imágenes (visible para OpenAI y Flux-kontext ) -->
                                    <div class="relative" x-show="($wire.servicioImagen === 'openai' || $wire.servicioImagen === 'flux-kontext-pro'|| $wire.servicioImagen === 'flux-kontext-max') && $wire.tipo === 'imagen' ||$wire.tipo === 'editimagen'">
                                        <label for="imageUpload" class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $servicioImagen !== 'openai' ? 'Subir imagen' : 'Subir imágenes' }}</span>
                                        </label>
                                        <input id="imageUpload" type="file" class="hidden" wire:model="temporaryImages" accept="image/*" {{ $servicioImagen === 'openai' ? 'multiple' : '' }}>
                                    </div>
                                    <!-- Selector de calidad (solo visible cuando OpenAI está seleccionado) -->
                                    <div x-data="{ open: false }" class="relative" x-show="$wire.servicioImagen === 'openai' && $wire.tipo === 'imagen'">
                                        <button 
                                            @click="open = !open"
                                            class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <span>Calidad: {{ $calidadesDisponibles[$calidadImagen] }}</span>
                                        </button>
                                        
                                        <!-- Menú desplegable de calidad -->
                                        <div 
                                            x-show="open" 
                                            x-cloak
                                            @click.away="open = false"
                                            class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                        >
                                            <div class="text-center mb-2 text-gray-600 font-medium">Calidad de imagen</div>
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach($calidadesDisponibles as $key => $label)
                                                    <button 
                                                        wire:click="seleccionarCalidadImagen('{{ $key }}')"
                                                        @click="open = false"
                                                        class="bg-{{ $calidadImagen === $key ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm"
                                                    >
                                                        {{ $label }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                   
                                
                            <!-- Select para documentos genesis-- cuando está vacio el chat -->

                            @if($tipo === 'gprompt')
                                <div class="mt-4 bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                       
                                    </div>
                                    
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
                                    
                                    @if($documentoInfo)
                                    <div class="mt-3 flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $documentoInfo['name'] }}</p>
                                                <p class="text-xs text-gray-500">Creado el {{ $documentoInfo['fecha'] }}</p>
                                            </div>
                                        </div>
                                        <button 
                                            wire:click="quitarDocumentoGenesis"
                                            class="p-1 hover:bg-gray-200 rounded-full transition-colors duration-200"
                                            title="Quitar documento"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                </div>
                            @endif 
                           
                            <!-- Fin Select para documentos genesis-- cuando está vacio el chat -->
                            

<!-- Selector de duración (solo visible cuando Luma está seleccionado) -->
<div x-data="{ open: false }" class="relative" x-show="$wire.tipo === 'video' && $wire.servicioImagen === 'luma'||$wire.tipo === 'video' && $wire.servicioImagen === 'luma2'">
    <button 
        @click="open = !open"
        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Duración: {{ $duracionesDisponiblesLuma[$duracionVideo] }}</span>
    </button>
    
    <!-- Menú desplegable de duración -->
    <div 
        x-show="open" 
        x-cloak
        @click.away="open = false"
        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
    >
        <div class="text-center mb-2 text-gray-600 font-medium">Duración del video</div>
        <div class="grid grid-cols-1 gap-2">
            @foreach($duracionesDisponiblesLuma as $valor => $etiqueta)
                <button 
                    wire:click="seleccionarDuracion('{{ $valor }}')"
                    @click="open = false"
                    class="bg-{{ $duracionVideo === $valor ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                >
                    <span>{{ $etiqueta }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>

                                 
                                    <!-- Botones para Runway y Luma y Veo2 con previsualización ARRIBA -->
                                    <div class="flex items-center gap-3" x-show="$wire.tipo === 'video' && ($wire.servicioImagen === 'runway'|| $wire.servicioImagen === 'runway2'|| $wire.servicioImagen === 'luma' || $wire.servicioImagen === 'luma2'|| $wire.servicioImagen === 'gemini') ">
                                        <!-- Previsualizaciones ARRIBA de los botones -->
                                        <div class="flex flex-wrap mb-2" x-show="$wire.tipo === 'video' && ($wire.servicioImagen === 'runway'|| $wire.servicioImagen === 'runway2'|| $wire.servicioImagen === 'luma' || $wire.servicioImagen === 'luma2'|| $wire.servicioImagen === 'gemini')">
                                            <!-- Previsualización imagen de inicio -->
                                            @if(!empty($imageFilesStart))
                                                <div class="mr-4">
                                                    <div class="image-preview">
                                                        @if(is_object($imageFilesStart[0]))
                                                            <img src="{{ $imageFilesStart[0]->temporaryUrl() }}" alt="Imagen de inicio">
                                                        @else
                                                            <img src="{{ $imageFilesStart[0]['url'] }}" alt="Imagen de inicio">
                                                        @endif
                                                        <div class="remove-image" wire:click="quitarImagenInicio(0)">×</div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Previsualización imagen de fin -->
                                            @if(!empty($imageFilesEnd))
                                                <div>
                                                    <div class="image-preview">
                                                        <img src="{{ $imageFilesEnd[0]->temporaryUrl() }}" alt="Imagen de fin">
                                                        <div class="remove-image" wire:click="quitarImagenFin(0)">×</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Botones DEBAJO de las previsualizaciones -->
                                        <div class="flex items-center gap-3" x-show="$wire.tipo === 'video' && ($wire.servicioImagen === 'runway'|| $wire.servicioImagen === 'runway2'|| $wire.servicioImagen === 'luma' || $wire.servicioImagen === 'luma2'|| $wire.servicioImagen === 'gemini')">
                                            <!-- Botón de imagen de inicio -->
                                            <button type="button" onclick="document.getElementById('imageUploadStart_main').click()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700 cursor-pointer transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                <span>Imagen de inicio</span>
                                                @if(!empty($imageFilesStart))
                                                    <span class="bg-green-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">✓</span>
                                                @endif
                                            </button>
                                            <input id="imageUploadStart_main" type="file" class="hidden" wire:model.live="temporaryImagesStart" accept="image/*">
                                            
                                            <!-- Botón de imagen de fin -->
                                            <button x-show="$wire.servicioImagen !== 'runway2' && $wire.servicioImagen !== 'gemini'" type="button" onclick="document.getElementById('imageUploadEnd_main').click()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700 cursor-pointer transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                                <span>Imagen de fin</span>
                                                @if(!empty($imageFilesEnd))
                                                    <span class="bg-green-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">✓</span>
                                                @endif
                                            </button>
                                            <input id="imageUploadEnd_main" type="file" class="hidden" wire:model.live="temporaryImagesEnd" accept="image/*">
                                        </div>
                                    </div>
                                
                                </div>
                                
                                <!-- Boton para generar cuando no hay nada generado -->
                                <div class="flex justify-end mt-1">
                                    <button 
                                        wire:click="{{ $tipo === 'video' ? 'generarVideoPrincipal' : 'generar' }}"
                                        wire:loading.attr="disabled"
                                        wire:target="{{ $tipo === 'video' ? 'generarVideo' : 'generar' }}"
                                        class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                        x-bind:disabled="$wire.isGenerating || $wire.runwayGenerating || $wire.veo2Generating"
                                    >
                                        <!-- Spinner de carga -->
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" 
                                             x-show="$wire.isGenerating || $wire.runwayGenerating || $wire.veo2Generating" 
                                             style="display: none;">
                                        </div>
                                        
                                        <!-- Texto del botón - Simplificar la lógica -->
                                        <span x-text="$wire.isGenerating || $wire.runwayGenerating || $wire.veo2Generating
                                                      ? 'Generando...'
                                                      : ($wire.tipo === 'editimagen'||$wire.tipo === 'imagen'|| $wire.tipo === 'gprompt' ? 'Generar' : 'Generar Video')">
                                            Generar
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                <!--seccion cuando hay algo generado-->
                   <!--Inicio seccion generado-->
                <div x-show="$wire.modoEdicion !== 'expand' && $wire.modoEdicion !== 'fill' && $wire.tipo !== 'editvideo'">
                    <!-- Mensaje de error justo antes de la galería, pero después del área de entrada -->
                    
                    @if(session()->has('error'))
                        {{-- <div class="error-message flex items-center my-4 sticky top-[70px] z-20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-medium">Error en la generación</p>
                                <p>{{ session('error') }}</p>
                            </div>
                        </div> --}}
                        <div class="error-message flex items-center my-4 p-4 bg-red-50 border border-red-200 text-red-700 sticky top-[70px] z-20 shadow-md rounded-lg overflow-hidden relative">
                        <div class="flex items-start space-x-3 w-full pr-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-red-800 mb-0.5">Error en la generación</p>
                                <p class="text-red-700">{{ session('error') }}</p>
                            </div>
                            </div>
                             <button onclick="this.parentElement.remove()" class="absolute top-3 right-3 text-red-400 hover:text-red-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />Add commentMore actions
                            </svg>
                        </button>
                        </div>
                    @endif
                    <div class="gallery-container bg-gray-50">
                        @foreach($chatHistory as $index => $mensaje)
                            <div class="message-item" wire:key="mensaje-{{ $index }}">
                                @if($mensaje['tipo'] === 'usuario' && !empty($mensaje['imagenes']))
                                    <!-- Mensaje de usuario con imágenes adjuntas -->
                                    <div class="flex justify-center">
                                        <div class="message-images-container">
                                            @foreach($mensaje['imagenes'] as $imagen)
                                                <!-- Modificar las imágenes para usar @click de Alpine -->
                                                <img 
                                                    src="{{ $imagen['url'] }}" 
                                                    class="generated-image rounded-lg object-contain cursor-pointer"
                                                    @click="$dispatch('open-lightbox', { imgSrc: '{{ $imagen['url'] }}' })"
                                                >
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($mensaje['tipo'] === 'sistema')
                                    @if(!empty($mensaje['imagenes']) && is_array($mensaje['imagenes']))
                                        <!-- Renderizar imágenes generadas -->
                                        <div class="w-full my-4">
                                            <div class="images-container">
                                                @foreach($mensaje['imagenes'] as $imagen)
                                                    <div class="image-card">
                                                        <img 
                                                            src="{{ $imagen['url'] }}" 
                                                            class="generated-image rounded-lg max-w-full h-auto max-h-[300px] object-contain cursor-pointer"
                                                            {{-- wire:click="abrirLightbox('{{ $imagen['url'] }}')" --}}
                                                             @click="$dispatch('open-lightbox', { imgSrc: '{{ $imagen['url'] }}' })"
                                                        >
                                                        <div class="absolute bottom-2 right-2 flex space-x-2">
                                                            
                                                            @can('haveaccess','generador.video')
                                                            {{-- <button wire:click="cargarImagenParaVideo('{{ $imagen['url'] }}')" class="bg-purple-600 bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                                </svg>
                                                                Crear Video
                                                            </button> --}}
                                                            @endcan
                                                            <a href="{{ $imagen['url'] }}" download="imagen_generada_{{ $loop->index }}.png" class="bg-black bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                </svg>
                                                                Descargar
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif(!empty($mensaje['url']))
                                        <!-- Renderizar videos -->
                                        <div class="w-full my-4 flex justify-center">
                                            <div class="relative" style="width: 100%; max-width: 800px;">
                                                @if(isset($mensaje['esVideo']) && $mensaje['esVideo'])
                                                
                                                  
                                                    <video 
                                                        controls 
                                                        class="w-full max-h-72 rounded-lg shadow-lg  object-contain my-2" 
                                                        controlsList="nodownload" 
                                                        autoplay 
                                                        playsinline 
                                                        preload="auto"
                                                        src="{{ $mensaje['url'] }}"
                                                    >
                                                        Tu navegador no soporta la etiqueta de video.
                                                    </video>
                                                   
                                                    <div class="absolute bottom-2 right-2 flex space-x-2">
                                                        <button 
                                                            wire:click="abrirEditorVideo('{{ $mensaje['url'] }}')"
                                                            class="bg-blue-600 bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center cursor-pointer"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                            </svg>
                                                            Editar Video
                                                        </button>
                                                        <button 
                                                            onclick="downloadVideo('{{ $mensaje['url'] }}', 'video_generado_{{ $index }}.mp4')"
                                                            class="bg-black bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center cursor-pointer"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            Descargar
                                                        </button>
                                                    </div>
                                                {{-- @else
                                                    <img src="{{ $mensaje['url'] }}" alt="Imagen generada" class="w-full h-auto rounded-lg shadow-md">
                                                    <div class="absolute bottom-2 right-2 flex space-x-2">
                                                        @can('haveaccess','user.index')<button wire:click="cargarImagenParaVideo('{{ $mensaje['url'] }}')" class="bg-purple-600 bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                            </svg>
                                                            Crear Video
                                                        </button>@endcan
                                                        <a href="{{ $mensaje['url'] }}" download="imagen_generada_{{ $index }}.png" class="bg-black bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            Descargarsss
                                                        </a>
                                                    </div> --}}
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Renderizar prompts generados -->
                                        <div class="bg-gray-100 rounded-lg p-4 my-4 shadow-sm">
                                            <div class="flex items-start space-x-2">
                                                <div class="flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-grow">
                                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $mensaje['contenido'] }}</p>
                                                    <button wire:click="cargarPromptParaGenerar('{{ $mensaje['contenido'] }}','video')" class="bg-purple-600 bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                                </svg>
                                                                Crear Video
                                                            </button>
                                                            <button wire:click="cargarPromptParaGenerar('{{ $mensaje['contenido'] }}','imagen')" class="bg-purple-600 bg-opacity-70 hover:bg-opacity-100 text-white rounded-lg px-2 py-1 text-xs shadow-sm inline-flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                Crear Imagen
                                                            </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                        <!-- Sección de spinner de Generando -->
                        @if($videoGenerating || ($isGenerating && $tipo === 'imagen') || $fluxGenerating || $runwayGenerating)
                       
                        <div class="rounded-lg shadow my-4 bg-white p-4 border border-gray-200">
    
    <div class="flex justify-center items-center" wire:loading.remove wire:target="verificarEstadoVideoRunway,verificarEstadoVideoVeo2">
        <div class="mr-2 {{ $isGenerating ? 'generating-animation' : '' }}">
            @if($isGenerating)
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            @endif
        </div>
        <span class="{{ $isGenerating ? 'generating-animation' : '' }}">Generando...</span>
    </div>

    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
        <div class="bg-black h-2 rounded-full animate-pulse" style="width: 70%"></div>
    </div>
</div>

                        @endif
                        <!-- Fin Sección de spinner de Generando -->
                    </div>
                  
                    <div class="input-container bg-black rounded-b-xl p-2 shadow-lg" x-show="$wire.tipo !== 'editvideo'">
                        <div class="relative bg-white rounded-lg">
                             <textarea 
                                    wire:model="prompt" 
                                    class="w-full outline-none resize-none text-lg min-h-[80px] text-gray-700 no-border" 
                                    {{-- placeholder="{{ $tipo === 'imagen' ? 'Describe una imagen para generar...' : 'Describe una secuencia de video para generar...' }}" --}}
                                     @switch($tipo)
                                        @case('imagen')
                                            placeholder="Describe una imagen para generar..."
                                            @break

                                        @case('video')
                                            placeholder="Describe una secuencia de video para generar..."
                                            @break

                                        @case('gprompt')
                                            placeholder="Describe un prompt para generar..."
                                            @break

                                        @case('editvideo')
                                            placeholder="Editor de videos..."
                                            @break

                                        @default
                                            placeholder="Describe el contenido para generar..."
                                    @endswitch
                                    @keydown.enter.prevent="$event.shiftKey || $wire.generar()"
                                ></textarea>
                            
                            <!--vista previa de imagen seleccionada-->
                            @if(!empty($imageFiles))
                                <div class="mb-4">
                                    <div class="flex flex-wrap">
                                        @foreach($imageFiles as $index => $image)
                                            <div class="image-preview">
                                                <img src="{{ $image->temporaryUrl() }}" alt="Imagen de referencia">
                                                <div class="remove-image" wire:click="quitarImagen({{ $index }})">×</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif(!empty($imagenBaseParaVideo))
                                <!-- Vista previa de la imagen seleccionada para video -->
                                <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="text-sm font-medium text-gray-700 mb-2">Imagen base para video:</div>
                                    <div class="flex flex-wrap">
                                        <div style="position: relative; width: 120px; height: 120px; margin: 0.5rem;">
                                            <img src="{{ $imagenBaseParaVideo }}" alt="Imagen base para video" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem; border: 1px solid #d1d5db;">
                                            <div style="position: absolute; top: -8px; right: -8px; background-color: #ef4444; color: white; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" wire:click="quitarImagenBaseVideo()">×</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- Incio Contenido de selectores chat lleno-->
                            <div class="flex flex-wrap items-center gap-2">

                                <!-- Selector de tamaño (solo visible cuando OpenAI está seleccionado) -->
                                <div x-data="{ open: false }" class="relative" x-show="$wire.servicioImagen === 'openai' && $wire.tipo === 'imagen'">
                                    <button 
                                        @click="open = !open"
                                        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <span>{{ $tamanoOpenAI }}</span>
                                    </button>
                                    
                                    <!-- Menú desplegable de tamaño para OpenAI -->
                                    <div 
                                        x-show="open" 
                                        x-cloak
                                        @click.away="open = false"
                                        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                    >
                                        <div class="text-center mb-2 text-gray-600 font-medium">Tamaño de imagen</div>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach($tamanosOpenAI as $tamano => $descripcion)
                                                <button 
                                                    wire:click="seleccionarTamanoOpenAI('{{ $tamano }}')"
                                                    @click="open = false"
                                                    class="bg-{{ $tamanoOpenAI === $tamano ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                                >
                                                    <span>{{ $tamano }}</span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $descripcion }}
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                 <!-- Selector de relación de aspecto para videos -->
<div x-data="{ open: false }" class="relative" x-show="$wire.tipo === 'video'">
    <button 
        @click="open = !open"
        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
        </svg>
        <span>{{ $ratioVideo }}</span>
    </button>
    
    <!-- Menú desplegable de ratio -->
    <div 
        x-show="open" 
        x-cloak
        @click.away="open = false"
        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
    >
        <div class="text-center mb-2 text-gray-600 font-medium">Relación de aspecto</div>
        <div class="grid grid-cols-1 gap-2">
            @foreach($ratiosVideoDisponibles[$servicioImagen] ?? [] as $ratioOption => $label)
                <button 
                    wire:click="seleccionarRatioVideo('{{ $ratioOption }}')"
                    @click="open = false"
                    class="bg-{{ $ratioVideo === $ratioOption ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                >
                    <span>{{ $ratioOption }}</span>
                    <span class="text-xs text-gray-500">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div><!-- fin Selector de relación de aspecto para videos -->
                                <!-- Selector de relación de aspecto (no mostrar cuando OpenAI está seleccionado) -->
                                <div x-data="{ open: false }" class="relative"x-show="($wire.tipo === 'imagen' || $wire.tipo === 'editimagen') && $wire.servicioImagen !== 'openai' ||$wire.tipo === 'editimagen'">
                                    <button 
                                        @click="open = !open"
                                        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <span>{{ $ratio }}</span>
                                    </button>
                                    
                                    <!-- Menú desplegable de ratio -->
                                    <div 
                                        x-show="open" 
                                        x-cloak
                                        @click.away="open = false"
                                        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                    >
                                        <div class="text-center mb-2 text-gray-600 font-medium">Relación de aspecto</div>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach(['1:1', '4:3', '3:4', '16:9', '9:16'] as $ratioOption)
                                                <button 
                                                    wire:click="seleccionarRatio('{{ $ratioOption }}')"
                                                    @click="open = false"
                                                    class="bg-{{ $ratio === $ratioOption ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                                >
                                                    <span>{{ $ratioOption }}</span>
                                                    <span class="text-xs text-gray-500">
                                                        @if($ratioOption == '1:1')
                                                            Cuadrado
                                                        @elseif($ratioOption == '4:3')
                                                            Horizontal
                                                        @elseif($ratioOption == '3:4')
                                                            Vertical
                                                        @elseif($ratioOption == '16:9')
                                                            Panorámico
                                                        @elseif($ratioOption == '9:16')
                                                            Vertical móvil
                                                        @endif
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selector de cantidad de imágenes (mostrar para Gemini3, Gemini4 y OpenAI) -->
                                <div x-data="{ open: false }" class="relative" x-show="($wire.tipo === 'imagen') && ($wire.servicioImagen === 'gemini' || $wire.servicioImagen === 'gemini4' || $wire.servicioImagen === 'openai')">
                                    <button 
                                        @click="open = !open"
                                        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $cantidadImagenes }} {{ $cantidadImagenes === 1 ? 'imagen' : 'imágenes' }}</span>
                                    </button>
                                    
                                    <!-- Menú desplegable de cantidad -->
                                    <div 
                                        x-show="open" 
                                        x-cloak
                                        @click.away="open = false"
                                        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                    >
                                        <div class="text-center mb-2 text-gray-600 font-medium">Cantidad de imágenes</div>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach([1, 2, 3, 4] as $cantidad)
                                                <button 
                                                    wire:click="seleccionarCantidad({{ $cantidad }})"
                                                    @click="open = false"
                                                    class="bg-{{ $cantidadImagenes === $cantidad ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                                                >
                                                    <span>{{ $cantidad }}</span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $cantidad === 1 ? 'imagen' : 'imágenes' }}
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <!-- Selector de subida de imágenes (visible para OpenAI y Flux-kontext) -->
                                 <div class="relative" x-show="($wire.servicioImagen === 'openai' || $wire.servicioImagen === 'flux-kontext-pro'|| $wire.servicioImagen === 'flux-kontext-max') && $wire.tipo === 'imagen'||$wire.tipo === 'editimagen'">
                                        <label for="imageUpload" class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $servicioImagen !== 'openai' ? 'Subir imagen base' : 'Subir imágenes' }}</span>
                                        </label>
                                        <input id="imageUpload" type="file" class="hidden" wire:model="temporaryImages" accept="image/*" {{ $servicioImagen === 'openai' ? 'multiple' : '' }}>
                                    </div>
                                
                                <!-- Selector de calidad (solo visible cuando OpenAI está seleccionado) -->
                                <div x-data="{ open: false }" class="relative" x-show="$wire.servicioImagen === 'openai' && $wire.tipo === 'imagen'">
                                    <button 
                                        @click="open = !open"
                                        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span>Calidad: {{ $calidadesDisponibles[$calidadImagen] }}</span>
                                    </button>
                                    
                                    <!-- Menú desplegable de calidad -->
                                    <div 
                                        x-show="open" 
                                        x-cloak
                                        @click.away="open = false"
                                        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
                                    >
                                        <div class="text-center mb-2 text-gray-600 font-medium">Calidad de imagen</div>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach($calidadesDisponibles as $key => $label)
                                                <button 
                                                    wire:click="seleccionarCalidadImagen('{{ $key }}')"
                                                    @click="open = false"
                                                    class="bg-{{ $calidadImagen === $key ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm"
                                                >
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                
                                

<!-- Selector de duración (solo visible cuando Luma está seleccionado) -->
<div x-data="{ open: false }" class="relative" x-show="$wire.tipo === 'video' && $wire.servicioImagen === 'luma' ||$wire.tipo === 'video' && $wire.servicioImagen === 'luma2' ">
    <button 
        @click="open = !open"
        class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Duración: {{ $duracionesDisponiblesLuma[$duracionVideo] }}</span>
    </button>
    
    <!-- Menú desplegable de duración -->
    <div 
        x-show="open" 
        x-cloak
        @click.away="open = false"
        class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg"
    >
        <div class="text-center mb-2 text-gray-600 font-medium">Duración del video</div>
        <div class="grid grid-cols-1 gap-2">
            @foreach($duracionesDisponiblesLuma as $valor => $etiqueta)
                <button 
                    wire:click="seleccionarDuracion('{{ $valor }}')"
                    @click="open = false"
                    class="bg-{{ $duracionVideo === $valor ? 'black text-white' : 'gray-100 hover:bg-gray-200 text-gray-800' }} rounded text-center py-2 text-sm flex justify-between items-center px-3"
                >
                    <span>{{ $etiqueta }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>


                                <!--boton para subir imagenes de runway luma y veo 2-->
                                <div class="flex items-center gap-3" x-show="$wire.tipo === 'video' && ($wire.servicioImagen === 'runway' || $wire.servicioImagen === 'runway2'|| $wire.servicioImagen === 'luma' || $wire.servicioImagen === 'luma2'|| $wire.servicioImagen === 'gemini')">
                                    
                                    <!-- Previsualizaciones ARRIBA de los botones -->
                                        <div class="flex flex-wrap mb-2" x-show="$wire.tipo === 'video' && ($wire.servicioImagen === 'runway'|| $wire.servicioImagen === 'runway2'|| $wire.servicioImagen === 'luma' || $wire.servicioImagen === 'luma2'|| $wire.servicioImagen === 'gemini')">
                                            <!-- Previsualización imagen de inicio -->
                                            @if(!empty($imageFilesStart))
                                                <div class="mr-4">
                                                    <div class="image-preview">
                                                        @if(is_object($imageFilesStart[0]))
                                                            <img src="{{ $imageFilesStart[0]->temporaryUrl() }}" alt="Imagen de inicio">
                                                        @else
                                                            <img src="{{ $imageFilesStart[0]['url'] }}" alt="Imagen de inicio">
                                                        @endif
                                                        <div class="remove-image" wire:click="quitarImagenInicio(0)">×</div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Previsualización imagen de fin -->
                                            @if(!empty($imageFilesEnd))
                                                <div>
                                                    <div class="image-preview">
                                                        <img src="{{ $imageFilesEnd[0]->temporaryUrl() }}" alt="Imagen de fin">
                                                        <div class="remove-image" wire:click="quitarImagenFin(0)">×</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>        
                                    <!-- Botón de imagen de inicio -->
                                            <button type="button" onclick="document.getElementById('imageUploadStart_main').click()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700 cursor-pointer transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                <span>Imagen de inicio</span>
                                                @if(!empty($imageFilesStart))
                                                    <span class="bg-green-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">✓</span>
                                                @endif
                                            </button>
                                            <input id="imageUploadStart_main" type="file" class="hidden" wire:model.live="temporaryImagesStart" accept="image/*">
                                            
                                            <!-- Botón de imagen de fin -->
                                            <button x-show="$wire.servicioImagen !== 'runway2' && $wire.servicioImagen !== 'gemini'" type="button" onclick="document.getElementById('imageUploadEnd_main').click()" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm shadow-sm text-gray-700 cursor-pointer transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                                <span>Imagen de fin</span>
                                                @if(!empty($imageFilesEnd))
                                                    <span class="bg-green-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">✓</span>
                                                @endif
                                            </button>
                                            <input id="imageUploadEnd_main" type="file" class="hidden" wire:model.live="temporaryImagesEnd" accept="image/*">
                                        </div>
                                <!--fin boton para subir imagenes de runway-->

                                        <!-- Select para documentos genesis-- cuando está lleno el chat -->

                            @if($tipo === 'gprompt')
                                <div class="mt-4 bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                       
                                    </div>
                                    
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
                                    
                                    @if($documentoInfo)
                                    <div class="mt-3 flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $documentoInfo['name'] }}</p>
                                                <p class="text-xs text-gray-500">Creado el {{ $documentoInfo['fecha'] }}</p>
                                            </div>
                                        </div>
                                        <button 
                                            wire:click="quitarDocumentoGenesis"
                                            class="p-1 hover:bg-gray-200 rounded-full transition-colors duration-200"
                                            title="Quitar documento"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                </div>
                            @endif 
                           
                            <!-- Fin Select para documentos genesis-- cuando está lleno el chat -->


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
                            <!--boton para generar cuando hay algo generado-->
                            <div class="flex justify-end mt-1">
                                <button 
                                    wire:click="{{ $tipo === 'video' ? 'generarVideoPrincipal' : 'generar' }}"
                                    wire:loading.attr="disabled"
                                    wire:target="{{ $tipo === 'video' ? 'generarVideo' : 'generar' }}"
                                    class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    x-bind:disabled="$wire.isGenerating"
                                >
                                    <!-- Spinner de carga -->
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" 
                                         x-show="$wire.isGenerating" 
                                         style="display: none;">
                                    </div>
                                    
                        
                                    <span x-text="$wire.isGenerating || $wire.runwayGenerating || $wire.veo2Generating
                                                  ? 'Generando...'
                                                  : ($wire.tipo === 'editimagen'||$wire.tipo === 'imagen'|| $wire.tipo === 'gprompt' ? 'Generar' : 'Generar Video')">
                                        Generar
                                    </span>
                                </button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                    <!--fin seccion generado-->
                @endif
            </div>
        </div>
    
    <!-- Mensaje de confirmación -->
    @if(session()->has('mensaje'))
    <div class="bg-green-50 text-green-700 p-2 rounded-lg text-sm mb-2 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        {{ session('mensaje') }}
    </div>
    @endif

    <!-- contenedor fijo en la parte inferior izquierda para los selectores -->
    <div class="fixed bottom-6 left-6 z-30 model-selector-container" x-show="$wire.tipo !== 'gprompt' && $wire.modoEdicion !== 'expand' && $wire.tipo !== 'editvideo'" >
    <div class="flex flex-col space-y-2 bg-white rounded-xl p-3 shadow-lg border border-gray-200">
        <!-- Selector de modelo de IA -->
        <div x-data="{ open: false }" class="relative">
            <button 
                @click="open = !open"
                class="flex items-center justify-between gap-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-800 shadow-sm min-w-[150px]"
                >
                <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    <span>{{ $tipo === 'editvideo' ? 'Editor de Videos' : ($serviciosDisponibles[$servicioImagen] ?? 'Seleccionar') }}</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <!-- Menú desplegable de modelos -->
            <div 
                x-show="open" 
                x-cloak
                @click.away="open = false"
                class="absolute bottom-full left-0 mb-1 bg-white border border-gray-200 rounded-xl p-4 w-[200px] z-20 shadow-lg model-dropdown-direction"
            >
                <div class="text-center mb-2 text-gray-600 font-medium">Modelo de IA</div>
                <div class="grid grid-cols-1 gap-2">
                    {{-- @foreach($serviciosDisponibles as $key => $label)
                        <button 
                            wire:click="{{ $key !== 'gemini4' ? "seleccionarServicioImagen('$key')" : '' }}"
                            @click="open = false"
                            class="flex items-center justify-between px-3 py-2 rounded-lg w-full text-left 
                                {{ $servicioImagen === $key ? 'bg-black text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}
                                {{ $key === 'gemini4' ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $key === 'gemini4' ? 'disabled' : '' }}
                        >
                            <span>
                                {{ $label }}
                                @if($key === 'gemini4')
                                    <span class="ml-2 text-sm text-gray-500">(Próx.)</span>
                                @endif
                            </span>

                            @if($servicioImagen === $key && $key !== 'gemini4')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach --}}
                    @foreach($serviciosDisponibles as $key => $label)
                        <button 
                            wire:click="seleccionarServicioImagen('{{ $key }}')"
                            @click="open = false"
                            class="flex items-center justify-between px-3 py-2 rounded-lg {{ $servicioImagen === $key ? 'bg-black text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}"
                        >
                            <span>{{ $label }}</span>
                          
                            @if($servicioImagen === $key)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


<!-- lightbox -->

<div 
    id="imageLightbox"
    x-data="{ open: false, currentImage: '' }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open = false"
    @open-lightbox.window="currentImage = $event.detail.imgSrc; open = true"
    class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center"
    style="display: none;"
>
    <div class="relative max-w-4xl mx-auto p-4">
        <!-- Botón de cerrar -->
        <button 
            @click="open = false"
            class="absolute -top-4 -right-4 bg-black rounded-full p-2 text-white hover:bg-gray-800"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <!-- Imagen -->
        <img 
            :src="currentImage" 
            class="max-h-[80vh] w-auto mx-auto object-contain rounded-lg"
            alt="Imagen ampliada"
        >
        
        <!-- Botón de descarga -->
        <a 
            :href="currentImage"
            download
            class="mt-4 block w-full text-center bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-800"
        >
            Descargar Imagen
        </a>
    </div>
</div>



</div>

<script>
    
// Envolver todo el código en el evento livewire:init
document.addEventListener('livewire:init', () => {


// Evento específico para scroll cuando se genera una imagen expandida
Livewire.on('scrollToExpandedImage', () => {
    console.log('Scrolling to expanded image section...');
    
    // Esperar un momento para que la imagen se agregue al DOM
    setTimeout(() => {
        // Buscar la sección de imágenes expandidas
        const expandedSection = document.querySelector('.expanded-images-section');
        if (expandedSection) {
            // Scroll suave hacia la sección de imágenes expandidas
            expandedSection.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
            
            // También buscar la imagen más reciente (última agregada)
            const lastExpandedImage = expandedSection.querySelector('.image-card:first-child, .min-w-\\[120px\\]:first-child');
            if (lastExpandedImage) {
                // Pequeño delay adicional y scroll hacia la imagen específica
                setTimeout(() => {
                    lastExpandedImage.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 300);
            }
        } else {
            // Fallback: scroll hacia el área del expandor si no encuentra la sección
            const expandArea = document.querySelector('[x-data="expandImage()"]');
            if (expandArea) {
                expandArea.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    }, 500); // Esperar 500ms para que la imagen se cargue
});

// Evento específico para scroll cuando se genera una imagen rellenada
Livewire.on('scrollToFilledImage', () => {
    console.log('Scrolling to filled image section...');
    
    // Esperar un momento para que la imagen se agregue al DOM
    setTimeout(() => {
        // Buscar la sección de imágenes rellenadas
        const filledSection = document.querySelector('.filled-images-section');
        if (filledSection) {
            // Scroll suave hacia la sección de imágenes rellenadas
            filledSection.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
            
            // También buscar la imagen más reciente (primera en el carrusel)
            const lastFilledImage = filledSection.querySelector('.min-w-\\[120px\\]:first-child');
            if (lastFilledImage) {
                // Pequeño delay adicional y scroll hacia la imagen específica
                setTimeout(() => {
                    lastFilledImage.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 300);
            }
        } else {
            // Fallback: scroll hacia el área del editor de relleno si no encuentra la sección
            const fillArea = document.querySelector('[x-data="fillImage()"]');
            if (fillArea) {
                fillArea.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    }, 500); // Esperar 500ms para que la imagen se cargue
});


// Función mejorada para hacer scroll suave a un elemento
    function scrollToElement(element) {
        if (element) {
            // Obtener la posición del elemento
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset;
            
            // Hacer scroll hasta el elemento
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }

    // Función para hacer scroll al último mensaje
    function scrollToLastMessage() {
        const lastMessage = document.querySelector('.message-item:last-child');
        if (lastMessage) {
            scrollToElement(lastMessage);
        }
    }

    // Observer para detectar cambios en el DOM
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                // Detectar cuando aparece el spinner de generación
                const spinner = document.querySelector('.generating-animation');
                if (spinner) {
                    scrollToElement(spinner);
                }

                // Detectar cuando se agrega una nueva imagen
                const newImage = document.querySelector('.message-item:last-child img');
                if (newImage) {
                    // Esperar a que la imagen se cargue
                    newImage.onload = () => {
                        scrollToLastMessage();
                    };
                    // Si la imagen ya está cargada
                    if (newImage.complete) {
                        scrollToLastMessage();
                    }
                }
            }
        });
    });

    // Configurar el observer
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Evento para scroll automático
    Livewire.on('scrollTo', (data) => {
        const { target, offset } = data;
        const element = document.querySelector(target);
        if (element) {
            scrollToElement(element);
        }
    });

    // Cuando se genera una imagen
    Livewire.on('imagenGenerada', () => {
        scrollToLastMessage();
    });

    // Cuando se genera un video
    Livewire.on('videoGenerado', () => {
        scrollToLastMessage();
    });

    // Cuando se actualiza el historial
    Livewire.on('historialActualizado', () => {
        scrollToLastMessage();
    });

    // Cuando se inicia la generación
    Livewire.on('generacionIniciada', () => {
        const spinner = document.querySelector('.generating-animation');
        if (spinner) {
            scrollToElement(spinner);
        }
    });


    Livewire.on('abrirLightbox', (data) => {
        console.log("Evento abrirLightbox recibido:", data);
        const lightbox = document.getElementById('imageLightbox');
        if (lightbox) {
            const alpineData = Alpine.$data(lightbox);
            console.log("Estado actual:", alpineData);
            alpineData.currentImage = data.imgSrc;
            alpineData.open = true;
            console.log("Nuevo estado:", alpineData);
        } else {
            console.error("No se encontró el elemento lightbox");
        }
    });

    // Registrar los eventos de Livewire
    Livewire.on('verificarEstadoRunway', () => {
        console.log('Verificando estado de video en Runway...');
        
        const allIndicators = document.querySelectorAll('.animate-pulse');
        allIndicators.forEach(indicator => {
            indicator.classList.remove('animate-pulse');
            setTimeout(() => {
                indicator.classList.add('animate-pulse');
            }, 10);
        });
        
        setTimeout(scrollToBottom, 100);
    });


    Livewire.on('videoEnviadoARuntimeway', (data) => {
        console.log('Video enviado a Runway, ID:', data.taskId);
        if (runwayCheckIntervalId !== null) {
            clearInterval(runwayCheckIntervalId);
            runwayCheckIntervalId = null;
        }
        runwayCheckAttempts = 0;
        iniciarVerificacionRunway(data.taskId);
    });

    Livewire.on('videoGeneradoExitosamente', () => {
        // Scroll al último mensaje del chat
        const lastMessage = document.querySelector('.message-item:last-child');
        if (lastMessage) {
            scrollToElement(lastMessage);
        }
        console.log('Video generado exitosamente, deteniendo verificaciones');
        if (runwayCheckIntervalId !== null) {
            clearInterval(runwayCheckIntervalId);
            runwayCheckIntervalId = null;
        }
    });

    Livewire.on('errorOcurrido', () => {
        console.log('Error en la generación del video, deteniendo verificaciones');
        if (runwayCheckIntervalId !== null) {
            clearInterval(runwayCheckIntervalId);
            runwayCheckIntervalId = null;
        }
    });
    
    Livewire.on('videoEnviadoALuma', (data) => {
    console.log('Video enviado a Luma, ID recibido:', data);
    
    // Validación para asegurarnos de que tenemos un ID válido
    let taskId = null;
    
    if (typeof data === 'object' && data !== null) {
        if (data.taskId) {
            taskId = data.taskId;
        } else if (Array.isArray(data) && data.length > 0 && data[0].taskId) {
            taskId = data[0].taskId;
        }
    }
    
    if (!taskId) {
        console.error('Error: No se pudo extraer un ID de tarea válido para Luma. Datos completos:', data);
        return;
    }
    
    console.log('ID de tarea Luma procesado correctamente:', taskId);
    
    // Inicializar variables globales para Luma si no existen
    if (typeof window.lumaCheckIntervalId === 'undefined') {
        window.lumaCheckIntervalId = null;
    }
    
    // Limpiar intervalo existente
    if (window.lumaCheckIntervalId) {
        console.log('Limpiando intervalo existente de Luma');
        clearInterval(window.lumaCheckIntervalId);
        window.lumaCheckIntervalId = null;
    }
    
    // Ejecutar verificación con retraso pequeño
    setTimeout(() => {
        console.log('Iniciando verificación periódica para Luma con ID:', taskId);
        iniciarVerificacionLuma(taskId);
    }, 500);
});

   
    Livewire.on('videoEnviadoAVeo2', (data) => {
        console.log('Video enviado a Veo2, ID recibido:', data);
        
        // Validación para manejar tanto objetos como arrays
        let taskId = null;
        
        // Si es un array, extraer el primer elemento
        if (Array.isArray(data)) {
            console.log('Datos recibidos como array, extrayendo primer elemento');
            if (data.length > 0 && data[0].taskId) {
                taskId = data[0].taskId;
            }
        } 
        // Si es un objeto, usar directamente
        else if (data && data.taskId) {
            taskId = data.taskId;
        }
        
        if (!taskId) {
            console.error('Error: No se pudo extraer un ID de tarea válido para Veo2. Datos completos:', data);
            return;
        }
        
        console.log('ID de tarea Veo2 procesado correctamente:', taskId);
        
        // Inicializar variables globales si no existen
        if (typeof window.veo2CheckIntervalId === 'undefined') {
            window.veo2CheckIntervalId = null;
        }
        
        if (typeof window.veo2CheckAttempts === 'undefined') {
            window.veo2CheckAttempts = 0;
        }
        
        if (typeof window.MAX_ATTEMPTS === 'undefined') {
            window.MAX_ATTEMPTS = 30;
        }
        
        // Limpiar intervalo existente
        if (window.veo2CheckIntervalId) {
            console.log('Limpiando intervalo existente de Veo2');
            clearInterval(window.veo2CheckIntervalId);
            window.veo2CheckIntervalId = null;
        }
        
        // Reiniciar contador
        window.veo2CheckAttempts = 0;
        
        // Ejecutar verificación con retraso pequeño para asegurar que los estados estén actualizados
        setTimeout(() => {
            console.log('Iniciando verificación periódica para Veo2 con ID:', taskId);
            iniciarVerificacionVeo2(taskId);
        }, 400);
    });

    // Variables globales para los intervalos
    window.runwayCheckIntervalId = null;
    window.veo2CheckIntervalId = null;
    window.runwayCheckAttempts = 0;
    window.veo2CheckAttempts = 0;
    window.MAX_ATTEMPTS = 30;

// Función para verificar Luma
function iniciarVerificacionLuma(taskId) {
    console.log('Iniciando verificación periódica para Luma, ID:', taskId);
    
    // Primera verificación inmediata
    @this.verificarEstadoVideoLuma(taskId);
    
    // Configurar verificación periódica
    window.lumaCheckIntervalId = setInterval(() => {
        console.log('Verificando estado de video Luma...');
        
        // Verificar si la generación ya terminó
        if (!@this.videoGenerating) {
            console.log('Generación Luma completada, deteniendo verificaciones');
            clearInterval(window.lumaCheckIntervalId);
            window.lumaCheckIntervalId = null;
            return;
        }
        
        // Llamar a la verificación
        @this.verificarEstadoVideoLuma(taskId);
    }, 5000); // Verificar cada 5 segundos
}


    // Función para verificar Runway
    function iniciarVerificacionRunway(taskId) {
        console.log('Iniciando verificación periódica para ID:', taskId);
        
        @this.verificarEstadoVideoRunway(taskId);
        runwayCheckAttempts++;
        
        runwayCheckIntervalId = setInterval(() => {
            runwayCheckAttempts++;
            
            if (runwayCheckAttempts > MAX_ATTEMPTS) {
                console.log('Alcanzado máximo de intentos (30), pausando verificaciones automáticas');
                clearInterval(runwayCheckIntervalId);
                runwayCheckIntervalId = null;
                @this.generatingMessage = "La verificación automática ha terminado. El video puede seguir procesándose. Haz clic en 'Verificar estado' para comprobar.";
                return;
            }
            
            if (!@this.runwayGenerating) {
                console.log('Deteniendo verificación periódica - generación completada');
                clearInterval(runwayCheckIntervalId);
                runwayCheckIntervalId = null;
                return;
            }
            
            console.log(`Verificando estado de video (intento ${runwayCheckAttempts}/${MAX_ATTEMPTS})...`);
            @this.verificarEstadoVideoRunway(taskId);
            
            if (runwayCheckAttempts > 10) {
                clearInterval(runwayCheckIntervalId);
                runwayCheckIntervalId = setInterval(() => {
                    runwayCheckAttempts++;
                    if (runwayCheckAttempts > MAX_ATTEMPTS || !@this.runwayGenerating) {
                        clearInterval(runwayCheckIntervalId);
                        runwayCheckIntervalId = null;
                        return;
                    }
                    @this.verificarEstadoVideoRunway(taskId);
                }, 2000);
            }
        }, 1000);
    }

    // Mejorar la función de verificación para mostrar más información
    function iniciarVerificacionVeo2(taskId) {
        console.log('INICIO: Verificación periódica para Veo2, ID:', taskId);
        
        // Primera verificación inmediata
        console.log('Ejecutando primera verificación Veo2...');
        @this.verificarEstadoVideoVeo2(taskId);
        window.veo2CheckAttempts++;
        
        // Configurar verificación periódica
        console.log('Configurando verificación periódica para Veo2...');
        window.veo2CheckIntervalId = setInterval(() => {
            window.veo2CheckAttempts++;
            
            console.log(`Verificando estado de video Veo2 (intento ${window.veo2CheckAttempts}/${window.MAX_ATTEMPTS})...`);
            
            // Verificar estado actual antes de continuar
            console.log('Estado actual - veo2Generating:', @this.veo2Generating);
            
            // Verificar si debemos detener
            if (window.veo2CheckAttempts > window.MAX_ATTEMPTS) {
                console.log('Alcanzado máximo de intentos, deteniendo verificaciones de Veo2');
                clearInterval(window.veo2CheckIntervalId);
                window.veo2CheckIntervalId = null;
                @this.generatingMessage = "La verificación automática ha terminado. El video puede seguir procesándose. Haz clic en 'Verificar estado' para comprobar.";
                return;
            }
            
            // Verificar si la generación ya terminó
            if (!@this.veo2Generating) {
                console.log('Generación Veo2 completada, deteniendo verificaciones');
                clearInterval(window.veo2CheckIntervalId);
                window.veo2CheckIntervalId = null;
                return;
            }
            
            // Llamar a la verificación
            @this.verificarEstadoVideoVeo2(taskId);
            
            // Después de 10 intentos, cambiar a intervalo más largo
            if (window.veo2CheckAttempts === 10) {
                console.log('Cambiando a intervalo de 2 segundos para Veo2');
                clearInterval(window.veo2CheckIntervalId);
                window.veo2CheckIntervalId = setInterval(() => {
                    window.veo2CheckAttempts++;
                    console.log(`Verificando estado extendido de Veo2 (intento ${window.veo2CheckAttempts}/${window.MAX_ATTEMPTS})...`);
                    
                    if (window.veo2CheckAttempts > window.MAX_ATTEMPTS || !@this.veo2Generating) {
                        console.log('Finalizando verificación extendida de Veo2');
                        clearInterval(window.veo2CheckIntervalId);
                        window.veo2CheckIntervalId = null;
                        return;
                    }
                    @this.verificarEstadoVideoVeo2(taskId);
                }, 2000);
            }
        }, 1000);
    }
});

function downloadVideo(url, filename) {
    fetch(url)
        .then(response => response.blob())
        .then(blob => {
            const blobUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = blobUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(blobUrl);
            document.body.removeChild(a);
        })
        .catch(error => console.error('Error downloading video:', error));
}

// Función para hacer scroll suave a un elemento
function scrollToElement(element) {
    if (element) {
        element.scrollIntoView({ 
            behavior: 'smooth',
            block: 'end'
        });
    }
}


//gestion de edit
// Reemplazar toda la función fillImage() con esta versión corregida:
function fillImage() {
    return {
        // Propiedades principales
        imageSrc: null,
        imageLoaded: false,
        dragOver: false,
        paintedPixels: new Set(), // coordenadas de píxeles pintados
        
        // Propiedades del canvas
        canvas: null,
        ctx: null,
        isDrawing: false,
        
        // Propiedades del pincel
        brushSize: 20,
        brushOpacity: 0.8,
        showPreview: false,

        // 🔧 AGREGAR INIT PARA ESCUCHAR EVENTOS
        init() {
            // Escuchar evento de Livewire cuando se complete el fill
            this.$wire.on('fill-completed', () => {
                console.log('✅ Fill completado - manteniendo imagen');
                // La imagen se mantiene visible automáticamente
                // Solo limpiamos las marcas de pincel
                this.paintedPixels.clear();
                if (this.canvas && this.ctx) {
                    this.setupCanvas(); // Redibujar la imagen limpia
                }
            });
            
            this.$wire.on('fill-error', () => {
                console.log('❌ Error en fill - manteniendo imagen');
                // En caso de error, mantener la imagen visible
            });
        },
        // Manejo de drag & drop
        handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.loadFile(file);
            }
        },

        // Manejo de selección de archivo
        handleFileFill(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                this.loadFile(file);
            }
        },

        // Cargar archivo seleccionado
        loadFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imageSrc = e.target.result;
                this.imageLoaded = false;
                // Limpiar píxeles pintados cuando se carga nueva imagen
                this.paintedPixels.clear();
                this.$nextTick(() => {
                    this.setupCanvas();
                });
            };
            reader.readAsDataURL(file);
        },

        // Configurar el canvas
        setupCanvas() {
            this.canvas = this.$refs.canvas;
            this.ctx = this.canvas.getContext('2d');
            
            const img = new Image();
            img.onload = () => {
                // Ajustar el tamaño del canvas a la imagen
                const maxWidth = 600;
                const maxHeight = 400;
                
                let { width, height } = img;
                
                // Redimensionar manteniendo proporción
                if (width > maxWidth || height > maxHeight) {
                    const ratio = Math.min(maxWidth / width, maxHeight / height);
                    width *= ratio;
                    height *= ratio;
                }
                
                this.canvas.width = width;
                this.canvas.height = height;
                
                // Dibujar la imagen de fondo
                this.ctx.drawImage(img, 0, 0, width, height);
                
                // Configurar el contexto para el pincel
                this.ctx.globalCompositeOperation = 'source-over';
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
                
                this.imageLoaded = true;
            };
            img.src = this.imageSrc;
        },

        // 🔧 EVENTOS DE MOUSE CORREGIDOS
              // 🔧 EVENTOS DE MOUSE CORREGIDOS - TRANSPARENCIA ARREGLADA
              startDrawing(e) {
            if (!this.imageLoaded) return;
            
            e.preventDefault();
            this.isDrawing = true;
            
            const rect = this.canvas.getBoundingClientRect();
            const x = (e.clientX - rect.left) * (this.canvas.width / rect.width);
            const y = (e.clientY - rect.top) * (this.canvas.height / rect.height);
            
            // 🎨 CONFIGURAR PINCEL CON TRANSPARENCIA CORRECTA
            this.ctx.globalCompositeOperation = 'source-over';
            this.ctx.strokeStyle = `rgba(255, 255, 255, ${this.brushOpacity})`;
            this.ctx.fillStyle = `rgba(255, 255, 255, ${this.brushOpacity})`;
            this.ctx.lineWidth = this.brushSize;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            
            // Dibujar punto inicial
            this.ctx.beginPath();
            this.ctx.arc(x, y, this.brushSize / 2, 0, Math.PI * 2);
            this.ctx.fill();
            
            // Preparar para línea
            this.ctx.beginPath();
            this.ctx.moveTo(x, y);
            
            // Marcar píxeles pintados
            this.markPaintedArea(x, y);
        },

        draw(e) {
            if (!this.isDrawing || !this.imageLoaded) return;
            
            e.preventDefault();
            const rect = this.canvas.getBoundingClientRect();
            const x = (e.clientX - rect.left) * (this.canvas.width / rect.width);
            const y = (e.clientY - rect.top) * (this.canvas.height / rect.height);
            
            // 🎨 DIBUJAR LÍNEA CON TRANSPARENCIA
            this.ctx.globalAlpha = this.brushOpacity;
            this.ctx.lineTo(x, y);
            this.ctx.stroke();
            
            // Dibujar punto en la posición actual para suavizar
            this.ctx.beginPath();
            this.ctx.arc(x, y, this.brushSize / 2, 0, Math.PI * 2);
            this.ctx.fill();
            
            // Continuar línea
            this.ctx.beginPath();
            this.ctx.moveTo(x, y);
            
            // Restaurar alpha
            this.ctx.globalAlpha = 1.0;
            
            // Marcar píxeles pintados
            this.markPaintedArea(x, y);
        },

        stopDrawing() {
            if (this.isDrawing) {
                this.isDrawing = false;
                this.ctx.beginPath(); // Limpiar el path
            }
        },

    
      // 🔧 VISTA PREVIA CORREGIDA - ROJO PURO
togglePreview() {
    this.showPreview = !this.showPreview;
    
    if (this.showPreview) {
        // Obtener imagen original
        const imageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
        const overlay = this.ctx.createImageData(this.canvas.width, this.canvas.height);
        
        // Copiar imagen original
        for (let i = 0; i < imageData.data.length; i++) {
            overlay.data[i] = imageData.data[i];
        }
        
        // 🎨 APLICAR ROJO PURO SEMI-TRANSPARENTE
        this.paintedPixels.forEach(coordStr => {
            const [x, y] = coordStr.split(',').map(Number);
            if (x >= 0 && x < this.canvas.width && y >= 0 && y < this.canvas.height) {
                const index = (y * this.canvas.width + x) * 4;
                
                if (index >= 0 && index < overlay.data.length) {
                    // Obtener colores originales
                    const originalR = overlay.data[index];
                    const originalG = overlay.data[index + 1];
                    const originalB = overlay.data[index + 2];
                    
                    // Mezclar con rojo semi-transparente (alpha blending)
                    const redAlpha = 0.6; // 60% de opacidad del rojo
                    const backgroundAlpha = 1 - redAlpha;
                    
                    overlay.data[index] = Math.round(255 * redAlpha + originalR * backgroundAlpha);     // R
                    overlay.data[index + 1] = Math.round(0 * redAlpha + originalG * backgroundAlpha); // G
                    overlay.data[index + 2] = Math.round(0 * redAlpha + originalB * backgroundAlpha); // B
                    // A se mantiene igual
                }
            }
        });
        
        this.ctx.putImageData(overlay, 0, 0);
        
    } else {
        // Restaurar imagen original
        this.setupCanvas();
    }
},

        // 🔧 MARCAR ÁREA PINTADA CON MEJOR PRECISIÓN
        markPaintedArea(centerX, centerY) {
            const radius = this.brushSize / 2;
            
            // Marcar en un área circular más precisa
            for (let dx = -radius; dx <= radius; dx++) {
                for (let dy = -radius; dy <= radius; dy++) {
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance <= radius) {
                        const x = Math.round(centerX + dx);
                        const y = Math.round(centerY + dy);
                        
                        if (x >= 0 && x < this.canvas.width && y >= 0 && y < this.canvas.height) {
                            this.paintedPixels.add(`${x},${y}`);
                        }
                    }
                }
            }
        },

        // Limpiar máscara
        clearMask() {
            if (this.canvas && this.ctx) {
                // Limpiar registro de píxeles pintados
                this.paintedPixels.clear();
                
                // Asegurar que no estamos en modo preview
                this.showPreview = false;
                
                // Redibujar solo la imagen original
                const img = new Image();
                img.onload = () => {
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                    this.ctx.drawImage(img, 0, 0, this.canvas.width, this.canvas.height);
                };
                img.src = this.imageSrc;
            }
        },

    
    
               // Procesar relleno - ACTUALIZADO para retornar validación
        processFill() {
            console.log('🎨 Iniciando processFill');
            
            // 🔧 VALIDACIONES QUE RETORNAN FALSE SI FALLAN
            if (!this.canvas || !this.imageLoaded) {
                alert('No hay imagen cargada');
                return false; // ❌ Validación fallida
            }

            if (this.paintedPixels.size === 0) {
                alert('No has marcado ninguna área para rellenar');
                return false; // ❌ Validación fallida
            }

            // 🔧 GUARDAR ESTADO ANTES DE PROCESAR
            const originalImageSrc = this.imageSrc;
            const originalPaintedPixels = new Set(this.paintedPixels);

            const img = new Image();
            img.onload = () => {
                const originalWidth = img.naturalWidth;
                const originalHeight = img.naturalHeight;
                const canvasWidth = this.canvas.width;
                const canvasHeight = this.canvas.height;
                
                console.log('📊 Dimensiones:', {
                    original: `${originalWidth}x${originalHeight}`,
                    canvas: `${canvasWidth}x${canvasHeight}`,
                    pixelesPintados: this.paintedPixels.size
                });

                // Crear imagen en tamaño ORIGINAL
                const imageCanvas = document.createElement('canvas');
                imageCanvas.width = originalWidth;
                imageCanvas.height = originalHeight;
                const imageCtx = imageCanvas.getContext('2d');
                imageCtx.drawImage(img, 0, 0, originalWidth, originalHeight);
                
                // Crear máscara en tamaño ORIGINAL
                const maskCanvas = document.createElement('canvas');
                maskCanvas.width = originalWidth;
                maskCanvas.height = originalHeight;
                const maskCtx = maskCanvas.getContext('2d');
                
                // Fondo negro
                maskCtx.fillStyle = 'black';
                maskCtx.fillRect(0, 0, originalWidth, originalHeight);
                
                // Escalar píxeles pintados al tamaño original
                const scaleX = originalWidth / canvasWidth;
                const scaleY = originalHeight / canvasHeight;
                
                maskCtx.fillStyle = 'white';
                this.paintedPixels.forEach(coordStr => {
                    const [x, y] = coordStr.split(',').map(Number);
                    const originalX = Math.floor(x * scaleX);
                    const originalY = Math.floor(y * scaleY);
                    const originalW = Math.ceil(scaleX);
                    const originalH = Math.ceil(scaleY);
                    
                    maskCtx.fillRect(originalX, originalY, originalW, originalH);
                });
                
                // OPTIMIZAR: Comprimir imágenes base64
                const imageBase64 = imageCanvas.toDataURL('image/jpeg', 0.8).split(',')[1];  
                const maskBase64 = maskCanvas.toDataURL('image/png').split(',')[1];          

                // 📊 LOG para debug
                console.log('📏 Tamaños base64:', {
                    imagen: `${Math.round(imageBase64.length / 1024)}KB`,
                    mascara: `${Math.round(maskBase64.length / 1024)}KB`,
                    total: `${Math.round((imageBase64.length + maskBase64.length) / 1024)}KB`
                });
                
                console.log('📤 Enviando datos en resolución original');
                
                // 🔧 MANTENER LA IMAGEN DESPUÉS DEL PROCESAMIENTO
                const fillComponent = this;
                
                // Escuchar cuando termine el procesamiento para restaurar la imagen
                const handleFillComplete = () => {
                    // Restaurar la imagen en el canvas después de un breve delay
                    setTimeout(() => {
                        if (fillComponent.imageSrc === originalImageSrc) {
                            fillComponent.setupCanvas();
                            // Limpiar las marcas de pincel después de procesar
                            fillComponent.paintedPixels.clear();
                        }
                    }, 500);
                    window.removeEventListener('fill-completed', handleFillComplete);
                };
                
                const handleFillError = () => {
                    // En caso de error, restaurar el estado original
                    fillComponent.imageSrc = originalImageSrc;
                    fillComponent.paintedPixels = originalPaintedPixels;
                    fillComponent.setupCanvas();
                    window.removeEventListener('fill-error', handleFillError);
                };
                
                window.addEventListener('fill-completed', handleFillComplete);
                window.addEventListener('fill-error', handleFillError);
                
                @this.rellenarImagenFlux({
                    imageBase64: imageBase64,
                    maskBase64: maskBase64
                });
            };
            
            img.src = this.imageSrc;
            
            return true; // ✅ Validaciones pasadas, procesar
        },
        // Resetear todo
        reset() {
            
    this.imageSrc = null;
    this.imageLoaded = false;
    this.dragOver = false;
    this.isDrawing = false;
    this.showPreview = false;
    this.canvas = null;
    this.ctx = null;
    this.paintedPixels.clear();
    
    // @this.call('limpiarImagenesTemporales');
           
        },
        resetFill() {
    this.imageSrc = null;
    this.imageLoaded = false;
    this.dragOver = false;
    this.isDrawing = false;
    this.showPreview = false;
    this.canvas = null;
    this.ctx = null;
    this.paintedPixels.clear();
    
    @this.call('limpiarImagenesTemporales');
}
    }
}

function expandImage() {
    return {
        // Propiedades principales
        imgDisplayWidth: 0,
        imgDisplayHeight: 0,
        overlayWidth: 0,
        overlayHeight: 0,
        imgOffsetX: 0,
        imgOffsetY: 0,
        dragOver: false,
        imageSrc: null,
        imageLoaded: false,
        aspect: 'landscape',
        imgWidth: 0,
        imgHeight: 0,
        customWidth: 800,
        customHeight: 450,
        overlayRealWidth: 800,
        overlayRealHeight: 450,
        sizeDropdownOpen: false,
        
        // Propiedades de zoom
        zoom: 1,
        minZoom: 0.5,
        maxZoom: 1.1,
        zoomStep: 0.1,

        // NUEVAS propiedades para el redimensionamiento
        isResizing: false,
        resizeDirection: null,
        startMousePos: { x: 0, y: 0 },
        startDimensions: { width: 0, height: 0 },
        minSize: 256,
        maxSize: 2048,

        // Método de zoom con rueda del mouse
        onWheelZoom(e) {
            e.preventDefault();
            if (e.deltaY < 0) {
                // Zoom in
                this.zoom = Math.min(this.zoom + this.zoomStep, this.maxZoom);
            } else {
                // Zoom out
                this.zoom = Math.max(this.zoom - this.zoomStep, this.minZoom);
            }
        },

        // Método para actualizar tamaños personalizados
        updateCustomSize(dimension, value) {
            if (dimension === 'width') {
                this.overlayRealWidth = value;
                this.customWidth = value;
            } else {
                this.overlayRealHeight = value;
                this.customHeight = value;
            }
            
            // Cambiar automáticamente a modo custom
            this.aspect = 'custom';
            this.applyCurrentDimensions();
        },

        // NUEVO: Iniciar redimensionamiento
        startResize(e, direction) {
            e.preventDefault();
            e.stopPropagation();
            
            this.isResizing = true;
            this.resizeDirection = direction;
            this.startMousePos = { x: e.clientX, y: e.clientY };
            this.startDimensions = { 
                width: this.overlayRealWidth, 
                height: this.overlayRealHeight 
            };
            
            // Cambiar a modo custom cuando se inicia el redimensionamiento
            this.aspect = 'custom';
            
            // Agregar event listeners para el movimiento y liberación del mouse
            document.addEventListener('mousemove', this.handleResize.bind(this));
            document.addEventListener('mouseup', this.stopResize.bind(this));
            
            // Prevenir selección de texto durante el arrastre
            document.body.style.userSelect = 'none';
            document.body.style.cursor = this.getResizeCursor(direction);
        },

        // NUEVO: Manejar el redimensionamiento durante el arrastre
        handleResize(e) {
            if (!this.isResizing) return;

            const deltaX = e.clientX - this.startMousePos.x;
            const deltaY = e.clientY - this.startMousePos.y;
            
            let newWidth = this.startDimensions.width;
            let newHeight = this.startDimensions.height;

            // Calcular nuevas dimensiones según la dirección
            switch (this.resizeDirection) {
                case 'right':
                    newWidth = Math.max(this.minSize, Math.min(this.maxSize, this.startDimensions.width + deltaX * 2));
                    break;
                case 'left':
                    newWidth = Math.max(this.minSize, Math.min(this.maxSize, this.startDimensions.width - deltaX * 2));
                    break;
                case 'bottom':
                    newHeight = Math.max(this.minSize, Math.min(this.maxSize, this.startDimensions.height + deltaY * 2));
                    break;
                case 'top':
                    newHeight = Math.max(this.minSize, Math.min(this.maxSize, this.startDimensions.height - deltaY * 2));
                    break;
            }

            // Asegurar que las dimensiones no sean menores que la imagen original
            newWidth = Math.max(newWidth, this.imgWidth);
            newHeight = Math.max(newHeight, this.imgHeight);

            // Actualizar las dimensiones
            this.overlayRealWidth = Math.round(newWidth);
            this.overlayRealHeight = Math.round(newHeight);
            this.customWidth = this.overlayRealWidth;
            this.customHeight = this.overlayRealHeight;

            // Aplicar los cambios visuales
            this.applyCurrentDimensions();
        },

        // NUEVO: Detener redimensionamiento
        stopResize() {
            if (!this.isResizing) return;

            this.isResizing = false;
            this.resizeDirection = null;

            // Remover event listeners
            document.removeEventListener('mousemove', this.handleResize.bind(this));
            document.removeEventListener('mouseup', this.stopResize.bind(this));

            // Restaurar estilos del body
            document.body.style.userSelect = '';
            document.body.style.cursor = '';
        },

        // NUEVO: Obtener cursor apropiado para la dirección
        getResizeCursor(direction) {
            switch (direction) {
                case 'top':
                case 'bottom':
                    return 'ns-resize';
                case 'left':
                case 'right':
                    return 'ew-resize';
                default:
                    return 'default';
            }
        },

        // Aplicar las dimensiones actuales al canvas
        applyCurrentDimensions() {
            if (!this.imageLoaded) return;

            const w = this.imgWidth;
            const h = this.imgHeight;
            const overlayW = this.overlayRealWidth;
            const overlayH = this.overlayRealHeight;

            // Escalado para que el área expandida quepa en el canvas
            const maxW = 600, maxH = 400;
            const scale = Math.min(maxW / overlayW, maxH / overlayH, 1);
            this.overlayWidth = Math.round(overlayW * scale);
            this.overlayHeight = Math.round(overlayH * scale);
            this.imgDisplayWidth = Math.round(w * scale);
            this.imgDisplayHeight = Math.round(h * scale);
            
            // Centrar la imagen original dentro del área expandida
            this.imgOffsetX = Math.round((this.overlayWidth - this.imgDisplayWidth) / 2);
            this.imgOffsetY = Math.round((this.overlayHeight - this.imgDisplayHeight) / 2);
        },

        // Cambiar la relación de aspecto
        // Cambiar la relación de aspecto - VERSIÓN CORREGIDA
setAspect(aspect) {
    // Detener cualquier redimensionamiento en curso
    if (this.isResizing) {
        this.stopResize();
    }

    this.aspect = aspect;
    if (!this.imageLoaded) return;
    
    // Dimensiones originales de la imagen
    let w = this.imgWidth;   // ancho original
    let h = this.imgHeight;  // alto original
    let overlayW, overlayH;  // nuevas dimensiones que vamos a calcular
    
    if (aspect === 'square') {
        // 🟦 CUADRADO (1:1)
        // Tomar la dimensión más grande y hacer ambas iguales
        const size = Math.max(w, h);
        overlayW = size;
        overlayH = size;
        
    } else if (aspect === 'landscape') {
        // 🖼️ LANDSCAPE (16:9) - MÁS ANCHO QUE ALTO
        
        // Paso 1: Asegurar ancho mínimo
        overlayW = Math.max(w, 1280);  // al menos 1280px de ancho
        
        // Paso 2: Calcular altura proporcional
        overlayH = Math.round(overlayW * 9 / 16);  // altura = ancho × (9/16)
        
        // Paso 3: Si la altura queda muy pequeña, ajustar
        if (overlayH < h) {
            overlayH = h;  // mantener al menos la altura original
            overlayW = Math.round(overlayH * 16 / 9);  // recalcular ancho
        }
        
    } else if (aspect === 'portrait') {
        // 📱 PORTRAIT (9:16) - MÁS ALTO QUE ANCHO
        
        // Paso 1: Asegurar altura mínima
        overlayH = Math.max(h, 1280);  // al menos 1280px de alto
        
        // Paso 2: Calcular ancho proporcional
        overlayW = Math.round(overlayH * 9 / 16);  // ✅ CORREGIDO: ancho = altura × (9/16)
        
        // Paso 3: Si el ancho queda muy pequeño, ajustar
        if (overlayW < w) {
            overlayW = w;  // mantener al menos el ancho original
            overlayH = Math.round(overlayW * 16 / 9);  // recalcular altura
        }
        
    } else if (aspect === 'custom') {
        // ⚙️ PERSONALIZADO
        overlayW = this.customWidth || this.overlayRealWidth || w;
        overlayH = this.customHeight || this.overlayRealHeight || h;
    }
    
    // Actualizar las propiedades
    this.overlayRealWidth = overlayW;
    this.overlayRealHeight = overlayH;
    this.customWidth = overlayW;
    this.customHeight = overlayH;

    // Aplicar cambios visuales
    this.applyCurrentDimensions();
},

        // Cuando la imagen se carga completamente
        onImageLoad() {
            const img = this.$refs.img;
            this.imgWidth = img.naturalWidth;
            this.imgHeight = img.naturalHeight;
            this.imageLoaded = true;
            this.setAspect(this.aspect);
        },

        // Getter para el estilo de la máscara
        get maskStyle() {
            const x0 = this.imgOffsetX;
            const y0 = this.imgOffsetY;
            const x1 = this.imgOffsetX + this.imgDisplayWidth;
            const y1 = this.imgOffsetY + this.imgDisplayHeight;
            const w = this.overlayWidth;
            const h = this.overlayHeight;
            return `
                background: rgba(59,130,246,0.15);
                border: 2px dashed #3b82f6;
                pointer-events: none;
                clip-path: polygon(
                    0% 0%, 0% 100%, 100% 100%, 100% 0%, 0% 0%,
                    0% ${y0}px, ${x0}px ${y0}px, ${x0}px ${y1}px, ${x1}px ${y1}px, ${x1}px ${y0}px, ${w}px ${y0}px, ${w}px 0%, 0% 0%
                );
            `;
        },

        // Manejo de drag & drop
        handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.loadFile(file);
            }
        },

        // Manejo de selección de archivo
        handleFile(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                this.loadFile(file);
            }
        },

        // Cargar archivo seleccionado
        loadFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imageSrc = e.target.result;
                this.imageLoaded = false;
                this.$nextTick(() => {
                    const img = this.$refs.img;
                    if (img && img.complete) {
                        this.onImageLoad();
                    } else if (img) {
                        img.onload = () => this.onImageLoad();
                    }
                });
            };
            reader.readAsDataURL(file);
        },

        // Aplicar valores personalizados (método legacy, opcional)
        applyCustom() {
            this.setAspect('custom');
        },

        // Resetear todo
        reset() {
            // Detener redimensionamiento si está activo
            if (this.isResizing) {
                this.stopResize();
            }

            this.imageSrc = null;
            this.imageLoaded = false;
            this.customWidth = 800;
            this.customHeight = 450;
            this.overlayRealWidth = 800;
            this.overlayRealHeight = 450;
            this.aspect = 'landscape';
            this.zoom = 1;
            this.sizeDropdownOpen = false;
            this.dragOver = false;
            
            // Resetear propiedades de redimensionamiento
            this.isResizing = false;
            this.resizeDirection = null;
            
            // Resetear dimensiones de display
            this.imgDisplayWidth = 0;
            this.imgDisplayHeight = 0;
            this.overlayWidth = 0;
            this.overlayHeight = 0;
            this.imgOffsetX = 0;
            this.imgOffsetY = 0;
            this.imgWidth = 0;
            this.imgHeight = 0;

            @this.call('limpiarImagenesTemporales');
        }
    }
}

document.addEventListener('livewire:initialized', () => {
        // Escuchar el evento errorOcurrido para asegurar que el mensaje se muestra
        @this.on('errorOcurrido', () => {
            // Forzar scroll al mensaje de error
            setTimeout(() => {
                const errorMsg = document.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Destacar visualmente el mensaje
                    errorMsg.classList.add('animate-pulse');
                    setTimeout(() => {
                        errorMsg.classList.remove('animate-pulse');
                    }, 2000);
                }
            }, 100);
        });
        
        // Verificar límites al cargar la página
        @this.call('verificarLimitesServicio');
    });
</script>



