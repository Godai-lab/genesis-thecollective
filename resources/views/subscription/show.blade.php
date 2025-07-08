<x-app-layout>
    <x-slot name="title">Génesis - Subscripción</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Subscripción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">


                    
                    <div class="container mx-auto p-4">
                        <h2 class="text-2xl font-bold mb-6">Elige tu Plan de Suscripción</h2>
                
                        <!-- Mostrar mensajes de éxito o error -->
                        @if(session('success'))
                            <div class="bg-green-500 text-white p-4 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif
                
                        @if(session('error'))
                            <div class="bg-red-500 text-white p-4 rounded mb-4">
                                {{ session('error') }}
                            </div>
                        @endif
                
                        <!-- Mostrar errores de validación -->
                        @if($errors->any())
                            <div class="bg-red-500 text-white p-4 rounded mb-4">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                
                        <!-- Lista de planes -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($plans as $plan)
                                <div class="bg-white shadow-md rounded-lg p-6">
                                    <h5 class="text-xl font-semibold mb-4">{{ $plan['name'] }}</h5>
                                    <p class="text-gray-700 mb-4">
                                        <strong>Precio:</strong> {{ $plan['price'] }}<br>
                                        <strong>Intervalo:</strong> {{ $plan['interval'] }}
                                    </p>
                                    <!-- Botón para seleccionar el plan -->
                                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition select-plan-btn"
                                            data-plan-id="{{ $plan['stripe_plan_id'] }}" data-plan-name="{{ $plan['name'] }}">
                                        Seleccionar Plan
                                    </button>
                                </div>
                            @endforeach
                        </div>
                
                        <!-- Modal para el formulario de pago -->
                        <div class="fixed z-10 inset-0 overflow-y-auto hidden" id="paymentModal" aria-labelledby="modal-title" aria-hidden="true">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6">
                                    <div class="mb-6">
                                        <h5 id="paymentModalLabel" class="text-2xl font-semibold">Información de Pago</h5>
                                    </div>
                
                                    <form id="payment-form" action="{{ route('subscription.create') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan_id" id="plan-id-input">
                
                                        <!-- Campo para Stripe Elements -->
                                        <div class="mb-4">
                                            <label for="card-element" class="block text-sm font-medium text-gray-700">Tarjeta de Crédito o Débito</label>
                                            <div id="card-element" class="border border-gray-300 rounded-md p-2"></div>
                                            <div id="card-errors" class="text-red-500 mt-2"></div>
                                        </div>
                
                                        <div class="flex justify-end">
                                            <button type="button" id="cancelModal" class="bg-gray-500 text-white px-4 py-2 rounded mr-3 hover:bg-gray-600 transition">Cancelar</button>
                                            <button id="card-button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition" type="submit">
                                                Procesar Pago
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Incluir Stripe.js -->
                    <script src="https://js.stripe.com/v3/"></script>
                
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
                            var elements = stripe.elements();
                            var cardElement = elements.create('card');
                
                            // Cuando se hace clic en el botón "Seleccionar Plan"
                            document.querySelectorAll('.select-plan-btn').forEach(function(button) {
                                button.addEventListener('click', function() {
                                    var planId = this.dataset.planId;
                                    var planName = this.dataset.planName;
                
                                    // Establecer el ID del plan en el input oculto
                                    document.getElementById('plan-id-input').value = planId;
                
                                    // Actualizar el título del modal con el nombre del plan
                                    document.getElementById('paymentModalLabel').textContent = 'Información de Pago - Plan ' + planName;
                
                                    // Montar el elemento de la tarjeta si aún no está montado
                                    if (!document.getElementById('card-element').children.length) {
                                        cardElement.mount('#card-element');
                                    }
                
                                    // Mostrar el modal
                                    document.getElementById('paymentModal').classList.remove('hidden');
                                });
                            });
                
                            // Manejar el cierre del modal
                            document.getElementById('cancelModal').addEventListener('click', function() {
                                document.getElementById('paymentModal').classList.add('hidden');
                                document.getElementById('card-errors').textContent = '';
                                cardElement.clear();
                            });
                
                            // Manejar el envío del formulario de pago
                            var form = document.getElementById('payment-form');
                
                            form.addEventListener('submit', function(event) {
                                event.preventDefault();
                
                                stripe.createPaymentMethod({
                                    type: 'card',
                                    card: cardElement,
                                }).then(function(result) {
                                    if (result.error) {
                                        // Mostrar error en el formulario
                                        document.getElementById('card-errors').textContent = result.error.message;
                                    } else {
                                        // Añadir el PaymentMethod ID al formulario y enviarlo
                                        var hiddenInput = document.createElement('input');
                                        hiddenInput.setAttribute('type', 'hidden');
                                        hiddenInput.setAttribute('name', 'paymentMethodId');
                                        hiddenInput.setAttribute('value', result.paymentMethod.id);
                                        form.appendChild(hiddenInput);
                
                                        // Deshabilitar el botón para evitar múltiples clics
                                        document.getElementById('card-button').disabled = true;
                
                                        form.submit();
                                    }
                                });
                            });
                        });
                    </script>



                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>