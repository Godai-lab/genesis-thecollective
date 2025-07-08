<!-- step4.blade.php -->
@php 

$arquetipos = [
    ["id"=> "El Inocente", "name"=> "El Inocente"],
    ["id"=> "El Explorador", "name"=> "El Explorador"],
    ["id"=> "El Sabio", "name"=> "El Sabio"],
    ["id"=> "El Héroe", "name"=> "El Héroe"],
    ["id"=> "El Forajido", "name"=> "El Forajido"],
    ["id"=> "El Mago", "name"=> "El Mago"],
    ["id"=> "El Hombre/Mujer Común", "name"=> "El Hombre/Mujer Común"],
    ["id"=> "El Amante", "name"=> "El Amante"],
    ["id"=> "El Bufón", "name"=> "El Bufón"],
    ["id"=> "El Cuidador", "name"=> "El Cuidador"],
    ["id"=> "El Gobernante", "name"=> "El Gobernante"],
    ["id"=> "El Creador", "name"=> "El Creador"]
];
$paises = [
    ["id"=> "Argentina", "name"=> "Argentina"],
    ["id"=> "Bolivia", "name"=> "Bolivia"],
    ["id"=> "Brasil", "name"=> "Brasil"],
    ["id"=> "Chile", "name"=> "Chile"],
    ["id"=> "Colombia", "name"=> "Colombia"],
    ["id"=> "Costa Rica", "name"=> "Costa Rica"],
    ["id"=> "Cuba", "name"=> "Cuba"],
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
    ["id"=> "Venezuela", "name"=> "Venezuela"]
];
@endphp
<form id="step-4-form" method="POST" action="{{route('herramienta1.savefields')}}" data-validate="true">
    @csrf 
    <x-dynamic-form 
        :fields="[
            ['label'=>'País','type'=>'select', 'name'=>'country', 'id'=>'country', 'col'=>'sm:col-span-4', 'value'=>old('country'), 'attr'=>'validate-required=required validate-name=país', 'list'=>$paises],
            ]"
            >
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Ubicación</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
    </x-dynamic-form>
    <x-dynamic-form 
        :fields="[
            ['label'=>'Nombre de la marca','placeholder'=>'Escribe el nombre de la marca','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-3', 'value'=>old('name'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=nombre'],

            ['label'=>'Slogan','type'=>'text', 'name'=>'slogan', 'id'=>'slogan', 'col'=>'sm:col-span-3', 'value'=>old('slogan'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=slogan'],

            ['label'=>'Misión','type'=>'text-file', 'name'=>'mission', 'id'=>'mission', 'col'=>'sm:col-span-3', 'value'=>old('mission'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=misión'],

            ['label'=>'Visión','type'=>'text-file', 'name'=>'vision', 'id'=>'vision', 'col'=>'sm:col-span-3', 'value'=>old('vision'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=visión'],

            ['label'=>'Valores','type'=>'dynamic-list', 'limit' =>3, 'name'=>'valores', 'id'=>'valores', 'col'=>'sm:col-span-4', 'value'=>old('valores'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=valores'],

            ['label'=>'¿Por qué existe tu marca?','type'=>'textarea', 'name'=>'por_que_existe_tu_marca', 'id'=>'por_que_existe_tu_marca', 'col'=>'sm:col-span-3', 'value'=>old('por_que_existe_tu_marca'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=por_qué_existe_tu_marca'],

            ['label'=>'¿Cuándo y cómo se fundó la marca?','type'=>'textarea', 'name'=>'fundacion_marca', 'id'=>'fundacion_marca', 'col'=>'sm:col-span-3', 'value'=>old('fundacion_marca'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuándo_y_cómo_se_fundó_la_marca'],

            ['label'=>'¿Cuáles son los hitos destacados en la evolución de la marca?','type'=>'textarea', 'name'=>'hitos_marca', 'id'=>'hitos_marca', 'col'=>'sm:col-span-3', 'value'=>old('hitos_marca'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuáles_son_los_hitos_destacados_en_la_evolución_de_la_marca'],

            ['label'=>'¿Qué diferencia tu marca de sus competidores?','type'=>'textarea', 'name'=>'diferencia_marca', 'id'=>'diferencia_marca', 'col'=>'sm:col-span-3', 'value'=>old('diferencia_marca'), 'attr'=>'data-validation-rules=max:300 data-field-name=qué_diferencia_tu_marca_de_sus_competidores'],

            ['label'=>'Tono de voz','type'=>'textarea', 'name'=>'tono_de_voz', 'id'=>'tono_de_voz', 'col'=>'sm:col-span-3', 'value'=>old('tono_de_voz'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=tono_de_voz'],

            ['label'=>'¿Cuál es la situación económica, social y cultural del lugar donde opera tu marca?','type'=>'text-file', 'name'=>'situacion_lugar_marca', 'id'=>'situacion_lugar_marca', 'col'=>'sm:col-span-3', 'value'=>old('situacion_lugar_marca'), 'attr'=>'data-validation-rules=max:300 data-field-name=cuál_es_la_situación_económica,_social_y_cultural_del_lugar_donde_opera_tu_marca'],

            ]"
        >
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Marca</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
    </x-dynamic-form>

    <x-dynamic-group-form  
        :conf="['id'=>'product', 'name'=>'product', 'limit' =>3, 'single' => 'Producto' ]"
        :fields="[
                ['label'=>'Nombre del producto','type'=>'text', 'name'=>'product_name', 'id'=>'product_name', 'col'=>'sm:col-span-3', 'value'=>old('product_name'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=nombre_del_producto'],
                ['label'=>'Slogan del producto','type'=>'text', 'name'=>'product_slogan', 'id'=>'product_slogan', 'col'=>'sm:col-span-3', 'value'=>old('product_slogan'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=slogan_del_producto'],
                ['label'=>'Presentaciones','type'=>'textarea', 'name'=>'presentaciones', 'id'=>'presentaciones', 'col'=>'sm:col-span-4', 'value'=>old('presentaciones'), 'attr'=>'data-validation-rules=required data-field-name=presentaciones'],
                ['label'=>'Características del producto','type'=>'dynamic-list', 'limit' =>3, 'name'=>'product_characteristics', 'id'=>'product_characteristics', 'col'=>'sm:col-span-4', 'value'=>old('product_characteristics'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=características_del_producto'],
                ['label'=>'Beneficios del producto','type'=>'dynamic-list', 'limit' =>3, 'name'=>'product_benefits', 'id'=>'product_benefits', 'col'=>'sm:col-span-4', 'value'=>old('product_benefits'), 'attr'=>'data-validation-rules=required|max:100 data-field-name=beneficios_del_producto'],
            ]"
        >
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Productos</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">Puedes añadir más de 1 producto, límite 3</p>
    </x-dynamic-group-form>

    <x-dynamic-form 
        :fields="[
            ['label'=>'Arquetipo','type'=>'select', 'name'=>'archetype', 'id'=>'archetype', 'col'=>'sm:col-span-4', 'value'=>old('archetype'), 'attr'=>'validate-required=required validate-name=arquetipo', 'list'=>$arquetipos],
            ]"
        >
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Opcional</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
    </x-dynamic-form>
    <input type="hidden" name="step" value="5">
    <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
        <x-button-genesis type="button" data-step="1" class="step-button">Regresar</x-button-genesis>
        <x-button-genesis type="button" id="btnCrearBriefIA" class="form-button">Siguiente</x-button-genesis>
        {{-- <button data-step="1" class="step-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
            type="button">
            Regresar
        </button>
        <button class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
            type="button">
            Siguiente
        </button> --}}
    </div>
</form>