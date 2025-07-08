<x-app-layout>
    <x-slot name="title">Génesis - Investigación</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Brief') }}
        </h2>
    </x-slot>

    <!-- Overlay para el loader -->
    <div id="overlay" class="overlay"></div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden">
                <div class="p-6 text-black dark:text-gray-100">
                    <div class="block p-3"></div>
                    
                    <div id="Content" class="block max-w-2xl mx-auto">
                        <div class="step step-1">
                            @include('herramienta1.dashboard.investigacion.steps.step1')
                        </div>

                        <div class="step step-2" style="display: none;">
                            @include('herramienta1.dashboard.investigacion.steps.step2')
                        </div>

                        <div class="step step-3" style="display: none;">
                            @include('herramienta1.dashboard.investigacion.steps.step3')
                        </div>
                        
                        <div class="step step-4" style="display: none;">
                            @include('herramienta1.dashboard.investigacion.steps.step4')
                        </div>
                        
                        <!-- El contenedor de progreso se insertará aquí vía JavaScript -->
                        <div class="loader" id="loader" style="display: none;">Cargando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #loader, #progress-container {
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
            position: fixed;
            left: 50%;
            top: 25%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }
        #loader {
            margin-top: 60px;
            width: 100%;
            font-size: 16px;
        }
        #progress-text {
            font-size: 50px !important;
            margin-bottom: 30px;
            font-weight: bold;
        }
        #loader.fade-out, #progress-container.fade-out {
            opacity: 0;
        }
        #progress-bar {
            transition: width 0.5s ease-out;
            display: none;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            z-index: 999;
        }
        
        /* Añadir estilos específicos para el editor */
        /* .container-edit {
            overflow-y: auto !important;
            min-height: 500px !important;
            max-height: 800px !important;
        } */
        
        /* Asegurar que todo el contenido HTML se visualiza correctamente */
        /* .ql-editor {
            overflow-y: auto !important;
            max-height: none !important;
            min-height: 450px !important;
        } */
        
        /* Mejorar la visualización de listas */
        /* .ql-editor ul, .ql-editor ol {
            padding-left: 1.5em !important;
        } */
        
        /* Asegurar que los tags em se rendericen como espacios */
        /* .ql-editor em:empty {
            display: block !important;
            height: 1em !important;
            width: 100% !important;
        } */
    </style>

    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.bubble.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        let mensajeIndex = 0;
        let mensajeInterval;
        let duracionMensaje = 5000;
        let procesando = false;
        let progressBar;
        let progressText;
        let progressContainer;
        let investigacionHTML = ''; 
        
        const mensajes = [
    "Estamos explorando cada rincón de la red en busca de datos jugosos.",
    "Un momento… desmenuzando información profunda para revelar cada detalle.",
    "Escarbando en el universo digital para extraer lo mejor.",
    "Por favor, espera… afinando la investigación a nivel microscópico.",
    "Sumergiéndonos en la marea de datos para hallar ese ángulo perfecto.",
    "Un instante más… desentrañando los secretos ocultos en cada byte de información.",
    "Por favor espera… investigando a fondo como un detective digital en acción.",
    "Creo que encontré varias pistas en cada recoveco del ciberespacio.",
    "Organizando la información como piezas de un rompecabezas virtual... ya casi terminamos.",
    "Solo un momento más… mapeando la red para extraer lo mejor de cada fuente.",
    "Por favor, espera un segundo… desenterrando detalles con precisión quirúrgica.",
    "Un instante, por favor… rastreando cada pista en la vasta jungla de datos.",
    "Procesando documento...",
    "¡Ya casi finalizamos! Gracias por tu paciencia."
];



        // Función para actualizar la barra de progreso
        function actualizarProgreso(porcentaje) {
            progressBar.style.width = `${porcentaje}%`;
            progressText.textContent = `${Math.round(porcentaje)}%`;
        }

        // Función para mostrar el mensaje con efecto de desvanecimiento
        function mostrarMensajeConEfecto(loader, mensaje) {
            if (!loader) return;
            loader.classList.add('fade-out');
            
            setTimeout(() => {
                loader.textContent = mensaje;
                loader.classList.remove('fade-out');
            }, 500);
        }

        // Modificamos la función mostrarLoader para que acepte un parámetro de modo
        function mostrarLoader(modo = 'default') {
            procesando = true;
            const loader = document.getElementById('loader');
            progressBar = document.getElementById('progress-bar');
            progressText = document.getElementById('progress-text');
            progressContainer = document.getElementById('progress-container');
            const overlay = document.getElementById('overlay');
            
            overlay.style.display = 'block';
            loader.style.display = 'block';
            
            // Si estamos en modo 'guardar', mostrar solo el mensaje de guardar
            if (modo === 'guardar') {
                // Ocultar el contenedor de progreso
                progressContainer.style.display = 'none';
                // Mostrar mensaje simple
                loader.textContent = "Guardando archivo...";
                return;
            }
            
            // Si no es modo guardar, mostrar los mensajes animados normales
            progressContainer.style.display = 'block';
            
            // Reiniciar progreso
            actualizarProgreso(0);
            
            // Mostrar el primer mensaje inmediatamente
            mostrarMensajeConEfecto(loader, mensajes[0]);
            mensajeIndex = 1;
            
            // Calcular el progreso por mensaje
            const progresoPorMensaje = 100 / mensajes.length;

            function mostrarSiguienteMensaje() {
                if (!procesando) return;

                if (mensajeIndex < mensajes.length - 1) {
                    mostrarMensajeConEfecto(loader, mensajes[mensajeIndex]);
                    actualizarProgreso(progresoPorMensaje * (mensajeIndex + 1));
                    mensajeIndex++;
                    setTimeout(mostrarSiguienteMensaje, duracionMensaje);
                } else if (mensajeIndex === mensajes.length - 1) {
                    mostrarMensajeConEfecto(loader, mensajes[mensajeIndex]);
                    actualizarProgreso(95); // Dejamos el último 5% para cuando termine el proceso
                }
            }

            setTimeout(mostrarSiguienteMensaje, duracionMensaje);
        }

        // Función para ocultar el loader y la barra de progreso
        function ocultarLoader() {
            procesando = false;
            const loader = document.getElementById('loader');
            const overlay = document.getElementById('overlay');
            
            // Completar la barra de progreso antes de ocultar
            actualizarProgreso(100);
            
            setTimeout(() => {
                loader.classList.add('fade-out');
                progressContainer.classList.add('fade-out');
                overlay.classList.add('fade-out');
                
                setTimeout(() => {
                    loader.style.display = 'none';
                    progressContainer.style.display = 'none';
                    overlay.style.display = 'none';
                    loader.classList.remove('fade-out');
                    progressContainer.classList.remove('fade-out');
                    overlay.classList.remove('fade-out');
                    mensajeIndex = 0;
                }, 500);
            }, 500);
        }

        // Agregar esta función después de la definición de mostrarLoader y ocultarLoader
        function cambiarMensajeLoader(mensaje) {
            const loader = document.getElementById('loader');
            if (loader) {
                loader.textContent = mensaje;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var allFormData = {};
            var accountID = null;
            
            // Primero agregamos el HTML para la barra de progreso justo antes del loader
            const loaderDiv = document.getElementById('loader');
            loaderDiv.insertAdjacentHTML('afterend', `
                <div id="progress-container" style="display: none;" class="w-full max-w-md mx-auto mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                        <div id="progress-bar" 
                            class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                            style="width: 0%">
                        </div>
                    </div>
                    <div style="font-size:30px !important;" id="progress-text" class="text-center text-sm mt-2 text-gray-600 dark:text-gray-300 font-bold">0%</div>
                </div>
            `);
            
            function goToStep(step) {
                // Ocultar todos los pasos
                document.querySelectorAll('.step').forEach(function(el) {
                    el.style.display = 'none';
                });
                
                // Mostrar el paso solicitado
                var nextStepDiv = document.querySelector('.step-' + step);
                nextStepDiv.style.display = 'block';
                
                // Si vamos al paso 3, asegurarnos de que el ID de cuenta esté actualizado
                if (step == 3) {
                    const accountIdField = document.getElementById('account_id_step3');
                    if (accountIdField && accountID) {
                        accountIdField.value = accountID;
                    }
                }
                
                // Si vamos al paso 4, crear una copia del HTML para poder restaurarlo después
                if (step == 4) {
                    localStorage.setItem('investigacionHTML', investigacionHTML);
                }
            }

            // Modificamos cómo se maneja la selección de cuenta del primer paso
            // Encontramos el formulario del paso 1 directamente
            const step1Form = document.querySelector('#accountForm');
            if (step1Form) {
                const accountSelect = step1Form.querySelector('select[name="account"]');
                if (accountSelect) {
                    // Guardar el ID de cuenta cuando cambia el select
                    accountSelect.addEventListener('change', function() {
                        accountID = this.value;
                    });
                    
                    // Establecer el valor inicial si hay uno seleccionado
                    if (accountSelect.value) {
                        accountID = accountSelect.value;
                    }
                }
            }

            // Selecciona todos los contenedores de pasos
            const contenedores = document.querySelectorAll('.step');
            
            contenedores.forEach(function(contenedor) {
                // Encuentra el botón dentro del contenedor
                const botones = contenedor.querySelectorAll('.step-button');
                botones.forEach(function(boton) {
                    boton.addEventListener('click', function() {
                        const step = this.getAttribute('data-step');
                        contenedor.style.display = 'none';
                        goToStep(step);
                    });
                });

                const formButtonsSteps = contenedor.querySelectorAll('.form-button-step');
                formButtonsSteps.forEach(function(formButtonsStep) {
                    formButtonsStep.addEventListener('click', function() {
                        const idFormButton = this.getAttribute('data-form');
                        var formButton = document.getElementById(idFormButton);
                        contenedor.style.display = 'none';
                        formButton.click();
                    });
                });

                // Manejo específico para el botón de investigar
                const investigarBtn = contenedor.querySelector('#investigarIA');
                if (investigarBtn) {
                    investigarBtn.addEventListener('click', async function(event) {
                        event.preventDefault();
                        const form = event.target.closest('form');

                        if (!ValidarCampos(form)) {
                            return;
                        }

                        // Verificar si tenemos accountID
                        if (!accountID) {
                            alert('No se ha seleccionado una cuenta. Por favor, vuelve al paso 1.');
                            return;
                        }

                        mostrarLoader();
                        
                        try {
                            const formData = new FormData(form);
                            formData.append('account', accountID);

                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                }
                            });

                            const data = await response.json();
                            console.log('Respuesta del servidor investigación:', data);

                            if (data.success) {
                                // El formato ha cambiado, ahora details es directamente el HTML
                                if (data.details) {
                                    // Guardamos el HTML para poder referenciarlo después
                                    investigacionHTML = data.details;
                                    
                                    // Actualizar el editor con la investigación HTML
                                    if (quill_investigacion) {
                                        // Insertamos el HTML directamente en el editor
                                        quill_investigacion.root.innerHTML = data.details;
                                        
                                        // Esperar a que Quill termine de renderizar y luego refrescar la vista
                                        setTimeout(() => {
                                            // Forzar que Quill actualice su visualización
                                            quill_investigacion.update();
                                            
                                            // Ajustar el tamaño del contenedor si es necesario
                                            const editorContainer = document.querySelector('#editorinvestigacion');
                                            if (editorContainer) {
                                                // Asegurar que el contenedor tenga suficiente altura
                                                editorContainer.style.minHeight = '500px';
                                                
                                                // Desplazarse al inicio del editor
                                                editorContainer.scrollTop = 0;
                                            }
                                        }, 500);
                                    }
                                    
                                    // Actualizar el input hidden con el HTML
                                    const investigacionInput = document.getElementById('investigaciongenerada');
                                    if (investigacionInput) {
                                        investigacionInput.value = data.details;
                                    }
                                    
                                    // Guardar para futuras referencias
                                    localStorage.setItem('investigacion_account_id', accountID);
                                    localStorage.setItem('investigacionHTML', data.details);
                                }

                                // // Las fuentes pueden estar en un formato diferente ahora
                                // const fuentesLista = document.getElementById('fuentes-lista');
                                // if (fuentesLista && data.citations) {  // Usamos citations si existe
                                //     fuentesLista.innerHTML = (data.citations || [])
                                //         .map(fuente => `<p class="mb-2">• ${fuente}</p>`)
                                //         .join('');
                                // } else if (fuentesLista) {
                                //     // Si no hay citations específicas, dejamos el contenedor vacío
                                //     fuentesLista.innerHTML = '<p class="mb-2">No se encontraron fuentes para esta investigación.</p>';
                                // }

                                // Navegar al siguiente paso (Step 3)
                                contenedor.style.display = 'none';
                                if (data.goto) {
                                    goToStep(data.goto);
                                }
                            } else {
                                throw new Error(data.error || 'Error al generar la investigación');
                            }
                        } catch (error) {
                            const mensaje = contenedor.querySelector('.message');
                            if (mensaje) {
                                mensaje.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                            }
                            contenedor.style.display = 'block';
                        } finally {
                            ocultarLoader();
                        }
                    });
                }

                // Manejo de otros botones de formulario
                const formButtons = contenedor.querySelectorAll('.form-button');
                formButtons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (procesando) return;

                        const form = this.closest('form');
                        if (form && form.id) {
                            if (ValidarCampos(form)) {
                                // Si es el formulario del step-3, usar modo 'guardar'
                                if (form.id === 'step-3-form') {
                                    mostrarLoader('guardar');
                                } else {
                                    // Para otros formularios (como el del step-2), usar modo normal
                                    mostrarLoader();
                                }
                                
                                // Resto del código para enviar el formulario...
                                // Importante: mantener el código original de procesamiento del formulario
                                
                                // Verificar si se trata del formulario del paso 3 (el de guardar)
                                if (form.id === 'step-3-form') {
                                    // Código específico para el guardado...
                                }
                                
                                // Enviar el formulario al servidor
                                fetch(form.action, {
                                    method: 'POST',
                                    body: new FormData(form),
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    ocultarLoader();
                                    
                                    // Manejar la respuesta según el formulario
                                    if (form.id === 'step-3-form') { // Si es el formulario de guardar (step3)
                                        if (data.success) {
                                            // Almacenar el ID del documento generado si está disponible
                                            if (data.generated_id) {
                                                localStorage.setItem('last_generated_id', data.generated_id);
                                            }
                                            
                                            // Almacenar el ID de la cuenta para la descarga
                                            localStorage.setItem('investigacion_account_id', document.getElementById('account_id_step3').value);
                                            
                                            // Navegar al paso 4 (éxito)
                                            const contenedor = form.closest('.step');
                                            contenedor.style.display = 'none';
                                            goToStep(4);
                                            
                                            // Configurar el botón de descarga
                                            if (window.setupDownloadButton) {
                                                setTimeout(window.setupDownloadButton, 100);
                                            }
                                        } else {
                                            // Mostrar error si la operación falló
                                            const mensaje = form.querySelector('.message');
                                            if (mensaje) {
                                                mensaje.innerHTML = `<div class="alert alert-danger">${data.error || 'Error al guardar la investigación'}</div>`;
                                            }
                                        }
                                    } else if (data.goto) {
                                        // Para otros formularios, usar la propiedad goto si existe
                                        const contenedor = form.closest('.step');
                                        contenedor.style.display = 'none';
                                        goToStep(data.goto);
                                    }
                                })
                                .catch(error => {
                                    ocultarLoader();
                                    console.error('Error en la solicitud:', error);
                                    
                                    // Mostrar mensaje de error en el formulario
                                    const mensaje = form.querySelector('.message');
                                    if (mensaje) {
                                        mensaje.innerHTML = `<div class="alert alert-danger">${error.message || 'Error en la solicitud'}</div>`;
                                    }
                                });
                            }
                        }
                    });
                });
            });

            const stars = document.querySelectorAll('.rating .fa-star');
            const ratingInput = document.getElementById('rating-value');

            stars.forEach(star => {
                star.addEventListener('mouseover', selectStars);
                star.addEventListener('mouseout', unselectStars);
                star.addEventListener('click', setRating);
            });

            function selectStars(e) {
                const rating = e.target.getAttribute('data-rating');
                highlightStars(rating);
            }

            function unselectStars() {
                highlightStars(ratingInput.value);
            }

            function setRating(e) {
                const rating = e.target.getAttribute('data-rating');
                ratingInput.value = rating;
                highlightStars(rating);
            }

            function highlightStars(rating) {
                stars.forEach(star => {
                    star.classList.toggle('text-yellow-400', star.getAttribute('data-rating') <= rating);
                });
            }
        });

        // Inicialización del editor Quill
        var quill_investigacion = new Quill('#editorinvestigacion', {
            theme: 'snow',
            modules: {
            toolbar: [
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['bold', 'italic', 'underline'],
            [{ 'align': [] }],
            ['link']
    ]
  }
        });

        function generarInvestigacion(data) {
            // Pero la mantenemos por compatibilidad
            console.log("Contenido insertado:", data.data || data);

            quill_investigacion.clipboard.dangerouslyPasteHTML(data.data || data);
        }

        function ValidarCampos(form) {
            let isValid = true;
            const camposRequeridos = form.querySelectorAll('[data-validation-rules]');
            
            camposRequeridos.forEach(campo => {
                const reglas = campo.getAttribute('data-validation-rules').split('|');
                const nombreCampo = campo.getAttribute('data-field-name') || campo.name;
                let mensajeError = '';
                
                // Verificar cada regla
                reglas.forEach(regla => {
                    if (regla === 'required' && !campo.value.trim()) {
                        mensajeError = `El campo ${nombreCampo} es obligatorio.`;
                        isValid = false;
                    } else if (regla.startsWith('max:')) {
                        const maxLength = parseInt(regla.split(':')[1]);
                        if (campo.value.length > maxLength) {
                            mensajeError = `El campo ${nombreCampo} no debe exceder los ${maxLength} caracteres.`;
                            isValid = false;
                        }
                    }
                });
                
                // Mostrar mensaje de error si es necesario
                const contenedorError = campo.parentElement.querySelector('.campo-error');
                if (contenedorError) {
                    contenedorError.textContent = mensajeError;
                    contenedorError.style.display = mensajeError ? 'block' : 'none';
                } else if (mensajeError) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'campo-error text-red-500 text-sm mt-1';
                    errorDiv.textContent = mensajeError;
                    campo.parentElement.appendChild(errorDiv);
                }
            });
            
            return isValid;
        }
    </script>
</x-app-layout>