<x-app-layout>
    <x-slot name="title">Génesis - Subscripción - Editar</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ciudad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('subscription.update',$subscription->id)}}" method="POST">
                            @csrf 
                            @method('PUT') 
                            @php
                                $start_date = \Carbon\Carbon::parse($subscription->start_date);
                                $end_date = \Carbon\Carbon::parse($subscription->end_date);
                            @endphp
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Usuario','type'=>'select', 'name'=>'user_id', 'id'=>'user_id', 'col'=>'sm:col-span-4', 'value'=>old('user_id', $subscription->user_id), 'list'=>$users],
                                    ['label'=>'Plan','type'=>'select', 'name'=>'plan_id', 'id'=>'plan_id', 'col'=>'sm:col-span-4', 'value'=>old('plan_id', $subscription->plan_id), 'list'=>$plans],
                                    ]"
                                >
                                <h2 class="text-base font-semibold leading-7 text-black">Actualización de ciudad</h2>
                                <p class="mt-1 text-sm leading-6 text-black">Por favor, complete los siguientes campos:</p>
                            </x-dynamic-form>
                            
                            <div class="mt-6 flex items-center justify-end gap-x-6">
                                <x-dynamic-button-link :type="'cancel'" :action="route('subscription.index')" />
                                <x-dynamic-button-link :type="'save'" />
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>