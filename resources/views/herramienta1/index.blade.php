<x-app-layout>
    <x-slot name="title">Génesis - Brief</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Brief') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden">
                <div class="p-6 text-black dark:text-gray-100">
                    <div class="block p-3"></div>
                    
                    <div id="Content" class="block max-w-2xl mx-auto">
                        <!-- solo mostrar el step 1 si no existe un data_generated o si existe y es un array y existe el step 1 -->
                        <div class="step step-1" style="display: {{ !isset($data_generated['step']) || (isset($data_generated) && is_array($data_generated) && isset($data_generated['step']) && $data_generated['step'] == 1) ? 'block' : 'none' }};">
                            @include('herramienta1.steps.step1')
                        </div>

                        <div class="step step-2" style="display: none;">
                            @include('herramienta1.steps.step2')
                        </div>
                    
                        <div class="step step-3" style="display: none;">
                            @include('herramienta1.steps.step3')
                        </div>

                        <div class="step step-4" style="display: none;">
                            @include('herramienta1.steps.step4')
                        </div>

                        <div class="step step-5" style="display: none;">
                            @include('herramienta1.steps.step5')
                        </div>

                        <div class="step step-6" style="display: none;">
                            @include('herramienta1.steps.step6')
                        </div>

                        <div class="step step-7" style="display: none;">
                            @include('herramienta1.steps.step7')
                        </div>

                        <div class="step step-8" style="display: none;">
                            @include('herramienta1.steps.step8')
                        </div>

                        <div class="step step-9" style="display: none;">
                            @include('herramienta1.steps.step9')
                        </div>

                        <div class="step step-10" style="display: none;">
                            @include('herramienta1.steps.step10')
                        </div>

                        <div class="loader" id="loader" style="display: none;">Cargando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.bubble.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        #loader {
            opacity: 1;
            transition: opacity 0.5s ease-in-out; /* Transición suave */
        }
        #loader.fade-out {
            opacity: 0;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var allFormData = {};
            var id_generated = {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['id_generated']) ? $data_generated['id_generated'] : 'null' !!}; // id_generated su valor puede venir desde el controlador como null o un array primero verificar si existe un data_generated y es un array
            var accountID = {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['account_id']) ? $data_generated['account_id'] : 'null' !!}; // accountID su valor puede venir desde el controlador como null o un valor
            var step = {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['step']) ? $data_generated['step'] : 'null' !!}; // step su valor puede venir desde el controlador como null o un valor
            var metadata = {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['metadata']) ? json_encode($data_generated['metadata']) : 'null' !!}; // metadata es un texto json que debemos convertir a un objeto

            console.log('id_generated', id_generated);
            
            function goToStep(step) {
                var nextStepDiv = document.querySelector('.step-' + step);
                nextStepDiv.style.display = 'block';
            }

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
                    recargarInvestigation(accountID);
                    // seleccionar el pais y el nombre de la marca
                    let form = document.getElementById('step-2-form');
                    let country = form.querySelector('select[name="country"]');
                    if (country) {
                        country.value = metadata['country'];
                    }
                    let name = form.querySelector('input[name="name"]');
                    if (name) {
                        name.value = metadata['name'];
                    }
                    let slogan = form.querySelector('input[name="slogan"]');
                    if (slogan) {
                        slogan.value = metadata['slogan'];
                    }

                    let btnregresar = form.querySelector('.step-button');
                    if (btnregresar) {
                        btnregresar.style.display = 'none';
                    }
                    
                    goToStepFinal = true;
                }
                if(step >= 9){
                    quill_briefAIField.clipboard.dangerouslyPasteHTML(marked.parse(metadata['brief']));
                }
                goToStep(step)
            }

            // Selecciona todos los contenedores de pasos
            const contenedores = document.querySelectorAll('.step');
            
            contenedores.forEach(function(contenedor) {
                // Encuentra el botón dentro del contenedor
                const botones = contenedor.querySelectorAll('.step-button');;
                botones.forEach(function(boton) {
                    boton.addEventListener('click', function() {
                        const step = this.getAttribute('data-step');
                        contenedor.style.display = 'none';
                        goToStep(step);
                    });
                });
                const formButtonsSteps = contenedor.querySelectorAll('.form-button-step');;
                formButtonsSteps.forEach(function(formButtonsStep) {
                    formButtonsStep.addEventListener('click', function() {
                        const idFormButton = this.getAttribute('data-form');
                        var formButton = document.getElementById(idFormButton);
                        contenedor.style.display = 'none';
                        formButton.click();
                    });
                });
                const formButtons = contenedor.querySelectorAll('.form-button');
                formButtons.forEach(function(formBoton) {
                    formBoton.addEventListener('click', function(event) {
                        event.preventDefault();
                        var form = event.target.form;

                        var extraccionIAField = form.querySelector('input[name="extraccionIA"]');
                        if (extraccionIAField) {
                            // Obtener el contenido de Quill y asignarlo al campo extraccionIA
                            var quillContent = quill.getSemanticHTML();
                            extraccionIAField.value = quillContent;
                        }

                        var extraMarcaField = form.querySelector('input[name="extraMarca"]');
                        if (extraMarcaField) {
                            extraMarcaField.value = quill_extraMarca.getSemanticHTML();
                        }
                        var extraProductosField = form.querySelector('input[name="extraProductos"]');
                        if (extraProductosField) {
                            extraProductosField.value = quill_extraProductos.getSemanticHTML();
                        }
                        var extraCompetenciaField = form.querySelector('input[name="extraCompetencia"]');
                        if (extraCompetenciaField) {
                            extraCompetenciaField.value = quill_extraCompetencia.getSemanticHTML();
                        }
                        var extraEstudiosMercadoField = form.querySelector('input[name="extraEstudiosMercado"]');
                        if (extraEstudiosMercadoField) {
                            extraEstudiosMercadoField.value = quill_extraEstudiosMercado.getSemanticHTML();
                        }
                        var extraCiudadPaisEconomiaField = form.querySelector('input[name="extraCiudadPaisEconomia"]');
                        if (extraCiudadPaisEconomiaField) {
                            extraCiudadPaisEconomiaField.value = quill_extraCiudadPaisEconomia.getSemanticHTML();
                        }
                        var extraNecesidadesField = form.querySelector('input[name="extraNecesidades"]');
                        if (extraNecesidadesField) {
                            extraNecesidadesField.value = quill_extraNecesidades.getSemanticHTML();
                        }

                        var briefAIField = form.querySelector('input[id="Brief-GenerateIA"]');
                        if (briefAIField) {
                            briefAIField.value = quill_briefAIField.getSemanticHTML();
                        }

                        var briefField = form.querySelector('input[id="Brief"]');
                        if (briefField) {
                            briefField.value = quill_brief.getSemanticHTML();
                        }
                        

                        if (ValidarCampos(form)) {
                            // Verificar si vamos al paso 10 (mensaje de éxito)
                            const isStep10 = form.action.includes('saveBrief');
                            
                            if (!isStep10) {
                                mostrarLoader();
                            }
                            contenedor.style.display = 'none';
                            
                            var formData = new FormData(form);
                            if (form.id !== 'accountForm') {
                                if (accountID !== null && !isNaN(accountID)) {
                                    formData.append('account', accountID);
                                    if(id_generated !== null && !isNaN(id_generated)){
                                        formData.append('id_generated', id_generated);
                                    }

                                    fetch(form.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                        }
                                    }).then(response => response.json())
                                    .then(data => {
                                        if (!isStep10) {
                                            ocultarLoader();
                                        }
                                        console.log(data);
                                        if(!data.error){
                                            if(data.id_generated){
                                                console.log('ID generado:', data.id_generated);
                                                id_generated = data.id_generated;
                                            }
                                            if(data.function) {
                                                switch (data.function) {
                                                    case 'extraccionIA':
                                                        extraccionIA(data.details.data);
                                                        break;
                                                    case 'datosextras':
                                                        datosextras(data.details.data);
                                                        break;
                                                    case 'BriefGenerado':
                                                        BriefGenerado(data.details.data);
                                                        break;
                                                    case 'BriefGeneradoFormIA':
                                                        BriefGeneradoFormIA(data.details.data);
                                                        break;
                                                    default:
                                                        break;
                                                }                            
                                            }
                                            if(data.goto){
                                                contenedor.style.display = 'none';
                                                goToStep(data.goto);
                                            }else{
                                                contenedor.style.display = 'block';
                                            }
                                        }else{
                                            contenedor.style.display = 'block';
                                            const mensaje = contenedor.querySelector('.message');
                                            mensaje.innerHTML = '';
                                            mensaje.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                                        }
                                    }).catch(error => {
                                        if (!isStep10) {
                                            ocultarLoader();
                                        }
                                        contenedor.style.display = 'block';
                                    });
                                }
                            }else{
                                accountID = formData.get('account');
                                console.log('accountID', accountID);
                                recargarInvestigation(accountID);
                                const step = this.getAttribute('data-step');
                                goToStep(step);
                                if (!isStep10) {
                                    ocultarLoader();
                                }
                            }
                        };
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

        

        var quill = new Quill('#editor-container', {
            theme: 'snow'
        });

        var quill_extraMarca = new Quill('#editor-extraMarca', {
            theme: 'bubble'
        });
        var quill_extraProductos = new Quill('#editor-extraProductos', {
            theme: 'bubble'
        });
        var quill_extraCompetencia = new Quill('#editor-extraCompetencia', {
            theme: 'bubble'
        });
        var quill_extraEstudiosMercado = new Quill('#editor-extraEstudiosMercado', {
            theme: 'bubble'
        });
        var quill_extraCiudadPaisEconomia = new Quill('#editor-extraCiudadPaisEconomia', {
            theme: 'bubble'
        });
        var quill_extraNecesidades = new Quill('#editor-extraNecesidades', {
            theme: 'bubble'
        });

        var quill_briefAIField = new Quill('#contentBrief-GenerateIA', {
            theme: 'snow'
        });

        var quill_brief = new Quill('#contentBrief', {
            theme: 'snow'
        });
        
        function recargarInvestigation(accountID){
            let url = "{{ route('getGeneratedInvestigation') }}";
            let formData = new FormData();
            formData.append('accountID', accountID);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
            .then(data => {
                let selectElement = document.querySelector('select[name="investigation[]"]');
                selectElement.innerHTML = '';
                data.forEach(item => {
                    let option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = `${item.name}`;
                    selectElement.appendChild(option);
                });
                console.log('Options updated successfully!');
            }).catch(error => {
                console.error('Error:', error);
            });
        }

        function extraccionIA(data){
            quill.clipboard.dangerouslyPasteHTML(marked.parse(data));
        }

        function datosextras(data){
            console.log(data);
            quill_extraMarca.clipboard.dangerouslyPasteHTML(data['extraMarca']);
            quill_extraProductos.clipboard.dangerouslyPasteHTML(data['extraProductos']);
            quill_extraCompetencia.clipboard.dangerouslyPasteHTML(data['extraCompetencia']);
            quill_extraEstudiosMercado.clipboard.dangerouslyPasteHTML(data['extraEstudiosMercado']);
            quill_extraCiudadPaisEconomia.clipboard.dangerouslyPasteHTML(data['extraCiudadPaisEconomia']);
            quill_extraNecesidades.clipboard.dangerouslyPasteHTML(data['extraNecesidades']);
        }
        
        function BriefGenerado(data){
            // document.getElementById('contentBrief').innerHTML = marked.parse(data);
            quill_brief.clipboard.dangerouslyPasteHTML(marked.parse(data));
        }
        function BriefGeneradoFormIA(data){
           
            quill_briefAIField.clipboard.dangerouslyPasteHTML(marked.parse(data));
        }


        const mensajes = [
    "Cargando… La creación está en marcha.",
    "Recopilando tus datos… No hay detalle tan pequeño que se escape de la precisión divina.",
    "¿Esperabas milagros sin esfuerzo?",
    "Explorando la vastedad de internet para descubrir la sabiduría oculta sobre tu marca y su entorno.",
    "Un instante… La inspiración, al igual que las grandes ideas, toma su tiempo.",
    "Ordenando cada detalle… En Génesis by god-ai, creemos que hasta el átomo tiene un propósito sagrado para forjar una propuesta ganadora.",
    "Un momento, por favor… La divinidad no se apresura."
    ];

        let mensajeIndex = 0;
        let mensajeInterval;
        let duracionMensaje = 5000;
        let procesando = false;
        let progressBar;
        let progressText;
        let progressContainer;

        function actualizarProgreso(porcentaje) {
            progressBar.style.width = `${porcentaje}%`;
            progressText.textContent = `${Math.round(porcentaje)}%`;
        }

        function mostrarLoader() {
            procesando = true;
            const loader = document.getElementById('loader');
            progressBar = document.getElementById('progress-bar');
            progressText = document.getElementById('progress-text');
            progressContainer = document.getElementById('progress-container');
            
            loader.style.display = 'block';
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

        function mostrarMensajeConEfecto(loader, mensaje) {
            loader.classList.add('fade-out');
            
            setTimeout(() => {
                loader.textContent = mensaje;
                loader.classList.remove('fade-out');
            }, 500);
        }

        function ocultarLoader() {
            procesando = false;
            const loader = document.getElementById('loader');
            
            // Completar la barra de progreso antes de ocultar
            actualizarProgreso(100);
            
            setTimeout(() => {
                loader.classList.add('fade-out');
                progressContainer.classList.add('fade-out');
                
                setTimeout(() => {
                    loader.style.display = 'none';
                    progressContainer.style.display = 'none';
                    loader.classList.remove('fade-out');
                    progressContainer.classList.remove('fade-out');
                    mensajeIndex = 0;
                }, 500);
            }, 500);
        }

        // Primero agregamos el HTML para la barra de progreso justo antes del loader
        const loaderDiv = document.getElementById('loader');
        loaderDiv.insertAdjacentHTML('beforebegin', `
            <div id="progress-container" style="display: none;" class="w-full max-w-md mx-auto mb-4">
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                    <div id="progress-bar" 
                        class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                        style="width: 0%">
                    </div>
                </div>
                <div style="font-size:30px !important;"" id="progress-text" class="text-center text-sm mt-2 text-gray-600 dark:text-gray-300 font-bold">0%</div>
            </div>
        `);

        // Agregamos los estilos necesarios
        const style = document.createElement('style');
        style.textContent = `
            #loader, #progress-container {
                opacity: 1;
                transition: opacity 0.5s ease-in-out;
            }
            #loader.fade-out, #progress-container.fade-out {
                opacity: 0;
            }
            #progress-bar {
                transition: width 0.5s ease-out;
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>