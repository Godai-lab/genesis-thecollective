<x-app-layout>
    <x-slot name="title">Génesis - Cuenta - Configurar</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-black leading-tight">
            {{ __('Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('config.update',$account->id)}}" method="POST" onSubmit="return  ValidarCampos(this)">
                            @csrf 
                            @method('PUT') 
                            @php 
                            $openai_api_key = '';
                            $openai_api_key_des = '';
                            $model_openai = '';
                            $model_openai_des = '';
                            if($account->configs){
                                foreach ($account->configs as $config) {
                                    if ($config['key'] === 'openai_api_key') {
                                        $openai_api_key = $config['value'];
                                        $openai_api_key_des = $config['description'];
                                    }elseif ($config['key'] === 'model_openai') {
                                        $model_openai = $config['value'];
                                        $model_openai_des = $config['description'];
                                    }
                                }
                            }
                            @endphp
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Openai API key','type'=>'textarea', 'name'=>'openai_api_key', 'id'=>'openai_api_key', 'col'=>'sm:col-span-4', 'value'=>old('openai_api_key', $openai_api_key), 'attr'=>'validate-max=250', 'description'=>$openai_api_key_des],
                                    ['label'=>'Modelo OpenAi','type'=>'textarea', 'name'=>'model_openai', 'id'=>'model_openai', 'col'=>'sm:col-span-4', 'value'=>old('model_openai', $model_openai), 'attr'=>'validate-max=250', 'description'=>$model_openai_des]
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-black">Actualización de cuenta -> {{$account->name}}</h2>
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