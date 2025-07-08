<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\On;

class VideoGenerador extends Component
{
    use WithFileUploads;
    
    // Propiedades básicas
    public $prompt = ''; 
    public $ratio = '16:9'; // Valor predeterminado para video
    public $isGenerating = false;
    public $videoGenerating = false;
    public $errorMessage = null;
    
    // Historial de chat
    public $chatHistory = [];
    
    // Opciones disponibles
    public $ratiosDisponibles = [
        '16:9' => 'Panorámico',
        '9:16' => 'Vertical móvil'
    ];
    
    // Añadir estas propiedades al componente
    public $imageFile = null; // Para almacenar la imagen subida
    public $temporaryImage = null; // Para manejar la carga temporal
    public $imagePreview = null; // URL temporal para previsualización
    
    /**
     * Inicializar el componente y cargar el historial desde la sesión
     */
    public function mount()
    {
        // Cargar el historial desde la sesión
        $this->chatHistory = session()->get('video_generador_chat_history', []);
    }
    
    /**
     * Cambia el ratio seleccionado
     */
    public function seleccionarRatio($nuevoRatio)
    {
        $this->ratio = $nuevoRatio;
    }
    
    /**
     * Limpia los errores al modificar campos
     */
    public function updated($field)
    {
        $this->errorMessage = null;
    }
    
    /**
     * Maneja la carga de imágenes
     */
    public function updatedTemporaryImage()
    {
        try {
            // Validar la imagen
            $this->validate([
                'temporaryImage' => 'image|max:5120', // máximo 5MB
            ], [
                'temporaryImage.image' => 'El archivo debe ser una imagen válida.',
                'temporaryImage.max' => 'La imagen no debe superar los 5MB.',
            ]);

            // Si ya hay una imagen, la reemplazamos
            $this->imageFile = $this->temporaryImage;
            $this->imagePreview = $this->temporaryImage->temporaryUrl();
            
            // Limpiar la imagen temporal
            $this->temporaryImage = null;
        } catch (Exception $e) {
            $this->errorMessage = 'Error cargando imagen: ' . $e->getMessage();
            session()->flash('error', 'Error: ' . $this->errorMessage);
        }
    }
    
    /**
     * Elimina la imagen cargada
     */
    public function quitarImagen()
    {
        $this->imageFile = null;
        $this->imagePreview = null;
    }
    
    /**
     * Método para generar un video
     */
    public function generar()
    {
        try {
            // Validación básica
            if (empty(trim($this->prompt)) && !$this->imageFile) {
                $this->errorMessage = 'Por favor ingrese una descripción o suba una imagen para generar el video.';
                session()->flash('error', 'Por favor ingrese una descripción o suba una imagen para generar el video.');
                return;
            }
            
            // Establecer isGenerating a true
            $this->isGenerating = true;
            
            $this->errorMessage = null;
            
            // Primero agregar el mensaje del usuario al chat
            $ratioTexto = " con relación de aspecto " . $this->ratio;
            
            // Guardar el prompt antes de limpiarlo
            $promptActual = $this->prompt;
            $ratioActual = $this->ratio;
            
            // Crear array de datos para el mensaje del usuario
            $mensajeUsuario = [
                'tipo' => 'usuario',
                'contenido' => $promptActual . $ratioTexto,
                'tiempo' => now()->format('H:i')
            ];
            
            // Si hay imagen, añadirla al mensaje
            if ($this->imageFile) {
                try {
                    $mensajeUsuario['imagenes'] = [
                        [
                            'url' => $this->imageFile->temporaryUrl(),
                            'name' => $this->imageFile->getClientOriginalName()
                        ]
                    ];
                } catch (Exception $e) {
                    // Omitir errores al obtener la URL temporal
                }
            }
            
            // Agregar al historial
            $this->chatHistory[] = $mensajeUsuario;
            
            // Guardar el historial en la sesión
            session()->put('video_generador_chat_history', $this->chatHistory);
            
            // Limpiar el input inmediatamente
            $this->prompt = '';
            
            // Forzar actualización de UI después de agregar el mensaje del usuario
            $this->dispatch('historialActualizado');
            
            // Iniciar generación de video
            $this->generarVideo($promptActual, $ratioActual);
            
        } catch (Exception $e) {
            Log::error('Error generando video: ' . $e->getMessage());
            $this->errorMessage = 'Error: ' . $e->getMessage();
            session()->flash('error', 'Error: ' . $e->getMessage());
            $this->isGenerating = false;
            $this->videoGenerating = false;
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Método para generar video con Gemini
     */
    private function generarVideo($prompt, $ratio)
    {
        try {
            // Mostrar indicador de generación
            $this->isGenerating = true;
            $this->videoGenerating = true;
            
            Log::info('Generando video con prompt: ' . $prompt);
            
            // Procesar la imagen si existe
            $imageBase64 = null;
            if ($this->imageFile) {
                // Leer la imagen y convertirla a base64
                $imagePath = $this->imageFile->getRealPath();
                $imageContent = file_get_contents($imagePath);
                $imageBase64 = base64_encode($imageContent);
            }
            
            // Iniciar la generación de video con GeminiService
            $response = GeminiService::generateVideo(
                $prompt,
                "veo-2.0-generate-001",
                $ratio,
                $imageBase64 // Pasamos la imagen en base64 si existe
            );
            
            Log::info('Respuesta inicial de generación de video: ', ['response' => json_encode($response)]);
            
            // Procesar la respuesta inicial
            if (!isset($response['success']) || !$response['success']) {
                $errorMsg = $response['error'] ?? 'Error desconocido al iniciar la generación del video';
                if (is_array($errorMsg) && isset($errorMsg['message'])) {
                    $errorMsg = $errorMsg['message'];
                }
                
                $this->errorMessage = 'Error: ' . $errorMsg;
                $this->videoGenerating = false;
                Log::error('Error en respuesta de GeminiService para video', ['error' => $errorMsg]);
                session()->flash('error', 'Error: ' . $this->errorMessage);
                $this->dispatch('errorOcurrido');
                return;
            }
            
            // Obtener el ID de operación para consultar el estado
            $operationId = $response['operationId'] ?? null;
            
            if (!$operationId) {
                $this->errorMessage = 'No se pudo obtener el ID de operación para el video';
                $this->videoGenerating = false;
                Log::error('Falta ID de operación en respuesta de video');
                session()->flash('error', 'Error: ' . $this->errorMessage);
                $this->dispatch('errorOcurrido');
                return;
            }
            
            // Guardar operationId y prompt en propiedades para consultas
            $this->dispatch('verificarEstadoVideo', [
                'operationId' => $operationId,
                'prompt' => $prompt,
                'ratio' => $ratio
            ]);
            
            // Limpiar la imagen después de usarla
            $this->imageFile = null;
            $this->imagePreview = null;
            
        } catch (Exception $e) {
            $this->errorMessage = 'Error generando video: ' . $e->getMessage();
            $this->videoGenerating = false;
            Log::error('Excepción generando video: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Método para verificar el estado de generación del video
     */
    #[On('verificarEstadoVideo')]
    public function verificarEstadoVideo($datos)
    {
        $operationId = $datos['operationId'] ?? null;
        
        if (!$operationId) {
            $this->errorMessage = 'ID de operación no válido';
            $this->isGenerating = false;
            $this->videoGenerating = false;
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->dispatch('errorOcurrido');
            return;
        }
        
        try {
            // Asegurarse de que el indicador esté visible
            $this->videoGenerating = true;
            
            // Consultar el estado de la operación de video
            $response = GeminiService::getVideoOperation($operationId);
            
            Log::info('Estado de operación de video: ', ['response' => json_encode($response)]);
            
            // Verificar si hay error en la respuesta
            if (!isset($response['success']) || !$response['success']) {
                $errorMsg = $response['error'] ?? 'Error al consultar el estado del video';
                $this->errorMessage = 'Error: ' . $errorMsg;
                session()->flash('error', 'Error: ' . $this->errorMessage);
                $this->isGenerating = false;
                $this->videoGenerating = false;
                $this->dispatch('errorOcurrido');
                return;
            }
            
            // Verificar si la operación está completa
            $done = $response['done'] ?? false;
            
            if ($done) {
                // Video generado correctamente
                if (isset($response['videoUrl'])) {
                    $videoUrl = $response['videoUrl'];
                    
                    // Crear objeto de resultados para el video
                    $resultados = [
                        'url' => $videoUrl,
                        'prompt' => $datos['prompt'] ?? '',
                        'ratio' => $datos['ratio'] ?? '',
                        'text' => "Video generado correctamente.",
                        'esVideo' => true
                    ];
                    
                    // Agregar al historial como respuesta del sistema
                    $this->agregarRespuestaSistema($resultados);
                    
                    // Marcar como completado
                    $this->isGenerating = false;
                    $this->videoGenerating = false;
                    $this->dispatch('videoGenerado');
                } else {
                    // La operación está completa pero no hay URL de video
                    $this->errorMessage = 'El video se generó pero no se encontró la URL del resultado';
                    session()->flash('error', 'El video se generó pero no se encontró la URL del resultado');
                    $this->isGenerating = false;
                    $this->videoGenerating = false;
                }
            } else {
                // La operación aún está en curso, programar nueva verificación después de 2 segundos
                $this->dispatch('verificarEstadoVideo', $datos);
            }
            
        } catch (Exception $e) {
            $this->errorMessage = 'Error verificando estado del video: ' . $e->getMessage();
            session()->flash('error', 'Error verificando estado del video: ' . $this->errorMessage);
            Log::error('Error verificando estado del video: ' . $e->getMessage());
            $this->isGenerating = false;
            $this->videoGenerating = false;
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Limpia el historial de chat
     */
    public function limpiarHistorial()
    {
        // Limpiar el historial local
        $this->chatHistory = [];
        
        // Eliminar de la sesión
        session()->forget('video_generador_chat_history');
        
        // Forzar actualización de UI
        $this->dispatch('historialActualizado');
        
        // Mostrar mensaje de confirmación
        session()->flash('mensaje', 'Historial limpiado correctamente');
    }
    
    /**
     * Agrega la respuesta del sistema al historial
     */
    private function agregarRespuestaSistema($resultados)
    {
        // Crear un nuevo mensaje en el historial
        $nuevoMensaje = [
            'tipo' => 'sistema',
            'contenido' => $resultados['text'] ?? '',
            'tiempo' => now()->format('H:i'),
            'esVideo' => true
        ];
        
        // Si hay URL de video
        if (isset($resultados['url'])) {
            $nuevoMensaje['url'] = $resultados['url'];
        }
        
        // Agregar al historial y guardar en sesión
        $this->chatHistory[] = $nuevoMensaje;
        session()->put('video_generador_chat_history', $this->chatHistory);
        
        $this->prompt = '';
    }
    
    public function render()
    {
        return view('livewire.video-generador');
    }
} 