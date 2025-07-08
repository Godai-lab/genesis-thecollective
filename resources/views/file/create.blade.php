<x-app-layout>
    <x-slot name="title">Génesis - Archivo - Crear</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Archivo -> '.$account->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('account.file.store',$account->id)}}" method="POST" enctype="multipart/form-data" onSubmit="return  ValidarCampos(this)">
                            @csrf 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('name'), 'attr'=>'validate-required=required validate-name=nombre validate-max=50'],
                                    ['label'=>'Archivo','type'=>'file', 'name'=>'file', 'id'=>'file', 'col'=>'sm:col-span-4', 'value'=>old('file'), 'attr'=>''],
                                    ['label'=>'Estado','type'=>'switch', 'name'=>'status', 'id'=>'status', 'col'=>'sm:col-span-4', 'value'=>old('status')],
                                    ['label'=>'Leer desde base de datos','type'=>'switch', 'name'=>'read_from_db', 'id'=>'read_from_db', 'col'=>'sm:col-span-4', 'value'=>old('read_from_db'), 'description'=>'Activado: Leer siempre desde la base de datos. | Desactivado: Leer siempre desde el archivo físico.']
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-gray-100">Registro de archivo</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-400">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('account.file.index',$account->id)" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>