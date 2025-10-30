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
@endphp
<x-app-layout>
    <x-slot name="title">Brief - Agregar</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-gray-800 text-black
         leading-tight">
            {{ __('Generado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('generated.store') }}" method="POST" data-validate="true">
                            @csrf 
                            
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Cuenta','type'=>'select', 'name'=>'account', 'id'=>'account', 'col'=>'sm:col-span-4', 'value'=>old('account'), 'attr'=>'data-validation-rules=required|max:50', 'list'=>$accounts],
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'file_name', 'id'=>'file_name', 'col'=>'sm:col-span-4', 'value'=>old('file_name'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=nombre'],

                                    ['label'=>'País','type'=>'select', 'name'=>'country', 'id'=>'country', 'col'=>'sm:col-span-4', 'value'=>old('country'), 'attr'=>'data-validation-rules=required data-field-name=país', 'list'=>$paises],

                                    ['label'=>'Nombre de la marca','placeholder'=>'Escribe el nombre de la marca','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-3', 'value'=>old('name'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=nombre'],

                                    ['label'=>'Slogan','type'=>'text', 'name'=>'slogan', 'id'=>'slogan', 'col'=>'sm:col-span-3', 'value'=>old('slogan'), 'attr'=>'data-validation-rules=max:100 data-field-name=slogan'],
                                    
                                ]" 
                            >
                                <h2 class="text-base font-semibold leading-7 dark:text-gray-100 text-black">Creación de archivo</h2>
                                <p class="mt-1 text-sm leading-6 dark:text-gray-400 text-black">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            
                            <div class="mt-4">
                                <div>
                                    <label for="brief" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contenido</label>
                                    <input type="hidden" name="brief" data-validation-rules="required" id="brief" value="{{ old('brief') }}">
                                    <div id="editor-container-value"></div>
                                    <p id="error-value" class="text-red-500 text-sm mt-1 hidden"></p>
                                </div>
                            </div>
    
                            <div class="mt-4">
                                <div>
                                    <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valoración</label>
                                    <div class="rating mt-1" id="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-2xl cursor-pointer" data-rating="{{ $i }}"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="rating-value" data-validation-rules="required" data-field-name="valoración" value="{{ old('rating') }}">
                                </div>
                            </div>
    
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('generated.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.bubble.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa el editor Quill sin contenido previo
            var quill = new Quill('#editor-container-value', {
                theme: 'snow',
                placeholder: 'Copia y pega tu contenido aquí...',
            });
    
            // Sin contenido inicial
            
            const errorValue = document.getElementById('error-value');
            const valueInput = document.getElementById('brief');
            valueInput.value = '';
    
            // Actualiza el campo oculto con el contenido del editor
            quill.on('text-change', function() {
                valueInput.value = quill.getSemanticHTML();
            });
    
            // Gestión de calificación por estrellas
            const stars = document.querySelectorAll('.rating .fa-star');
            const ratingInput = document.getElementById('rating-value');
    
            // Establece la calificación en 5 por defecto
            // ratingInput.value = 0;
            // highlightStars(0);
    
            stars.forEach(star => {
                star.addEventListener('mouseover', selectStars);
                star.addEventListener('mouseout', resetStars);
                star.addEventListener('click', setRating);
            });
    
            function selectStars(e) {
                highlightStars(e.target.getAttribute('data-rating'));
            }
    
            function resetStars() {
                highlightStars(ratingInput.value);
            }
    
            function setRating(e) {
                const rating = e.target.getAttribute('data-rating');
                ratingInput.value = rating === ratingInput.value ? 0 : rating; // Permite deseleccionar
                highlightStars(ratingInput.value);
            }
    
            function highlightStars(rating) {
                stars.forEach(star => {
                    star.classList.toggle('text-yellow-400', star.getAttribute('data-rating') <= rating);
                });
            }

            
        });
    </script>
    
</x-app-layout>