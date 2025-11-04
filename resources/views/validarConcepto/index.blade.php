<x-app-layout>
    <x-slot name="title">Génesis - Validar Concepto</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Validar Concepto') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="block p-3"></div>

                    <div id="Content" class="block max-w-2xl mx-auto">
                        <div class="step step-1" >
                            @include('validarConcepto.steps.step1')
                        </div>

                        <div class="step step-2" style="display: none;">
                            @include('validarConcepto.steps.step2')
                        </div>
                    
                        <div class="step step-3" style="display: none;">
                            @include('validarConcepto.steps.step3')
                        </div>

                        <div class="step step-4" style="display: none;">
                            @include('validarConcepto.steps.step4')
                        </div>

                        <div class="step step-5" style="display: none;">
                            @include('validarConcepto.steps.step5')
                        </div>

                        <div class="loader" id="loader" style="display: none;">
                            cargando ...
                        </div>
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
        #loader, #progress-container {
            opacity: 1;
            transition: none;
        }
        .step {
            transition: display 0.3s ease-in-out;
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
        #loader {
            position: relative;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #loader.fade-out {
            opacity: 0;
            transform: translateY(-10px);
        }
        #loader.fade-in {
            opacity: 1;
            transform: translateY(0);
        }
        #progress-container {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            let id_generated =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['id_generated']) ? $data_generated['id_generated'] : 'null' !!}; // id_generated su valor puede venir desde el controlador como null o un array
            let accountID =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['account_id']) ? $data_generated['account_id'] : 'null' !!}; // accountID su valor puede venir desde el controlador como null o un valor
            let step =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['step']) ? $data_generated['step'] : 'null' !!}; // step su valor puede venir desde el controlador como null o un valor
            let metadata =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['metadata']) ? json_encode($data_generated['metadata']) : 'null' !!}; // metadata es un texto json que debemos convertir a un objeto
            let genesisID =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['metadata']) && isset($data_generated['metadata']['id_genesis']) ? $data_generated['metadata']['id_genesis'] : 'null' !!}; // genesisID su valor puede venir desde el controlador como null o un valor

            console.log('id_generated', id_generated);
            console.log('accountID', accountID);
            console.log('step', step);
            console.log('genesisID', genesisID);

            let mensajeIndex = 0;
            let mensajeInterval;
            let duracionMensaje = 5000;
            let procesando = false;
            let progressBar;
            let progressText;
            let progressContainer;

            const loaderDiv = document.getElementById('loader');
            loaderDiv.insertAdjacentHTML('beforebegin', `
                <div id="progress-container" style="display: none;" class="w-full max-w-md mx-auto mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                        <div id="progress-bar" 
                            class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                            style="width: 0%;">
                        </div>
                    </div>
                <div style="font-size:30px !important;"" id="progress-text" class="text-center text-sm mt-2 text-gray-600 dark:text-gray-300 font-bold">0%</div>

                </div>
            `);

            var quillConcepto = new Quill('#editor-container-concepto', {theme: 'snow'});

            if(step){
                mostrarLoader();
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
                    recargarGenesis(accountID);
                    let form = document.getElementById('step2Form');

                    let inputgenesis = form.querySelector('select[name="genesis"]');
                    if (inputgenesis) {
                        seleccionarCuandoExista(inputgenesis, metadata['id_genesis']);
                        function seleccionarCuandoExista(select, valor, intervalo = 300, maxIntentos = 10) {
                            let intentos = 0;

                            function intentar() {
                                const opcionExiste = Array.from(select.options).some(opt => opt.value === valor);
                                if (opcionExiste) {
                                    select.value = valor;
                                    select.dispatchEvent(new Event('change')); // opcional
                                } else {
                                    intentos++;
                                    if (intentos < maxIntentos) {
                                        setTimeout(intentar, intervalo);
                                    }
                                }
                            }
                            intentar();
                        }

                    }
                    goToStepFinal = true;
                }
                if(step >= 3){
                    let form = document.getElementById('step3Form');
                    let inputConceptoPais = form.querySelector('input[name="concepto_pais"]');
                    let inputConceptoNombreMarca = form.querySelector('input[name="concepto_nombre_marca"]');
                    let inputConceptoConcepto = form.querySelector('textarea[name="concepto_concepto"]');
                    let inputConceptoCategoria = form.querySelector('input[name="concepto_categoria"]');
                    let inputConceptoPeriodoCampania = form.querySelector('input[name="concepto_periodo_campania"]');
                    if(inputConceptoCategoria && metadata['concepto_categoria']){
                        inputConceptoCategoria.value = metadata['concepto_categoria'];
                    }
                    if(inputConceptoPeriodoCampania && metadata['concepto_periodo_campania']){
                        inputConceptoPeriodoCampania.value = metadata['concepto_periodo_campania'];
                    }
                    if(inputConceptoConcepto && metadata['concepto_concepto']){
                        inputConceptoConcepto.value = metadata['concepto_concepto'];
                    }
                    if(inputConceptoNombreMarca && metadata['concepto_nombre_marca']){
                        inputConceptoNombreMarca.value = metadata['concepto_nombre_marca'];
                    }
                    if(inputConceptoPais && metadata['concepto_pais']){
                        inputConceptoPais.value = metadata['concepto_pais'];
                    }
                    if(genesisID){
                        cargarFormConcepto(genesisID);
                    }
                    goToStepFinal = true;
                }
                if(step >= 4){
                    mostrarLoader('mensajesValidarConcepto');
                    let form = document.getElementById('step4Form');
                    let status =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['status']) ? json_encode($data_generated['status']) : 'null' !!};
                    

                    if(status != 'completed'){
                        if( (metadata['id_generacion_concepto']) && (metadata['generacion_concepto_data'])){
                            if(!metadata['generacion_concepto_status'] || metadata['generacion_concepto_status'] === 'pending'){
                                pollingValidarConcepto(id_generated);
                                goToStepFinal = false;
                            }
                            if(metadata['generacion_concepto_status'] === 'completed'){
                                let dataConcepto = {data: metadata['generacion_concepto_content'], sources: metadata['generacion_concepto_sources']};
                                mostrarConceptoValidado(dataConcepto);
                                goToStepFinal = true;
                            }
                        }
                    }else{
                        let name =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['name']) ? json_encode($data_generated['name']) : 'null' !!};
                        let value =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['value']) ? json_encode($data_generated['value']) : 'null' !!};
                        let rating =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['rating']) ? json_encode($data_generated['rating']) : 'null' !!};

                        let dataConcepto = {data: value, sources: metadata['generacion_concepto_sources'], name: name, rating: rating};
                        mostrarConceptoValidado(dataConcepto);

                        form.querySelector('[name="file_name"]').value = name;
                        form.querySelector('[name="rating"]').value = rating;

                        goToStepFinal = true;
                    }
                }
                if(step >= 5){
                    let value =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['value']) ? json_encode($data_generated['value']) : 'null' !!};
                    mostrarConceptoValidadoGuardado(value);
                    goToStepFinal = true;
                }
                if(goToStepFinal){
                    goToStep(step);
                }
            }

            // Selecciona todos los contenedores de pasos
            const contenedores = document.querySelectorAll('.step');

            contenedores.forEach(function(contenedor) {
                // Aquí necesito capturar el form y el botón de todos los contenedores
                const form = contenedor.querySelectorAll('form');
                form.forEach(function(form) {
                    const btnForm = form.querySelectorAll('button');
                    btnForm.forEach(function(btnForm) {
                        btnForm.addEventListener('click', function(event) {
                            event.preventDefault();
                            const btnForm = this.getAttribute('data-btnForm');
                            // let formData = new FormData(form);
                            if (btnForm && functionMapActions[btnForm]) {
                                // 👇 Llamada dinámica según el nombre recibido
                                functionMapActions[btnForm](form, this);
                                
                            }else{
                                const step = this.getAttribute('data-step');
                                if(step){
                                    mostrarLoader();
                                    console.log('step', step);
                                    goToStep(step);
                                }
                            }
                        });
                    });
                });
            });

            const functionMapActions = {
                accountForm: (form, button) => {
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader();
                        accountID = formData.get('account');
                        recargarGenesis(accountID);
                        goToStep(2);
                    }
                },
                selectGenesisForm: (form, button) => {
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader();
                        genesisID = formData.get('genesis');
                        cargarFormConcepto(genesisID);
                        goToStep(3);
                    }
                },
                validarConceptoForm: (form, button) => {
                    let formData = new FormData(form);
                    // volver a obtener el form actualizado desde el DOM
                    let currentForm = document.querySelector('#step3Form');
                    currentForm.querySelectorAll(':disabled').forEach(el => el.remove());
                    let formDataUpdated = new FormData(currentForm);
                    console.log('action', currentForm.action);
                    // Clonar el formulario sin los disabled
                    let formClone = currentForm.cloneNode(true);
                    formClone.querySelectorAll(':disabled').forEach(el => el.remove());

                    if(ValidarCampos(formClone)){
                        mostrarLoader();
                        formDataUpdated.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formDataUpdated.append('id_generated', id_generated);
                        }
                        if(genesisID !== null && !isNaN(genesisID)){
                            formDataUpdated.append('id_genesis', genesisID);
                        }
                        sendForm(currentForm.action, formDataUpdated);
                    }
                },
                guardarConceptoForm: (form, button) => {
                    let inputvalidarconcepto = form.querySelector('input[name="validarConcepto"]');
                    if (inputvalidarconcepto) {
                        inputvalidarconcepto.value = quillConcepto.getSemanticHTML();
                    }
                    let formData = new FormData(form);
                    if(ValidarCampos(form)){
                        mostrarLoader();
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        console.log('formData', formData);
                        sendForm(form.action, formData);
                    }
                },
                mejorarConceptoForm: (form, button) => {
                    let inputvalidarconcepto = form.querySelector('input[name="validarConcepto"]');
                    if (inputvalidarconcepto) {
                        inputvalidarconcepto.value = quillConcepto.getSemanticHTML();
                    }
                    let formData = new FormData(form);
                    // if(ValidarCampos(form)){
                    let actionbutton = button.getAttribute('data-route');
                    mostrarLoader();
                    formData.append('id_account', accountID);
                    if(id_generated !== null && !isNaN(id_generated)){
                        formData.append('id_generated', id_generated);
                    }
                    if(genesisID !== null && !isNaN(genesisID)){
                        formData.append('id_genesis', genesisID);
                    }
                    console.log('formData', formData);
                    sendForm(actionbutton, formData);
                    // }
                },
                downloadValidarConceptoForm: (form, button) => {
                    let formData = new FormData(form);
                    console.log(button);
                    let actionbutton = "{{ route('generated.download', ':id') }}".replace(':id', id_generated);
                    window.location.href = actionbutton;
                }
            }

            // Definimos un "mapa" de funciones disponibles
            const functionMapResponse = {
                getValidarConceptoForm: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Validación en proceso, esto puede tomar varios minutos. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                    console.log('data', data);
                    pollingValidarConcepto(id);
                },
                saveValidarConcepto: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Validar concepto guardado correctamente.',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                    mostrarConceptoValidadoGuardado(data);
                    goToStep(5);
                },
                mejorarConcepto: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        icon: 'info',
                        title: 'Mejorar concepto en proceso en un nuevo generado',
                        text: '¿Desea ver el resultado del concepto mejorado o desea continuar aquí?',
                        showCancelButton: true,
                        confirmButtonText: 'Ver resultado',
                        cancelButtonText: 'Continuar aquí',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('herramienta2.index', '') }}?generated=" + id;
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // El usuario eligió "Continuar aquí", solo cierra el modal
                            
                        }
                    });
                    goToStep(4);
                }
                // puedes seguir agregando aquí
            };

            function cargarFormConcepto(id_genesis){
                let contenedor = document.querySelector('.step-3');
                let formConcepto = contenedor.querySelector('form');
                if(id_genesis){
                    formConcepto.action = "{{ route('validar-concepto.getValidarConceptoGenesis') }}";
                    // ocultar todos los divs que tengan la clase not-genesis
                    let divsNotGenesis = contenedor.querySelectorAll('.not-genesis');
                    divsNotGenesis.forEach(div => {
                        div.style.display = 'none';
                        //buscar los inputs, select y textarea dentro de el div y deshabilitarlos
                        let inputs = div.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.disabled = true;
                        });
                        let selects = div.querySelectorAll('select');
                        selects.forEach(select => {
                            select.disabled = true;
                        });
                        let textareas = div.querySelectorAll('textarea');
                        textareas.forEach(textarea => {
                            textarea.disabled = true;
                        });
                    });
                }else{
                    formConcepto.action = "{{ route('validar-concepto.getValidarConceptoForm') }}";
                    // mostrar todos los divs que tengan la clase not-genesis
                    let divsNotGenesis = contenedor.querySelectorAll('.not-genesis');
                    divsNotGenesis.forEach(div => {
                        div.style.display = 'block';
                        //buscar los inputs dentro de el div y habilitarlos
                        let inputs = div.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.disabled = false;
                        });
                        let selects = div.querySelectorAll('select');
                        selects.forEach(select => {
                            select.disabled = false;
                        });
                        let textareas = div.querySelectorAll('textarea');
                        textareas.forEach(textarea => {
                            textarea.disabled = false;
                        });
                    });
                }
            }

            function pollingValidarConcepto(id){
                const contenedor = document.querySelector('.step-3');
                const mensaje = contenedor.querySelector('.message');
                // Función de polling
                const pollInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`{{ route('validar-concepto.get_concepto', '') }}/${id}`, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        });
                        const data = await response.json();
                        if(!data.success){
                            throw new Error(data.error);
                        }
                        if(data.success && data.status === 'completed'){
                            clearInterval(pollInterval);
                            console.log('Validación completada');
                            mostrarConceptoValidado(data);
                            goToStep(4);
                        }else if(data.success && data.status === 'processing'){
                            console.log('Validación aún en proceso...');
                        }else{
                            throw new Error('Error al consultar estado');
                        }

                    }catch(error){
                        console.error('Error en polling:', error);
                        clearInterval(pollInterval);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al consultar estado: ' + error.message,
                            showConfirmButton: false
                        });
                        // if (mensaje) {
                        //     mensaje.innerHTML = `
                        //         <div class="alert alert-danger">
                        //             <i class="fas fa-exclamation-triangle mr-2"></i>
                        //             Error al consultar estado: ${error.message}
                        //         </div>
                        //     `;
                        // }
                        // contenedor.style.display = 'block';
                        // ocultarLoader();
                    }
                }, 10000);

                // Timeout de seguridad (máximo 10 minutos)
                setTimeout(() => {
                    clearInterval(pollInterval);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Tiempo de espera agotado. La validación de concepto puede estar aún en proceso. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                }, 600000); // 10 minutos
            }

            function mostrarConceptoValidado(data){
                let contenedor = document.querySelector('.step-4');
                // var formConcepto = contenedor.querySelector('#form-concepto');
                quillConcepto.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                var ResultadoConcepto = contenedor.querySelector('#ResultadoConcepto');

                var fuenteslistconcepto = contenedor.querySelector('#fuentes-lista-concepto');
                fuenteslistconcepto.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

                if (Array.isArray(data.sources) && data.sources.length > 0) {
                    let lista = "<ul>";
                    data.sources.forEach(fuente => {
                        // Convertir la fuente en un enlace clickeable
                        lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                    });
                    lista += "</ul>";
                    fuenteslistconcepto.innerHTML = lista;
                } else {
                    fuenteslistconcepto.innerHTML = "<p>No hay fuentes disponibles.</p>";
                }
                if(genesisID){
                    // buscar el boton con el data-btnForm="mejorarCampaniaForm"
                    let btnMejorarConcepto = contenedor.querySelector('[data-btnForm="mejorarConceptoForm"]');
                    btnMejorarConcepto.style.display = 'block';
                }
                // goToStep(4);
            }

            function mostrarConceptoValidadoGuardado(data){
                var containerResultValidarConcepto = document.getElementById('container-result-validar-concepto');
                containerResultValidarConcepto.innerHTML = data;
            }

            function goToStep(nextStep) {
                console.log('nextStep', nextStep);
                step = nextStep;
                // Ocultar todos los pasos primero
                document.querySelectorAll('.step').forEach(step => {
                    step.style.display = 'none';
                });
                ocultarLoader();
                setTimeout(() => {
                    // Mostrar el paso deseado
                    let nextStepDiv = document.querySelector('.step-' + nextStep);
                    if (nextStepDiv) {
                        nextStepDiv.style.display = 'block';
                    }
                }, 500);
            }

            function sendForm(formAction, formData) {
                fetch(formAction, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error HTTP: " + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('data', data);
                    if (data.success && data.data) {
                        console.log("✅ Éxito:", data);
                        if (data.function && functionMapResponse[data.function]) {
                            // 👇 Llamada dinámica según el nombre recibido
                            functionMapResponse[data.function](data.data, data.id_generated);
                        }
                    }else if(data.error && typeof data.error === "object"){
                        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());

                        Object.entries(data.error).forEach(([campo, mensajes]) => {
                            const input = document.querySelector(`input[name="${campo}"]`);
                            if(input){
                                // Subimos al padre y al padre del padre
                                const primerDiv = input.parentElement;
                                const ul = document.createElement("ul");
                                ul.className = "text-sm text-red-600 dark:text-red-400 space-y-1 mt-2 invalid-feedback";

                                mensajes.forEach(msg => {
                                    const li = document.createElement("li");
                                    li.textContent = msg;
                                    ul.appendChild(li);
                                });

                                // Insertar después del div del padre del padre
                                primerDiv.insertAdjacentElement("afterend", ul);
                            }

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: mensajes,
                                showConfirmButton: false,
                                timer: 6000,
                                timerProgressBar: true
                            });
                        });
                        goToStep(step);
                    } else {
                        throw new Error(data.error || "Ocurrió un error en el servidor");
                    }
                })
                .catch(error => {
                    console.error("❌ Error:", error);
                    ocultarLoader();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: error.message || 'Error inesperado',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                    goToStep(step);
                });
            }

            function recargarGenesis(accountID){
                let url = "{{ route('getGeneratedGenesisV2') }}";
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
                    let selectElement = document.querySelector('select[name="genesis"]');
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

            /**
             * Inicio loader
             */

            function mostrarLoader(proceso = null) {
                procesando = true;
                const loader = document.getElementById('loader');
                progressBar = document.getElementById('progress-bar');
                progressText = document.getElementById('progress-text');
                progressContainer = document.getElementById('progress-container');

                // Ocultar todos los pasos primero
                document.querySelectorAll('.step').forEach(step => {
                    step.style.display = 'none';
                });
                
                // Reiniciar el índice y limpiar cualquier intervalo anterior
                mensajeIndex = 0;
                if (mensajeInterval) {
                    clearInterval(mensajeInterval);
                }
                
                // Limpiar inmediatamente el mensaje anterior
                loader.textContent = '';
                
                // Asegurarse de que los elementos estén completamente reseteados
                loader.style.opacity = '0';
                progressContainer.style.opacity = '0';
                loader.style.display = 'block';
                progressContainer.style.display = 'block';
                actualizarProgreso(0);
                
                // Ajustar la duración según el proceso
                const duracionProceso = proceso === 'bajadacreativa' ? 8000 : duracionMensaje;
                
                setTimeout(() => {
                    const mensajesProceso = seleccionarMensajes(proceso);
                    
                    requestAnimationFrame(() => {
                        loader.style.opacity = '1';
                        progressContainer.style.opacity = '1';
                        mostrarMensajeConEfecto(loader, mensajesProceso[0]);
                        mensajeIndex = 1;
                        
                        // if (proceso === 'regenerar') {
                        //     let progreso = 0;
                        //     mensajeInterval = setInterval(() => {
                        //         if (!procesando || progreso >= 95) {
                        //             clearInterval(mensajeInterval);
                        //             return;
                        //         }
                        //         progreso += 5;
                        //         actualizarProgreso(progreso);
                        //     }, duracionMensaje / 20);
                        // } else if (proceso === 'bajadacreativa') {
                        //     // Manejo especial para bajada creativa
                        //     const progresoPorMensaje = 85 / mensajesProceso.length; // Dejamos más espacio al final
                        //     mensajeInterval = setInterval(() => {
                        //         if (!procesando || mensajeIndex >= mensajesProceso.length) {
                        //             clearInterval(mensajeInterval);
                        //             return;
                        //         }
                                
                        //         mostrarMensajeConEfecto(loader, mensajesProceso[mensajeIndex]);
                        //         actualizarProgreso(progresoPorMensaje * (mensajeIndex + 1));
                        //         mensajeIndex++;
                                
                        //         if (mensajeIndex >= mensajesProceso.length) {
                        //             // Progreso más lento al final
                        //             let progresoFinal = progresoPorMensaje * mensajeIndex;
                        //             const intervalFinal = setInterval(() => {
                        //                 if (!procesando || progresoFinal >= 95) {
                        //                     clearInterval(intervalFinal);
                        //                     return;
                        //                 }
                        //                 progresoFinal += 0.5;
                        //                 actualizarProgreso(progresoFinal);
                        //             }, 500);
                        //         }
                        //     }, duracionProceso);
                        // } else {
                            // Comportamiento normal para otros procesos
                            const progresoPorMensaje = 100 / mensajesProceso.length;
                            mensajeInterval = setInterval(() => {
                                if (!procesando || mensajeIndex >= mensajesProceso.length) {
                                    clearInterval(mensajeInterval);
                                    return;
                                }
                                
                                mostrarMensajeConEfecto(loader, mensajesProceso[mensajeIndex]);
                                actualizarProgreso(progresoPorMensaje * (mensajeIndex + 1));
                                mensajeIndex++;
                                
                                if (mensajeIndex >= mensajesProceso.length) {
                                    actualizarProgreso(95);
                                    clearInterval(mensajeInterval);
                                }
                            }, duracionMensaje);
                        // }
                    });
                }, 50);
            }

            function actualizarProgreso(porcentaje) {
                progressBar.style.width = `${porcentaje}%`;
                progressText.textContent = `${Math.round(porcentaje)}%`;
            }

            function mostrarMensajeConEfecto(loader, mensaje) {
                if (!procesando) return;
                
                // Primero hacemos fade out
                loader.classList.add('fade-out');
                loader.classList.remove('fade-in');
                
                setTimeout(() => {
                    if (procesando) {
                        // Cambiamos el texto cuando está invisible
                        loader.textContent = mensaje;
                        
                        // Forzamos un reflow
                        void loader.offsetHeight;
                        
                        // Hacemos fade in
                        loader.classList.remove('fade-out');
                        loader.classList.add('fade-in');
                    }
                }, 300); // Reducimos el tiempo de transición para que sea más fluido
            }

            function seleccionarMensajes(proceso = null) {
                const mensajesGenericos = [
                    "Cargando...",
                    "Procesando… Porque incluso los milagros necesitan un algoritmo.",
                    "Ahora invocaré a un séquito de asistentes expertos, cada uno con su toque divino, para iluminar este camino creativo.",
                    "Cada asistente comparte su sabiduría, como oráculos en la era digital que no se andan con rodeos.",
                ];
                
                const mensajesSeleccionarCuenta = [
                    "Seleccionando la cuenta...",
                    "Procesando… Porque incluso los milagros necesitan un algoritmo.",
                ];
                const mensajesSeleccionarGenesis = [
                    "Seleccionando el Genesis...",
                    "Procesando… Espera un momento.",
                ];
                const mensajesValidarConcepto = [
                    "Validando el concepto...",
                    "Procesando… Espera un momento.",
                    "Ahora invocaré a un séquito de asistentes expertos, cada uno con su toque divino, para iluminar este camino creativo.",
                    "Cada asistente comparte su sabiduría, como oráculos en la era digital que no se andan con rodeos.",
                ];
                switch(proceso) {
                    case 'mensajesSeleccionarCuenta':
                        return mensajesSeleccionarCuenta;
                    case 'mensajesSeleccionarGenesis':
                        return mensajesSeleccionarGenesis;
                    case 'mensajesValidarConcepto':
                        return mensajesValidarConcepto;
                    default:
                        return mensajesGenericos;
                }
            }

            function ocultarLoader() {
                if (!procesando) return;
                
                procesando = false;
                if (mensajeInterval) {
                    clearInterval(mensajeInterval);
                }
                
                const loader = document.getElementById('loader');
                const progressContainer = document.getElementById('progress-container');
                
                actualizarProgreso(100);
                
                // Ocultar con transición
                loader.style.opacity = '0';
                progressContainer.style.opacity = '0';
                
                setTimeout(() => {
                    loader.style.display = 'none';
                    progressContainer.style.display = 'none';
                    mensajeIndex = 0;
                }, 500);
            }

            /* Fin loader */
            const contentRating = document.querySelectorAll('.content-rating');
            contentRating.forEach(contentRating => {
                const stars = contentRating.querySelectorAll('.rating .fa-star');
                const ratingInput = contentRating.querySelector('input[name="rating"]');
                // const ratingInputValidarConcepto = contentRating.querySelector('input[name="rating-validar-concepto"]');

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
            })
        });
        
    </script>

</x-app-layout>