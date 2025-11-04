<x-app-layout>
    <x-slot name="title">Génesis - Génesis</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Génesis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="block p-3"></div>
                    
                    <div id="Content" class="block max-w-2xl mx-auto">
                        <div class="step step-1" >
                            @include('herramienta2.steps.step1')
                        </div>

                        <div class="step step-2" style="display: none;">
                            @include('herramienta2.steps.step2')
                        </div>
                    
                        <div class="step step-3" style="display: none;">
                            @include('herramienta2.steps.step3')
                        </div>

                        <div class="step step-4" style="display: none;">
                            @include('herramienta2.steps.step4')
                        </div>

                        <div class="step step-5" style="display: none;">
                            @include('herramienta2.steps.step5')
                        </div>

                        <div class="step step-6" style="display: none;">
                            @include('herramienta2.steps.step6')
                        </div>

                        <div class="step step-7" style="display: none;">
                            @include('herramienta2.steps.step7')
                        </div>

                        <div class="step step-7-1" style="display: none;">
                            @include('herramienta2.steps.step7-1')
                        </div>

                        <div class="step step-7-2" style="display: none;">
                            @include('herramienta2.steps.step7-2')
                        </div>

                        <div class="step step-8" style="display: none;">
                            @include('herramienta2.steps.step8')
                        </div>

                        <div class="step step-8-1" style="display: none;">
                            @include('herramienta2.steps.step8-1')
                        </div>

                        <div class="step step-9" style="display: none;">
                            @include('herramienta2.steps.step9')
                        </div>

                        <div class="step step-10" style="display: none;">
                            @include('herramienta2.steps.step10')
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

            var allFormData = {};
            var id_generated =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['id_generated']) ? $data_generated['id_generated'] : 'null' !!}; // id_generated su valor puede venir desde el controlador como null o un array primero verificar si existe un data_generated y es un array
            var accountID =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['account_id']) ? $data_generated['account_id'] : 'null' !!}; // accountID su valor puede venir desde el controlador como null o un valor
            var step =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['step']) ? $data_generated['step'] : 'null' !!}; // step su valor puede venir desde el controlador como null o un valor
            var metadata =  {!! isset($data_generated) && is_array($data_generated) && isset($data_generated['metadata']) ? json_encode($data_generated['metadata']) : 'null' !!}; // metadata es un texto json que debemos convertir a un objeto

            console.log('id_generated', id_generated);
            console.log('accountID', accountID);
            console.log('step', step);

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

            var quillGenesis = new Quill('#editorGenesis', {theme: 'snow'});
            var quillGenesisOld = new Quill('#RegenerateGenesisOld', {theme: 'snow'});
            var quillEscenario = new Quill('#editor-container-construccionescenario', {theme: 'snow'});
            var quillEscenarioOld = new Quill('#RegenerateEscenarioOld', {theme: 'snow'});
            // var quillEstrategia = new Quill('#editor-container-construccionEstrategia', {theme: 'snow'});
            // var quillCreatividad = new Quill('#editor-container-construccionCreatividad', {theme: 'snow'});
            // var quillIdeasContenido = new Quill('#editor-container-construccionIdeasContenido', {theme: 'snow'});
            var quillConcepto = new Quill('#editor-container-concepto', {theme: 'snow'});
            var quillConceptoMejorado = new Quill('#editor-container-construccionescenario-concepto-mejorado', {theme: 'snow'});
            var quillCreatividad = new Quill('#editor-container-creatividad', {theme: 'snow'});
            var quillCreatividadOld = new Quill('#RegenerateCreatividadOld', {theme: 'snow'});
            var quillEstrategia = new Quill('#editor-container-estrategia', {theme: 'snow'});
            var quillEstrategiaOld = new Quill('#RegenerateEstrategiaOld', {theme: 'snow'});
            var quillIdeasContenido = new Quill('#editor-container-ideas-contenido', {theme: 'snow'});
            var quillIdeasContenidoOld = new Quill('#RegenerateIdeasContenidoOld', {theme: 'snow'});
            
            if(step){
                // crear una variable boleana para verifar si al final hay que hacer goToStep
                let goToStepFinal = false;
                if(step >= 1){
                    let form = document.getElementById('accountForm');
                    let account = form.querySelector('select[name="account"]');
                    if (account) {
                        account.value = accountID;
                    }
                    // goToStep(step);
                    goToStepFinal = true;
                }
                if(step >= 2){
                    recargarBrief(accountID);
                    recargarInvestigation(accountID);
                    let stepAccount = document.querySelector('.step-1');
                    stepAccount.style.display = 'none';
                    let form = document.getElementById('step2Form');
                    let input360_objective = form.querySelector('textarea[name="360_objective"]');
                    if (input360_objective) {
                        input360_objective.value = metadata['objective'];
                    }
                    
                    let btnregresar = form.querySelector('.step-button');
                    if (btnregresar) {
                        btnregresar.style.display = 'none';
                    }
                    
                    let inputbrief = form.querySelector('select[name="brief"]');
                    if (inputbrief) {
                        seleccionarCuandoExista(inputbrief, metadata['id_brief']);
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

                    let inputinvestigation = form.querySelector('select[name="investigation"]');
                    if (inputinvestigation) {
                        seleccionarCuandoExista(inputinvestigation, metadata['id_investigation']);
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
                    // goToStep(step);
                    goToStepFinal = true;
                }
                
                if(step >= 3){
                    let form = document.getElementById('step3Form');
                    let dataGenesis = {data: metadata['genesis'], fuentes: metadata['genesis_insight_fuentes']};
                    generarGenesis(dataGenesis);
                    let btnregresar = form.querySelector('.step-button');
                    if (btnregresar) {
                        btnregresar.style.display = 'none';
                    }
                    // goToStep(step);
                    goToStepFinal = true;
                }

                if(step >= 4){
                    let dataEscenario = {data: metadata['construccionescenario'], fuentes: metadata['escenario_insight_fuentes']};
                    construccionescenario(dataEscenario);
                    // goToStep(step);
                    goToStepFinal = true;
                }
                
                if(step >= 5){
                    let formConcepto = document.getElementById('step5Form');
                    let inputConceptoCategoria = formConcepto.querySelector('input[name="concepto_categoria"]');
                    let inputConceptoPeriodoCampania = formConcepto.querySelector('input[name="concepto_periodo_campania"]');
                    if(inputConceptoCategoria && metadata['concepto_categoria']){
                        inputConceptoCategoria.value = metadata['concepto_categoria'];
                    }
                    if(inputConceptoPeriodoCampania && metadata['concepto_periodo_campania']){
                        inputConceptoPeriodoCampania.value = metadata['concepto_periodo_campania'];
                    }
                    goToStepFinal = true;
                }
                if(step >= 6 && step < 9){
                    let step6Form = document.getElementById('step6Form')
                    let input360_Tipo_de_campaña = step6Form.querySelector('select[name="360_Tipo_de_campaña"]');
                    if(input360_Tipo_de_campaña && metadata['tipo_de_campaña']){
                        input360_Tipo_de_campaña.value = metadata['tipo_de_campaña'];
                    }
                    // goToStep(step);
                    goToStepFinal = true;
                }
                
                if(step >= 7 && step < 9){
                    if(!metadata['generacion_creatividad_status'] || metadata['generacion_creatividad_status'] === 'processing'){
                        mostrarLoader('construccionCreatividad');
                        pollingconstruccionCreatividad(id_generated);
                        goToStepFinal = false;
                    }else{
                        let dataCreatividad = {data: metadata['generacion_creatividad_content'], fuentes: metadata['generacion_creatividad_sources']};

                        construccionCreatividad(dataCreatividad);
                        goToStepFinal = true;
                    }
                }

                if(step >= 7.1 && step < 9){
                    if(!metadata['generacion_estrategia_status'] || metadata['generacion_estrategia_status'] === 'processing'){
                        mostrarLoader('construccionEstrategia');
                        pollingconstruccionEstrategia(id_generated);
                        goToStepFinal = false;
                    }else{
                        let dataEstrategia = {data: metadata['generacion_estrategia_content'], fuentes: metadata['generacion_estrategia_sources']};
                        construccionEstrategia(dataEstrategia);
                        goToStepFinal = true;
                    }
                }

                if(step >= 7.2 && step < 9){
                    if(!metadata['generacion_ideas_contenido_status'] || metadata['generacion_ideas_contenido_status'] === 'processing'){
                        mostrarLoader('construccionIdeasContenido');
                        pollingconstruccionIdeasContenido(id_generated);
                        goToStepFinal = false;
                    }else{
                        let dataIdeasContenido = {data: metadata['generacion_ideas_contenido_content'], fuentes: metadata['generacion_ideas_contenido_sources']};
                        construccionIdeasContenido(dataIdeasContenido);
                        goToStepFinal = true;
                    }
                }

                if(step >= 8 && step < 9){
                    let dataEstrategia = {genesis: metadata['genesis'], fuentesGenesis: metadata['genesis_insight_fuentes_html'], escenario: metadata['construccionescenario'], fuentesEscenario: metadata['escenario_insight_fuentes_html'], creatividad: metadata['generacion_creatividad_content'], estrategia: metadata['generacion_estrategia_content'], contenido: metadata['generacion_ideas_contenido_content']};

                    mostrarEstrategiaCreatividadInnovacion(dataEstrategia);
                    // goToStep(step);
                    goToStepFinal = true;
                }
                if(step == 9){
                    if( (metadata['id_generacion_mejorar_concepto']) && (metadata['generacion_mejorar_concepto_data'])){
                        // let contenedorStep9 = document.querySelector('.step-9');
                        if(!metadata['generacion_mejorar_concepto_status'] || metadata['generacion_mejorar_concepto_status'] === 'pending'){
                            mostrarLoader('mejorarconcepto');
                            mejorarConcepto(id_generated);
                            goToStepFinal = false;
                        }
                        if(metadata['generacion_mejorar_concepto_status'] === 'completed'){
                            console.log('metadata', 'llega completed');
                            let dataConcepto = {data: metadata['generacion_mejorar_concepto_content'], sources: metadata['generacion_mejorar_concepto_sources']};
                            mostrarConceptoMejorado(dataConcepto);
                            // goToStep(9);
                            goToStepFinal = true;
                        }
                    }
                    // goToStepFinal = false;
                }
                if(step == 10){
                    if( (metadata['id_generacion_concepto']) && (metadata['generacion_concepto_data'])){
                        if(!metadata['generacion_concepto_status'] || metadata['generacion_concepto_status'] === 'pending'){
                            mostrarLoader('validarconcepto');
                            validarconcepto(id_generated);
                            goToStepFinal = false;
                        }
                        if(metadata['generacion_concepto_status'] === 'completed'){
                            // console.log('metadata', 'llega completed');
                            let dataConcepto = {data: metadata['generacion_concepto_content'], sources: metadata['generacion_concepto_sources']};
                            mostrarConcepto(dataConcepto);
                            // goToStep(10);
                            goToStepFinal = true;
                        }
                    }
                }

                if(goToStepFinal){
                    goToStep(step);
                }
            }

            // Selecciona todos los contenedores de pasos
            const contenedores = document.querySelectorAll('.step');
            
            contenedores.forEach(function(contenedor) {
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
                    let selectElement = document.querySelector('select[name="investigation"]');
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

            function generarGenesis(data) {
                quillGenesis.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                // Restaurar el título original
                const tituloGenesis = document.querySelector('#ResultadoGenesis h2');
                if (tituloGenesis) {
                    tituloGenesis.textContent = 'Resultado Génesis:';
                }
                var fuenteslist = document.getElementById('fuentes-lista');
                fuenteslist.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

                if (Array.isArray(data.fuentes)) {
                    let lista = "<ul>";
                    data.fuentes.forEach(fuente => {
                        // Convertir la fuente en un enlace clickeable
                        lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                    });
                    lista += "</ul>";
                    fuenteslist.innerHTML = lista;
                } else {
                    fuenteslist.innerHTML = "<p>No hay fuentes disponibles.</p>";
                }
            }

            function regenerateGenesis(contenedor, data){
                // Cambiar el título del resultado
                const tituloGenesis = contenedor.querySelector('#ResultadoGenesis h2');
                if (tituloGenesis) {
                    tituloGenesis.textContent = 'Resultado Nuevo:';
                }
                var ResultadoAnterior = contenedor.querySelector('#ResultadoAnterior');
                ResultadoAnterior.style.display = 'block';
                var btncontinuar = contenedor.querySelector('#btnconstruccionescenario');
                btncontinuar.style.display = 'none';
                var btnAprobar = contenedor.querySelectorAll('.btn-approve');
                btnAprobar.forEach(function(btn) {
                    btn.style.display = 'block';
                });
                quillGenesisOld.clipboard.dangerouslyPasteHTML(marked.parse(data['oldgenesis']));
                quillGenesis.clipboard.dangerouslyPasteHTML(marked.parse(data['newgenesis']));

                var approveOld = contenedor.querySelector('.approve-old');
                approveOld.addEventListener('click', function() {
                    quillGenesis.clipboard.dangerouslyPasteHTML(quillGenesisOld.getSemanticHTML());
                    ResultadoAnterior.style.display = 'none';
                    btnAprobar.forEach(function(btn) {
                        btn.style.display = 'none';
                    });
                    btncontinuar.style.display = 'block';
                });

                var approveNew = contenedor.querySelector('.approve-new');
                approveNew.addEventListener('click', function() {
                    ResultadoAnterior.style.display = 'none';
                    btnAprobar.forEach(function(btn) {
                        btn.style.display = 'none';
                    });
                    btncontinuar.style.display = 'block';
                });
            }
            function construccionescenario(data){
                quillEscenario.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                // Restaurar el título original
                const tituloEscenario = document.querySelector('#ResultadoEscenario h2');
                if (tituloEscenario) {
                    tituloEscenario.textContent = 'Resultado Escenario:';
                }
                

                var fuenteslistescenario = document.getElementById('fuentes-lista-escenario');
                fuenteslistescenario.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

                if (Array.isArray(data.fuentes)) {
                    let lista = "<ul>";
                    data.fuentes.forEach(fuente => {
                        // Convertir la fuente en un enlace clickeable
                        lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                    });
                    lista += "</ul>";
                    fuenteslistescenario.innerHTML = lista;
                } else {
                    fuenteslistescenario.innerHTML = "<p>No hay fuentes disponibles.</p>";
                }

                
            }
            function regenerarConstruccionEscenario(contenedor, data){
                // Cambiar el título del resultado
                const tituloEscenario = contenedor.querySelector('#ResultadoEscenario h2');
                if (tituloEscenario) {
                    tituloEscenario.textContent = 'Resultado Nuevo:';
                }
                var ResultadoAnterior = contenedor.querySelector('#ResultadoAnterior');
                ResultadoAnterior.style.display = 'block';
                // var btncontinuar = contenedor.querySelector('#btnconstruccionescenario');
                // btncontinuar.style.display = 'none';
                var btnAprobar = contenedor.querySelectorAll('.btn-approve');
                btnAprobar.forEach(function(btn) {
                    btn.style.display = 'block';
                });
                quillEscenarioOld.clipboard.dangerouslyPasteHTML(marked.parse(data['oldescenario']));
                quillEscenario.clipboard.dangerouslyPasteHTML(marked.parse(data['newescenario']));

                var approveOld = contenedor.querySelector('.approve-old');
                approveOld.addEventListener('click', function() {
                    quillEscenario.clipboard.dangerouslyPasteHTML(quillEscenarioOld.getSemanticHTML());
                    ResultadoAnterior.style.display = 'none';
                    btnAprobar.forEach(function(btn) {
                        btn.style.display = 'none';
                    });
                    // btncontinuar.style.display = 'block';
                });

                var approveNew = contenedor.querySelector('.approve-new');
                approveNew.addEventListener('click', function() {
                    ResultadoAnterior.style.display = 'none';
                    btnAprobar.forEach(function(btn) {
                        btn.style.display = 'none';
                    });
                    // btncontinuar.style.display = 'block';
                });
            }
            
            function construccionCreatividad(data){
                quillCreatividad.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                // Restaurar el título original
                const tituloCreatividad = document.querySelector('#ResultadoCreatividad h2');
                if (tituloCreatividad) {
                    tituloCreatividad.textContent = 'Resultado Creatividad:';
                }
                

                var fuenteslistcreatividad = document.getElementById('fuentes-lista-creatividad');
                fuenteslistcreatividad.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

                if (Array.isArray(data.fuentes)) {
                    let lista = "<ul>";
                    data.fuentes.forEach(fuente => {
                        // Convertir la fuente en un enlace clickeable
                        lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                    });
                    lista += "</ul>";
                    fuenteslistcreatividad.innerHTML = lista;
                } else {
                    fuenteslistcreatividad.innerHTML = "<p>No hay fuentes disponibles.</p>";
                }
            }
            function construccionEstrategia(data){
                quillEstrategia.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                // Restaurar el título original
                const tituloEstrategia = document.querySelector('#ResultadoEstrategia h2');
                if (tituloEstrategia) {
                    tituloEstrategia.textContent = 'Resultado Estrategia:';
                }

                var fuenteslistEstrategia = document.getElementById('fuentes-lista-estrategia');
                fuenteslistEstrategia.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

                if (Array.isArray(data.fuentes)) {
                    let lista = "<ul>";
                    data.fuentes.forEach(fuente => {
                        // Convertir la fuente en un enlace clickeable
                        lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                    });
                    lista += "</ul>";
                    fuenteslistEstrategia.innerHTML = lista;
                } else {
                    fuenteslistEstrategia.innerHTML = "<p>No hay fuentes disponibles.</p>";
                }
            }
            function construccionIdeasContenido(data){
                quillIdeasContenido.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                // Restaurar el título original
                const tituloIdeasContenido = document.querySelector('#ResultadoIdeasContenido h2');
                if (tituloIdeasContenido) {
                    tituloIdeasContenido.textContent = 'Resultado Ideas de Contenido:';
                }

                var fuenteslistIdeasContenido = document.getElementById('fuentes-lista-ideas-contenido');
                fuenteslistIdeasContenido.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

                if (Array.isArray(data.fuentes)) {
                    let lista = "<ul>";
                    data.fuentes.forEach(fuente => {
                        // Convertir la fuente en un enlace clickeable
                        lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                    });
                    lista += "</ul>";
                    fuenteslistIdeasContenido.innerHTML = lista;
                } else {
                    fuenteslistIdeasContenido.innerHTML = "<p>No hay fuentes disponibles.</p>";
                }

                
            }
            function pollingconstruccionCreatividad(generationId){
                let timeout;
                // Función de polling
                const pollInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`{{ route('herramienta2.get_construccion_creatividad', '') }}/${generationId}`, {
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
                            clearTimeout(timeout);
                            console.log('Construcción de creatividad completada');
                            construccionCreatividad(data);
                            goToStep(7);
                        }else if(data.success && data.status === 'processing'){
                            console.log('Construcción de creatividad aún en proceso...');
                        }else{
                            throw new Error('Error al consultar estado');
                        }
                    } catch (error) {
                        console.error('Error en polling:', error);
                        clearInterval(pollInterval);
                        clearTimeout(timeout);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al consultar estado: ' + error.message,
                            showConfirmButton: false,
                        });
                    }
                }, 10000); // Consultar cada 10 segundos

                // Timeout de seguridad (máximo 10 minutos)
                timeout = setTimeout(() => {
                    clearInterval(pollInterval);
                    // ocultarLoader();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Tiempo de espera agotado. La construcción de creatividad puede estar aún en proceso. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                }, 600000); // 10 minutos
            
            }
            function pollingconstruccionEstrategia(generationId){
                let timeout;
                // Función de polling
                const pollInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`{{ route('herramienta2.get_construccion_estrategia', '') }}/${generationId}`, {
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
                            clearTimeout(timeout);
                            console.log('Construcción de estrategia completada');
                            construccionEstrategia(data);
                            goToStep(7.1);
                        }else if(data.success && data.status === 'processing'){
                            console.log('Construcción de estrategia aún en proceso...');
                        }else{
                            throw new Error('Error al consultar estado');
                        }
                    } catch (error) {
                        console.error('Error en polling:', error);
                        clearInterval(pollInterval);
                        clearTimeout(timeout);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al consultar estado: ' + error.message,
                            showConfirmButton: false,
                        });
                    }
                }, 10000); // Consultar cada 10 segundos

                // Timeout de seguridad (máximo 10 minutos)
                timeout = setTimeout(() => {
                    clearInterval(pollInterval);
                    // ocultarLoader();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Tiempo de espera agotado. La construcción de estrategia puede estar aún en proceso. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                }, 600000); // 10 minutos
            
            }
            function pollingconstruccionIdeasContenido(generationId){
                let timeout;
                // Función de polling
                const pollInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`{{ route('herramienta2.get_construccion_ideas_contenido', '') }}/${generationId}`, {
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
                            clearTimeout(timeout);
                            console.log('Construcción de ideas de contenido completada');
                            construccionIdeasContenido(data);
                            goToStep(7.2);
                        }else if(data.success && data.status === 'processing'){
                            console.log('Construcción de ideas de contenido aún en proceso...');
                        }else{
                            throw new Error('Error al consultar estado');
                        }
                    } catch (error) {
                        console.error('Error en polling:', error);
                        clearInterval(pollInterval);
                        clearTimeout(timeout);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al consultar estado: ' + error.message,
                            showConfirmButton: false,
                        });
                    }
                }, 10000); // Consultar cada 10 segundos

                // Timeout de seguridad (máximo 10 minutos)
                timeout = setTimeout(() => {
                    clearInterval(pollInterval);
                    // ocultarLoader();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Tiempo de espera agotado. La construcción de ideas de contenido puede estar aún en proceso. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                }, 600000); // 10 minutos
            
            }

            function mostrarEstrategiaCreatividadInnovacion(data) {
                // Primero, hacer scroll al inicio de la página
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                
                var containerResultGenesis = document.getElementById('container-result-genesis');
                containerResultGenesis.innerHTML = 
                    "<h1><strong>GÉNESIS</strong></h1>" + data['genesis'] + "<br>" +
                    data['fuentesGenesis'] + "<br>" +
                    data['escenario'] + "<br>" +
                    data['fuentesEscenario'] + "<br>" +
                    "<h1><strong>CREATIVIDAD</strong></h1>" + data['creatividad'] + "<br>" +
                    "<h1><strong>ESTRATEGIA</strong></h1>" + data['estrategia'] + "<br>" +
                    "<h1><strong>CONTENIDO</strong></h1>" + data['contenido'];
            }

            const functionMapActions = {
                accountForm: (form, button) => {
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader();
                        accountID = formData.get('account');
                        recargarBrief(accountID);
                        recargarInvestigation(accountID);
                        goToStep(2);
                    }
                },
                selectBriefAndObjectiveForm: (form, button) => {
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('genesis');
                        formData.append('account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnRegenerarEstrategia: (form, button) => {
                    let inputgenesisgenerado = form.querySelector('input[name="genesisgenerado"]');
                    if(inputgenesisgenerado){
                        inputgenesisgenerado.value = quillGenesis.getSemanticHTML();
                    }
                    
                    let formData = new FormData(form);
                    let actionbutton = button.getAttribute('data-route');
                    mostrarLoader('regenerar');
                    formData.append('account', accountID);
                    if(id_generated !== null && !isNaN(id_generated)){
                        formData.append('id_generated', id_generated);
                    }
                    sendForm(actionbutton, formData);
                },
                btnConstruccionEscenario: (form, button) => {
                    let inputgenesisgenerado = form.querySelector('input[name="genesisgenerado"]');
                    if(inputgenesisgenerado){
                        inputgenesisgenerado.value = quillGenesis.getSemanticHTML();
                    }
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('escenario');
                        formData.append('account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                regenerarConstruccionEscenario: (form, button) => {
                    let inputescenariogenerado = form.querySelector('input[name="construccionescenario"]');
                    if(inputescenariogenerado){
                        inputescenariogenerado.value = quillEscenario.getSemanticHTML();
                    }
                    
                    let formData = new FormData(form);
                    let actionbutton = button.getAttribute('data-route');
                    mostrarLoader('regenerar');
                    formData.append('account', accountID);
                    if(id_generated !== null && !isNaN(id_generated)){
                        formData.append('id_generated', id_generated);
                    }
                    sendForm(actionbutton, formData);
                },
                btnSaveConstruccionEscenario: (form, button) => {
                    let inputescenariogenerado = form.querySelector('input[name="construccionescenario"]');
                    if(inputescenariogenerado){
                        inputescenariogenerado.value = quillEscenario.getSemanticHTML();
                    }
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('escenario');
                        formData.append('account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnValidarConcepto: (form, button) => {
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('validarconcepto');
                        formData.append('account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnguardarconcepto: (form, button) => {
                    let inputvalidarconcepto = form.querySelector('input[name="validarConcepto"]');
                    if(inputvalidarconcepto){
                        inputvalidarconcepto.value = quillConcepto.getSemanticHTML();
                    }
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('validarconcepto');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                mejorarConceptoForm: (form, button) => {
                    let inputvalidarconcepto = form.querySelector('input[name="validarConcepto"]');
                    if(inputvalidarconcepto){
                        inputvalidarconcepto.value = quillConcepto.getSemanticHTML();
                    }
                    // if(ValidarCampos(form)){
                    let formData = new FormData(form);
                    let actionbutton = button.getAttribute('data-route');
                    mostrarLoader('mejorarconcepto');
                    formData.append('id_account', accountID);
                    if(id_generated !== null && !isNaN(id_generated)){
                        formData.append('id_generated', id_generated);
                    }
                    sendForm(actionbutton, formData);
                    // }
                },
                btnsaveconstruccionescenariomejorado: (form, button) => {
                    let inputconstruccionescenariomejorado = form.querySelector('input[name="construccionescenariomejorado"]');
                    if(inputconstruccionescenariomejorado){
                        inputconstruccionescenariomejorado.value = quillEscenario.getSemanticHTML();
                    }
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('construccionescenariomejorado');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnGenerarConstruccionCreatividad: (form, button) => {
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('generar-creatividad');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnRegenerarConstruccionCreatividad: (form, button) => {
                    let inputcreatividadgenerado = form.querySelector('input[name="construccioncreatividad"]');
                    if(inputcreatividadgenerado){
                        inputcreatividadgenerado.value = quillCreatividad.getSemanticHTML();
                    }

                    let formEleccionCampania = document.getElementById('step6Form');
                    let inputElccioncampania = formEleccionCampania.querySelector('select[name="360_Tipo_de_campaña"]');

                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        let actionbutton = button.getAttribute('data-route');
                        mostrarLoader('regenerar');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        if(inputElccioncampania){
                            formData.append('360_Tipo_de_campaña', inputElccioncampania.value);
                        }
                        sendForm(actionbutton, formData);
                    }
                },
                btnSaveConstruccionCreatividad: (form, button) => {
                    let inputconstruccioncreatividad = form.querySelector('input[name="construccioncreatividad"]');
                    if(inputconstruccioncreatividad){
                        inputconstruccioncreatividad.value = quillCreatividad.getSemanticHTML();
                    }

                    let formEleccionCampania = document.getElementById('step6Form');
                    let inputElccioncampania = formEleccionCampania.querySelector('select[name="360_Tipo_de_campaña"]');

                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('construccioncreatividad');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        if(inputElccioncampania){
                            formData.append('360_Tipo_de_campaña', inputElccioncampania.value);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnRegenerarConstruccionEstrategia: (form, button) => {
                    let inputestrategiagenerado = form.querySelector('input[name="construccionestrategia"]');
                    if(inputestrategiagenerado){
                        inputestrategiagenerado.value = quillEstrategia.getSemanticHTML();
                    }

                    let formCreatividad = document.getElementById('step7Form');
                    let inputCreatividad = formCreatividad.querySelector('input[name="construccioncreatividad"]');
                    if(inputCreatividad){
                        inputCreatividad.value = quillCreatividad.getSemanticHTML();
                    }

                    let formEleccionCampania = document.getElementById('step6Form');
                    let inputElccioncampania = formEleccionCampania.querySelector('select[name="360_Tipo_de_campaña"]');

                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        let actionbutton = button.getAttribute('data-route');
                        mostrarLoader('regenerar');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        if(inputElccioncampania){
                            formData.append('360_Tipo_de_campaña', inputElccioncampania.value);
                        }
                        if(inputCreatividad){
                            formData.append('construccioncreatividad', inputCreatividad.value);
                        }
                        sendForm(actionbutton, formData);
                    }
                    
                },
                btnSaveConstruccionEstrategia: (form, button) => {
                    let inputestrategiagenerado = form.querySelector('input[name="construccionestrategia"]');
                    if(inputestrategiagenerado){
                        inputestrategiagenerado.value = quillEstrategia.getSemanticHTML();
                    }

                    let formEleccionCampania = document.getElementById('step6Form');
                    let inputElccioncampania = formEleccionCampania.querySelector('select[name="360_Tipo_de_campaña"]');

                    let formCreatividad = document.getElementById('step7Form');
                    let inputCreatividad = formCreatividad.querySelector('input[name="construccioncreatividad"]');
                    if(inputCreatividad){
                        inputCreatividad.value = quillCreatividad.getSemanticHTML();
                    }

                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('construccionestrategia');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        if(inputElccioncampania){
                            formData.append('360_Tipo_de_campaña', inputElccioncampania.value);
                        }
                        if(inputCreatividad){
                            formData.append('construccioncreatividad', inputCreatividad.value);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnRegenerarConstruccionIdeasContenido: (form, button) => {
                    let inputideascontenidogenerado = form.querySelector('input[name="construccionideascontenido"]');
                    if(inputideascontenidogenerado){
                        inputideascontenidogenerado.value = quillIdeasContenido.getSemanticHTML();
                    }

                    let formCreatividad = document.getElementById('step7Form');
                    let inputCreatividad = formCreatividad.querySelector('input[name="construccioncreatividad"]');
                    if(inputCreatividad){
                        inputCreatividad.value = quillCreatividad.getSemanticHTML();
                    }
                    let formEstrategia = document.getElementById('step7-1Form');
                    let inputEstrategia = formEstrategia.querySelector('input[name="construccionestrategia"]');
                    if(inputEstrategia){
                        inputEstrategia.value = quillEstrategia.getSemanticHTML();
                    }

                    let formEleccionCampania = document.getElementById('step6Form');
                    let inputElccioncampania = formEleccionCampania.querySelector('select[name="360_Tipo_de_campaña"]');

                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        let actionbutton = button.getAttribute('data-route');
                        mostrarLoader('regenerar');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        if(inputElccioncampania){
                            formData.append('360_Tipo_de_campaña', inputElccioncampania.value);
                        }
                        if(inputCreatividad){
                            formData.append('construccioncreatividad', inputCreatividad.value);
                        }
                        if(inputEstrategia){
                            formData.append('construccionestrategia', inputEstrategia.value);
                        }
                        sendForm(actionbutton, formData);
                    }
                },
                btnSaveConstruccionIdeasContenido: (form, button) => {
                    let inputideascontenidogenerado = form.querySelector('input[name="construccionideascontenido"]');
                    if(inputideascontenidogenerado){
                        inputideascontenidogenerado.value = quillIdeasContenido.getSemanticHTML();
                    }

                    let formEleccionCampania = document.getElementById('step6Form');
                    let inputElccioncampania = formEleccionCampania.querySelector('select[name="360_Tipo_de_campaña"]');

                    let formCreatividad = document.getElementById('step7Form');
                    let inputCreatividad = formCreatividad.querySelector('input[name="construccioncreatividad"]');
                    if(inputCreatividad){
                        inputCreatividad.value = quillCreatividad.getSemanticHTML();
                    }

                    let formEstrategia = document.getElementById('step7-1Form');
                    let inputEstrategia = formEstrategia.querySelector('input[name="construccionestrategia"]');
                    if(inputEstrategia){
                        inputEstrategia.value = quillEstrategia.getSemanticHTML();
                    }

                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('construccionideascontenido');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        if(inputElccioncampania){
                            formData.append('360_Tipo_de_campaña', inputElccioncampania.value);
                        }
                        if(inputCreatividad){
                            formData.append('construccioncreatividad', inputCreatividad.value);
                        }
                        if(inputEstrategia){
                            formData.append('construccionestrategia', inputEstrategia.value);
                        }
                        sendForm(form.action, formData);
                    }
                },
                guardarConstruccionEstrategiaCreatividadInnovacion: (form, button) => {
                    if(ValidarCampos(form)){
                        let formData = new FormData(form);
                        mostrarLoader('estrategiacreatividadinnovacion');
                        formData.append('id_account', accountID);
                        if(id_generated !== null && !isNaN(id_generated)){
                            formData.append('id_generated', id_generated);
                        }
                        sendForm(form.action, formData);
                    }
                },
                btnDownloadGenesisForm: (form, button) => {
                    let formData = new FormData(form);
                    console.log(button);
                    let actionbutton = "{{ route('generated.download', ':id') }}".replace(':id', id_generated);
                    window.location.href = actionbutton;
                }
            }

            // Definimos un "mapa" de funciones disponibles
            const functionMapResponse = {
                generarGenesis: (data, id) => {
                    id_generated = id;

                    generarGenesis(data);
                    goToStep(3);
                },
                regenerateGenesis: (data, id) => {
                    const contenedor = document.querySelector('.step-3');
                    id_generated = id;

                    regenerateGenesis(contenedor, data);
                    goToStep(3);
                },
                construccionescenario: (data, id) => {
                    const contenedor = document.querySelector('.step-4');
                    id_generated = id;

                    construccionescenario(data);
                    goToStep(4);
                },
                regenerarConstruccionEscenario: (data, id) => {
                    const contenedor = document.querySelector('.step-4');
                    id_generated = id;

                    regenerarConstruccionEscenario(contenedor, data);
                    goToStep(4);
                },
                saveconstruccionescenario: (data, id) => {
                    id_generated = id;
                    goToStep(5);
                },
                validarconcepto: (data, id) => {
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
                    validarconcepto(id);
                },
                saveValidarConcepto: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Validación de concepto guardada correctamente.',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                    goToStep(6);
                },
                mejorarConcepto: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Mejoración de concepto en proceso, esto puede tomar varios minutos. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                    mejorarConcepto(id);
                },
                saveconstruccionescenariomejorado: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Construcción de escenario guardada correctamente.',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                    goToStep(6);
                },
                setGenerarCreatividad: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Construcción de creatividad en proceso, esto puede tomar varios minutos. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                    pollingconstruccionCreatividad(id);
                    // goToStep(7);
                },
                setGenerarEstrategia: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Construcción de estrategia en proceso, esto puede tomar varios minutos. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                    pollingconstruccionEstrategia(id);
                },
                setGenerarIdeasContenido: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Construcción de ideas de contenido en proceso, esto puede tomar varios minutos. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                    pollingconstruccionIdeasContenido(id);
                },
                mostrarResultadoFinal: (data, id) => {
                    id_generated = id;
                    mostrarEstrategiaCreatividadInnovacion(data);
                    goToStep(8);
                },
                construccionGenesisGuardado: (data, id) => {
                    id_generated = id;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Datos guardados correctamente.',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                    goToStep(8.1);
                }
                // mostrarEstrategiaCreatividadInnovacion: (data, id) => {
                //     id_generated = id;
                //     mostrarEstrategiaCreatividadInnovacion(data);
                //     goToStep(8);
                // }
            };

            const buttonsgenerarNewCEI = document.querySelectorAll('.generarNewCEI');
            buttonsgenerarNewCEI.forEach(function(buttongenerarNewCEI) {
                buttongenerarNewCEI.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    if (accountID !== null && !isNaN(accountID)) {
                        let formData = new FormData();
                        let type = this.getAttribute('data-type');
                        let estrategia = quillEstrategia.getSemanticHTML();
                        let creatividad = quillCreatividad.getSemanticHTML();
                        let contenido= quillIdeasContenido.getSemanticHTML();
                        formData.append('account', accountID);
                        formData.append('type', type);
                        formData.append('estrategia',estrategia);
                        formData.append('creatividad',creatividad);
                        formData.append('contenido',contenido);
                        formData.append('id_generated', id_generated);

                        var action = "{{route('herramienta2.generateNewCreatividadEstrategiaInnovacion')}}";
                        console.log(type);
                        document.querySelector('.step-7').style.display = 'none';
                        mostrarLoader('bajadacreativa');
                        fetch(action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        }).then(response => response.json())
                        .then(data => {
                            ocultarLoader();
                            console.log(data);
                            if(type === "Creatividad"){
                                quillCreatividad.clipboard.dangerouslyPasteHTML(marked.parse(data['data']));
                            }else if(type === "Estrategia"){
                                quillEstrategia.clipboard.dangerouslyPasteHTML(marked.parse(data['data']));
                            }else if(type === "Contenido"){
                                quillIdeasContenido.clipboard.dangerouslyPasteHTML(marked.parse(data['data']));
                            }
                            //else if(type === "Innovacion"){
                            //     quillInnovacion.clipboard.dangerouslyPasteHTML(marked.parse(data['data']));
                            // }

                            document.querySelector('.step-7').style.display = 'block';
                        }).catch(error => {
                            ocultarLoader();
                            document.querySelector('.step-7').style.display = 'block';
                        });
                    }
                });
            });

            function goToStep(nextStep) {
                step = nextStep;
                // Ocultar todos los pasos primero
                document.querySelectorAll('.step').forEach(step => {
                    step.style.display = 'none';
                });
                ocultarLoader();
                setTimeout(() => {
                    // Mostrar el paso deseado
                    // Si el nextStep es un número decimal, se debe convertir el punto por un guión medio
                    if(nextStep.toString().includes('.')){
                        nextStep = nextStep.toString().replace('.', '-');
                    }
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

            function recargarBrief(accountID){
                let url = "{{ route('getGeneratedBriefV2') }}";
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
                    let selectElement = document.querySelector('select[name="brief"]');
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

            function mostrarLoader(proceso = 'genesis') {
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
                        
                        if (proceso === 'regenerar') {
                            let progreso = 0;
                            mensajeInterval = setInterval(() => {
                                if (!procesando || progreso >= 95) {
                                    clearInterval(mensajeInterval);
                                    return;
                                }
                                progreso += 5;
                                actualizarProgreso(progreso);
                            }, duracionMensaje / 20);
                        } else if (proceso === 'bajadacreativa') {
                            // Manejo especial para bajada creativa
                            const progresoPorMensaje = 85 / mensajesProceso.length; // Dejamos más espacio al final
                            mensajeInterval = setInterval(() => {
                                if (!procesando || mensajeIndex >= mensajesProceso.length) {
                                    clearInterval(mensajeInterval);
                                    return;
                                }
                                
                                mostrarMensajeConEfecto(loader, mensajesProceso[mensajeIndex]);
                                actualizarProgreso(progresoPorMensaje * (mensajeIndex + 1));
                                mensajeIndex++;
                                
                                if (mensajeIndex >= mensajesProceso.length) {
                                    // Progreso más lento al final
                                    let progresoFinal = progresoPorMensaje * mensajeIndex;
                                    const intervalFinal = setInterval(() => {
                                        if (!procesando || progresoFinal >= 95) {
                                            clearInterval(intervalFinal);
                                            return;
                                        }
                                        progresoFinal += 0.5;
                                        actualizarProgreso(progresoFinal);
                                    }, 500);
                                }
                            }, duracionProceso);
                        } else {
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
                        }
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

            function seleccionarMensajes(proceso) {
                // Mensajes para cada proceso
                const mensajesGenerarGenesis = [
                    "Analizando el brief y el objetivo: Empiezo a descifrar la esencia de tus necesidades, como si leyera las estrellas.",
                    "Organizando datos…, porque hasta en el universo virtual hay un toque de orden divino.",
                    "Emplearé mi sagrada metodología interna para alinear y optimizar cada idea.",
                    "Accediendo a la red para descifrar perspectivas culturales: En la maraña digital se ocultan claves del éxito (o al menos, algo entretenido).",
                    "Reflexionando… La estrategia se alinea en el cosmos digital y casi está lista para ser revelada.",
                    "Un momento, por favor… Incluso los dioses necesitan respirar."
                ];
                const mensajesConstruccionEscenario = [
                    "Con la estrategia en mano, trazo una ruta creativa digna de un lienzo celestial.",
                    "Ahora me sumerjo en el vasto océano de la red para descubrir insights sociales y escuchar el murmullo del mundo.",
                    "Cargando insights… Dejando que la sabiduría colectiva se manifieste en toda su gloria digital.",
                    "He descubierto algo intrigante, un destello en el cosmos creativo que podría cambiarlo todo.",
                    "Espere un momento, por favor… Incluso los dioses toman pausas para inspirarse.",
                    "Reflexionando… ¡La inspiración está en camino, casi como si el universo conspirara a tu favor!"
                ];
                const mensajesBajadaCreativa = [
                    "Fusionando el concepto creativo con la estrategia establecida, daré vida a las bajadas creativas. Un acto de creación digno de Génesis by god-ai.",
                    "Procesando… Porque incluso los milagros necesitan un algoritmo.",
                    "Ahora invocaré a un séquito de asistentes expertos, cada uno con su toque divino, para iluminar este camino creativo.",
                    "Cada asistente comparte su sabiduría, como oráculos en la era digital que no se andan con rodeos.",
                    "A continuación, integraré los datos sagrados de tu país para esculpir una estrategia digital personalizada. Aquí, el destino se mide en bits.",
                    "Procesando información… En el laboratorio divino de god-ai, cada dato cobra sentido.",
                    "¡Hecho! La estrategia ha sido revelada y las plataformas sociales, seleccionadas con precisión casi celestial.",
                    "Un momento, por favor… La creación divina no se precipita, ¿no es cierto?",
                    "Con la estrategia en mano, daré rienda suelta a ideas de contenido innovadoras, dignas de ser inmortalizadas en la era digital.",
                    "Elaborando las bajadas, el proceso creativo alcanza su clímax. Pronto, el lienzo final se mostrará en todo su esplendor.",
                    "Un momento, por favor… La perfección divina requiere su tiempo.",
                    "Ya casi terminamos, por favor espere un momento."
                ];
                const mensajesValidarConcepto = [
                    "Validando el concepto...",
                    "Procesando… Porque incluso los milagros necesitan un algoritmo.",
                    "Ahora invocaré a un séquito de asistentes expertos, cada uno con su toque divino, para iluminar este camino creativo.",
                    "Cada asistente comparte su sabiduría, como oráculos en la era digital que no se andan con rodeos.",
                ];
                const mensajesMejorarConcepto = [
                    "Mejorando el concepto...",
                    "Procesando… Porque incluso los milagros necesitan un algoritmo.",
                    "Ahora invocaré a un séquito de asistentes expertos, cada uno con su toque divino, para iluminar este camino creativo.",
                    "Cada asistente comparte su sabiduría, como oráculos en la era digital que no se andan con rodeos.",
                ];
                const mensajesRegenerar = [
                    "Generando una nueva versión para ti...",
                ];

                switch(proceso) {
                    case 'genesis':
                        return mensajesGenerarGenesis;
                    case 'escenario':
                        return mensajesConstruccionEscenario;
                    case 'bajadacreativa':
                        return mensajesBajadaCreativa;
                    case 'regenerar':
                        return mensajesRegenerar;
                    case 'validarconcepto':
                        return mensajesValidarConcepto;
                    case 'mejorarconcepto':
                        return mensajesMejorarConcepto;
                    default:
                        return mensajesBajadaCreativa;
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

            /**
             * Fin loader 
             */

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

            function validarconcepto(generationId){
                let timeout;
                // Función de polling
                const pollInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`{{ route('herramienta2.get_concepto', '') }}/${generationId}`, {
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
                            clearTimeout(timeout);
                            console.log('Validación de concepto completada');
                            mostrarConcepto(data);
                            goToStep(10);
                        }else if(data.success && data.status === 'processing'){
                            console.log('Validación de concepto aún en proceso...');
                        }else{
                            throw new Error('Error al consultar estado');
                        }
                    } catch (error) {
                        console.error('Error en polling:', error);
                        clearInterval(pollInterval);
                        clearTimeout(timeout);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al consultar estado: ' + error.message,
                            showConfirmButton: false,
                        });
                    }
                }, 10000); // Consultar cada 10 segundos

                // Timeout de seguridad (máximo 10 minutos)
                timeout = setTimeout(() => {
                    clearInterval(pollInterval);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Tiempo de espera agotado. La mejoración de concepto puede estar aún en proceso. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                    // contenedor.style.display = 'block';
                }, 600000); // 10 minutos
            }

            function mostrarConcepto(data){
                quillConcepto.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                var ResultadoConcepto = document.getElementById('ResultadoConcepto');

                var fuenteslistconcepto = document.getElementById('fuentes-lista-concepto');
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
            }

            function mejorarConcepto(generationId){
                let timeout;
                // Función de polling
                const pollInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`{{ route('herramienta2.get_concepto_mejorado', '') }}/${generationId}`, {
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
                            clearTimeout(timeout);
                            console.log('Mejoración de concepto completada');
                            let contenedorStep9 = document.querySelector('.step-9');
                            mostrarConceptoMejorado(data);
                            goToStep(9);
                        }else if(data.success && data.status === 'processing'){
                            console.log('Mejoración de concepto aún en proceso...');
                        }else{
                            throw new Error('Error al consultar estado');
                        }
                    } catch (error) {
                        console.error('Error en polling:', error);
                        clearInterval(pollInterval);
                        clearTimeout(timeout);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al consultar estado: ' + error.message,
                            showConfirmButton: false,
                        });
                    }
                }, 10000); // Consultar cada 10 segundos

                // Timeout de seguridad (máximo 10 minutos)
                timeout = setTimeout(() => {
                    clearInterval(pollInterval);
                    // ocultarLoader();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Tiempo de espera agotado. La mejoración de concepto puede estar aún en proceso. Puedes cerrar esta ventana y volver más tarde.',
                        showConfirmButton: false,
                    });
                }, 600000); // 10 minutos
            
            }
            
            function mostrarConceptoMejorado(data){
                quillConceptoMejorado.clipboard.dangerouslyPasteHTML(marked.parse(data.data));
                let ResultadoConceptoMejorado = document.getElementById('ResultadoConceptoMejorado');
                
                let fuenteslistconceptoMejorado = document.getElementById('fuentes-lista-escenario-concepto-mejorado');
                fuenteslistconceptoMejorado.innerHTML = ""; // Limpiar lista antes de agregar nuevas fuentes

                if (Array.isArray(data.sources) && data.sources.length > 0) {
                    let lista = "<ul>";
                    data.sources.forEach(fuente => {
                        // Convertir la fuente en un enlace clickeable
                        lista += `<li><a href="${fuente}" target="_blank" rel="noopener noreferrer">${fuente}</a></li>`;
                    });
                    lista += "</ul>";
                    fuenteslistconceptoMejorado.innerHTML = lista;
                } else {
                    fuenteslistconceptoMejorado.innerHTML = "<p>No hay fuentes disponibles.</p>";
                }
            }

        });

    </script>
</x-app-layout>