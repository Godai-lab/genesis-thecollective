<!-- step6.blade.php -->
<div id="step-6-form">
    <div id="step-6-form-content">
        <form id="step-2-form" method="POST" action="{{route('herramienta1.datosextras')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    ['label'=>'Urls','type'=>'dynamic-list', 'limit' =>3, 'name'=>'urls', 'id'=>'urls', 'col'=>'sm:col-span-4', 'value'=>old('urls'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=urls', 'description'=>'urls'],
                    ['label'=>'Archivos','type'=>'dynamic-list-file', 'limit' =>3, 'name'=>'files', 'id'=>'files', 'col'=>'sm:col-span-4', 'value'=>old('urls'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=archivos', 'description'=>'Sube tus archivos'],
                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Datos, links o informes</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> 
                    <ul>
                        <li>Sobre tu marca que desees compartir</li>
                        <li>Sobre tus productos</li>
                        <li>Sobre tu competencia</li>
                        <li>Estudios de mercado</li>
                        <li>Sobre tu ciudad, país, situación económica</li>
                        <li>Para conocer las necesidades del cliente actual</li>
                    </ul>
                </p>
            </x-dynamic-form>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="5" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" class="form-button">Siguiente</x-button-genesis>
                {{-- <button data-step="5" class="step-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
            type="button">
                    Regresar
                </button>
                <button class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Siguiente
                </button> --}}
            </div>
        </form>
    </div>
</div>
