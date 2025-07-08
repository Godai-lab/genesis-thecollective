@php 
$estilos = [
    ["id"=> "minimalist", "name"=> "minimalista"],
    ["id"=> "classic", "name"=> "clásico"],
    ["id"=> "modern", "name"=> "moderno"],
];
@endphp
<!-- step2.blade.php -->
<div id="step-2-form">
    <div id="step-2-form-content">
        <form id="step-2-form" method="POST" action="{{route('asistenteGrafica.generarLogo')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    ['label'=>'Selecciona el estilo:','type'=>'select', 'name'=>'style', 'id'=>'style', 'col'=>'sm:col-span-4', 'value'=>old('style'), 'attr'=>'validate-required=required validate-name=estilo', 'list'=>$estilos],
                    ['label'=>'Describe la idea de tu logo:','type'=>'textarea', 'name'=>'asistenteGraficaPrompt', 'id'=>'asistenteGraficaPrompt', 'col'=>'sm:col-span-4', 'value'=>old('asistenteGraficaPrompt'), 'attr'=>'data-validation-rules=required|max:800 data-field-name=describe_la_idea_de_tu_logo', 'description'=>'Ejemplo: una tienda de ropa para mascotas con el texto DOGUI con una ilustración de un cachorro con gafas de sol'],
                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Genera tu logo</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
            </x-dynamic-form>
            <div class="message text-sm text-red-600 dark:text-red-400 space-y-1"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btngenerarGenesis" class="form-button">Continuar</x-button-genesis>
            </div>
        </form>
    </div>
    
</div>