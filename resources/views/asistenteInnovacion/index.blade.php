<x-app-layout>
    <x-slot name="title">Génesis - Asistente Innovación</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
            {{ __('Asistente Innovación') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden">
                <div class="p-6 text-black dark:text-gray-100">
                    <div class="block p-3"></div>
                    
                    <div id="Content" class="block max-w-2xl mx-auto">
                        <div class="step step-1" >
                            @include('asistenteInnovacion.steps.step1')
                        </div>

                        <div class="step step-2" style="display: none;">
                            @include('asistenteInnovacion.steps.step2')
                        </div>
                    
                        <div class="step step-3" style="display: none;">
                            @include('asistenteInnovacion.steps.step3')
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
            var accountID = null;
            
            function goToStep(step) {
                var nextStepDiv = document.querySelector('.step-' + step);
                nextStepDiv.style.display = 'block';
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

                        if (ValidarCampos(form)) {
                            mostrarLoader();
                            contenedor.style.display = 'none';
                            var formData = new FormData(form);
                            if (form.id !== 'accountForm') {
                                if (accountID !== null && !isNaN(accountID)) {
                                    // Agregar el valor de accountID a formData
                                    formData.append('account', accountID);
                                }
                                fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                    }
                                }).then(response => response.json())
                                .then(data => {
                                    ocultarLoader();
                                    console.log(data);
                                    if(!data.error){
                                        if(data.function) {
                                            switch (data.function) {
                                                case 'asistenteInnovacionGenerate':
                                                    asistenteInnovacionGenerate(data.details.data);
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
                                    // document.querySelector('.loader').style.display = 'none';
                                    ocultarLoader();
                                    contenedor.style.display = 'block';
                                });
                                
                            }else{
                                accountID = formData.get('account');
                                const step = this.getAttribute('data-step');
                                goToStep(step);
                                // document.querySelector('.loader').style.display = 'none';
                                ocultarLoader();
                            }
                        };
                    });
                });
            });

            var btnGenerarPDF = document.getElementById('btnGenerarPDF');
            btnGenerarPDF.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                if (accountID !== null && !isNaN(accountID)) {
                    // Crear un formulario para enviar los datos por POST
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('asistente-innovacion.download') }}";

                    // Añadir el token CSRF (muy importante en Laravel para solicitudes POST)
                    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    var inputCsrf = document.createElement('input');
                    inputCsrf.type = 'hidden';
                    inputCsrf.name = '_token';
                    inputCsrf.value = csrfToken;
                    form.appendChild(inputCsrf);

                    // Añadir el campo accountID
                    var inputAccount = document.createElement('input');
                    inputAccount.type = 'hidden';
                    inputAccount.name = 'account';
                    inputAccount.value = accountID;
                    form.appendChild(inputAccount);

                    // Añadir el contenido de quill
                    var inputContent = document.createElement('input');
                    inputContent.type = 'hidden';
                    inputContent.name = 'asistenteInnovacionGenerateContainer';
                    inputContent.value = quill.getSemanticHTML();
                    form.appendChild(inputContent);

                    // var href = btnGenerarPDF.getAttribute('href');
                    // var urlConParametro = href + '?account=' + encodeURIComponent(accountID) + '&asistenteInnovacionGenerateContainer=' + encodeURIComponent(quill.getSemanticHTML());
                    // window.location.href = urlConParametro;

                    document.body.appendChild(form);
                    form.submit();
                    
                }
            });

        });
        

        var quill = new Quill('#asistenteInnovacionGenerateContainer', {
            theme: 'snow'
        });

        function asistenteInnovacionGenerate(data){
            quill.clipboard.dangerouslyPasteHTML(marked.parse(data));
        }

        const mensajes = [
            "Cargando...",
            "Procesando...",
            "Esto puede demorar un poco...",
            "La IA está trabajando para ti..."
        ];

        let mensajeIndex = 0;
        let mensajeInterval;

        function mostrarLoader() {
            const loader = document.getElementById('loader');
            loader.style.display = 'block'; // Mostrar el loader

            mensajeInterval = setInterval(() => {
                // Añadir clase para hacer fade out
                loader.classList.add('fade-out');

                // Esperar a que el fade out termine
                setTimeout(() => {
                    loader.textContent = mensajes[mensajeIndex];
                    mensajeIndex++;

                    // Si llegamos al final de los mensajes, reiniciamos
                    if (mensajeIndex >= mensajes.length) {
                        mensajeIndex = 0;
                    }

                    // Quitar clase fade-out para hacer fade in
                    loader.classList.remove('fade-out');
                }, 500); // Tiempo del fade out, debe coincidir con la duración en el CSS
            }, 2500); // Tiempo entre mensajes
        }

        function ocultarLoader() {
            clearInterval(mensajeInterval); // Detener el cambio de mensajes
            const loader = document.getElementById('loader');
            loader.style.display = 'none'; // Ocultar el loader
        }
    </script>
</x-app-layout>