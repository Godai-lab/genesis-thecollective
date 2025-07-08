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