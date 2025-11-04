
<!-- step2.blade.php -->
<style>
    .category-tag-social.selected {
        background-color: #000 !important;
        border-color: #000 !important;
        color: #fff !important;
    }
    
    .dark .category-tag-social.selected {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: #fff !important;
    }
    
    .category-tag-social:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const maxSelections = 2;
        const selectedCategories = new Set();
        const categoryTags = document.querySelectorAll('.category-tag-social');
        const selectedCountSpan = document.getElementById('selected-count-social');
        
        categoryTags.forEach(tag => {
            tag.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category');
                
                if (this.classList.contains('selected')) {
                    // Deseleccionar
                    this.classList.remove('selected');
                    selectedCategories.delete(categoryId);
                } else {
                    // Seleccionar si no se ha alcanzado el límite
                    if (selectedCategories.size < maxSelections) {
                        this.classList.add('selected');
                        selectedCategories.add(categoryId);
                    } else {
                        alert('Solo puedes seleccionar hasta 2 categorías');
                    }
                }
                
                // Actualizar contador
                selectedCountSpan.textContent = selectedCategories.size;
                
                // Actualizar campos hidden
                updateHiddenInputs();
            });
        });
        
        function updateHiddenInputs() {
            // Remover inputs hidden anteriores
            document.querySelectorAll('input[name="categories[]"]').forEach(input => input.remove());
            
            // Crear nuevos inputs hidden para cada categoría seleccionada
            const form = document.getElementById('step-2-form');
            selectedCategories.forEach(categoryId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'categories[]';
                input.value = categoryId;
                form.appendChild(input);
            });
        }
    });
</script>
<div id="step-2-form">
    <div id="step-2-form-content">
        <form id="step-2-form" method="POST" action="{{route('asistenteSocialMedia.generarPrompt')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    ['label'=>'Brief','type'=>'select', 'name'=>'brief', 'id'=>'brief', 'col'=>'sm:col-span-4', 'value'=>old('brief'), 'attr'=>'', 'list'=>[]],
                    ['label'=>'Genesis','type'=>'select', 'name'=>'genesis', 'id'=>'genesis', 'col'=>'sm:col-span-4', 'value'=>old('genesis'), 'attr'=>'', 'list'=>[]],
                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Genera tu creatividad</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
            </x-dynamic-form>
            <x-dynamic-form 
                :fields="[
                    
                    ['label'=>'Describe tu social media','type'=>'textarea', 'name'=>'asistenteSocialMediaPrompt', 'id'=>'asistenteSocialMediaPrompt', 'col'=>'sm:col-span-4', 'value'=>old('asistenteSocialMediaPrompt'), 'attr'=>'data-validation-rules=required|max:800 data-field-name=describe_tu_social_media'],
                    ]"
                >
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Puedes elegir entre un brief o un genesis solo uno</p>
            </x-dynamic-form>
            
            {{-- Selector de categorías con badges --}}
            <div class="space-y-12">
                <div class="border-b border-gray-700 pb-12 mb-6">
                    <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Enfoques de contenido (opcional)</h2>
                    <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Selecciona hasta 2 enfoques para obtener propuestas más específicas</p>
                    
                    <div class="mt-6">
                        <div id="categories-container-social" class="flex flex-wrap gap-2">
                            @foreach($categories as $category)
                                <button type="button" 
                                    class="category-tag-social px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 border-2 border-gray-600 bg-transparent text-black dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 cursor-pointer"
                                    data-category="{{ $category['id'] }}"
                                    data-vector="{{ $category['vector_store'] }}">
                                    {{ $category['name'] }}
                                </button>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-500">
                            <span id="selected-count-social">0</span> / 2 seleccionados
                        </p>
                    </div>
                </div>
            </div>
            
           
            <div class="message text-sm text-red-600 dark:text-red-400 space-y-1"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btngenerarCreatividad" class="form-button">Continuar</x-button-genesis>
            </div>
        </form>
    </div>
    
</div>