<div class="text-center">
    <svg class="w-12 h-12 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <h2 class="text-2xl font-bold mb-4">¡Investigación guardada con éxito!</h2>
    <p class="mb-6">Tu investigación ha sido guardada correctamente en el sistema.</p>
    <div class="flex justify-center space-x-4">
        <x-button-genesis type="button" class="form-button-regenerate">
            <a href="{{route('dashboard')}}" >
                Volver al dashboard
            </a>
        </x-button-genesis>
        <x-button-genesis type="button" id="btnDescargar" class="">Descargar</x-button-genesis>
    </div>
    
</div>

<script>
// Función para configurar el botón de descarga
function setupDownloadButton() {
    const btnDescargar = document.getElementById('btnDescargar');
    if (btnDescargar) {
        // Eliminar cualquier event listener previo
        btnDescargar.replaceWith(btnDescargar.cloneNode(true));
        
        // Obtener referencia al nuevo botón
        const newBtnDescargar = document.getElementById('btnDescargar');
        
        // Obtener el ID de la cuenta actual
        const accountId = localStorage.getItem('investigacion_account_id');
        console.log('Account ID para descarga:', accountId);
        
        if (accountId) {
            // Configurar el nuevo event listener con la cuenta
            newBtnDescargar.addEventListener('click', function(e) {
                e.preventDefault();
                const downloadUrl = "{{ url('/investigacion/account') }}/" + accountId + "/download-last";
                console.log('Intentando descarga desde URL:', downloadUrl);
                window.location.href = downloadUrl;
            });
        } else {
            newBtnDescargar.disabled = true;
            newBtnDescargar.classList.add('opacity-50', 'cursor-not-allowed');
            console.error('No se encontró ID de cuenta para la descarga');
        }
    }
}

// Ejecutar la función cuando se carga inicialmente
document.addEventListener('DOMContentLoaded', setupDownloadButton);

// También necesitamos agregar esta función al objeto window para llamarla desde index.blade.php
window.setupDownloadButton = setupDownloadButton;
</script>
