<?php

namespace App\Livewire\Generador\Herramientas;

use App\Models\Generated;
use App\Services\OpenAiService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

/**
 * Generador de Prompts con IA
 *
 * Permite a los usuarios generar prompts mejorados usando OpenAI Assistant
 * con la opción de incluir documentos Genesis como contexto
 */
class PromptGenerator extends Component
{
    /** Texto del prompt inicial */
    #[Validate('nullable|string|min:3')]
    public string $promptText = '';

    /** Estado de procesamiento */
    public bool $isGenerating = false;

    /** Historial de chat */
    public array $chatHistory = [];

    /** Documentos Genesis disponibles */
    public array $documentos = [];

    /** Documento seleccionado */
    public ?string $documentoSeleccionado = null;

    /** Información del documento seleccionado */
    public ?array $documentoInfo = null;

    /** ID del Assistant de OpenAI para generación de prompts */
    private string $assistantId = "asst_A2O3TljT02t6ILUgYhKqGemQ";

    public function mount()
    {
        // Cargar historial de chat desde la sesión
        $this->chatHistory = session()->get('generador_chat_history', []);
        
        // Cargar documentos Genesis disponibles
        $this->cargarDocumentosGenesis();
    }

    /**
     * Carga los documentos Genesis disponibles
     */
    public function cargarDocumentosGenesis()
    {
        try {
            $user = auth()->user();
            
            // Crear la consulta base
            $query = Generated::select('id', 'name', 'key', 'account_id', 'created_at')
                             ->where('key', 'Genesis')
                             ->orderBy('created_at', 'desc')
                             ->limit(30);
            
            // Si el usuario es Super Admin o Admin, puede ver todos los documentos
            if ($user->roles->pluck('name')->contains(fn($rol) => in_array($rol, ['Admin', 'Super Admin']))) {
                $documentos = $query->get();
            } else {
                $accountIds = $user->accounts->pluck('id')->toArray();
                $documentos = $query->whereIn('account_id', $accountIds)->get();
            }
            
            // Transformar los datos para el selector
            $this->documentos = $documentos->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'texto' => $doc->name,
                    'fecha' => $doc->created_at->format('d/m/Y')
                ];
            })->toArray();

            Log::info('Documentos Genesis cargados', [
                'count' => count($this->documentos),
                'user_id' => $user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error cargando documentos Genesis', [
                'error' => $e->getMessage()
            ]);
            $this->documentos = [];
        }
    }

    /**
     * Selecciona un documento Genesis
     */
    public function seleccionarDocumentoGenesis()
    {
        if (!$this->documentoSeleccionado) {
            $this->documentoInfo = null;
            return;
        }

        try {
            $documento = Generated::find($this->documentoSeleccionado);
            
            if ($documento) {
                $user = auth()->user();
                $puedeAcceder = $user->roles->pluck('name')->contains(fn($rol) => in_array($rol, ['Admin', 'Super Admin'])) ||
                               $user->accounts->pluck('id')->contains($documento->account_id);
                
                if ($puedeAcceder) {
                    $this->documentoInfo = [
                        'id' => $documento->id,
                        'name' => $documento->name,
                        'fecha' => $documento->created_at->format('d/m/Y'),
                        'contenido' => $documento->value
                    ];

                    Log::info('Documento Genesis seleccionado', [
                        'documentoId' => $documento->id,
                        'documentoName' => $documento->name,
                        'contenidoLength' => strlen($documento->value ?? '')
                    ]);
                } else {
                    $this->documentoInfo = null;
                    Log::warning('Usuario sin permisos para acceder al documento Genesis', [
                        'documentoId' => $documento->id,
                        'userId' => $user->id
                    ]);
                }
            } else {
                $this->documentoInfo = null;
            }
        } catch (\Exception $e) {
            Log::error('Error seleccionando documento Genesis', [
                'documentoId' => $this->documentoSeleccionado,
                'error' => $e->getMessage()
            ]);
            $this->documentoInfo = null;
        }
    }

    /**
     * Quita el documento Genesis seleccionado
     */
    public function quitarDocumentoGenesis()
    {
        $this->documentoSeleccionado = null;
        $this->documentoInfo = null;
    }

    /**
     * Genera un prompt mejorado
     */
    public function generate(): void
    {
        // Validación personalizada: debe haber al menos una instrucción o un documento Genesis
        if (empty(trim($this->promptText)) && empty($this->documentoSeleccionado)) {
            $this->addError('promptText', 'Debe escribir una instrucción o seleccionar un documento Genesis.');
            return;
        }
        
        // Validar el prompt si no está vacío
        if (!empty(trim($this->promptText))) {
            if (strlen(trim($this->promptText)) < 3) {
                $this->addError('promptText', 'La instrucción debe tener al menos 3 caracteres.');
                return;
            }
        }
        
        Log::info('🚀 Iniciando generación de prompt', [
            'prompt' => !empty($this->promptText) ? substr($this->promptText, 0, 50) . '...' : 'Vacío',
            'hasDocumento' => !is_null($this->documentoSeleccionado),
            'modo' => empty(trim($this->promptText)) ? 'Solo Genesis' : (!empty($this->documentoSeleccionado) ? 'Instrucción + Genesis' : 'Solo instrucción')
        ]);
        
        // 1. ACTIVAR INMEDIATAMENTE el spinner
        $this->isGenerating = true;
        
        Log::info('✅ Estado de generación activado', [
            'isGenerating' => $this->isGenerating
        ]);
        
        // 2. DISPARAR EVENTO para mostrar spinner en frontend
        $this->dispatch('generationStarted');
        
        Log::info('📡 Evento generationStarted disparado al frontend');
        
        // 3. DISPARAR EVENTO para iniciar generación REAL (con delay)
        $this->dispatch('startPromptGeneration', [
            'prompt' => $this->promptText,
            'documento' => $this->documentoSeleccionado
        ]);
        
        Log::info('📡 Evento startPromptGeneration disparado con datos', [
            'prompt' => !empty($this->promptText) ? substr($this->promptText, 0, 50) . '...' : 'Vacío',
            'documento' => $this->documentoSeleccionado
        ]);
    }

    /**
     * Ejecuta la generación real del prompt
     */
    #[On('startPromptGeneration')]
    public function executeGeneration($data): void
    {
        Log::info('🔄 Ejecutando generación real de prompt', [
            'prompt' => substr($data['prompt'], 0, 50) . '...',
            'documento' => $data['documento'],
            'timestamp' => now()->toIso8601String()
        ]);
        
        try {
            $this->generarPromptConOpenAI($data['prompt'], $data['documento']);
        } catch (\Exception $e) {
            Log::error('💥 Error en executeGeneration', [
                'prompt' => substr($data['prompt'], 0, 50) . '...',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Error: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            // Enviar error al componente principal
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'prompt-generator'
            );
            
            $this->dispatch('generationError');
            $this->isGenerating = false; // Solo en caso de error
        }
    }

    /**
     * Genera el prompt usando OpenAI Assistant
     */
    private function generarPromptConOpenAI(string $promptCompleto, ?string $documento): void
    {
        Log::info('🎨 Iniciando generación con OpenAI Assistant', [
            'prompt' => !empty($promptCompleto) ? substr($promptCompleto, 0, 50) . '...' : 'Vacío',
            'hasDocumento' => !is_null($documento)
        ]);

        try {
            $promptFinal = '';
            
            // Si hay instrucción del usuario, usarla como base
            if (!empty(trim($promptCompleto))) {
                $promptFinal = trim($promptCompleto);
            }
            
            // Agregar contenido del documento si está seleccionado
            if ($documento) {
                $documentoinfo = Generated::find($documento);

                if ($documentoinfo && $documentoinfo->value) {
                    if (!empty($promptFinal)) {
                        // Si hay instrucción + documento, combinar
                        $promptFinal .= "\n\nContenido relacionado:\n" . $documentoinfo->value;
                    } else {
                        // Si solo hay documento, usarlo como base
                        $promptFinal = "Basándome en el siguiente contenido, genera un prompt optimizado para IA:\n\n" . $documentoinfo->value;
                    }
                    
                    Log::info('📄 Documento Genesis procesado', [
                        'documentoId' => $documento,
                        'documentoName' => $documentoinfo->name,
                        'contenidoLength' => strlen($documentoinfo->value),
                        'modo' => !empty(trim($promptCompleto)) ? 'Instrucción + Genesis' : 'Solo Genesis'
                    ]);
                }
            }

            // Llamar al servicio de OpenAI
            $response = OpenAiService::CompletionsAssistants($promptFinal, $this->assistantId);

            Log::info('📡 Respuesta de OpenAiService::CompletionsAssistants', [
                'hasError' => isset($response['error']),
                'hasData' => isset($response['data']),
                'responseKeys' => array_keys($response)
            ]);

            if (isset($response['data'])) {
                $textoGenerado = $response['data'];

                // Agregar al historial de chat local
                $this->chatHistory[] = [
                    'tipo' => 'sistema',
                    'contenido' => $textoGenerado,
                    'tiempo' => now()->format('H:i')
                ];

                // Guardar en sesión
                session()->put('generador_chat_history', $this->chatHistory);

                // Agregar al historial principal del generador
                $this->dispatch('addToHistory', 
                    type: 'prompt/generate', 
                    prompt: !empty(trim($this->promptText)) ? $this->promptText : 'Solo documento Genesis',
                    generatedPrompt: $textoGenerado,
                    documento: $this->documentoSeleccionado,
                    model: 'OpenAI Assistant',
                    date: now()->toIso8601String()
                );

                Log::info('✅ Prompt generado exitosamente', [
                    'textoLength' => strlen($textoGenerado),
                    'chatHistoryCount' => count($this->chatHistory)
                ]);

                // Disparar evento de finalización
                $this->dispatch('generationCompleted');
                
                // Disparar evento para actualizar historial
                $this->dispatch('historialActualizado');

            } elseif (isset($response['error'])) {
                Log::error('❌ Error en OpenAI Assistant', [
                    'error' => $response['error']
                ]);
                
                $errorMessage = 'Error generando prompt: ' . $response['error'];
                $this->addError('promptText', $errorMessage);
                
                $this->dispatch('addErrorToList', 
                    message: $errorMessage, 
                    type: 'generation', 
                    tool: 'prompt-generator'
                );
                
                $this->dispatch('generationError');
            }

        } catch (\Exception $e) {
            Log::error('💥 Error en generarPromptConOpenAI', [
                'prompt' => substr($promptCompleto, 0, 50) . '...',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Error generando prompt: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'prompt-generator'
            );
            
            $this->dispatch('generationError');
        } finally {
            $this->isGenerating = false;
            Log::info('🏁 Finalizando generarPromptConOpenAI', [
                'isGenerating' => $this->isGenerating
            ]);
        }
    }

    /**
     * Limpia el historial de chat
     */
    public function limpiarHistorial(): void
    {
        $this->chatHistory = [];
        session()->forget('generador_chat_history');
        
        Log::info('🧹 Historial de chat limpiado');
        
        $this->dispatch('historialActualizado');
    }

    /**
     * Carga un prompt desde el historial para editar
     */
    #[On('loadPromptFromHistory')]
    public function loadPromptFromHistory($promptData): void
    {
        Log::info('📝 Cargando prompt desde historial', [
            'prompt' => substr($promptData['prompt'], 0, 50) . '...'
        ]);
        
        $this->promptText = $promptData['prompt'];
        
        if (isset($promptData['documento'])) {
            $this->documentoSeleccionado = $promptData['documento'];
            $this->seleccionarDocumentoGenesis();
        }
    }

    public function render()
    {
        return view('livewire.generador.herramientas.prompt-generator');
    }
}
