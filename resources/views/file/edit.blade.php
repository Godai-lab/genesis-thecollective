<x-app-layout>
    <x-slot name="title">Génesis - Archivo - Editar</x-slot>
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
                        <form action="{{ route('account.file.update',[$account->id,$file->id])}}" method="POST" onSubmit="return  ValidarCampos(this)">
                            @csrf 
                            @method('PUT') 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('name', $file->name), 'attr'=>'disabled', 'class'=>'bg-gray-800 ring-gray-800'],
                                    ['label'=>'Tipo','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('file_type', $file->file_type), 'attr'=>'disabled', 'class'=>'bg-gray-800 ring-gray-800'],
                                    ['label'=>'Tamaño','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('file_size', $file->file_size), 'attr'=>'disabled', 'class'=>'bg-gray-800 ring-gray-800'],
                                    ['label'=>'Contenido','type'=>'textarea', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('file_size', $file->content), 'attr'=>'disabled', 'class'=>'bg-gray-800 ring-gray-800'],
                                    ['label'=>'Estado','type'=>'switch', 'name'=>'status', 'id'=>'status', 'col'=>'sm:col-span-4', 'value'=>old('status', $file->status)],
                                    ['label'=>'Leer desde base de datos','type'=>'switch', 'name'=>'read_from_db', 'id'=>'read_from_db', 'col'=>'sm:col-span-4', 'value'=>old('read_from_db', $file->read_from_db), 'description'=>'Activado: Leer siempre desde la base de datos. | Desactivado: Leer siempre desde el archivo físico.']
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-gray-100">Actualización de archivo</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-400">Solo se puede actualizar el estado:</p>
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