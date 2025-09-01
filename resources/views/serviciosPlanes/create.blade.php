<x-app-layout>
    <x-slot name="title">Génesis - ServiciosPlanes - Crear</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ServiciosPlanes') }}
        </h2>
    </x-slot>
@php 

$servicios = [
    ["id" => "imagen-gemini4", "name" => "Gemini Image 4"],
    ["id" => "imagen-gemini3", "name" => "Gemini Image 3"],
    ["id" => "imagen-openai", "name" => "OpenAI GPT Image"],
    ["id" => "imagen-flux-kontext-max", "name" => "Flux Kontext Max"],
    ["id" => "imagen-flux-kontext-pro", "name" => "Flux Kontext Pro"],
    ["id" => "imagen-flux-pro", "name" => "Flux Pro"],
    ["id" => "imagen-flux-ultra", "name" => "Flux Ultra"],
    // Servicios de Generación de Videos
    ["id" => "video-veo2", "name" => "Video - Gemini Veo2"],
    ["id" => "video-runway-gen3", "name" => "Video - Runway Gen3 Alpha Turbo"],
    ["id" => "video-runway-gen4", "name" => "Video - Runway Gen4 Turbo"],
    ["id" => "video-luma-ray-flash", "name" => "Video - Luma Ray Flash"],
    ["id" => "video-luma-ray2", "name" => "Video - Luma Ray2"],
    
    // Servicios de Edición de Imágenes
    ["id" => "edicion-flux-expand", "name" => "Edición - Flux Expandir Imagen"],
    ["id" => "edicion-flux-fill", "name" => "Edición - Flux Rellenar Imagen"],
    ["id" => "edit-video", "name" => "Edición - Video Editor"],
    // Servicios  de Generacion de prompts
    ["id" => "prompt-generation", "name" => "Prompt - Generar Prompt-OpenAi"],
];

@endphp
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="max-w-2xl mx-auto">
                        <form action="{{ route('planServiceLimits.store')}}" method="POST" data-validate="true">
                            @csrf 
                            <x-dynamic-form 
                                :fields="[
                                    ['label'=>'Servicios','type'=>'select', 'name'=>'service_name', 'id'=>'service_name', 'col'=>'sm:col-span-4', 'value'=>old('service_name'), 'list'=>$servicios],
                                    ['label'=>'Plan','type'=>'select', 'name'=>'plan_id', 'id'=>'plan_id', 'col'=>'sm:col-span-4', 'value'=>old('plan_id'), 'list'=>$plans],
                                    ['label'=>'N° Peticiones','type'=>'number', 'name'=>'monthly_limit', 'id'=>'monthly_limit', 'col'=>'sm:col-span-4', 'value'=>old('monthly_limit')],
                                ]" 
                            >
                                <h2 class="text-base font-semibold leading-7 text-black">Registro de ServiciosPlanes</h2>
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