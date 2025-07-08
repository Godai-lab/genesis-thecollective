<x-app-layout>
    <x-slot name="title">Génesis - ServiciosPlanes - Editar</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Ciudad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('planServiceLimits.update',$planservice->id)}}" method="POST" data-validate="true">
                            @csrf 
                            @method('PUT') 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Servicio','type'=>'text', 'name'=>'service_name', 'id'=>'service_name', 'col'=>'sm:col-span-3', 'value'=>old('service_name', $planservice->service_name), 'attr'=>'data-validation-rules=required|max:50 data-field-name=nombre'],
                                    ['label'=>'Plan','type'=>'select', 'name'=>'plan_id', 'id'=>'plan_id', 'col'=>'sm:col-span-4', 'value'=>old('plan_id', $planservice->plan_id), 'attr'=>'data-validation-rules=required data-field-name=plan','list'=>$planes],
                                    ['label'=>'Cant.Peticiones','type'=>'number', 'name'=>'monthly_limit', 'id'=>'monthly_limit', 'col'=>'sm:col-span-3', 'value'=>old('monthly_limit', $planservice->monthly_limit), 'attr'=>'data-validation-rules=required|max:50 data-field-name=peticiones'],
                                    ]" 
                                >
                                <h2 class="text-base font-semibold leading-7 text-black">Actualización de Servicio-Plan</h2>
                                <p class="mt-1 text-sm leading-6 text-black">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            
                            
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('planServiceLimits.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>