@props(['fields','conf'])

@if ($fields && $conf)
<div class="dynamic-group-form" data-conf-id="{{ $conf['id'] }}" data-conf-limit="{{ $conf['limit'] }}" data-conf-name="{{ $conf['name'] }}" data-conf-single="@isset($conf['single']){{$conf['single']}}@endisset">
        <div class="border-b border-dashed border-gray-700 pb-12 mb-6">
            {{ $slot }}
            <div id="{{ $conf['id'] }}-container" class="">
                <div class="dynamic-field">
                    @php
                        $fieldsaux = $fields;
                        foreach ($fieldsaux as $key => &$field){
                            $field['name'] = $conf['name'].'[0]['.$field['name'].']';
                            $field['id'] = $conf['id'].'_0_'.$field['id'];
                            $field['autocomplete'] = $field['name'];
                        }
                    @endphp
                    <x-dynamic-form :fields=$fieldsaux >
                        <h2 id="dynamic-title-single-{{ $conf['id'] }}" class="text-sm font-semibold leading-7 text-black dark:text-gray-100">@isset($conf['single']){{$conf['single']}} 1 @endisset</h2>
                    </x-dynamic-form>
                </div>
            </div>
            <button type="button" class="add-group-btn py-1.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-md border border-black bg-transparent dark:bg-blue-600 text-black dark:text-white hover:bg-black hover:text-white dark:hover:bg-blue-700">
                Agregar {{$conf['single']}}
            </button>
        </div>
    </div> 
    <script>
        (function() {
            if (window.DynamicGroupForm) return;
    
            window.DynamicGroupForm = class {
                constructor(element) {
                    this.element = element;
                    this.confId = element.dataset.confId;
                    this.confSingle = element.dataset.confSingle;
                    this.confLimit = parseInt(element.dataset.confLimit);
                    this.confName = element.dataset.confName;
                    this.groupCounter = 1;
                    this.container = element.querySelector(`#${this.confId}-container`);
                    this.addButton = element.querySelector('.add-group-btn');
                    
                    this.addButton.addEventListener('click', () => this.addGroupInput());
                    this.container.addEventListener('click', (e) => this.handleDelete(e));
                }
    
                addGroupInput() {
                    if (this.groupCounter < this.confLimit) {
                        const originalField = this.container.querySelector('.dynamic-field');
                        const newField = originalField.cloneNode(true);
    
                        newField.querySelectorAll(`[name^="${this.confName}"], [id^="${this.confId}"], [autocomplete^="${this.confName}"]`).forEach(element => {
                            if (element.name) {
                                element.name = element.name.replace(`[0]`, `[${this.groupCounter}]`);
                            }
                            if (element.id) {
                                element.id = element.id.replace(`[0]`, `[${this.groupCounter}]`);
                            }
                            if (element.hasAttribute('autocomplete')) {
                                let autocomplete = element.getAttribute('autocomplete');
                                element.setAttribute('autocomplete', autocomplete.replace(`[0]`, `[${this.groupCounter}]`));
                            }
                        });

                        const TitleSingle = newField.querySelector(`[id^="dynamic-title-single-${this.confId}"]`);
                        if (TitleSingle) {
                            TitleSingle.textContent = `${this.confSingle} ${this.groupCounter + 1}`;
                        }

                        // Manejar dynamic-lists
                        newField.querySelectorAll('.dynamic-list-container').forEach((container, index) => {
                            const listId = container.querySelector('[id^="dynamic-list-"]').id;
                            const newListId = listId.replace(`_0_`, `_${this.groupCounter}_`);
                            container.querySelector('[id^="dynamic-list-"]').id = newListId;
                            container.dataset.fieldId = newListId.replace('dynamic-list-', '');

                            // Mantener solo el primer elemento de la lista y eliminar los demás
                            const listItems = container.querySelectorAll('.flex.items-center');
                            for (let i = 1; i < listItems.length; i++) {
                                listItems[i].remove();
                            }

                            // Limpiar el valor del input en el primer elemento
                            const firstInput = listItems[0].querySelector('input');
                            if (firstInput) {
                                firstInput.value = '';
                            }

                            // Eliminar el botón de eliminar del primer elemento si existe
                            const deleteBtn = listItems[0].querySelector('.delete-list-item');
                            if (deleteBtn) {
                                deleteBtn.remove();
                            }
                        });
    
                        newField.querySelectorAll('input, select, textarea').forEach(input => {
                            input.value = '';
                            if (input.type === 'checkbox' || input.type === 'radio') {
                                input.checked = false;
                            }
                        });

                        // Agregar botón de eliminar
                        const deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.className = 'delete-group-btn mt-2 mb-2 py-1.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-md border border-black dark:border-transparent bg-transparent dark:bg-red-600 text-black dark:text-white hover:bg-black dark:hover:bg-red-700 hover:text-white';
                        deleteButton.textContent = 'Eliminar Grupo';
                        
                        newField.appendChild(deleteButton);
                        this.container.appendChild(newField);
                        this.groupCounter++;

                        // Reinicializar DynamicListHandler para el nuevo grupo
                        newField.querySelectorAll('.dynamic-list-container').forEach(container => new DynamicListHandler(container));
                    } else {
                        alert('Se ha alcanzado el límite máximo de grupos.');
                    }
                }
                handleDelete(event) {
                    if (event.target.classList.contains('delete-group-btn')) {
                        const groupToRemove = event.target.closest('.dynamic-field');
                        if (groupToRemove && this.container.children.length > 1) {
                            groupToRemove.remove();
                            this.groupCounter--;
                        }
                    }
                }
            }
    
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.dynamic-group-form').forEach(form => new DynamicGroupForm(form));
            });
        })();
    </script>                       
@endif

