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
            <form id="step-2-form" method="POST" action="{{route('herramienta1.generatebriefia')}}" enctype="multipart/form-data" data-validate="true">
            @csrf 
            <x-dynamic-form 
                :fields="[
                    ['label'=>'País','type'=>'select', 'name'=>'country', 'id'=>'country', 'col'=>'sm:col-span-4', 'value'=>old('country'), 'attr'=>'data-validation-rules=required data-field-name=país', 'list'=>$paises],

                    ['label'=>'Nombre de la marca','placeholder'=>'Escribe el nombre de la marca','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-3', 'value'=>old('name'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=nombre'],

                    ['label'=>'Slogan','type'=>'text', 'name'=>'slogan', 'id'=>'slogan', 'col'=>'sm:col-span-3', 'value'=>old('slogan'), 'attr'=>'data-validation-rules=max:100 data-field-name=slogan'],

                    ]"
                    >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Ubicación</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
            </x-dynamic-form>
            <x-dynamic-form 
                :fields="[
                    ['label'=>'Urls (sube enlaces que creas conveniente que ayudarán a construir un buen brief)','type'=>'dynamic-list', 'limit' =>5, 'name'=>'urls', 'id'=>'urls', 'col'=>'sm:col-span-4', 'value'=>old('urls'), 'attr'=>'data-validation-rules=max:600 data-field-name=urls', 'description'=>'Puedes añadir hasta 5 enlaces web'],

                    ['label'=>'Archivos (sube archivos relevantes que ayuden a construir un buen brief)','type'=>'dynamic-list-file', 'limit' =>5, 'name'=>'files', 'id'=>'files', 'col'=>'sm:col-span-4', 'value'=>old('urls'), 'attr'=>'accept=.pdf,.doc,.docx,.xls,.xlsx,.txt', 'description'=>'Puedes adjuntar hasta 5 archivos de hasta 20 Mb, pueden ser .pdf/.doc./.txt'],

                    ['label'=>'Investigación','type'=>'select', 'name'=>'investigation[]', 'id'=>'investigation', 'col'=>'sm:col-span-4', 'value'=>old('investigation'), 'attr'=>'multiple', 'list'=>[], 'description'=>'Puedes seleccionar la investigación que deseas utilizar'],

                    ['label'=>'Tienes información de tu mercado (estudios, competencia, análisis, documentación), súbelo aquí:','type'=>'singlefile', 'name'=>'files', 'id'=>'fileextra1', 'col'=>'sm:col-span-4', 'value'=>old('fileextra1'), 'attr'=>'accept=.pdf,.doc,.docx,.xls,.xlsx,.txt', 'description'=>'Puedes adjuntar 1 archivo de hasta 20 Mb, pueden ser .pdf/.doc./.txt'],

                    ['label'=>'Tienes un documento de construcción de FODA ya establecido, súbelo aquí:','type'=>'singlefile', 'name'=>'files', 'id'=>'fileextra2', 'col'=>'sm:col-span-4', 'value'=>old('fileextra2'), 'attr'=>'accept=.pdf,.doc,.docx,.xls,.xlsx,.txt', 'description'=>'Puedes adjuntar 1 archivo de hasta 20 Mb, pueden ser .pdf/.doc./.txt'],

                    ['label'=>'Sube aquí cualquier información adicional que creas conveniente para potenciar tu brief:','type'=>'singlefile', 'name'=>'files', 'id'=>'fileextra3', 'col'=>'sm:col-span-4', 'value'=>old('fileextra3'), 'attr'=>'accept=.pdf,.doc,.docx,.xls,.xlsx,.txt', 'description'=>'Puedes adjuntar 1 archivo de hasta 20 Mb, pueden ser .pdf/.doc./.txt'],

                    ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Información</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Sube la información que tengas</p>
            </x-dynamic-form>
            <div class="message text-sm text-red-600 dark:text-red-400 space-y-1"></div>
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btnCrearBriefIA" class="form-button">Rellenar con IA</x-button-genesis>
                {{-- <button data-step="1" class="step-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Regresar
                </button>
                <button id="btnCrearBriefIA" class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Rellenar con IA
                </button> --}}
            </div>
        </form>
    </div>

    <script>
        const select = document.getElementById("investigation");
        const max = 5;
      
        select.addEventListener("change", function () {
          const seleccionados = [...select.selectedOptions];
          if (seleccionados.length > max) {
            // deselecciona el último que intentó marcar
            seleccionados[seleccionados.length - 1].selected = false;
            alert("Solo puedes seleccionar hasta " + max + " opciones");
          }
        });
      </script>
    
</div>