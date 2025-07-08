<x-app-layout>
    <x-slot name="title">Génesis - Asistente Experimental</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Asistente Experimental') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden">
                <div class="p-6 text-black dark:text-gray-100">
                    <div class="block p-3"></div>
                    
                    <div id="Content" class="block mx-auto">
                        <div class="chat-container max-w-2xl mx-auto h-[calc(100vh-200px)] flex flex-col">
                            <div id="error-message" class="hidden mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded"></div>
                            <div id="chat-messages" class="flex-1 overflow-y-auto mb-4 space-y-4">
                                <!-- Los mensajes se agregarán aquí -->
                            </div>
                            
                            <div class="sticky bottom-0 bg-white dark:bg-gray-800 p-4 border-t border-gray-200 dark:border-gray-700">
                                <div id="image-preview" class="mb-4 grid grid-cols-6 gap-2 max-h-32">
                                    <!-- Las previsualizaciones de imágenes se agregarán aquí -->
                                </div>
                                
                                <form id="chat-form" class="flex items-center space-x-2" action="{{ route('asistenteExperimental.generarExperimental') }}" method="POST">
                                    @csrf
                                    <div class="flex-1">
                                        <input type="text" 
                                               id="message-input" 
                                               class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500" 
                                               placeholder="Escribe tu mensaje...">
                                    </div>
                                    
                                    <div class="relative">
                                        <input type="file" 
                                               id="image-input" 
                                               class="hidden" 
                                               accept="image/*" 
                                               multiple>
                                        <button type="button" 
                                                onclick="document.getElementById('image-input').click()" 
                                                class="p-2 text-gray-600 hover:text-gray-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                        Enviar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        const chatMessages = document.getElementById('chat-messages');
        const maxImages = 2; // Límite de imágenes configurable desde el backend
        let selectedImages = []; // Array para almacenar las imágenes seleccionadas

        // Manejar la selección de imágenes
        imageInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            // Verificar el límite de imágenes
            if (selectedImages.length + files.length > maxImages) {
                alert(`Solo puedes subir un máximo de ${maxImages} imágenes`);
                this.value = '';
                return;
            }

            // Agregar nuevas imágenes al array
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    selectedImages.push(file);
                }
            });

            // Actualizar previsualizaciones
            updateImagePreviews();
            
            // Limpiar el input para permitir seleccionar las mismas imágenes nuevamente
            this.value = '';
        });

        // Función para actualizar las previsualizaciones
        function updateImagePreviews() {
            imagePreview.innerHTML = '';
            
            selectedImages.forEach((file, index) => {
                const reader = new FileReader();
                const previewContainer = document.createElement('div');
                previewContainer.className = 'relative w-20 h-20';

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-cover rounded-lg';
                    previewContainer.appendChild(img);

                    // Botón para eliminar imagen
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
                    removeBtn.innerHTML = '×';
                    removeBtn.onclick = function() {
                        // Eliminar la imagen del array
                        selectedImages.splice(index, 1);
                        // Actualizar previsualizaciones
                        updateImagePreviews();
                    };
                    previewContainer.appendChild(removeBtn);
                };

                reader.readAsDataURL(file);
                imagePreview.appendChild(previewContainer);
            });
        }

        // Manejar el envío del formulario
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            
            if (!message && selectedImages.length === 0) {
                alert('Por favor, escribe un mensaje o selecciona imágenes');
                return;
            }

            // Crear elemento de mensaje
            const messageElement = document.createElement('div');
            messageElement.className = 'bg-white dark:bg-gray-700 p-4 rounded-lg shadow';
            
            if (message) {
                const textElement = document.createElement('p');
                textElement.className = 'text-gray-800 dark:text-gray-200';
                textElement.textContent = message;
                messageElement.appendChild(textElement);
            }

            // Agregar imágenes al mensaje
            if (selectedImages.length > 0) {
                const imagesContainer = document.createElement('div');
                imagesContainer.className = 'mt-2 grid grid-cols-4 gap-2';
                
                selectedImages.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-24 object-cover rounded-lg';
                        imagesContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
                
                messageElement.appendChild(imagesContainer);
            }

            // Agregar mensaje al chat
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Preparar FormData para enviar al servidor
            const formData = new FormData();
            formData.append('message', message);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            // Agregar cada imagen al FormData
            selectedImages.forEach((image, index) => {
                formData.append(`images[${index}]`, image);
            });

            // Mostrar indicador de carga
            const submitButton = chatForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enviando...
            `;

            // Enviar datos al servidor
            fetch(chatForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || 'Error en la respuesta del servidor');
                    });
                }
                return response.json();
            })
            .then(data => {
                // Verificar si la respuesta indica error
                if (!data.success) {
                    throw new Error(data.error || 'Error desconocido');
                }

                console.log(data);
                
                // Si la respuesta es exitosa, mostrar el contenido
                const responseElement = document.createElement('div');
                responseElement.className = 'bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow';
                
                // Verificar si hay texto en la respuesta
                if (data.data && data.data.text) {
                    const textElement = document.createElement('p');
                    textElement.className = 'text-gray-800 dark:text-gray-200';
                    textElement.textContent = data.data.text;
                    responseElement.appendChild(textElement);
                }
                
                // Verificar si hay imágenes en la respuesta
                if (data.data && data.data.inlineData) {
                    const imageElement = document.createElement('img');
                    imageElement.className = 'mt-2 max-w-full h-auto rounded-lg';
                    if(data.data.inlineData.mimeType == "image/jpeg"){
                        imageElement.src = `data:image/jpeg;base64,${data.data.inlineData.data}`;
                    }else if(data.data.inlineData.mimeType == "image/png"){
                        imageElement.src = `data:image/png;base64,${data.data.inlineData.data}`;
                    }
                    responseElement.appendChild(imageElement);
                }
                
                chatMessages.appendChild(responseElement);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Limpiar formulario
                messageInput.value = '';
                selectedImages = [];
                updateImagePreviews();
            })
            .catch(error => {
                console.error('Error:', error);
                const errorContainer = document.getElementById('error-message');
                
                // Procesar diferentes tipos de errores
                let errorMessage = '';
                
                if (error.response) {
                    // Error de validación de Laravel
                    const validationErrors = error.response.data.error;
                    if (typeof validationErrors === 'object') {
                        errorMessage = Object.values(validationErrors).flat().join('\n');
                    } else {
                        errorMessage = validationErrors;
                    }
                } else if (error.message) {
                    // Error general
                    errorMessage = error.message;
                } else {
                    errorMessage = 'Ha ocurrido un error inesperado';
                }
                
                errorContainer.textContent = errorMessage;
                errorContainer.classList.remove('hidden');
                
                // Ocultar el mensaje de error después de 5 segundos
                setTimeout(() => {
                    errorContainer.classList.add('hidden');
                }, 5000);
            })
            .finally(() => {
                // Restaurar el botón
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    });
</script>