<x-app-layout>
    <x-slot name="title">Génesis - Investigación</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Investigación') }}
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
            /* top: 25%; */
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

        #fuentes-lista ul {
            list-style-type: disc; /* Muestra los puntos de la lista */
            padding-left: 20px; /* Agrega espacio a la izquierda */
            font-size: 13px;
        }

        #fuentes-lista li {
            margin-bottom: 10px; /* Espacio entre elementos de la lista */
            word-break: break-all; /* Permite que las URLs largas se rompan */
        }

        #fuentes-lista a {
            color: #3490dc; /* Color de enlace */
            text-decoration: underline;
        }

        #fuentes-lista a:hover {
            color: #2779bd; /* Color al pasar el mouse */
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
            
            overlay.style.display = 'none';
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

        // Función global para navegar entre pasos
        function goToStep(step) {
            // Ocultar todos los pasos
            document.querySelectorAll('.step').forEach(function(step) {
                step.style.display = 'none';
            });
            
            // Mostrar el paso deseado
            const nextStepDiv = document.querySelector('.step-' + step);
            if (nextStepDiv) {
                nextStepDiv.style.display = 'block';
            }
            
        }

        document.addEventListener('DOMContentLoaded', function() {
            var allFormData = {};
            var id_generated =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['id_generated']) ? $data_generated['id_generated'] : 'null' !!}; // id_generated su valor puede venir desde el controlador como null o un array primero verificar si existe un data_generated y es un array
            var accountID =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['account_id']) ? $data_generated['account_id'] : 'null' !!}; // accountID su valor puede venir desde el controlador como null o un valor
            var step =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['step']) ? $data_generated['step'] : 'null' !!}; // step su valor puede venir desde el controlador como null o un valor
            var metadata =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['metadata']) ? json_encode($data_generated['metadata']) : 'null' !!}; // metadata es un texto json que debemos convertir a un objeto
           
            console.log('id_generated', id_generated);

            // Primero agregamos el HTML para la barra de progreso justo antes del loader
            const loaderDiv = document.getElementById('loader');
            loaderDiv.insertAdjacentHTML('afterend', `
                <div id="progress-container" style="display: none;" class="w-full max-w-md mx-auto mb-4">
                    <div style="font-size:20px !important; text-align: center; margin-bottom: 10px;">La investigación está en proceso vuelve en unos minutos.</div>
                    <div style="display: none;" class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                        <div id="progress-bar" 
                            class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                            style="width: 0%">
                        </div>
                    </div>
                    <div style="font-size:30px !important; display: none;" id="progress-text" class="text-center text-sm mt-2 text-gray-600 dark:text-gray-300 font-bold">0%</div>
                </div>
            `);

            console.log('step', step);
            console.log('accountID', accountID);
            console.log('id_generated', id_generated)

            if(step){
                let goToStepFinal = false;

                if(step >= 1){
                    let form = document.getElementById('accountForm');
                    let account = form.querySelector('select[name="account"]');
                    if (account) {
                        account.value = accountID;
                    }
                    goToStepFinal = true;
                }
                if(step >= 2){
                    let form = document.getElementById('step-2-form');
                    let country = form.querySelector('select[name="country"]');
                    if (country) {
                        country.value = metadata['country'];
                    }
                    let brand = form.querySelector('input[name="brand"]');
                    if (brand) {
                        brand.value = metadata['brand'];
                    }
                    let instruccion = form.querySelector('textarea[name="instruccion"]');
                    if (instruccion) {
                        instruccion.value = metadata['instruccion'];
                    }
                    let modelo = form.querySelector('input[name="modelo"]');
                    if (modelo) {
                        modelo.value ='o4-mini-deep-research';
                    }
                    goToStepFinal = true;
                }
                if(step >= 3){
                    // var formInvestigacion = document.querySelector('#step-3-form');
                    let contenedorStep3 = document.querySelector('.step-3');
                    let contenedorStep1 = document.querySelector('.step-1');
                    if( (metadata['generacion_investigacion_status']) && (metadata['generacion_investigacion_status'] === 'processing')){
                        contenedorStep3.style.display = 'none';
                        contenedorStep1.style.display = 'none';
                        mostrarLoader();

                        iniciarPollingInvestigacion(id_generated, contenedorStep3);

                        goToStepFinal = false;
                    }else if(metadata['generacion_investigacion_status'] === 'completed'){
                        // Procesar la respuesta
                        dataInvestigacion = {data: metadata['generacion_investigacion_content'], sources: metadata['generacion_investigacion_sources']};

                        mostrarRespuestaInvestigacion(dataInvestigacion);

                        goToStepFinal = true;
                    }
                }
                if(step >= 4){

                }

                if(goToStepFinal){
                    goToStep(step);
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
                        const form = this.closest('form');
                        // Si es el botón del step1
                        if (step === "2" && form?.id === 'accountForm') {
                            if (ValidarCampos(form)) {
                                const formData = new FormData(form);
                                accountID = formData.get('account');
                                contenedor.style.display = 'none';
                                goToStep(step);
                            }
                        } else {
                            // Para navegación normal entre pasos
                            contenedor.style.display = 'none';
                            goToStep(step);
                        }
                        console.log('accountID', accountID);
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

                // Manejo de otros botones de formulario
                const formButtons = contenedor.querySelectorAll('.form-button');
                formButtons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (procesando) return;

                        const form = this.closest('form');
                        if (form && form.id) {

                             // Actualizar los valores de los editores
                            var inputInvestigacion = form.querySelector('input[name="investigaciongenerada"]');
                            if (inputInvestigacion) {
                                inputInvestigacion.value = quill_investigacion.getSemanticHTML();
                            }

                            if (ValidarCampos(form)) {
                                contenedor.style.display = 'none';
                                var formData = new FormData(form);
                            
                                // Solo mostrar loader y hacer fetch si no es el formulario de cuenta
                                if (form.id !== 'accountForm') {
                                    if (accountID !== null && !isNaN(accountID)) {

                                        mostrarLoader();

                                        formData.append('account', accountID);
                                        formData.append('id_generated', id_generated);
                                        
                                        // Enviar el formulario al servidor
                                        fetch(form.action, {
                                            method: 'POST',
                                            body: formData,
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            console.log(data);
                                            if(!data.error){
                                                if(data.id_generated){
                                                    console.log('ID generado:', data.id_generated);
                                                    id_generated = data.id_generated;
                                                }
                                                
                                                if(data.function) {
                                                    switch (data.function) {
                                                        case 'generarInvestigacion':
                                                            if (data.status === 'processing') {
                                                                // La investigación está en proceso, iniciar polling
                                                                console.log('Investigación iniciada con ID:', data.generation_id);
                                                                Swal.fire({
                                                                    toast: true,
                                                                    position: 'top-end',
                                                                    icon: 'success',
                                                                    title: 'Investigación en proceso, esto puede tomar varios minutos. Puedes cerrar esta ventana y volver más tarde.',
                                                                    showConfirmButton: false,
                                                                    timer: 6000,
                                                                    timerProgressBar: true
                                                                });
                                                                iniciarPollingInvestigacion(data.generation_id, contenedor);
                                                            } 
                                                            break;
                                                        case 'guardarInvestigacion':
                                                            ocultarLoader();
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                }else{
                                                    ocultarLoader();
                                                }
                                                if(data.goto){ 
                                                    contenedor.style.display = 'none';
                                                    if(data.goto != null){
                                                    goToStep(data.goto);
                                                    }
                                                }else{
                                                    if(data.goto != null){
                                                        contenedor.style.display = 'block';
                                                    }else{
                                                        contenedor.style.display = 'none';
                                                    }
                                                }
                                            }else{
                                                
                                                contenedor.style.display = 'block';
                                                const mensaje = contenedor.querySelector('.message');
                                                console.log('Error recibido:', data.error);
                                                mensaje.innerHTML = '';
                                                if(typeof data.error === 'object'){
                                                    Object.values(data.error).forEach(function(error) {
                                                        mensaje.innerHTML += `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">${error}</div>`;
                                                    });
                                                }else{
                                                    mensaje.innerHTML = `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">${data.error}</div>`;
                                                }
                                                ocultarLoader();
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
                                }else{
                                    // Si es el formulario de cuenta, solo navegar al siguiente paso
                                    accountID = formData.get('account');
                                    const step = this.getAttribute('data-step');
                                    recargarBrief(accountID);
                                    goToStep(step);
                                    console.log('accountID', accountID);
                                }
                            }
                        }
                    });
                });
            });

            const downloadButton = document.getElementById('btnDescargar');
            if (downloadButton) {
                downloadButton.addEventListener('click', function( event) {
                    // detener cualquier evento previo
                    event.preventDefault();
                    event.stopPropagation();

                    if(accountID !== null && !isNaN(accountID)) {
                        // la url de descarga es la ruta con el nombre investigacion.download y hay que enviar el id_generated Route::get('investigacion/{generated}/download', [InvestigacionController::class, 'download'])->name('investigacion.download');
                        const downloadUrl = "{{ url('/investigacion') }}/" + id_generated + "/download";
                        console.log('Intentando descarga desde URL:', downloadUrl);
                        window.location.href = downloadUrl;
                    }
                    
                });
            }

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

        function mostrarRespuestaInvestigacion(data) {
            // Procesar la respuesta
            procesarRespuestaInvestigacion(data.data);

            var fuenteslist = document.getElementById('fuentes-lista');
            fuenteslist.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

            if (Array.isArray(data.sources)) {
                let lista = "<ul>";
                data.sources.forEach(fuente => {
                    // Convertir la fuente en un enlace clickeable
                    lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                });
                lista += "</ul>";
                fuenteslist.innerHTML = lista;
            } else {
                fuenteslist.innerHTML = "<p>No hay fuentes disponibles.</p>";
            }
        }

        // Función para procesar la respuesta de investigación (caso legacy)
        function procesarRespuestaInvestigacion(details) {
            // Elimina los separadores '---' del Markdown
            let markdownContent = details.replace(/^---$/gm, '');

            // Convierte Markdown a HTML
            let htmlContent = marked.parse(markdownContent);

            // Elimina saltos de línea y espacios innecesarios
            htmlContent = htmlContent.replace(/>\s+</g, '><').replace(/\n/g, '');

            investigacionHTML = htmlContent;

            if (quill_investigacion) {
                quill_investigacion.root.innerHTML = htmlContent;
                setTimeout(() => {
                    quill_investigacion.update();
                    const editorContainer = document.querySelector('#editorinvestigacion');
                    if (editorContainer) {
                        editorContainer.style.minHeight = '500px';
                        editorContainer.scrollTop = 0;
                    }
                }, 500);
            }

            const investigacionInput = document.getElementById('investigaciongenerada');
            if (investigacionInput) {
                investigacionInput.value = htmlContent;
            }
        }

        // Función para iniciar el polling de la investigación
        function iniciarPollingInvestigacion(generationId, contenedor) {
            const mensaje = contenedor.querySelector('.message');
            if (mensaje) {
                mensaje.innerHTML = `
                    <div class="alert alert-info">
                        <div class="flex items-center">
                            <div class="spinner-border animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full mr-2" role="status"></div>
                            <span>Generando investigación... Esto puede tomar varios minutos.</span>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <div>ID de generación: ${generationId}</div>
                            <div>Consultando estado cada 5 segundos...</div>
                        </div>
                    </div>
                `;
            }

            // Función de polling
            const pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(`{{ route('investigacion.estado', '') }}/${generationId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });

                    const data = await response.json();
                    console.log('Estado de generación:', data);

                    // if (data.success) {
                        if (data.status === 'completed') {
                            // La investigación está completa
                            clearInterval(pollInterval);
                            console.log('Investigación completada');
                            
                            // Procesar la respuesta
                            mostrarRespuestaInvestigacion(data);
                            
                            // Ocultar mensaje de procesamiento
                            if (mensaje) {
                                mensaje.innerHTML = `
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Investigación completada exitosamente
                                    </div>
                                `;
                            }

                            // Navegar al siguiente paso después de un breve delay
                            setTimeout(() => {
                                contenedor.style.display = 'none';
                                goToStep(3);
                                ocultarLoader();
                            }, 2000);
                            
                        } else if (data.success === false) {
                            // Error en la generación
                            clearInterval(pollInterval);
                            console.error('Error en la generación:', data.error);
                            
                            if (mensaje) {
                                mensaje.innerHTML = `
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Error al generar la investigación: ${data.error}
                                    </div>
                                `;
                            }
                            
                            contenedor.style.display = 'block';
                            ocultarLoader();
                            
                        } else if (data.status === 'processing') {
                            // Aún en proceso, actualizar contador o información
                            console.log('Investigación aún en proceso...');
                        }
                    // } else {
                    //     throw new Error(data.error || 'Error al consultar estado');
                    // }
                } catch (error) {
                    console.error('Error en polling:', error);
                    clearInterval(pollInterval);
                    
                    if (mensaje) {
                        mensaje.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Error al consultar estado: ${error.message}
                            </div>
                        `;
                    }
                    
                    contenedor.style.display = 'block';
                }
            }, 5000); // Consultar cada 5 segundos

            // Timeout de seguridad (máximo 10 minutos)
            setTimeout(() => {
                clearInterval(pollInterval);
                if (mensaje) {
                    mensaje.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-clock mr-2"></i>
                            Tiempo de espera agotado. La investigación puede estar aún en proceso.
                        </div>
                    `;
                }
                contenedor.style.display = 'block';
            }, 600000); // 10 minutos
        }
    </script>
</x-app-layout>