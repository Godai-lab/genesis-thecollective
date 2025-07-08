<!-- step1.blade.php -->
<div id="step-1-form">
    <div id="step-1-form-content">
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Estrategia creativa</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Génesis se encarga de ordenar y enfocar los esfuerzos de manera cuantificable para llegar a una línea creativa poderosa. Es un framework diseñado para campañas de impacto y centradas en las personas
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
                {{-- <button data-step="2" class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Continuar
                </button> --}}
                <x-button-genesis type="button" data-step="2" class="step-button">Continuar</x-button-genesis>
            </div>
        </form>
    </div>
</div>
