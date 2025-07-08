<!-- step9.blade.php -->
<div id="step-9-form">
    <div id="step-9-form-content">
        <form id="step-9-form" method="POST" action="{{route('herramienta1.saveBrief')}}" data-validate="true">
            @csrf
            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Brief</h2>
            <p class="mt-1 m-b-2 text-sm leading-6 text-black dark:text-gray-400">Revisa la información detalladamente. Puedes editar, corregir y añadir. </p>
            <input type="hidden" name="Brief" id="Brief-GenerateIA"> 
            <div id="contentBrief-GenerateIA"></div>
            <x-dynamic-form 
                :fields="[

                    ['label'=>'Nombre del archivo','placeholder'=>'Escribe el nombre del archivo','type'=>'text', 'name'=>'file_name', 'id'=>'file_name', 'col'=>'sm:col-span-3', 'value'=>old('file_name'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=nombre_archivo'],

                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Guardar archivo</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">aquí puedes guardar tu archivo</p>
            </x-dynamic-form>

            <div class="mt-4">
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valoración</label>
                    <div class="rating mt-1" id="rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-2xl cursor-pointer" data-rating="{{ $i }}"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-value" data-validation-rules="required" data-field-name="valoración">
                </div>
            </div>

            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-form="btnCrearBriefIA" class="form-button-step">Volver a generar</x-button-genesis>
                <x-button-genesis type="button" class="form-button">Guardar</x-button-genesis>
                {{-- <button data-form="btnCrearBriefIA" class="form-button-step inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Volver a generar
                </button>
                <button class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Aceptar
                </button> --}}
            </div>
        </form>
    </div>
</div>