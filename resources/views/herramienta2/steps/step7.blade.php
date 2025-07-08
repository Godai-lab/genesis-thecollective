@php

@endphp

<style>
#container-result-genesis ul {
    list-style-type: disc !important;
    padding-left: 40px !important;
    margin-top: 5px;
    font-size: 14px;
}

</style>
<!-- step7.blade.php -->
<div id="step-7-form">
    <div id="step-7-form-content">
        <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">RESULTADO</h2>
        <div id="container-result-genesis"></div>
        <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
            <x-button-genesis type="button" href="{{route('herramienta2.download')}}" id="btnGenerarPDF" class="">Descargar</x-button-genesis>
        </div>
    </div>
</div>
