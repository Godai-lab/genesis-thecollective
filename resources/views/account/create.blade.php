@php 
$categorias = [
    
    ["id" => "", "name" => "Selecciona una categoría"],
    ["id" => "Alimentación y Bebidas", "name" => "Alimentación y Bebidas"],
    ["id" => "Moda y Belleza", "name" => "Moda y Belleza"],
    ["id" => "Salud y Bienestar", "name" => "Salud y Bienestar"],
    ["id" => "Tecnología y Electrónica", "name" => "Tecnología y Electrónica"],
    ["id" => "Educación y Formación", "name" => "Educación y Formación"],
    ["id" => "Turismo y Entretenimiento", "name" => "Turismo y Entretenimiento"],
    ["id" => "Automotriz y Transporte", "name" => "Automotriz y Transporte"],
    ["id" => "Bienes Raíces y Construcción", "name" => "Bienes Raíces y Construcción"],
    ["id" => "Servicios Profesionales", "name" => "Servicios Profesionales"],
    ["id" => "Deportes y Fitness", "name" => "Deportes y Fitness"],
    ["id" => "Salud y Medicina", "name" => "Salud y Medicina"],
    ["id" => "E-commerce y Retail", "name" => "E-commerce y Tiendas Online"],
    ["id" => "Bienestar y Estilo de Vida", "name" => "Bienestar y Estilo de Vida"],
    ["id" => "Hogar y Decoración", "name" => "Hogar y Decoración"],
    ["id" => "Servicios Financieros", "name" => "Servicios Financieros"],
    ["id" => "Energía y Sostenibilidad", "name" => "Energía y Sostenibilidad"],
    ["id" => "Agronegocios y Agroindustria", "name" => "Agronegocios y Agroindustria"],
    ["id" => "Medios, Comunicación y Contenido Digital", "name" => "Medios, Comunicación y Contenido Digital"],
    ["id" => "Logística y Cadena de Suministro", "name" => "Logística y Cadena de Suministro"],
    ["id" => "Emprendimiento e Innovación", "name" => "Emprendimiento e Innovación"],
    ["id" => "Arte, Cultura y Creatividad", "name" => "Arte, Cultura y Creatividad"],
    ["id" => "Negocios B2B y Servicios Industriales", "name" => "Negocios B2B y Servicios Industriales"],
    ["id" => "Gaming y eSports", "name" => "Gaming y eSports"],
    ["id" => "Otra", "name" => "Otra categoría"],
];


@endphp
<x-app-layout>
    <x-slot name="title">Génesis - Cuenta - Crear</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('account.store')}}" method="POST" data-validate="true">
                            @csrf 
        
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('name'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=nombre'],
                                    ['label'=>'Categoría','type'=>'select', 'name'=>'category', 'id'=>'category', 'col'=>'sm:col-span-4', 'value'=>old('category'), 'attr'=>'data-validation-rules=required data-field-name=categoria', 'list'=>$categorias],
                                    ['label'=>'Descripción','type'=>'text', 'name'=>'description', 'id'=>'description', 'col'=>'sm:col-span-4', 'value'=>old('description'), 'attr'=>'data-validation-rules=max:200 data-field-name=descripción'],
                                    ['label'=>'Estado','type'=>'switch', 'name'=>'status', 'id'=>'status', 'col'=>'sm:col-span-4', 'value'=>old('status')]
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-black">Registro de cuenta</h2>
                                <p class="mt-1 text-sm leading-6 text-black">Por favor, complete los siguientes campos:</p>
                            
                            </x-dynamic-form>
       
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('account.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectCategory = document.getElementById("category");
        const otherCategoryInput = document.getElementById("otherCategoryInput");
    
        selectCategory.addEventListener("change", function() {
            if (this.value === "Otra") { // que el valor coincide con el de "Otra categoría"
                otherCategoryInput.classList.remove("hidden"); 
            } else {
                otherCategoryInput.classList.add("hidden");
            }
        });
    });
    </script>