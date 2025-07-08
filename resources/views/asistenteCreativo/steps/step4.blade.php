<div id="step-4-container">
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¡Documento guardado correctamente!</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Tu documento ha sido guardado con éxito. Puedes visualizarlo en la sección de "Generados" o continuar trabajando con otros documentos.
                </p>
            </div>
        </div>
        
        <div class="mt-6 flex items-center flex-wrap justify-end gap-x-6 gap-y-2">
            <x-button-genesis type="button" data-step="1" class="step-button">Crear nuevo</x-button-genesis>
            {{-- <x-button-genesis type="button" onclick="window.location.href='{{ route('generados.index') }}'">Ver generados</x-button-genesis> --}}
        </div>
    </div>
</div> 