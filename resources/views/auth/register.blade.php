@php 
$categorias = [
    
    ["id" => "", "name" => "Selecciona una categoría"],
    ["id" => "Alimentación y Bebidas", "name" => "Alimentación y Bebidas"],
    ["id" => "Moda y Belleza", "name" => "Moda y Belleza"],
    ["id" => "Salud y Bienestar", "name" => "Salud y Bienestar"],
    ["id" => "Tecnología y Electrónica", "name" => "Tecnología y Electrónica"],
    ["id" => "Educación y Formación", "name" => "Educación y Formación"],
    ["id" => "Turismo y Entretenimiento", "name" => "Turismo y Entretenimiento"],
    ["id" => "Automotriz y Transporte", "name" => "Automotriz y Transporte"],
    ["id" => "Bienes Raíces y Construcción", "name" => "Bienes Raíces y Construcción"],
    ["id" => "Servicios Profesionales", "name" => "Servicios Profesionales"],
    ["id" => "Deportes y Fitness", "name" => "Deportes y Fitness"],
    ["id" => "Salud y Medicina", "name" => "Salud y Medicina"],
    ["id" => "E-commerce y Retail", "name" => "E-commerce y Tiendas Online"],
    ["id" => "Bienestar y Estilo de Vida", "name" => "Bienestar y Estilo de Vida"],
    ["id" => "Hogar y Decoración", "name" => "Hogar y Decoración"],
    ["id" => "Servicios Financieros", "name" => "Servicios Financieros"],
    ["id" => "Energía y Sostenibilidad", "name" => "Energía y Sostenibilidad"],
    ["id" => "Agronegocios y Agroindustria", "name" => "Agronegocios y Agroindustria"],
    ["id" => "Medios, Comunicación y Contenido Digital", "name" => "Medios, Comunicación y Contenido Digital"],
    ["id" => "Logística y Cadena de Suministro", "name" => "Logística y Cadena de Suministro"],
    ["id" => "Emprendimiento e Innovación", "name" => "Emprendimiento e Innovación"],
    ["id" => "Arte, Cultura y Creatividad", "name" => "Arte, Cultura y Creatividad"],
    ["id" => "Negocios B2B y Servicios Industriales", "name" => "Negocios B2B y Servicios Industriales"],
    ["id" => "Gaming y eSports", "name" => "Gaming y eSports"],
    ["id" => "Otra", "name" => "Otra categoría"],
];

@endphp
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" data-validate="true">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"  />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Usuario')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
            @error('username')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <!-- Categoría -->
        <div class="mt-4">
            <x-input-label for="categoria" :value="__('Categoría')" />
            <select id="categoria" name="categoria" onchange="mostrarCampoOtraCategoria()" class="border-black dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-black dark:focus:border-indigo-600 focus:ring-black dark:focus:ring-indigo-600 rounded-lg shadow-sm block mt-1 w-full">
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria['id'] }}">{{ $categoria['name'] }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        

        <!-- Campo para otra categoría -->
        <div id="otraCategoriaDiv" class="mt-4" style="display: none;">
            <x-input-label for="otra_categoria" :value="__('Escribe tu categoría')" />
            <x-text-input id="otra_categoria" class="block mt-1 w-full" type="text" name="otra_categoria" />
            <x-input-error :messages="$errors->get('otra_categoria')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function mostrarCampoOtraCategoria() {
            var categoriaSelect = document.getElementById('categoria');
            var otraCategoriaDiv = document.getElementById('otraCategoriaDiv');

            if (categoriaSelect.value === 'Otra') {
                otraCategoriaDiv.style.display = 'block';
            } else {
                otraCategoriaDiv.style.display = 'none';
            }
        }
    </script>
</x-guest-layout>
{{-- @csrf 
        <x-dynamic-form 
            :fields="[
                ['label'=>'Nombre de Marca','type'=>'text', 'name'=>'name', 'id'=>'name', 'col'=>'sm:col-span-3', 'value'=>old('name'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=nombre'],
                ['label'=>'Categorias','type'=>'select', 'name'=>'country', 'id'=>'country', 'col'=>'sm:col-span-3', 'value'=>old('country'), 'attr'=>'data-validation-rules=required data-field-name=país', 'list'=>$categorias],
                ['label'=>'Usuario','type'=>'text', 'name'=>'username', 'id'=>'username', 'col'=>'sm:col-span-3', 'value'=>old('username'), 'attr'=>'data-validation-rules=required|max:50 data-field-name=usuario'],
                ['label'=>'Correo electrónico','type'=>'email', 'name'=>'email', 'id'=>'email', 'col'=>'sm:col-span-3', 'value'=>old('email'), 'attr'=>'data-validation-rules=required|email|max:80 data-field-name=correo_electrónico'],
                ['label'=>'Contraseña','type'=>'password', 'name'=>'password', 'id'=>'password', 'col'=>'sm:col-span-3', 'value'=>'', 'attr'=>'autocomplete=off data-validation-rules=required|min:8|max:80|confirmed data-field-name=contraseña'],
                ['label'=>'Confirmar contraseña','type'=>'password', 'name'=>'password_confirmation', 'id'=>'password_confirmation', 'col'=>'sm:col-span-3', 'value'=>'', 'attr'=>'autocomplete=off data-validation-rules=required|max:80 data-field-name=confirmar_contraseña'],
               
                ]" 
            >
            <h2 class="text-base font-semibold leading-7 text-black">Registro de usuario</h2>
            <p class="mt-1 text-sm leading-6 text-black">Por favor, complete los siguientes campos:</p>
        </x-dynamic-form>
        
        
        <div class="mt-6 flex items-center justify-end gap-x-6">
            <x-dynamic-button-link :type="'cancel'" :action="route('user.index')" />
            <x-dynamic-button-link :type="'save'" />
        </div> --}}