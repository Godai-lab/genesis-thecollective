<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Marca') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('brand.update',$brand->id)}}" method="POST" onSubmit="return  ValidarCampos(this)">
                            @csrf 
                            @method('PUT') 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('name', $brand->name), 'attr'=>'validate-required=required validate-name=nombre validate-max=50'],

                                    ['label'=>'Descripción','type'=>'textarea', 'name'=>'description', 'id'=>'description', 'col'=>'sm:col-span-4', 'value'=>old('description', $brand->description), 'attr'=>'validate-name=descripción validate-max=200'],

                                    ['label'=>'País','type'=>'select', 'name'=>'country', 'id'=>'country', 'col'=>'sm:col-span-4', 'value'=>old('country', $brand->country), 'attr'=>'validate-required=required validate-name=país', 'list'=>$countries],

                                    ['label'=>'Id asistente','type'=>'textarea', 'name'=>'assistant', 'id'=>'assistant', 'col'=>'sm:col-span-4', 'value'=>old('assistant', $brand->assistant), 'attr'=>'rows=1 validate-name=id asistente validate-max=200'],

                                    ['label'=>'Cuenta','type'=>'select', 'name'=>'account_id', 'id'=>'account_id', 'col'=>'sm:col-span-4', 'value'=>old('account_id', $brand->account_id), 'attr'=>'validate-required=required validate-name=cuenta', 'list'=>$accounts],

                                    ['label'=>'Estado','type'=>'switch', 'name'=>'status', 'id'=>'status', 'col'=>'sm:col-span-4', 'value'=>old('status', $brand->status)]
                                    ]"
                                >
                                <h2 class="text-base font-semibold leading-7 text-gray-100">Actualización de marca</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-400">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('brand.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>