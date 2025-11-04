<style>

#visual-lista-fuentes-genesis ul,
#visual-lista-fuentes-escenario ul {
    list-style-type: disc !important;
    padding-left: 40px !important;
    margin-top: 5px;
    font-size: 14px;
}


</style>
<!-- step8-1.blade.php -->
<div id="step-8-1-form">
    <div id="step-8-1-form-content">
        <form id="step8-1Form" method="POST">
            @csrf
            <h2 class="text-base font-semibold leading-7 text-black dark:text-gray-100">GÉNESIS COMPLETADO EXITOSAMENTE</h2>
            <p class="mt-1 text-sm leading-6 text-black dark:text-gray-400">
                ¡Felicidades! Has culminado esta etapa del viaje creativo: tu Génesis está listo para brillar.<br>
                Descarga tu archivo y prepárate para explorar nuevas herramientas, dar el siguiente salto innovador e inspirar al mundo con tus ideas.
            </p>

            <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
                <x-button-genesis type="button" data-btnForm="btnDownloadGenesisForm" class="">Descargar</x-button-genesis>
            </div>
        </form>
    </div>
</div>
