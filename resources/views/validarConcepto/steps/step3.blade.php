<style>

</style>
<!-- step3.blade.php -->
<div id="step-3-form">
    <div id="step-3-form-content">
        <form id="step3Form" method="POST" action="{{route('validar-concepto.getValidarConceptoForm')}}" data-validate="true">
            @csrf
            <div id="form-concepto" class="space-y-12">
                <div class="border-b border-gray-700 pb-12 mb-6">
                    <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">Validar Concepto</h2>
                    <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400"></p>
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 items-start">
                        <div class="not-genesis sm:col-span-4">
                            <label for="concepto_pais" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">País <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="concepto_pais" id="concepto_pais" data-validation-rules="required|max:100" data-field-name="país" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div>
                        <div class="not-genesis sm:col-span-4">
                            <label for="concepto_nombre_marca" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Nombre de la marca <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="concepto_nombre_marca" id="concepto_nombre_marca" data-validation-rules="required|max:100" data-field-name="nombre_de_la_marca" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-4">
                            <label for="concepto_categoria" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Categoría <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="concepto_categoria" id="concepto_categoria" data-validation-rules="required|max:100" data-field-name="categoría" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-4">
                            <label for="concepto_periodo_campania" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Periodo de la campaña <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="concepto_periodo_campania" id="concepto_periodo_campania" data-validation-rules="required|max:100" data-field-name="periodo_de_la_campaña" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                            </div>
                        </div>

                        <div class="not-genesis sm:col-span-4">
                            <label for="concepto_concepto" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">Concepto <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <textarea name="concepto_concepto" id="concepto_concepto" data-validation-rules="required|max:100" data-field-name="concepto" class="block w-full rounded-md border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="message mt-4 text-sm space-y-1 text-red-600 font-medium"></div>
            
            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-step="2" class="step-button">Regresar</x-button-genesis>
                <x-button-genesis type="button" data-btnForm="validarConceptoForm" class="form-button">Validar</x-button-genesis>
            </div>
        </form>
    </div> 
    </div>

