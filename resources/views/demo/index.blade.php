<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Demo') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('upload')}}" method="POST" enctype="multipart/form-data" onSubmit="return  ValidarCampos(this)">
                            @csrf 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Nombre','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-4', 'value'=>old('name'), 'attr'=>'validate-required=required validate-name=nombre validate-max=50'],

                                    ['label'=>'Archivo','type'=>'file', 'name'=>'file', 'id'=>'file', 'col'=>'sm:col-span-4', 'value'=>old('file'), 'attr'=>''],

                                    ['label'=>'Url','type'=>'text', 'name'=>'url', 'id'=>'url', 'col'=>'sm:col-span-4', 'value'=>old('url'), 'attr'=>''],

                                    ['label'=>'Cuenta','type'=>'select', 'name'=>'account_id', 'id'=>'account_id', 'col'=>'sm:col-span-4', 'value'=>old('account_id'), 'attr'=>'validate-required=required validate-name=cuenta', 'list'=>$accounts],

                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-gray-100">Registro de demo</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-400">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('demo.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>