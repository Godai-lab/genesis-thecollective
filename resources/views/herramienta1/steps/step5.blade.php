<!-- step5.blade.php -->
@php 

@endphp
<form id="step-5-form" action="{{route('herramienta1.savefields')}}" method="POST" data-validate="true">
    @csrf 
    <x-dynamic-form 
        :fields="[
            ['label'=>'¿Cuáles son las tendencias del mercado relevantes para tu marca?','type'=>'textarea', 'name'=>'tendencias_mercado', 'id'=>'tendencias_mercado', 'col'=>'sm:col-span-3', 'value'=>old('tendencias_mercado'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuáles_son_las_tendencias_del_mercado_relevantes_para_tu_marca'],

            ['label'=>'¿Cuál es el tamaño del mercado y cómo se segmenta?','type'=>'textarea', 'name'=>'tamano_mercado_y_segmentacion', 'id'=>'tamano_mercado_y_segmentacion', 'col'=>'sm:col-span-3', 'value'=>old('tamano_mercado_y_segmentacion'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuál_es_el_tamano_del_mercado_y_cómo_se_segmenta'],
            ]"
        >
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Mercado</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
    </x-dynamic-form>

    <x-dynamic-form 
        :fields="[
            ['label'=>'¿Quiénes son los principales competidores de tu marca?','type'=>'text-file', 'name'=>'competidores_marca', 'id'=>'competidores_marca', 'col'=>'sm:col-span-3', 'value'=>old('competidores_marca'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=quiénes_son_los_principales_competidores_de_tu_marca'],

            ['label'=>'¿Cuál es el análisis FODA de tu competencia directa?','type'=>'textarea', 'name'=>'analisis_FODA_competencia', 'id'=>'analisis_FODA_competencia', 'col'=>'sm:col-span-3', 'value'=>old('analisis_FODA_competencia'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuál_es_el_análisis_FODA_de_tu_competencia_directa'],
            ]"
        >
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Competencia</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
    </x-dynamic-form>
    
    <x-dynamic-form 
        :fields="[
            ['label'=>'¿Cuál es la edad, género, ubicación y nivel de ingresos de tu público objetivo?','type'=>'text-file', 'name'=>'edad_genero_ubicacion_ingresos_publico_objetivo', 'id'=>'edad_genero_ubicacion_ingresos_publico_objetivo', 'col'=>'sm:col-span-3', 'value'=>old('edad_genero_ubicacion_ingresos_publico_objetivo'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuál_es_la_edad,_género,_ubicación_y_nivel_de_ingresos_de_tu_público_objetivo'],

            ['label'=>'¿Cuáles son los intereses, valores y estilo de vida de tu público objetivo?','type'=>'text-file', 'name'=>'intereses_valores_estilo_vida_publico_objetivo', 'id'=>'intereses_valores_estilo_vida_publico_objetivo', 'col'=>'sm:col-span-3', 'value'=>old('intereses_valores_estilo_vida_publico_objetivo'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuáles_son_los_intereses,_valores_y_estilo_de_vida_de_tu_público_objetivo'],

            ['label'=>'¿Cuáles son los hábitos de compra y lealtad a la marca de tu público objetivo?','type'=>'textarea', 'name'=>'habitos_compra_lealtad_publico_objetivo', 'id'=>'habitos_compra_lealtad_publico_objetivo', 'col'=>'sm:col-span-3', 'value'=>old('habitos_compra_lealtad_publico_objetivo'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cuáles_son_los_hábitos_de_compra_y_lealtad_a_la_marca_de_tu_público_objetivo'],

            ['label'=>'¿Cómo utilizan tu producto/servicio?','type'=>'textarea', 'name'=>'como_utilizan_tu_producto', 'id'=>'como_utilizan_tu_producto', 'col'=>'sm:col-span-3', 'value'=>old('como_utilizan_tu_producto'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=cómo_utilizan_tu_producto/servicio'],

            ['label'=>'¿En qué momento del día lo usan?','type'=>'textarea', 'name'=>'cuando_utilizan_tu_producto', 'id'=>'cuando_utilizan_tu_producto', 'col'=>'sm:col-span-4', 'value'=>old('cuando_utilizan_tu_producto'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=en_qué_momento_del_día_lo_usan'],

            ['label'=>'Puntos de contacto del cliente con la marca a lo largo de su experiencia.','type'=>'textarea', 'name'=>'puntos_contacto_cliente_marca', 'id'=>'puntos_contacto_cliente_marca', 'col'=>'sm:col-span-6', 'value'=>old('puntos_contacto_cliente_marca'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=puntos_de_contacto_del_cliente_con_la_marca_a_lo_largo_de_su_experiencia.'],

            ['label'=>'Canales en los que los clientes se comunican e interactúan con la marca','type'=>'textarea', 'name'=>'canales_comunican_interactuan_marca', 'id'=>'canales_comunican_interactuan_marca', 'col'=>'sm:col-span-6', 'value'=>old('canales_comunican_interactuan_marca'), 'attr'=>'data-validation-rules=required|max:300 data-field-name=canales_en_los_que_los_clientes_se_comunican_e_interactúan_con_la_marca'],

            ]"
        >
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Target</h2>
        <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"> </p>
    </x-dynamic-form>
    <input type="hidden" name="step" value="6">
    <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
        <x-button-genesis type="button" data-step="4" class="step-button">Regresar</x-button-genesis>
        <x-button-genesis type="button" class="form-button">Siguiente</x-button-genesis>
        {{-- <button data-step="4" class="step-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
            type="button">
            Regresar
        </button>
        <button class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
            type="button">
            Siguiente
        </button> --}}
    </div>
</form>