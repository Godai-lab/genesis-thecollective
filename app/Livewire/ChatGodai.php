<?php

namespace App\Livewire;

use App\Models\Generated;
use App\Services\OpenAiService;
use App\Services\PerplexityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\Attributes\On;

class ChatGodai extends Component
{
    public $abierto = false;
    public $mensaje = '';
    public $mensajes = []; // para almacenar el historial
    public $isTyping = false; // Para mostrar el indicador de escritura
    public $documentos = []; // Para almacenar la lista de documentos
    public $documentoSeleccionado = null; // Para guardar el ID del documento seleccionado
    public $documentoInfo = null; // Para guardar la información del documento seleccionado

    public function mount()
    {
        $this->mensajes = session()->get('chat_mensajes', []);
        // Cargar los documentos del usuario
        $this->cargarDocumentos();
    }

    /**
     * Carga los documentos generados según los permisos del usuario
     */
    public function cargarDocumentos()
    {
        $user = auth()->user();
        
        // Crear la consulta base
        $query = Generated::select('id', 'name', 'key', 'account_id', 'created_at')
                          ->orderBy('created_at', 'desc')
                          ->limit(30); // Limitamos a 30 documentos recientes
        
        // Si el usuario es Super Admin o Admin, puede ver todos los documentos
        if ($user->roles->pluck('name')->contains(fn($rol) => in_array($rol, ['Admin', 'Super Admin']))) {
            $documentos = $query->get();
        } else {
            $accountIds = $user->accounts->pluck('id')->toArray();
            $documentos = $query->whereIn('account_id', $accountIds)->get();
        }
        
        
        // Transformar los datos para el selector
        $this->documentos = $documentos->map(function($doc) {
            $tipo = $this->getTipoDocumento($doc->key);
            return [
                'id' => $doc->id,
                'texto' => "[{$tipo}] {$doc->name}",
                'tipo' => $doc->key,
                'fecha' => $doc->created_at->format('d/m/Y')
            ];
        })->toArray();
    }
    
    /**
     * Obtiene un nombre amigable para el tipo de documento
     */
    private function getTipoDocumento($key)
    {
        $tipos = [
            'Brief' => 'Brief',
            'Genesis' => 'Génesis',
            'Investigacion' => 'Investigación',
            'Creatividad' => 'Asistente Creativo',
            'Grafica' => 'Asistente Gráfico',
            'SocialMedia' => 'Social Media',
            'Innovacion' => 'Innovación'
        ];
        
        return $tipos[$key] ?? $key;
    }
    
    /**
     * Cuando se selecciona un documento
     */
    public function seleccionarDocumento()
    {
        if (!$this->documentoSeleccionado) {
            $this->documentoInfo = null;
            return;
        }
        
        // Buscar el documento seleccionado
        $documento = Generated::find($this->documentoSeleccionado);
        
        if ($documento) {
            $user = auth()->user();
            $puedeAcceder = $user->roles->pluck('name')->contains(fn($rol) => in_array($rol, ['Admin', 'Super Admin'])) ||
                $user->accounts->pluck('id')->contains($documento->account_id);

            
            if ($puedeAcceder) {
                // Guardar información resumida del documento
                $this->documentoInfo = [
                    'id' => $documento->id,
                    'name' => $documento->name,
                    'tipo' => $this->getTipoDocumento($documento->key),
                    'cuenta' => $documento->account ? $documento->account->name : 'Sin cuenta'
                ];
                
                // Guardar el ID en la sesión para futuras consultas
                session()->put('chat_documento', $documento->id);
            } else {
                $this->documentoInfo = null;
                session()->forget('chat_documento');
               
            }
        } else {
            $this->documentoInfo = null;
            session()->forget('chat_documento');
        }
    }

    public function toggleChat()
    {
        $this->abierto = !$this->abierto;
        
        if ($this->abierto) {
            // Cuando se abre, emitir un evento
            $this->dispatch('toggleChat');
        }
    }

    public function enviarMensaje()
    {
        if (trim($this->mensaje) === '') return;

        // Guardar una copia del mensaje
        $mensajeUsuario = $this->mensaje;
        
        $this->mensajes[] = [
            'tipo' => 'usuario',
            'texto' => $this->mensaje
        ];
        
        $this->mensaje = '';
        $this->isTyping = true;
        
        // Actualizar la sesión inmediatamente
        session()->put('chat_mensajes', $this->mensajes);
        
        // Dispatch con animación de transición más rápida
        $this->dispatch('mensajeEnviado');
        
        // Si hay un documento seleccionado, agregar contexto al mensaje
        $contextoDocumento = '';
        if ($this->documentoInfo) {
            $contextoDocumento = "Estoy consultando sobre el documento \"{$this->documentoInfo['name']}\" de tipo {$this->documentoInfo['tipo']}. ";
        }
        
        // Un pequeño retraso antes de solicitar la respuesta
        $this->dispatch('obtenerRespuestaAI', mensaje: $contextoDocumento . $mensajeUsuario);
    }

    #[On('obtenerRespuestaAI')]
    public function obtenerRespuestaAI($mensaje)
    {
        try {
            // Si hay un documento seleccionado, obtener su contenido para contextualizar
            $documentoId = session()->get('chat_documento');
            $documento = null;
            
            if ($documentoId) {
                $documento = Generated::find($documentoId);
            }
            
            // Preparar el prompt con o sin contexto del documento
            $respuesta = $this->callOpenAi($mensaje, $documento);
            
            $this->isTyping = false;
            
            if (isset($respuesta['data'])) {
                $this->mensajes[] = [
                    'tipo' => 'bot',
                    'texto' => $respuesta['data']
                ];
            } else {
                // Mensaje de error más amigable
                $error_msg = $respuesta['error'] ?? 'No se pudo procesar tu solicitud';
                \Log::error('Error en respuesta del asistente', [
                    'error' => $error_msg,
                    'prompt' => $mensaje
                ]);
                
                $this->mensajes[] = [
                    'tipo' => 'bot',
                    'texto' => 'Lo siento, estoy teniendo problemas para procesar tu solicitud. Por favor, intenta nuevamente con una consulta diferente.'
                ];
            }
            
            $this->dispatch('respuestaRecibida');
            session()->put('chat_mensajes', $this->mensajes);
        } catch (\Exception $e) {
            $this->isTyping = false;
            \Log::error('Excepción en obtenerRespuestaAI', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->mensajes[] = [
                'tipo' => 'bot',
                'texto' => 'Ha ocurrido un error inesperado. Por favor, intenta nuevamente más tarde.'
            ];
            
            $this->dispatch('respuestaRecibida');
            session()->put('chat_mensajes', $this->mensajes);
        }
    }

    public function callOpenAi($instruccion, $documento = null)
    {
        ini_set('max_execution_time', 500);
        
        // Si hay un documento, preparar un prompt con contexto
        if ($documento) {
            // Limitar el tamaño del contenido para evitar tokens excesivos
            $contenido = substr($documento->value, 0, 8000); // Ajustar según necesidad
            
            $prompt = <<<EOT
Estoy consultando sobre el siguiente documento "{$documento->name}":

Contenido del documento:
{$contenido}

Mi consulta es: {$instruccion}
EOT;
        } else {
            $prompt = $instruccion;
        }

        try {
            // ID del asistente configurado con la función search_perplexity
            $assistant_idIChat = "asst_I3Ez9cICESsKZKiB0gJCS4rL";
            
            // Usar el nuevo método que soporta funciones
            $response = OpenAiService::CompletionsAssistantsWithFunctions($prompt, $assistant_idIChat);

            if (!isset($response['data'])) {
                return ['data' => 'Lo siento, no pude procesar tu solicitud. ' . ($response['error'] ?? '')];
            }

            return ['data' => $response['data']];
        } catch (\Exception $e) {
            \Log::error('Error al llamar a Open AI: ' . $e->getMessage());
            return ['data' => 'Ocurrió un error al conectar con la IA. Por favor, intenta nuevamente más tarde.'];
        }
    }

    public function searchPerplexityFromAssistant(Request $request)
    {
        $query = $request->input('query');

        
        $prompt = <<<EOT
Busca en internet la siguiente información usando Perplexity:
$query
EOT;

        $model = "sonar-reasoning";
        $temperature = 0.7;

        // Llama a tu servicio Perplexity con el prompt modificado
        $response = PerplexityService::ChatCompletions($prompt, $model, $temperature);

        return response()->json([
            'result' => $response['data'],
            'citations' => $response['citations']
        ]);
    }

    /**
     * Quita el documento seleccionado actualmente
     */
    public function quitarDocumento()
    {
        $this->documentoSeleccionado = null;
        $this->documentoInfo = null;
        session()->forget('chat_documento');
        
        // Informar al usuario que se ha quitado el documento
        $this->mensajes[] = [
            'tipo' => 'sistema',
            'texto' => "Has quitado el documento de consulta. Ahora puedes seleccionar otro o continuar con una consulta general."
        ];
        
        // Actualizar la sesión
        session()->put('chat_mensajes', $this->mensajes);
        $this->dispatch('mensajeEnviado');
    }

    public function render()
    {
        return view('livewire.chat-godai');
    }
}
