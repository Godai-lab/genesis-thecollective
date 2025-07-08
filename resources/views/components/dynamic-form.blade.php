@props(['fields'])

@if ($fields)
    <div class="space-y-12">
        <div class="border-b border-gray-700 pb-12 mb-6">
            {{ $slot }}
            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 items-start">
               
                @foreach ($fields as $key => $field)
                    @if(isset($field['attr']) && $field['attr'])
                        @php
                            $attrs = explode(' ', $field['attr']);
                            $isRequired = false;
                            foreach ($attrs as $attr) {
                                if (strpos($attr, 'data-validation-rules=') !== false) {
                                    $rules = explode('|', substr($attr, strpos($attr, '=') + 1));
                                    if (in_array('required', $rules)) {
                                        $isRequired = true;
                                        break;
                                    }
                                }
                            }
                        @endphp
                    @endif
                    @if($field['type']=="text" or $field['type']=="date" or $field['type']=="email" or $field['type']=="password" or $field['type']=="number")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2">
                                <input 
                                    type="{{$field['type']}}" 
                                    name="{{$field['name']}}" 
                                    id="{{$field['id']}}" 
                                    @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                    placeholder="@if(isset($field['placeholder'])) {{$field['placeholder']}} @else {{$field['label']}} @endif" 
                                    autocomplete="{{$field['name']}}"
                                    value="{{$field['value']}}"
                                    class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700 @if(isset($field['class']) && $field['class']) {{$field['class']}} @endif">
                            </div>
                            @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                            @endif
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                    @elseif($field['type']=="textarea")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2">
                                <textarea 
                                    type="text" 
                                    name="{{$field['name']}}" 
                                    id="{{$field['id']}}"
                                    @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                    rows="3"
                                    class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">{{$field['value']}}</textarea>
                            </div>
                            @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                            @endif
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                    {{-- @elseif($field['type']=="select")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2">
                                <select 
                                    name="{{$field['name']}}" 
                                    id="{{$field['id']}}"
                                    @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                    class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
                                    @if(isset($field['list']))
                                        @foreach ($field['list'] as $key => $list)
                                            <option 
                                                value="{{$list['id']}}"
                                                @if($field['value'] == $list['id']) selected="selected" @endif
                                            >
                                                {{$list['name']}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                            @endif
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div> --}}
                        @elseif($field['type']=="select")
<div class="{{$field['col']}}">
    <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
        {{$field['label']}}
        @if(isset($isRequired) && $isRequired)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="mt-2">
        <select 
            name="{{$field['name']}}" 
            id="{{$field['id']}}"
            data-target="otherCategoryInput" {{-- Identificador para JS --}}
            @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
            class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
            @if(isset($field['list']))
                @foreach ($field['list'] as $key => $list)
                    <option 
                        value="{{$list['id']}}"
                        @if($field['value'] == $list['id']) selected="selected" @endif
                    >
                        {{$list['name']}}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    {{-- Input para "Otra categoría", oculto por defecto --}}
    <div id="otherCategoryInput" class="mt-2 hidden">
        <label for="other_category" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
            Especificar otra categoría
        </label>
        <input type="text" name="other_category" id="other_category"
            class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700">
    </div>

    <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
</div>

                    @elseif($field['type']=="switch")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2">
                                <div class="inline-flex items-center">
                                    <div class="relative inline-block h-4 w-8 cursor-pointer rounded-full">
                                        <input
                                            id="{{ $field['id'] }}"
                                            name="{{$field['name']}}" 
                                            @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                            type="checkbox"
                                            @if($field['value']) checked="checked" @endif
                                            class="peer absolute h-4 w-8 cursor-pointer appearance-none rounded-full bg-blue-gray-100 transition-colors duration-300 checked:bg-black dark:checked:bg-blue-500 peer-checked:border-black dark:peer-checked:border-blue-500 peer-checked:before:bg-black dark:peer-checked:before:bg-blue-500 !bg-none"
                                        />
                                        <label
                                            for="{{ $field['id'] }}"
                                            class="before:content[''] absolute top-2/4 -left-1 h-5 w-5 -translate-y-2/4 cursor-pointer rounded-full border border-blue-gray-100 bg-white shadow-md transition-all duration-300 before:absolute before:top-2/4 before:left-2/4 before:block before:h-10 before:w-10 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-gray-50 before:opacity-0 before:transition-opacity hover:before:opacity-10 peer-checked:translate-x-full peer-checked:border-blue-500 peer-checked:before:bg-blue-500"
                                        >
                                        <div
                                            class="top-2/4 left-2/4 inline-block -translate-x-2/4 -translate-y-2/4 rounded-full p-5"
                                            data-ripple-dark="true"
                                        >
                                        </div>
                                        </label>
                                    </div>
                                    <label
                                        for="auto-update"
                                        class="mt-px ml-3 mb-0 cursor-pointer select-none font-light text-gray-700"
                                        >
                                    </label>
                                </div>
                            </div>
                            @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                            @endif
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                    @elseif($field['type']=="checklist")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2">
                                <div class="mt-6 space-y-6">
                                    @foreach ($field['list'] as $key => $list)
                                        <div class="relative flex gap-x-3">
                                            <div class="flex h-6 items-center">
                                                <input 
                                                    id="{{$field['id'].$list['id']}}" 
                                                    name="{{$field['name'].'[]'}}" 
                                                    value="{{$list['id']}}"
                                                    @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                                    type="checkbox"
                                                    @if(is_object($field['value']) && count($field['value']) > 0)
                                                        @foreach ($field['value'] as $permision)
                                                            @if($permision['id'] == $list['id']) checked="checked" @endif
                                                        @endforeach
                                                    @endif
                                                    class="h-4 w-4 rounded border-black dark:border-gray-300 text-black dark:text-indigo-600 focus:ring-black dark:focus:ring-indigo-600">
                                            </div>
                                            <div class="text-sm leading-6">
                                                <label for="comments" class="font-medium text-black dark:text-gray-100">{{$list['name']}}</label>
                                                @if(isset($list['description'])) <p class="text-black dark:text-gray-400">{{$list['description']}}</p> @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                </div>
                            </div>
                            @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                            @endif
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                    @elseif($field['type']=="image")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-black dark:border-gray-600 px-6 py-10" ondragover="highlightDropArea(event)" ondragleave="resetDropArea()" ondrop="handleDrop(event)">
                                <div class="text-center">
                                    <div id="preview-container" class="">
                                        @if($field['value'])
                                            <img class="w-full h-auto max-h-32 object-contain" src="{{ asset("image/{$field['value']}") }}">
                                        @else
                                            <x-icon-image />
                                        @endif
                                    </div>
                                    <div class="mt-4 flex text-sm leading-6 text-black dark:text-gray-400">
                                        <label for="{{$field['id']}}" class="relative cursor-pointer rounded-lg font-semibold text-black dark:text-gray-100 hover:text-black dark:hover:text-gray-200">
                                            <span>Cargar una imagen</span>
                                            <input id="{{$field['id']}}" name="{{$field['name']}}" type="file" class="sr-only" onchange="previewImage(this)">
                                        </label>
                                        <p class="pl-1">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs leading-5 text-black dark:text-gray-400">PNG, JPG, WEBP hasta 200kb</p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                        <script>

                            function previewImage(input) {
                                const previewContainer = document.getElementById('preview-container');
                                const file = input.files[0];
                        
                                if (file) {
                                    const reader = new FileReader();
                        
                                    reader.onload = function (e) {
                                        const img = document.createElement('img');
                                        img.src = e.target.result;
                                        img.classList.add('w-full', 'h-auto', 'max-h-32', 'object-contain');
                                        previewContainer.innerHTML = '';
                                        previewContainer.appendChild(img);
                                    };
                        
                                    reader.readAsDataURL(file);
                                }
                            }
                        
                            function allowDrop(event) {
                                event.preventDefault();
                            }

                            function highlightDropArea(event) {
                                event.preventDefault();
                                const dropArea = document.querySelector('.border-dashed'); // Selecciona el contenedor
                                dropArea.classList.add('bg-gray-700'); // Cambia el color de fondo al arrastrar sobre el contenedor
                            }

                            function resetDropArea() {
                                const dropArea = document.querySelector('.border-dashed'); // Selecciona el contenedor
                                dropArea.classList.remove('bg-gray-700'); // Restaura el color de fondo al soltar fuera del contenedor
                            }
                        
                            function handleDrop(event) {
                                event.preventDefault();
                                resetDropArea();
                                const file = event.dataTransfer.files[0];
                                const input = document.getElementById('{{$field['id']}}');
                        
                                if (file && input) {
                                    input.files = event.dataTransfer.files;
                                    previewImage(input);
                                }
                            }
                        </script>
                    @elseif($field['type']=="file")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-black dark:border-gray-600 px-6 py-10" ondragover="highlightDropAreaFile(event)" ondragleave="resetDropAreaFile()" ondrop="handleDropFile(event)">
                                <div class="text-center">
                                    <div id="preview-container" class="">
                                        @if($field['value'])
                                            <img class="w-full h-auto max-h-32 object-contain" src="{{ asset("image/{$field['value']}") }}">
                                        @else
                                            <i class="fa-solid fa-file-import text-3xl"></i>
                                        @endif
                                    </div>
                                    <div class="mt-4 flex text-sm leading-6 text-black dark:text-gray-400">
                                        <label for="{{$field['id']}}" class="relative cursor-pointer rounded-lg font-semibold text-black dark:text-gray-100 hover:text-black dark:hover:text-gray-200">
                                            <span>Cargar un archivo</span>
                                            <input id="{{$field['id']}}" name="{{$field['name']}}" type="file" class="sr-only" onchange="previewFile(this)">
                                        </label>
                                        <p class="pl-1">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs leading-5 text-black dark:text-gray-400">PDF, WORD, TXT, EXCEL, CSV</p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                        <script>
                            function previewFile(input) {
                                const previewContainer = document.getElementById('preview-container');
                                const file = input.files[0];
                        
                                if (file) {
                                    const reader = new FileReader();
                        
                                    reader.onload = function (e) {
                                        const preview = document.createElement('div');
                                        const icon = document.createElement('i');
                                        const info = document.createElement('p');
                                        if (file.type === 'application/pdf') {
                                            icon.classList.add('fa-solid', 'fa-file-pdf', 'text-3xl');
                                        } else if (file.type === 'application/msword' || file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                            icon.classList.add('fa-solid', 'fa-file-word', 'text-3xl');
                                        } else if (file.type === 'application/vnd.ms-excel' || file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                            icon.classList.add('fa-solid', 'fa-file-excel', 'text-3xl');
                                        } else if (file.type === 'text/csv') {
                                            icon.classList.add('fa-solid', 'fa-file-csv', 'text-3xl');
                                        } else if (file.type === 'text/plain') {
                                            icon.classList.add('fa-solid', 'fa-file-lines', 'text-3xl');
                                        }else{
                                            icon.classList.add('fa-solid', 'fa-file-circle-check', 'text-3xl');
                                        }
                                        info.innerHTML = `Nombre: ${file.name},<br>Tamaño: ${file.size} bytes`;
                                        preview.classList.add('w-full', 'h-auto', 'max-h-32', 'object-contain');
                                        previewContainer.innerHTML = '';
                                        preview.appendChild(icon);
                                        preview.appendChild(info);
                                        previewContainer.appendChild(preview);
                                    };
                        
                                    reader.readAsDataURL(file);
                                }
                            }
                        
                            function allowDropFile(event) {
                                event.preventDefault();
                            }

                            function highlightDropAreaFile(event) {
                                event.preventDefault();
                                const dropArea = document.querySelector('.border-dashed'); // Selecciona el contenedor
                                dropArea.classList.add('bg-gray-700'); // Cambia el color de fondo al arrastrar sobre el contenedor
                            }

                            function resetDropAreaFile() {
                                const dropArea = document.querySelector('.border-dashed'); // Selecciona el contenedor
                                dropArea.classList.remove('bg-gray-700'); // Restaura el color de fondo al soltar fuera del contenedor
                            }
                        
                            function handleDropFile(event) {
                                event.preventDefault();
                                resetDropAreaFile();
                                const file = event.dataTransfer.files[0];
                                const input = document.getElementById('{{$field['id']}}');
                        
                                if (file && input) {
                                    input.files = event.dataTransfer.files;
                                    previewFile(input);
                                }
                            }
                        </script>
                    @elseif($field['type']=="singlefile")
                    <div class="{{$field['col']}}">
                        <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                            {{$field['label']}}
                            @if(isset($isRequired) && $isRequired)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <div class="mt-2 mb-2 space-y-2" id="dynamic-list-{{$field['id']}}">
                            <!-- Input inicial -->
                           <div class="flex items-center space-x-2">
                               <input 
                                   type="file" 
                                   name="{{$field['name']}}[]" 
                                   class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700"
                                   @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                   placeholder="Elemento"
                               >
                               
                           </div>
                        </div>
                        @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                        @endif
                        <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                    </div>
                    
                    @elseif($field['type']=="text-file")
                        <div class="{{$field['col']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2">
                                <div class="relative">
                                    <textarea 
                                    type="text" 
                                    name="{{$field['name']}}" 
                                    id="{{$field['id']}}" 
                                    @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                    placeholder="{{$field['label']}}"
                                    class="block mt-2 mb-2 rounded-lg w-full border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-black dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700 @if(isset($field['class']) && $field['class']) {{$field['class']}} @endif">{{$field['value']}}</textarea>
                                    <div class=" top-2 end-3 z-10">
                                        <button type="button" id="uploadButton-{{$field['id']}}" class="py-1.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-black dark:border-transparent bg-transparent dark:bg-blue-600 text-black dark:text-white hover:bg-black hover:text-white dark:hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            Subir
                                        </button>
                                    </div>
                                    <input type="file" id="fileInput-{{$field['id']}}" style="display: none;">
                                </div>
                            </div>
                            @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                            @endif
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                        <script>
                            document.getElementById('uploadButton-{{$field['id']}}').addEventListener('click', function() {
                                document.getElementById('fileInput-{{$field['id']}}').click();
                            });
                            document.getElementById('fileInput-{{$field['id']}}').addEventListener('change', function(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    const formData = new FormData();
                                    formData.append('file', file);
                                    axios.post('{{ route('process.file') }}', formData, {
                                        headers: {
                                            'Content-Type': 'multipart/form-data'
                                        }
                                    })
                                    .then(response => {
                                        document.getElementById('{{$field['id']}}').value = response.data.text;
                                    })
                                    .catch(error => {
                                        console.error('Error processing file:', error);
                                        const grandParentNode = this.parentNode.parentNode.parentNode;
                                        ErrorLists = grandParentNode.querySelector('.invalid-feedback');
                                        console.log(ErrorLists);
                                        if(ErrorLists){
                                            ErrorLists.remove();
                                        }
                                        const errorList = document.createElement('ul');
                                        errorList.classList.add('text-sm', 'text-red-600', 'dark:text-red-400', 'space-y-1', 'mt-2', 'invalid-feedback');
                                        const errorItem = document.createElement('li');
                                        errorItem.textContent = 'Error al procesar archivo';
                                        errorList.appendChild(errorItem);
                                        grandParentNode.appendChild(errorList);
                                    });
                                }
                            });
                        </script>
                    @elseif($field['type']=="dynamic-list" || $field['type']=="dynamic-list-file")
                        <div class="{{$field['col']}} dynamic-list-container" data-field-id="{{$field['id']}}" data-field-limit="{{$field['limit']}}">
                            <label for="{{$field['id']}}" class="block text-sm font-medium leading-6 text-black dark:text-gray-100">
                                {{$field['label']}}
                                @if(isset($isRequired) && $isRequired)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="mt-2 mb-2 space-y-2" id="dynamic-list-{{$field['id']}}">
                                 <!-- Input inicial -->
                                <div class="flex items-center space-x-2">
                                    @if($field['type']=="dynamic-list-file")
                                    <input 
                                        type="file" 
                                        name="{{$field['name']}}[]" 
                                        class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700"
                                        @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                        placeholder="Elemento"
                                    >
                                    @else
                                    <input 
                                        type="text" 
                                        name="{{$field['name']}}[]" 
                                        class="block w-full rounded-lg border-1 py-1.5 text-black dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-black dark:focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent dark:bg-gray-700"
                                        @if(isset($field['attr']) && $field['attr']) {{$field['attr']}} @endif
                                        placeholder="Elemento"
                                    >
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="add-dynamic-list-btn py-1.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-black dark:border-transparent bg-transparent dark:bg-blue-600 text-black dark:text-white hover:bg-black dark:hover:bg-blue-700 hover:text-white">
                                Agregar
                            </button>
                            @if(isset($field['description']) && $field['description'])
                            <p class="mt-3 text-sm leading-6 text-black dark:text-gray-400">{{$field['description']}}</p>
                            @endif
                            <x-input-error :messages="$errors->get($field['name'])" class="mt-2" />
                        </div>
                        <script>
                            (function() {
                                if (window.DynamicListHandler) return; // Si ya está definido, no hacer nada
                            
                                window.DynamicListHandler = class {
                                    constructor(container) {
                                        this.container = container;
                                        this.fieldId = container.dataset.fieldId;
                                        this.limit = parseInt(container.dataset.fieldLimit);
                                        this.listContainer = container.querySelector(`#dynamic-list-${this.fieldId}`);
                                        this.addButton = container.querySelector('.add-dynamic-list-btn');
                                        this.itemCount = 1;
                            
                                        this.addButton.addEventListener('click', () => this.addListItem());
                                        this.listContainer.addEventListener('click', (e) => this.handleDelete(e));
                                    }
                            
                                    addListItem() {
                                        if (this.itemCount < this.limit) {
                                            const newItem = this.listContainer.children[0].cloneNode(true);
                                            newItem.querySelector('input').value = '';
                                            // Agregar botón de eliminar
                                            const deleteButton = document.createElement('button');
                                            deleteButton.type = 'button';
                                            deleteButton.className = 'delete-list-item py-1.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-black dark:border-transparent bg-transparent dark:bg-red-600 text-black dark:text-white hover:bg-black dark:hover:bg-red-700 hover:text-white';
                                            deleteButton.textContent = 'Eliminar';

                                            newItem.appendChild(deleteButton);
                                            this.listContainer.appendChild(newItem);
                                            this.itemCount++;
                                        } else {
                                            alert('Se ha alcanzado el límite máximo de elementos.');
                                        }
                                    }

                                    handleDelete(event) {
                                        if (event.target.classList.contains('delete-list-item')) {
                                            const itemToRemove = event.target.closest('.flex.items-center');
                                            if (itemToRemove && this.listContainer.children.length > 1) {
                                                itemToRemove.remove();
                                                this.itemCount--;
                                            }
                                        }
                                    }
                                }
                            
                                document.addEventListener('DOMContentLoaded', function() {
                                    document.querySelectorAll('.dynamic-list-container').forEach(container => new DynamicListHandler(container));
                                });
                            })();
                        </script>
                    @else
                        <div> {{$field['label']}}</div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>                    
@endif
