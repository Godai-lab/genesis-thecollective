<x-app-layout>
    <x-slot name="title">Génesis - Generado - Editar</x-slot>
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
                        <form action="{{ route('generated.update',$generated->id)}}" method="POST" data-validate="true">
                            @csrf 
                            @method('PUT') 
                            <x-dynamic-form 
                                :fields="[

                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('name', $generated->name), 'attr'=>'data-validation-rules=required|max:50 data-field-name=nombre'],
                                    
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 dark:text-gray-100 text-black">Actualización de archivo</h2>
                                <p class="mt-1 text-sm leading-6 dark:text-gray-400 text-black">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            <div class="mt-4">
                                <div>
                                    <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contenido</label>
                                    <input type="hidden" name="value" id="value" value="{{old('value', $generated->value) }}">
                                    <div id="editor-container-value"></div>
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
                                    <input type="hidden" name="rating" id="rating-value" data-validation-rules="required" data-field-name="valoración" value="{{ old('rating', $generated->rating) }}">
                                </div>
                            </div>
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                {{-- <x-dynamic-button-link :type="'cancel'" :action="route('generated.index')" />
                                <x-dynamic-button-link :type="'save'" /> --}}
                                <x-button-genesis type="button" href="{{route('generated.index')}}"  class="form-button">Cancelar</x-button-genesis>
                                <x-button-genesis type="submit"  class="form-button">Guardar</x-button-genesis>
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
            var quill = new Quill('#editor-container-value', {
                theme: 'snow'
            });
            
            var value = @json(old('value', $generated->value));

            quill.clipboard.dangerouslyPasteHTML(value);

            quill.on('text-change', function() {
                const valueInput = document.getElementById('value');
                valueInput.value = quill.getSemanticHTML();
            });

            const stars = document.querySelectorAll('.rating .fa-star');
            const ratingInput = document.getElementById('rating-value');

            stars.forEach(star => {
                star.addEventListener('mouseover', selectStars);
                star.addEventListener('mouseout', unselectStars);
                star.addEventListener('click', setRating);
            });

            function selectStars(e) {
                const rating = e.target.getAttribute('data-rating');
                highlightStars(rating);
            }

            function unselectStars() {
                highlightStars(ratingInput.value);
            }

            function setRating(e) {
                const rating = e.target.getAttribute('data-rating');
                ratingInput.value = rating;
                highlightStars(rating);
            }

            function highlightStars(rating) {
                stars.forEach(star => {
                    star.classList.toggle('text-yellow-400', star.getAttribute('data-rating') <= rating);
                });
            }

            highlightStars(ratingInput.value);
        });
    </script>
</x-app-layout>