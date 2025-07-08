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

                        {{--<div class="step step-8" style="display: none;">
                            @include('herramienta1.steps.step8')
                        </div>

                        <div class="step step-9" style="display: none;">
                            @include('herramienta1.steps.step9')
                        </div> --}}

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
            // Agregar el HTML de la barra de progreso
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

            var allFormData = {};
            var accountID = null;
            
            function goToStep(step) {
                // Ocultar todos los pasos primero
                document.querySelectorAll('.step').forEach(step => {
                    step.style.display = 'none';
                });
                // Mostrar el paso deseado
                const nextStepDiv = document.querySelector('.step-' + step);
                if (nextStepDiv) {
                    nextStepDiv.style.display = 'block';
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
                                recargarBrief(accountID);
                                goToStep(step);
                            }
                        } else {
                            // Para navegación normal entre pasos
                            contenedor.style.display = 'none';
                            goToStep(step);
                        }
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
                const formButtons = contenedor.querySelectorAll('.form-button');
                formButtons.forEach(function(formBoton) {
                    formBoton.addEventListener('click', function(event) {
                        event.preventDefault();
                        var form = event.target.form;

                        var inputgenesisgenerado = form.querySelector('input[name="genesisgenerado"]');
                        if (inputgenesisgenerado) {
                            // var quillContent = quill.getSemanticHTML();
                            inputgenesisgenerado.value = quillGenesis.getSemanticHTML();
                        }

                        var inputconstruccionescenario = form.querySelector('input[name="construccionescenario"]');
                        if (inputconstruccionescenario) {
                            inputconstruccionescenario.value = quillEscenario.getSemanticHTML();;
                        }

                        var inputEstrategia = form.querySelector('input[name="construccionEstrategia"]');
                        if (inputEstrategia) {
                            inputEstrategia.value = quillEstrategia.getSemanticHTML();
                        }
                        
                        var inputCreatividad = form.querySelector('input[name="construccionCreatividad"]');
                        if (inputCreatividad) {
                            inputCreatividad.value = quillCreatividad.getSemanticHTML();
                        }

                        var inputIdeasContenido = form.querySelector('input[name="construccionIdeasContenido"]');
                        if (inputIdeasContenido) {
                            inputIdeasContenido.value = quillIdeasContenido.getSemanticHTML();
                        }

                        // var inputInnovacion = form.querySelector('input[name="construccionInnovacion"]');
                        // if (inputInnovacion) {
                        //     inputInnovacion.value = quillInnovacion.getSemanticHTML();
                        // }
                        

                        if (ValidarCampos(form)) {
                            contenedor.style.display = 'none';
                            var formData = new FormData(form);
                            
                            // Solo mostrar loader y hacer fetch si no es el formulario de cuenta
                            if (form.id !== 'accountForm') {
                                if (accountID !== null && !isNaN(accountID)) {
                                    // Determinar qué tipo de proceso se va a ejecutar
                                    let proceso = 'regenerar';
                                    const formAction = form.action.toLowerCase();
                                    console.log('Form Action:', formAction); // Para ver qué URL está llegando
                                    if (formAction.includes('generargenesis')) {
                                        proceso = 'genesis';
                                    } else if (formAction.includes('construccionescenario')) {
                                        proceso = 'escenario';
                                    } else if (formAction.includes('saveeleccioncampania') ) {
                                        proceso = 'bajadacreativa';
                                    }

                                    console.log('Proceso seleccionado:', proceso); // Para verificar qué proceso se eligió
                                    mostrarLoader(proceso);
                                    
                                    formData.append('account', accountID);
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
                                                    case 'generarGenesis':
                                                        generarGenesis(data.details);
                                                        break;
                                                    case 'generarInsight':
                                                        generarInsight(data.details.data);
                                                        break;
                                                    case 'generarReto':
                                                        generarReto(data.details.data);
                                                        break;
                                                    case 'construccionescenario':
                                                        construccionescenario(data.details);
                                                        break;
                                                    case 'construccionEstrategiaCreatividadInnovacion':
                                                        construccionEstrategiaCreatividadInnovacion(data.details);
                                                        break;
                                                    case 'mostrarEstrategiaCreatividadInnovacion':
                                                        mostrarEstrategiaCreatividadInnovacion(data.details);
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
                                            console.log('Error recibido:', data.error);
                                            mensaje.innerHTML = '';
                                            if(typeof data.error === 'object'){
                                                Object.values(data.error).forEach(function(error) {
                                                    mensaje.innerHTML += `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">${error}</div>`;
                                                });
                                            }else{
                                                mensaje.innerHTML = `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">${data.error}</div>`;
                                            }
                                            
                                            // Si hay un error en generarGenesis, asegurarnos de mostrar el step 2
                                            if(data.goto === 2) {
                                                goToStep(2);
                                            }
                                        }
                                    }).catch(error => {
                                        ocultarLoader();
                                        contenedor.style.display = 'block';
                                        const mensaje = contenedor.querySelector('.message');
                                        console.error('Error en la petición:', error);
                                        mensaje.innerHTML = `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">Error de conexión. Por favor, inténtalo de nuevo más tarde.</div>`;
                                    });
                                }
                            } else {
                                // Si es el formulario de cuenta, solo navegar al siguiente paso
                                accountID = formData.get('account');
                                const step = this.getAttribute('data-step');
                                recargarBrief(accountID);
                                goToStep(step);
                            }
                        }
                    });
                });
                const formButtonsRegenerate = contenedor.querySelectorAll('.form-button-regenerate');
                formButtonsRegenerate.forEach(function(formButtonsRegenerate) {
                    formButtonsRegenerate.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        const formRoute = this.getAttribute('data-route');
                        let form = event.target.form;
                        var inputgenesisgenerado = form.querySelector('input[name="genesisgenerado"]');
                        var inputescenariogenerado = form.querySelector('input[name="construccionescenario"]');
                        if (inputescenariogenerado) {
                            inputescenariogenerado.value = quillEscenario.getSemanticHTML();
                        }
                        if (inputgenesisgenerado) {
                            inputgenesisgenerado.value = quillGenesis.getSemanticHTML();
                        }
                        
                        mostrarLoader('regenerar');
                        contenedor.style.display = 'none';
                        let formData = new FormData(form);
                        if (form.id !== 'accountForm') {
                            if (accountID !== null && !isNaN(accountID)) {
                                // Agregar el valor de accountID a formData
                                formData.append('account', accountID);
                                fetch(formRoute, {
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
                                                case 'regenerarConstruccionEscenario':
                                                
                                                regenerarConstruccionEscenario(contenedor, data.details);
                                                break;
                                                case 'regenerateGenesis':
                                                    regenerateGenesis(contenedor, data.details);
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
                                        console.log('Error recibido:', data.error);
                                        mensaje.innerHTML = '';
                                        if(typeof data.error === 'object'){
                                            Object.values(data.error).forEach(function(error) {
                                                mensaje.innerHTML += `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">${error}</div>`;
                                            });
                                        }else{
                                            mensaje.innerHTML = `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">${data.error}</div>`;
                                        }
                                        
                                        // Si hay un goto específico en caso de error
                                        if(data.goto) {
                                            goToStep(data.goto);
                                        }
                                    }
                                }).catch(error => {
                                    ocultarLoader();
                                    contenedor.style.display = 'block';
                                    const mensaje = contenedor.querySelector('.message');
                                    console.error('Error en la petición:', error);
                                    mensaje.innerHTML = `<div class="p-4 mb-4 text-sm font-bold text-red-700 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-500 border border-red-500">Error de conexión. Por favor, inténtalo de nuevo más tarde.</div>`;
                                });
                            }
                        }
                    });
                });
                const formButtonsSave = contenedor.querySelectorAll('.form-button-save');
                formButtonsSave.forEach(function(formBoton) {
                    formBoton.addEventListener('click', function(event) {
                        event.preventDefault();
                        var form = event.target.form;

                        // Actualizar los valores de los editores
                        var inputEstrategia = form.querySelector('input[name="construccionEstrategia"]');
                        if (inputEstrategia) {
                            inputEstrategia.value = quillEstrategia.getSemanticHTML();
                        }
                        
                        var inputCreatividad = form.querySelector('input[name="construccionCreatividad"]');
                        if (inputCreatividad) {
                            inputCreatividad.value = quillCreatividad.getSemanticHTML();
                        }

                        var inputIdeasContenido = form.querySelector('input[name="construccionIdeasContenido"]');
                        if (inputIdeasContenido) {
                            inputIdeasContenido.value = quillIdeasContenido.getSemanticHTML();
                        }

                        if (ValidarCampos(form)) {
                            var formData = new FormData(form);
                            if (accountID !== null && !isNaN(accountID)) {
                                formData.append('account', accountID);
                                fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                    }
                                }).then(response => response.json())
                                .then(data => {
                                    if(!data.error){
                                        if(data.function) {
                                            switch (data.function) {
                                                case 'mostrarEstrategiaCreatividadInnovacion':
                                                    mostrarEstrategiaCreatividadInnovacion(data.details);
                                                    break;
                                                default:
                                                    break;
                                            }
                                        }
                                        if(data.goto){
                                            contenedor.style.display = 'none';
                                            goToStep(data.goto);
                                        }
                                    } else {
                                        const mensaje = contenedor.querySelector('.message');
                                        mensaje.innerHTML = '';
                                        if(typeof data.error === 'object'){
                                            Object.values(data.error).forEach(function(error) {
                                                mensaje.innerHTML += `<div class="alert alert-danger">${error}</div>`;
                                            });
                                        } else {
                                            mensaje.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                                        }
                                    }
                                });
                            }
                        }
                    });
                });
            });

            var btnGenerarPDF = document.getElementById('btnGenerarPDF');
            btnGenerarPDF.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                if (accountID !== null && !isNaN(accountID)) {
                    
                    // var formData = new FormData();
                    // formData.append('account', accountID);
                    var href = btnGenerarPDF.getAttribute('href');
                    var urlConParametro = href + '?account=' + encodeURIComponent(accountID);
                    window.location.href = urlConParametro;
                    
                }
            });

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

                        var action = "{{route('herramienta2.generateNewCreatividadEstrategiaInnovacion')}}";
                        console.log(type);
                        document.querySelector('.step-6').style.display = 'none';
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

                            document.querySelector('.step-6').style.display = 'block';
                        }).catch(error => {
                            ocultarLoader();
                            document.querySelector('.step-6').style.display = 'block';
                        });
                    }
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

        var quillGenesis = new Quill('#editorGenesis', {
            theme: 'snow'
        });
        

        var quillGenesisOld = new Quill('#RegenerateGenesisOld', {
                theme: 'snow'
            });

        var quillEscenario = new Quill('#editor-container-construccionescenario', {
            theme: 'snow'
        });
        var quillEscenarioOld = new Quill('#RegenerateEscenarioOld', {
            theme: 'snow'
        });

        var quillEstrategia = new Quill('#editor-container-construccionEstrategia', {
            theme: 'snow'
        });
        var quillCreatividad = new Quill('#editor-container-construccionCreatividad', {
            theme: 'snow'
        });
        var quillIdeasContenido = new Quill('#editor-container-construccionIdeasContenido', {
            theme: 'snow'
        });
        // var quillInnovacion = new Quill('#editor-container-construccionInnovacion', {
        //     theme: 'snow'
        // });

        function recargarBrief(accountID){
            let url = "{{ route('getGeneratedBrief') }}";
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
        function construccionEstrategiaCreatividadInnovacion(data){
            //console.log(data);
            var editorContainer = document.getElementById('editor-container-genesis');
            var visuallistafuentesgenesis= document.getElementById('visual-lista-fuentes-genesis');
            var visuallistafuentesescenario= document.getElementById('visual-lista-fuentes-escenario');
            var editorContainerEscenario = document.getElementById('editor-container-escenario');
           
            // Asigna el contenido HTML a ese elemento
            editorContainer.innerHTML = data['genesis'];
            visuallistafuentesgenesis.innerHTML= data['fuentesGenesis'];
            visuallistafuentesescenario.innerHTML= data['fuentesEscenario'];
            editorContainerEscenario.innerHTML= data['escenario'];
            

            quillEstrategia.clipboard.dangerouslyPasteHTML(marked.parse(data['estrategia']['data']));
            quillCreatividad.clipboard.dangerouslyPasteHTML(marked.parse(data['creatividad']['data']));
            quillIdeasContenido.clipboard.dangerouslyPasteHTML(marked.parse(data['contenido']['data']));
            //quillInnovacion.clipboard.dangerouslyPasteHTML(marked.parse(data['innovacion']['data']));
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

const mensajesRegenerar = [
    "Generando una nueva versión para ti...",    
   
];


let mensajeIndex = 0;
let mensajeInterval;
let duracionMensaje = 5000;
let procesando = false;
let progressBar;
let progressText;
let progressContainer;

// Función para seleccionar el array de mensajes según el proceso
function seleccionarMensajes(proceso) {
    switch(proceso) {
        case 'genesis':
            return mensajesGenerarGenesis;
        case 'escenario':
            return mensajesConstruccionEscenario;
        case 'bajadacreativa':
            return mensajesBajadaCreativa;
            case 'regenerar':
            return mensajesRegenerar;
        default:
            return mensajesBajadaCreativa;
    }
}

function mostrarLoader(proceso = 'genesis') {
    procesando = true;
    const loader = document.getElementById('loader');
    progressBar = document.getElementById('progress-bar');
    progressText = document.getElementById('progress-text');
    progressContainer = document.getElementById('progress-container');
    
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

function actualizarProgreso(porcentaje) {
    progressBar.style.width = `${porcentaje}%`;
    progressText.textContent = `${Math.round(porcentaje)}%`;
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

    </script>
</x-app-layout>