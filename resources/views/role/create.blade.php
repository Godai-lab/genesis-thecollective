<x-app-layout>
    <x-slot name="title">Génesis - Rol - Crear</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('role.store')}}" method="POST" data-validate="true">
                            @csrf 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('name'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=nombre'],
                                    ['label'=>'Slug','type'=>'text', 'name'=>'slug', 'id'=>'slug', 'col'=>'sm:col-span-4', 'value'=>old('slug'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=slug'],
                                    ['label'=>'Descripción','type'=>'textarea', 'name'=>'description', 'id'=>'description', 'col'=>'sm:col-span-4', 'value'=>old('description'), 'attr'=>'data-validation-rules=max:250 data-field-name=descripción'],
                                    ['label'=>'Control total','type'=>'switch', 'name'=>'full_access', 'id'=>'full_access', 'col'=>'sm:col-span-4', 'value'=>old('full_access')]
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-black">Registro de rol</h2>
                                <p class="mt-1 text-sm leading-6 text-black">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Permisos:','type'=>'checklist', 'name'=>'permissions', 'id'=>'permissions', 'col'=>'col-span-full', 'value'=>old('permissions'), 'list'=>$permissions]
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-black pt-4">Registro de permisos</h2>
                                <p class="mt-1 text-sm leading-6 text-black">Por favor, seleccione los permisos para este rol:</p>
                            </x-dynamic-form>
                            
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('role.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>