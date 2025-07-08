<!-- step1.blade.php -->
<div id="step-1-form">
    <div id="step-1-form-content">
        <form id="accountForm" method="POST" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    ['label'=>'Cuenta','type'=>'select', 'name'=>'account', 'id'=>'account', 'col'=>'sm:col-span-4', 'value'=>old('account'), 'attr'=>'validate-required=required validate-name=marca', 'list'=>$accounts],
                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Selecciona una cuenta</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
            </x-dynamic-form>
            Rellena los campos importantes para iniciar con tu campaña y deja que la IA te ayude:
            <div class="mt-6 flex items-center flex-wrap justify-start gap-x-6 gap-y-2">
                <!--<x-button-genesis type="button" data-step="4" class="form-button">Rellenar Manualmente</x-button-genesis>-->
                <x-button-genesis type="button" data-step="2" class="form-button">Rellenar con IA</x-button-genesis>
                <a href="{{ route('generated.create') }}">
                    <x-button-genesis type="button" >
                        Sube tu Brief
                    </x-button-genesis>
                </a>
            </div>
        </form>
    </div>
</div>
