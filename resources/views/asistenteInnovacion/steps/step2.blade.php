
<!-- step2.blade.php -->
<div id="step-2-form">
    <div id="step-2-form-content">
        <form id="step-2-form" method="POST" action="{{route('asistenteInnovacion.generarPrompt')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    
                    ['label'=>'Describe tu innovación','type'=>'textarea', 'name'=>'asistenteInnovacionPrompt', 'id'=>'asistenteInnovacionPrompt', 'col'=>'sm:col-span-4', 'value'=>old('asistenteInnovacionPrompt'), 'attr'=>'data-validation-rules=required|max:800 data-field-name=describe_tu_innovación', 'description'=>'Ejemplo: '],
                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Genera tu innovación</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
            </x-dynamic-form>
            <div class="message text-sm text-red-600 dark:text-red-400 space-y-1"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btngenerarCreatividad" class="form-button">Continuar</x-button-genesis>
            </div>
        </form>
    </div>
    
</div>