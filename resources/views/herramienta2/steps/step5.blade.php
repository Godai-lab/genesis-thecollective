@php
$lanzamiento = [
    ["id"=> "Nuevo al mercado", "name"=> "Nuevo al mercado"],
    ["id"=> "Extensión de línea", "name"=> "Extensión de línea"],
];
$posicionamiento = [
    ["id"=> "Construcción de marca", "name"=> "Construcción de marca"],
    ["id"=> "Cambio de imagen", "name"=> "Cambio de imagen"],
];
$mantenimiento = [
    ["id"=> "Recordación de marca", "name"=> "Recordación de marca"],
    ["id"=> "Fidelización", "name"=> "Fidelización"],
];
$promociones = [
    ["id"=> "Descuentos y ofertas", "name"=> "Descuentos y ofertas"],
    ["id"=> "Temporada", "name"=> "Temporada"],
    ["id"=> "Liquidación", "name"=> "Liquidación"],
    ["id"=> "Vinculada a eventos", "name"=> "Vinculada a eventos"],
    ["id"=> "Vinculada a temporadas", "name"=> "Vinculada a temporadas"],
]
@endphp
<!-- step5.blade.php -->
<div id="step-5-form">
    <div id="step-5-form-content">
        <form id="step-5-form" method="POST" action="{{route('herramienta2.saveeleccioncampania')}}" data-validate="true">
            @csrf
            <input type="hidden" name="construccionescenario" id="construccionescenario">
            <div class="space-y-12">
                <div class="border-b border-gray-700 pb-12 mb-6">
                    <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Elección de campaña</h2>
                    <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 items-start">
                        <div class="sm:col-span-4">
                            <label for="360_lanzamiento" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Tipo de campaña</label>
                            <div class="mt-2">
                                <select name="360_Tipo_de_campaña" id="360_Tipo_de_campaña" validate-required="required" validate-name="lanzamiento_de_producto/servicio" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                                    <optgroup label="Lanzamiento de producto/servicio">
                                        <option value="Nuevo al mercado">Nuevo al mercado</option>
                                        <option value="Extensión de línea">Extensión de línea</option>
                                    </optgroup>
                                    <optgroup label="Posicionamiento/Branding">
                                        <option value="Construcción de marca">Construcción de marca</option>
                                        <option value="Cambio de imagen">Cambio de imagen</option>
                                    </optgroup>
                                    <optgroup label="Mantenimiento">
                                        <option value="Recordación de marca">Recordación de marca</option>
                                        <option value="Fidelización">Fidelización</option>
                                    </optgroup>
                                    <optgroup label="Promociones">
                                        <option value="Descuentos y ofertas">Descuentos y ofertas</option>
                                        <option value="Temporada">Temporada</option>
                                        <option value="Liquidación">Liquidación</option>
                                        <option value="Vinculada a eventos">Vinculada a eventos</option>
                                        <option value="Vinculada a temporadas">Vinculada a temporadas</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <x-dynamic-form 
                :fields="[
                    ['label'=>'Lanzamiento de producto/servicio','type'=>'select', 'name'=>'360_lanzamiento', 'id'=>'360_lanzamiento', 'col'=>'sm:col-span-4', 'value'=>old('360_lanzamiento'), 'attr'=>'validate-required=required validate-name=lanzamiento_de_producto/servicio', 'list'=>$lanzamiento, 'description'=>'Lanzamiento de producto/servicio'],
                    ['label'=>'Posicionamiento/Branding','type'=>'select', 'name'=>'360_posicionamiento', 'id'=>'360_posicionamiento', 'col'=>'sm:col-span-4', 'value'=>old('360_posicionamiento'), 'attr'=>'validate-required=required validate-name=posicionamiento/Branding', 'list'=>$posicionamiento, 'description'=>'Posicionamiento/Branding'],
                    ['label'=>'Mantenimiento','type'=>'select', 'name'=>'360_mantenimiento', 'id'=>'360_mantenimiento', 'col'=>'sm:col-span-4', 'value'=>old('360_mantenimiento'), 'attr'=>'validate-required=required validate-name=mantenimiento', 'list'=>$mantenimiento, 'description'=>'Mantenimiento'],
                    ['label'=>'Promociones','type'=>'select', 'name'=>'360_promociones', 'id'=>'360_promociones', 'col'=>'sm:col-span-4', 'value'=>old('360_promociones'), 'attr'=>'validate-required=required validate-name=promociones', 'list'=>$promociones, 'description'=>'Promociones'],
                ]"
                >
                <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Elección de campaña</h2>
                <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
            </x-dynamic-form> --}}
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="4" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" id="btnsaveeleccioncampania" class="form-button">Aceptar</x-button-genesis>
                
                {{-- <button data-step="4" class="step-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="button">
                    Regresar
                </button>
                <button id="btnsaveeleccioncampania" class="form-button inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700"
                    type="submit">
                    Aceptar
                </button> --}}
            </div>
        </form>
    </div>
</div>
