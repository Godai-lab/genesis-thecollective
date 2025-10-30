<!-- step2.blade.php -->
<div id="step-2-form">
    <div id="step-2-form-content">
        <form id="step2Form" method="POST" data-validate="true">
            @csrf 
            <x-dynamic-form 
            :fields="[
                ['label'=>'Genesis','type'=>'select', 'name'=>'genesis', 'id'=>'genesis', 'col'=>'sm:col-span-4', 'value'=>old('genesis'), 'attr'=>'', 'list'=>[]]
                ]"
            >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Información</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
            </x-dynamic-form>
            <div class="message mt-4 text-sm space-y-1 text-red-600 font-medium"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" data-btnForm="selectGenesisForm" class="form-button">Continuar</x-button-genesis>
            </div>
        </form>
    </div>
</div>



                