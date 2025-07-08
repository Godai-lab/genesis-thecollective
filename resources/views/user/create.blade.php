<x-app-layout>
    <x-slot name="title">Génesis - Usuarios - Crear</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('user.store')}}" method="POST" data-validate="true">
                            @csrf 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-3', 'value'=>old('name'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=nombre'],
                                    ['label'=>'Usuario','type'=>'text', 'name'=>'username', 'id'=>'username', 'col'=>'sm:col-span-3', 'value'=>old('username'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=usuario'],
                                    ['label'=>'Correo electrónico','type'=>'email', 'name'=>'email', 'id'=>'email', 'col'=>'sm:col-span-3', 'value'=>old('email'), 'attr'=>'data-validation-rules=required|email|max:80 data-field-name=correo_electrónico'],
                                    ['label'=>'Contraseña','type'=>'password', 'name'=>'password', 'id'=>'password', 'col'=>'sm:col-span-3', 'value'=>'', 'attr'=>'autocomplete=off data-validation-rules=required|min:8|max:80|confirmed data-field-name=contraseña'],
                                    ['label'=>'Confirmar contraseña','type'=>'password', 'name'=>'password_confirmation', 'id'=>'password_confirmation', 'col'=>'sm:col-span-3', 'value'=>'', 'attr'=>'autocomplete=off data-validation-rules=required|max:80 data-field-name=confirmar_contraseña'],
                                    ['label'=>'Rol','type'=>'select', 'name'=>'role', 'id'=>'role', 'col'=>'sm:col-span-4', 'value'=>old('role'), 'attr'=>'data-validation-rules=required data-field-name=rol', 'list'=>$roles],
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-black">Registro de usuario</h2>
                                <p class="mt-1 text-sm leading-6 text-black">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Cuentas:','type'=>'checklist', 'name'=>'accounts', 'id'=>'accounts', 'col'=>'col-span-full', 'value'=>old('accounts'), 'list'=>$accounts]
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-black pt-4">Registro de cuentas</h2>
                                <p class="mt-1 text-sm leading-6 text-black">Por favor, seleccione las cuentas para este usuario:</p>
                            </x-dynamic-form>
                            
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('user.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>