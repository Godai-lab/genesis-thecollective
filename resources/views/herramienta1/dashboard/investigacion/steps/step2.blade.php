<!-- step2.blade.php -->
@php 
$paises = [
    ["id"=> "", "name"=> "Selecciona tu pais"],
    ["id"=> "Argentina", "name"=> "Argentina"],
    ["id"=> "Bolivia", "name"=> "Bolivia"],
    ["id"=> "Brasil", "name"=> "Brasil"],
    ["id"=> "Chile", "name"=> "Chile"],
    ["id"=> "Colombia", "name"=> "Colombia"],
    ["id"=> "Costa Rica", "name"=> "Costa Rica"],
    ["id"=> "República Dominicana", "name"=> "República Dominicana"],
    ["id"=> "Ecuador", "name"=> "Ecuador"],
    ["id"=> "El Salvador", "name"=> "El Salvador"],
    ["id"=> "Guatemala", "name"=> "Guatemala"],
    ["id"=> "Honduras", "name"=> "Honduras"],
    ["id"=> "México", "name"=> "México"],
    ["id"=> "Nicaragua", "name"=> "Nicaragua"],
    ["id"=> "Panamá", "name"=> "Panamá"],
    ["id"=> "Paraguay", "name"=> "Paraguay"],
    ["id"=> "Perú", "name"=> "Perú"],
    ["id"=> "Puerto Rico", "name"=> "Puerto Rico"],
    ["id"=> "Uruguay", "name"=> "Uruguay"],
];
@endphp
<div id="step-2-form">
    <div id="step-2-form-content">
        {{-- <form id="step-2-form" method="POST" action="{{route('herramienta1.rellenaria')}}" enctype="multipart/form-data" data-validate="true"> --}}
            <form id="step-2-form" method="POST" action="{{route('investigacion.generarInvestigacion')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            
            <x-dynamic-form 
                :fields="[
                    ['label'=>'País','type'=>'select', 'name'=>'country', 'id'=>'country', 'col'=>'sm:col-span-4', 'value'=>old('country'), 'attr'=>'data-validation-rules=required data-field-name=país', 'list'=>$paises],

                    ['label'=>'Nombre de la marca','placeholder'=>'Escribe el nombre de la marca','type'=>'text', 'name'=>'brand', 'id'=>'brand', 'col'=>'sm:col-span-4', 'value'=>old('brand'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=marca'],
                    ['label'=>'Describe tu investigacion','placeholder'=>'Escribe tu instrucción sobre la investigación para tu marca','type'=>'textarea', 'name'=>'instruccion', 'id'=>'instruccion', 'col'=>'sm:col-span-4', 'value'=>old('instruccion'), 'attr'=>'data-validation-rules=required|max:400 data-field-name=instruccion'],

                    ]"
                    >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Investigación</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Rellena los campos importantes para iniciar con tu investigación y deja que la IA te ayude: </p>
            </x-dynamic-form>
            
            <div class="bg-blue-50 p-3 mb-4 rounded text-sm">
                <strong>Nota:</strong> Este proceso puede tardar entre 5 y 15 minutos dependiendo de la complejidad de la investigación. Por favor, espera mientras completamos la operación.
            </div>
          
            <div class="message text-sm text-red-600 dark:text-red-400 space-y-1"></div>
            <div class="mt-6 flex items-center flex-wrap justify-start gap-x-6 gap-y-2">
                {{-- <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis> --}}
                <x-button-genesis type="button" id="investigarIA" class="form-button">Investigar</x-button-genesis>
            </div>
        </form>
    </div>
    
</div>