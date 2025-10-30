<!-- step1.blade.php -->
<div id="step-1-form">
    <div id="step-1-form-content">
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Validar concepto</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Valida tu concepto y mejóralo con nuestra herramienta de IA. Añade tus conocimientos y deja que nuestra poderosa herramienta con IA lo eleve.
        </p>
        <br>
        <form id="accountForm" method="POST" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    ['label'=>'Cuenta','type'=>'select', 'name'=>'account', 'id'=>'account', 'col'=>'sm:col-span-4', 'value'=>old('account'), 'attr'=>'validate-required=required validate-name=marca', 'list'=>$accounts],
                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Selecciona una cuenta</h2>
                <p class="mt-1 text-sm leading-6 text-gray-400"> </p>
            </x-dynamic-form>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-btnForm="accountForm" data-step="2" class="step-button">Continuar</x-button-genesis>
            </div>
        </form>
    </div>
</div>
