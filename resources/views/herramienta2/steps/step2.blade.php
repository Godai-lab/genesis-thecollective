<!-- step2.blade.php -->
<div id="step-2-form">
    <div id="step-2-form-content">
        <form id="step2Form" method="POST" action="{{route('herramienta2.generarGenesis')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            <x-dynamic-form 
            :fields="[
                ['label'=>'Brief','type'=>'select', 'name'=>'brief', 'id'=>'brief', 'col'=>'sm:col-span-4', 'value'=>old('brief'), 'attr'=>'data-validation-rules=required data-field-name=brief', 'list'=>[]],

                ['label'=>'Investigación','type'=>'select', 'name'=>'investigation', 'id'=>'investigation', 'col'=>'sm:col-span-4', 'value'=>old('investigation'), 'attr'=>'data-field-name=investigation', 'list'=>[], 'description'=>'Puedes seleccionar la investigación que deseas utilizar'],

                ['label'=>'Objetivo','type'=>'textarea', 'name'=>'360_objective', 'id'=>'360_objective', 'col'=>'sm:col-span-4', 'value'=>old('360_objective'), 'attr'=>'data-validation-rules=required|max:2000 data-field-name=objetivo', 'description'=>'¿Cuál es el objetivo comercial específico que nuestro cliente busca alcanzar? Este debe ser claramente definido y cuantificable. Plantea dos preguntas clave para guiar tu enfoque: “¿Qué razón estratégica nos impulsa a establecer comunicación con los consumidores en este momento?” y “¿Qué métricas específicas indicarán el logro exitoso de nuestro objetivo?'],
                ]"
            >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Información</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
            </x-dynamic-form>
            <div class="message mt-4 text-sm space-y-1 text-red-600 font-medium"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" data-btnForm="selectBriefAndObjectiveForm">Continuar</x-button-genesis>
            </div>
        </form>
    </div>
</div>



                