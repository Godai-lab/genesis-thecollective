<!-- step2.blade.php -->
@php 
$paises = [
    ["id"=> "", "name"=> "Selecciona tu pais"],
    ["id"=> "Argentina", "name"=> "Argentina"],
    ["id"=> "Bolivia", "name"=> "Bolivia"],
    ["id"=> "Brasil", "name"=> "Brasil"],
    ["id"=> "Chile", "name"=> "Chile"],
    ["id"=> "Colombia", "name"=> "Colombia"],
    ["id"=> "Costa Rica", "name"=> "Costa Rica"],
    ["id"=> "República Dominicana", "name"=> "República Dominicana"],
    ["id"=> "Ecuador", "name"=> "Ecuador"],
    ["id"=> "El Salvador", "name"=> "El Salvador"],
    ["id"=> "Guatemala", "name"=> "Guatemala"],
    ["id"=> "Honduras", "name"=> "Honduras"],
    ["id"=> "México", "name"=> "México"],
    ["id"=> "Nicaragua", "name"=> "Nicaragua"],
    ["id"=> "Panamá", "name"=> "Panamá"],
    ["id"=> "Paraguay", "name"=> "Paraguay"],
    ["id"=> "Perú", "name"=> "Perú"],
    ["id"=> "Puerto Rico", "name"=> "Puerto Rico"],
    ["id"=> "Uruguay", "name"=> "Uruguay"],
];

    $availableModels = [
        'sonar-deep-research' => [
            'name' => 'Perplexity',
            'price' => 'de 0.80 a 1.25 usd aprox.',
            'priceUnit' => 'por investigación',
            'description' => 'Modelo de investigación rápida y eficiente para análisis básicos',
            'bestFor' => 'Investigaciones rápidas, análisis de mercado, resúmenes ejecutivos',
            'speed' => 'Rápido (3-5 min)',
            'quality' => 'Alta'
        ],
        'o4-mini-deep-research' => [
            'name' => 'OpenAI o4-mini Deep Research',
            'price' => 'de 0.50 a 1.00 usd aprox.',
            'priceUnit' => 'por investigación',
            'description' => 'Modelo equilibrado entre velocidad y profundidad de análisis',
            'bestFor' => 'Investigaciones medias, análisis detallados, reportes completos',
            'speed' => 'Medio (4-7 min)',
            'quality' => 'Muy alta'
        ],
        'o3-deep-research' => [
            'name' => 'OpenAI o3 Deep Research',
            'price' => 'de 1.50 a 4.00 usd aprox.',
            'priceUnit' => 'por investigación',
            'description' => 'Modelo de investigación profunda para análisis complejos y detallados',
            'bestFor' => 'Investigaciones complejas, análisis exhaustivos, estudios académicos',
            'speed' => 'Lento (+5 min)',
            'quality' => 'Excelente'
        ]
    ];
@endphp
<div id="step-2-form">
    <div id="step-2-form-content">
        {{-- <form id="step-2-form" method="POST" action="{{route('herramienta1.rellenaria')}}" enctype="multipart/form-data" data-validate="true"> --}}
            <form id="step-2-form" method="POST" action="{{route('investigacion.generarInvestigacion')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            
            <x-dynamic-form 
                :fields="[
                    ['label'=>'País','type'=>'select', 'name'=>'country', 'id'=>'country', 'col'=>'sm:col-span-4', 'value'=>old('country'), 'attr'=>'data-validation-rules=required data-field-name=país', 'list'=>$paises],

                    ['label'=>'Nombre de la marca','placeholder'=>'Escribe el nombre de la marca','type'=>'text', 'name'=>'brand', 'id'=>'brand', 'col'=>'sm:col-span-4', 'value'=>old('brand'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=marca'],

                    ['label'=>'Describe tu investigacion','placeholder'=>'Escribe tu instrucción sobre la investigación para tu marca','type'=>'textarea', 'name'=>'instruccion', 'id'=>'instruccion', 'col'=>'sm:col-span-4', 'value'=>old('instruccion'), 'attr'=>'data-validation-rules=required|max:1000 data-field-name=instruccion'],
                ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Investigación</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Rellena los campos importantes para iniciar con tu investigación y deja que la IA te ayude: </p>
            </x-dynamic-form>

            <!-- Campo oculto para el modelo seleccionado -->
            <input type="hidden" name="modelo" id="modelo" value="{{ old('modelo', 'sonar-deep-research') }}">

            <!-- Visualización del modelo seleccionado -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Modelo de Investigación <span class="text-red-500">*</span>
                </label>
                <div id="selected-model-display" class="text-sm text-gray-600">
                    @php 
                        $selectedModelKey = old('modelo', 'sonar-deep-research');
                        $selectedModel = $availableModels[$selectedModelKey] ?? $availableModels['sonar-deep-research'];
                    @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <strong class="text-lg text-gray-800">{{ $selectedModel['name'] }}</strong>
                            <span class="text-sm font-medium text-blue-600">{{ $selectedModel['price'] }} {{ $selectedModel['priceUnit'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $selectedModel['description'] }}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span><strong>Velocidad:</strong> {{ $selectedModel['speed'] }}</span>
                            <span><strong>Calidad:</strong> {{ $selectedModel['quality'] }}</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            <strong>Ideal para:</strong> {{ $selectedModel['bestFor'] }}
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- <div class="bg-blue-50 p-3 mb-4 rounded text-sm">
                <strong>Nota:</strong> Este proceso puede tardar entre 2 y 4 minutos. Por favor, espera mientras completamos la operación.
            </div>

            <div class="bg-gray-50 p-4 mb-4 rounded-lg text-sm border-l-4 border-blue-500">
                <h4 class="font-semibold text-gray-800 mb-2">Información sobre los modelos:</h4>
                <ul class="space-y-1 text-gray-600">
                    <li><strong>Perplexity:</strong> Respuesta rápida (1-2 min), ideal para investigaciones básicas</li>
                    <li><strong>o4-mini-deep-research:</strong> Equilibrio entre velocidad y profundidad (2-3 min)</li>
                    <li><strong>o3-deep-research:</strong> Análisis más profundo y detallado (3-5 min), recomendado para investigaciones complejas</li>
                </ul>
            </div> --}}
          
            <div class="message text-sm text-red-600 dark:text-red-400 space-y-1"></div>
            <div class="mt-6 flex items-center flex-wrap justify-start gap-x-6 gap-y-2">
                {{-- <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis> --}}
                <x-button-genesis type="button" id="investigarIA" class="form-button">Investigar</x-button-genesis>
            </div>
        </form>
    </div>

    <!-- Componente ModelSelector -->
    <livewire:generador.components.model-selector
        :models="$availableModels"
        :selected="old('modelo', 'sonar-deep-research')"
        title="Modelo de Investigación"
        wire:key="model-selector-investigacion" />
</div>

<!-- Script para capturar el evento y actualizar el formulario -->
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('image-generator-model-selected', (event) => {
        console.log(event);
        const selectedKey = event.key;
        const availableModels = @json($availableModels);
        const selectedModel = availableModels[selectedKey];
        
        if (selectedModel) {
            // Actualizar el campo del formulario
            document.getElementById('modelo').value = selectedKey;
            
            // Remover mensaje de error si existe
            const errorElement = document.getElementById('modelo-error');
            if (errorElement) {
                errorElement.style.display = 'none';
            }
            
            // Actualizar la visualización del modelo seleccionado
            const displayElement = document.getElementById('selected-model-display');
            if (displayElement) {
                displayElement.innerHTML = `
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <strong class="text-lg text-gray-800">${selectedModel.name}</strong>
                            <span class="text-sm font-medium text-blue-600">${selectedModel.price} ${selectedModel.priceUnit}</span>
                        </div>
                        <p class="text-sm text-gray-600">${selectedModel.description}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span><strong>Velocidad:</strong> ${selectedModel.speed}</span>
                            <span><strong>Calidad:</strong> ${selectedModel.quality}</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            <strong>Ideal para:</strong> ${selectedModel.bestFor}
                        </div>
                    </div>
                `;
            }
        }
    });
    
    // Disparar evento para sincronizar el selector de modelos con el valor por defecto
    // después de que Livewire esté completamente inicializado
    setTimeout(() => {
        Livewire.dispatch('image-generator-model-selected', { key: 'sonar-deep-research' });
    }, 100);
});

// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('step-2-form');
    const modeloField = document.getElementById('modelo');
    
    // Función para validar el modelo
    function validateModelo() {
        const modeloValue = modeloField.value.trim();
        const errorElement = document.getElementById('modelo-error');
        
        if (!modeloValue || modeloValue === '') {
            if (!errorElement) {
                // Crear mensaje de error si no existe
                const errorDiv = document.createElement('div');
                errorDiv.id = 'modelo-error';
                errorDiv.className = 'text-sm text-red-600 dark:text-red-400 mt-2 p-2 bg-red-50 border border-red-200 rounded';
                errorDiv.textContent = '⚠️ Debes seleccionar un modelo de investigación';
                modeloField.parentNode.appendChild(errorDiv);
            } else {
                errorElement.style.display = 'block';
            }
            return false;
        } else {
            if (errorElement) {
                errorElement.style.display = 'none';
            }
            return true;
        }
    }
    
    // Validar al enviar el formulario
    form.addEventListener('submit', function(e) {
        if (!validateModelo()) {
            e.preventDefault();
            // Hacer scroll al campo con error
            modeloField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        
        // Si pasa la validación, continuar con el envío
        console.log('Formulario válido, enviando...');
    });
    
    // Validar cuando se hace clic en el botón de investigar
    const investigarButton = document.getElementById('investigarIA');
    if (investigarButton) {
        investigarButton.addEventListener('click', function(e) {
            if (!validateModelo()) {
                e.preventDefault();
                // Hacer scroll al campo con error
                modeloField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
        });
    }
});
</script>