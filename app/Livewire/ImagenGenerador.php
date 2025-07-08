<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\On;
use App\Services\OpenAiService;
use App\Services\FluxService;

class ImagenGenerador extends Component
{
    use WithFileUploads;
    
    // Propiedades para el generador
    public $prompt = ''; // Prompt para la generación
    public $estilo = ''; 
    public $ratio = '1:1'; // Valor predeterminado compatible
    public $isGenerating = false; 
    public $isTyping = false; // Indicador para mostrar "escribiendo..."
    public $resultados = []; 
    public $imageFiles = []; // Para almacenar imágenes adjuntas
    public $errorMessage = null; 
    
    // Historial de chat
    public $chatHistory = [];
    
    // Opciones disponibles
    public $ratiosDisponibles = [
        '1:1' => 'Cuadrado', 
        '4:3' => 'Horizontal',
        '3:4' => 'Vertical',
        '16:9' => 'Panorámico',
        '9:16' => 'Vertical móvil'
    ];
    
    // Propiedades para cantidad de imágenes
    public $cantidadImagenes = 1;
    public $cantidadesDisponibles = [
        1 => '1 imagen',
        2 => '2 imágenes',
        3 => '3 imágenes',
        4 => '4 imágenes'
    ];
    
    // Propiedades para servicios de IA
    public $servicioImagen = 'gemini'; 
    public $serviciosDisponibles = [
        'gemini' => 'Image 3',
        'openai' => 'ChatGPT',
        'flux' => 'Flux Pro'
    ];
    
    // Propiedades para calidad de imagen
    public $calidadImagen = 'auto'; 
    public $calidadesDisponibles = [
        'auto' => 'Automática',
        'high' => 'Alta',
        'medium' => 'Media',
        'low' => 'Baja'
    ];
    
    // Propiedades para tamaño de OpenAI
    public $tamanoOpenAI = 'auto';
    public $tamanosOpenAI = [
        'auto' => 'Automático',
        '1024x1024' => 'Cuadrado',
        '1536x1024' => 'Horizontal',
        '1024x1536' => 'Vertical'
    ];
    
    // Propiedad para Flux
    public $fluxGenerating = false;
    
    // Propiedad para imágenes temporales
    public $temporaryImages = [];
    
    /**
     * Observador para cuando se seleccionan nuevas imágenes
     */
    public function updatedTemporaryImages()
    {
        if (empty($this->temporaryImages)) {
            return;
        }
        
        // Si no hay imágenes previas, simplemente asignamos las nuevas
        if (empty($this->imageFiles)) {
            $this->imageFiles = $this->temporaryImages;
        } else {
            // Si ya hay imágenes, las combinamos con las nuevas
            foreach ($this->temporaryImages as $newImage) {
                $this->imageFiles[] = $newImage;
            }
        }
        
        // Limpiamos las imágenes temporales
        $this->temporaryImages = [];
    }
    
    /**
     * Inicializar el componente y cargar el historial desde la sesión
     */
    public function mount()
    {
        // Cargar el historial desde la sesión
        $this->chatHistory = session()->get('imagen_generador_chat_history', []);
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
     * Selecciona la cantidad de imágenes a generar
     */
    public function seleccionarCantidad($nuevaCantidad)
    {
        $this->cantidadImagenes = (int)$nuevaCantidad;
    }
    
    /**
     * Selecciona el servicio de imagen a utilizar
     */
    public function seleccionarServicioImagen($servicio)
    {
        if (isset($this->serviciosDisponibles[$servicio])) {
            $this->servicioImagen = $servicio;
        }
    }
    
    /**
     * Selecciona la calidad de imagen
     */
    public function seleccionarCalidadImagen($calidad)
    {
        if (isset($this->calidadesDisponibles[$calidad])) {
            $this->calidadImagen = $calidad;
        }
    }
    
    /**
     * Selecciona el tamaño de imagen para OpenAI
     */
    public function seleccionarTamanoOpenAI($tamano)
    {
        if (isset($this->tamanosOpenAI[$tamano])) {
            $this->tamanoOpenAI = $tamano;
        }
    }
    
    /**
     * Método para generar imágenes
     */
    public function generar()
    {
        try {
            // Validación básica
            if (empty(trim($this->prompt))) {
                $this->errorMessage = 'Por favor ingrese una descripción para generar.';
                session()->flash('error', 'Por favor ingrese una descripción para generar.');
                return;
            }
            
            // Establecer isGenerating a true
            $this->isGenerating = true;
            
            $this->errorMessage = null;
            
            // Primero agregar el mensaje del usuario al chat
            $ratioTexto = " con relación de aspecto " . $this->ratio;
            
            $imagenes = [];
            if (!empty($this->imageFiles)) {
                foreach ($this->imageFiles as $image) {
                    try {
                        $imagenes[] = [
                            'url' => $image->temporaryUrl(),
                            'name' => $image->getClientOriginalName()
                        ];
                    } catch (Exception $e) {
                        // Omitir imágenes con error
                    }
                }
            }
            
            // Guardar el prompt antes de limpiarlo
            $promptActual = $this->prompt;
            $ratioActual = $this->ratio;
            
            // Agregar el mensaje del usuario al historial
            $this->chatHistory[] = [
                'tipo' => 'usuario',
                'contenido' => $promptActual . $ratioTexto,
                'imagenes' => $imagenes,
                'tiempo' => now()->format('H:i')
            ];
            
            // Guardar el historial en la sesión
            session()->put('imagen_generador_chat_history', $this->chatHistory);
            
            // Limpiar el input inmediatamente
            $this->prompt = '';
            
            // Activar el indicador de escritura
            $this->isTyping = true;
            
            // Forzar actualización de UI
            $this->dispatch('historialActualizado');
            
            // Usar un evento para iniciar la generación después de que la UI se actualice
            $this->dispatch('iniciarGeneracion', [
                'prompt' => $promptActual,
                'ratio' => $ratioActual,
                'cantidad' => $this->cantidadImagenes
            ]);
            
            // Dispatch del evento de typing
            $this->dispatch('typingStarted');
            
        } catch (Exception $e) {
            Log::error('Error generando contenido: ' . $e->getMessage());
            $this->errorMessage = 'Error: ' . $e->getMessage();
            session()->flash('error', 'Error: ' . $e->getMessage());
            $this->isGenerating = false;
            $this->dispatch('errorOcurrido');
        } finally {
            $this->isTyping = false;
            $this->dispatch('imagenGenerada');
        }
    }
    
    /**
     * Método que inicia la generación después de que el mensaje del usuario se muestra
     */
    #[On('iniciarGeneracion')]
    public function iniciarGeneracion($datos)
    {
        try {
            $this->isGenerating = true;
            
            $this->generarImagenConDatos($datos);
        } catch (Exception $e) {
            $this->errorMessage = 'Error inesperado: ' . $e->getMessage();
            $this->isGenerating = false;
            $this->isTyping = false;
            Log::error('Error en generador: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $e->getMessage());
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Método para generar imagen con los datos proporcionados
     */
    private function generarImagenConDatos($datos)
    {
        try {
           
            $promptCompleto = $datos['prompt'];
            $aspectRatio = $datos['ratio'];
            
            Log::info('Generando imagen con prompt: ' . $promptCompleto . ' usando servicio: ' . $this->servicioImagen);
            
            switch ($this->servicioImagen) {
                case 'gemini':
                    return $this->generarImagenGemini($promptCompleto, $aspectRatio, $datos['cantidad']);
                case 'openai':
                    return $this->generarImagenOpenAI($promptCompleto, $aspectRatio, $datos['cantidad']);
                case 'flux':
                    $this->generarImagenConFlux($promptCompleto, $datos);
                    break;
                default:
                    throw new \Exception("Servicio de generación de imágenes no reconocido: {$this->servicioImagen}");
            }
        } catch (Exception $e) {
            $this->errorMessage = 'Error generando imagen: ' . $e->getMessage();
            Log::error('Excepción generando imagen: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error: ' . $e->getMessage());
            $this->dispatch('errorOcurrido');
        } finally {
            $this->isGenerating = false;
            $this->isTyping = false;
            $this->dispatch('imagenGenerada');
        }
    }
    
    /**
     * Genera imágenes usando el servicio de Gemini
     */
    private function generarImagenGemini($prompt, $aspectRatio, $cantidad)
    {
        try {
            // Establecer tiempo de ejecución máximo para esta operación
            set_time_limit(180); // 3 minutos
            
            // Ajustar el prompt basado en el ratio y otras consideraciones
            $promptFinal = $prompt . " Asegúrate de crear una imagen con una relación de aspecto de {$aspectRatio}.";
            
            Log::info('Generando imagen con Gemini: ' . substr($promptFinal, 0, 100) . '...');
            
            // Llamar al servicio de Gemini para generar la imagen
            $response = GeminiService::generateImage(
                $promptFinal,
                "imagen-3.0-generate-002", // Modelo Imagen v3
                $cantidad,
                $aspectRatio
            );
            
            // Verificar si hay errores en la respuesta
            if (!isset($response['success']) || !$response['success']) {
                $errorMsg = isset($response['error']['message']) ? $response['error']['message'] : 'Error desconocido al generar la imagen';
                $this->errorMessage = 'Error en Gemini: ' . $errorMsg;
                Log::error('Error en Gemini: ' . $errorMsg);
                session()->flash('error', 'Error: ' . $this->errorMessage);
                $this->isGenerating = false;
                $this->dispatch('errorOcurrido');
                return false;
            }
            
            // Procesar las imágenes generadas
            $generatedImages = [];
            if (isset($response['data']) && is_array($response['data'])) {
                foreach ($response['data'] as $image) {
                    // Guardar imagen en el disco
                    $imageBase64 = $image['bytesBase64Encoded'];
                    $mimeType = $image['mimeType'];
                    
                    $imageUrl = $this->guardarImagenEnDisco($imageBase64, $mimeType, 'gemini');
                    
                    if ($imageUrl) {
                        $generatedImages[] = [
                            'url' => $imageUrl,
                            'mimeType' => $mimeType
                        ];
                    }
                }
            }
            
            // Crear objeto de resultados
            $resultados = [
                'images' => $generatedImages,
                'prompt' => $prompt,
                'ratio' => $aspectRatio,
                'text' => count($generatedImages) . ' imágenes generadas con Gemini'
            ];
            
            // Guardar los resultados y agregar al historial
            $this->resultados = $resultados;
            $this->agregarRespuestaSistema($resultados);
            
            $this->isGenerating = false;
            $this->isTyping = false;
            $this->dispatch('imagenGenerada');
            
            return true;
            
        } catch (Exception $e) {
            $this->errorMessage = 'Error generando imagen con Gemini: ' . $e->getMessage();
            Log::error('Error con Gemini: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->dispatch('errorOcurrido');
            return false;
        }
    }
    
    /**
     * Genera imágenes usando el servicio de OpenAI
     */
    private function generarImagenOpenAI($prompt, $aspectRatio, $cantidad)
    {
        try {
            // Establecer tiempo de ejecución máximo para esta operación
            set_time_limit(180); // 3 minutos
            
            // Datos para la generación
            $size = $this->tamanoOpenAI === 'auto' ? '1024x1024' : $this->tamanoOpenAI;
            $quality = $this->calidadImagen;
            
            // Comprobar si hay imágenes adjuntas para editar
            if (!empty($this->imageFiles)) {
                // Si hay imágenes, procesarlas individualmente
                $generatedImages = [];
                
                foreach ($this->imageFiles as $image) {
                    $imagePath = $image->getRealPath();
                    
                    // Llamar al servicio de edición de imágenes de OpenAI para cada imagen
                    $response = OpenAiService::editImage($prompt, [$imagePath], 'gpt-image-1', $size, 'auto', 1);
                    
                    if (isset($response['error'])) {
                        // Registrar el error pero continuar con otras imágenes
                        Log::error('Error procesando imagen: ' . $response['error']);
                        continue;
                    }
                    
                    // Procesar respuesta y guardar las imágenes resultantes
                    if (isset($response['data']) && is_array($response['data'])) {
                        foreach ($response['data'] as $resultImage) {
                            if (isset($resultImage['b64_json'])) {
                                $imageBase64 = $resultImage['b64_json'];
                                $mimeType = 'image/jpeg';
                                
                                // Guardar la imagen en disco
                                $imageUrl = $this->guardarImagenEnDisco($imageBase64, $mimeType, 'openai');
                                
                                if ($imageUrl) {
                                    $generatedImages[] = [
                                        'url' => $imageUrl,
                                        'mimeType' => $mimeType,
                                        'original' => $image->getClientOriginalName()
                                    ];
                                }
                            } else if (isset($resultImage['url'])) {
                                // Si es una URL directa
                                $generatedImages[] = [
                                    'url' => $resultImage['url'],
                                    'mimeType' => 'image/jpeg',
                                    'original' => $image->getClientOriginalName()
                                ];
                            }
                        }
                    }
                }
                
                if (!empty($generatedImages)) {
                    $resultados = [
                        'images' => $generatedImages,
                        'prompt' => $prompt,
                        'ratio' => $aspectRatio,
                        'text' => count($generatedImages) . ' imágenes generadas con éxito'
                    ];
                    
                    $this->resultados = $resultados;
                    $this->agregarRespuestaSistema($resultados);
                } else {
                    $this->errorMessage = 'No se pudo generar ninguna imagen';
                    session()->flash('error', 'Error: ' . $this->errorMessage);
                }
            } else {
                // Generar imágenes desde cero
                $response = OpenAiService::generateImage(
                    $prompt, 
                    'gpt-image-1', 
                    $quality,
                    $size,
                    $cantidad
                );
                
                if (isset($response['error'])) {
                    $this->errorMessage = 'Error con OpenAI: ' . $response['error'];
                    session()->flash('error', 'Error: ' . $this->errorMessage);
                    return false;
                }
                
                $generatedImages = [];
                
                if (isset($response['data']) && is_array($response['data'])) {
                    foreach ($response['data'] as $image) {
                        if (isset($image['b64_json'])) {
                            // Si tenemos una imagen en base64
                            $imageBase64 = $image['b64_json'];
                            $mimeType = 'image/jpeg';
                            
                            // Guardar la imagen en disco
                            $imageUrl = $this->guardarImagenEnDisco($imageBase64, $mimeType, 'openai');
                            
                            if ($imageUrl) {
                                $generatedImages[] = [
                                    'url' => $imageUrl,
                                    'mimeType' => $mimeType
                                ];
                            }
                        } else if (isset($image['url'])) {
                            // Si tenemos una URL directa
                            $url = $image['url'];
                            
                            // Descargar y guardar la imagen localmente
                            $localUrl = $this->descargarYGuardarImagen($url, 'openai');
                            
                            if ($localUrl) {
                                $generatedImages[] = [
                                    'url' => $localUrl,
                                    'mimeType' => 'image/jpeg'
                                ];
                            } else {
                                // Si no podemos guardar localmente, usar la URL original
                                $generatedImages[] = [
                                    'url' => $url,
                                    'mimeType' => 'image/jpeg'
                                ];
                            }
                        }
                    }
                }
                
                if (!empty($generatedImages)) {
                    $resultados = [
                        'images' => $generatedImages,
                        'prompt' => $prompt,
                        'ratio' => $aspectRatio,
                        'text' => count($generatedImages) . ' imágenes generadas con OpenAI'
                    ];
                    
                    $this->resultados = $resultados;
                    $this->agregarRespuestaSistema($resultados);
                } else {
                    $this->errorMessage = 'No se pudo generar ninguna imagen';
                    session()->flash('error', 'Error: ' . $this->errorMessage);
                    return false;
                }
            }
            
            $this->isGenerating = false;
            $this->imageFiles = [];
            $this->dispatch('imagenGenerada');
            
            return true;
            
        } catch (Exception $e) {
            $this->errorMessage = 'Error generando imagen con OpenAI: ' . $e->getMessage();
            Log::error('Error con OpenAI: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->dispatch('errorOcurrido');
            return false;
        }
    }
    
    /**
     * Genera imágenes usando el servicio de Flux
     */
    private function generarImagenConFlux($prompt, $datos)
    {
        // Determinar dimensiones basadas en la relación de aspecto
        $dimensions = $this->getDimensionsFromRatio($datos['ratio']);
        $width = $dimensions['width'];
        $height = $dimensions['height'];
        
        // Activar ambos indicadores de generación
        $this->isGenerating = true;
        $this->fluxGenerating = true;
        
        // Iniciar la generación de la imagen con Flux
        $response = FluxService::GenerateImageFlux(
            $prompt,
            $width,
            $height,
            true, // prompt_upsampling
            null, // seed (aleatorio)
            2     // safety_tolerance
        );
        
        if (isset($response['error'])) {
            $this->errorMessage = 'Error con Flux: ' . ($response['error'] ?? 'Error desconocido');
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->isGenerating = false;
            $this->fluxGenerating = false;
            $this->dispatch('errorOcurrido');
            return;
        }
        
        if (isset($response['data'])) {
            $generationId = $response['data'];
            
            // Programar la verificación del estado de la generación
            $this->dispatch('verificarEstadoFlux', [
                'generationId' => $generationId,
                'prompt' => $prompt,
                'ratio' => $datos['ratio']
            ]);
        } else {
            $this->errorMessage = 'Respuesta inesperada de Flux';
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->isGenerating = false;
            $this->fluxGenerating = false;
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Método para convertir ratio a dimensiones
     */
    private function getDimensionsFromRatio($ratio)
    {
        switch ($ratio) {
            case '1:1':
                return ['width' => 1024, 'height' => 1024];
            case '4:3':
                return ['width' => 1024, 'height' => 768];
            case '3:4':
                return ['width' => 768, 'height' => 1024];
            case '16:9':
                return ['width' => 1024, 'height' => 576];
            case '9:16':
                return ['width' => 576, 'height' => 1024];
            default:
                return ['width' => 1024, 'height' => 1024];
        }
    }
    
    /**
     * Verifica el estado de una generación de imagen con Flux
     */
    #[On('verificarEstadoFlux')]
    public function verificarEstadoFlux($datos = [])
    {
        try {
            $generationId = $datos['generationId'] ?? null;
            
            // Asegurar que ambos indicadores estén activos
            $this->isGenerating = true;
            $this->fluxGenerating = true;
            
            if (!$generationId) {
                $this->errorMessage = 'ID de generación inválido para Flux';
                session()->flash('error', 'Error: ' . $this->errorMessage);
                $this->isGenerating = false;
                $this->fluxGenerating = false;
                $this->isTyping = false;
                $this->dispatch('errorOcurrido');
                return;
            }
            
            // Verificar el estado actual
            $result = FluxService::GetResult($generationId);
            
            // Verificamos el estado de la generación
            if (isset($result['status'])) {
                switch ($result['status']) {
                    case 'complete':
                        // La imagen está lista
                        $imageUrl = $result['data'];
                        
                        // Descargar y guardar la imagen localmente
                        $localImageUrl = $this->descargarYGuardarImagen($imageUrl, 'flux');
                        
                        // Si no pudimos guardar la imagen localmente, usar la URL original
                        $finalImageUrl = $localImageUrl ?: $imageUrl;
                        
                        // Crear objeto de resultados (formato similar a Gemini/OpenAI)
                        $resultados = [
                            'images' => [
                                [
                                    'url' => $finalImageUrl,
                                    'mimeType' => 'image/jpeg'
                                ]
                            ],
                            'prompt' => $datos['prompt'] ?? '',
                            'ratio' => $datos['ratio'] ?? '',
                            'text' => "Imagen generada con Flux Pro."
                        ];
                        
                        // Guardar en la propiedad del componente
                        $this->resultados = $resultados;
                        
                        // Agregar al historial como respuesta del sistema
                        $this->agregarRespuestaSistema($resultados);
                        
                        // Desactivar indicadores solo cuando está listo
                        $this->isGenerating = false;
                        $this->fluxGenerating = false;
                        $this->isTyping = false;
                        $this->dispatch('imagenGenerada');
                        break;
                        
                    case 'pending':
                        // La generación aún está en curso, mantener ambos indicadores activos
                        $this->isGenerating = true;
                        $this->fluxGenerating = true;
                        
                        // Programar nueva verificación después de 2 segundos
                        $this->dispatch('verificarEstadoFlux', $datos);
                        break;
                        
                    case 'failed':
                    case 'error':
                    case 'unknown':
                    default:
                        // Error en la generación
                        $this->errorMessage = 'Error generando imagen con Flux: ' . ($result['message'] ?? 'Error desconocido');
                        session()->flash('error', 'Error: ' . $this->errorMessage);
                        $this->isGenerating = false;
                        $this->fluxGenerating = false;
                        $this->isTyping = false;
                        $this->dispatch('errorOcurrido');
                        break;
                }
            } else {
                // Error en la respuesta
                $this->errorMessage = 'Respuesta inesperada de Flux';
                session()->flash('error', 'Error: ' . $this->errorMessage);
                $this->isGenerating = false;
                $this->fluxGenerating = false;
                $this->dispatch('errorOcurrido');
            }
        } catch (Exception $e) {
            $this->errorMessage = 'Error verificando estado de Flux: ' . $e->getMessage();
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->isGenerating = false;
            $this->fluxGenerating = false;
            $this->isTyping = false;
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Método para guardar imágenes en la carpeta pública
     */
    private function guardarImagenEnDisco($base64Image, $mimeType, $servicioOrigen)
    {
        try {
            // Crear carpeta si no existe
            $uploadPath = public_path('uploads/image-ia');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
    
            // Nombre de archivo único
            $filename = uniqid($servicioOrigen . '_') . '_' . time() . '.jpg';
            $filePath = $uploadPath . '/' . $filename;
    
            // Guardar imagen
            $imageData = base64_decode($base64Image);
            file_put_contents($filePath, $imageData);
    
            // Obtener el prefijo desde .env
            $prefix = trim(env('APP_PUBLIC_PREFIX', ''), '/');
            $urlPath = ($prefix ? "/$prefix" : '') . "/uploads/image-ia/$filename";
    
            // Devolver URL completa
            return url($urlPath);
    
        } catch (Exception $e) {
            Log::error('Error guardando imagen en disco: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Método para descargar una imagen desde una URL y guardarla localmente
     */
    private function descargarYGuardarImagen($imageUrl, $servicioOrigen)
    {
        try {
            // Crear carpeta si no existe
            $uploadPath = public_path('uploads/image-ia');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Nombre de archivo único
            $filename = uniqid($servicioOrigen . '_') . '_' . time() . '.jpg';
            $filePath = $uploadPath . '/' . $filename;

            // Descargar la imagen
            $imageContent = file_get_contents($imageUrl);
            if ($imageContent === false) {
                Log::error('Error descargando imagen desde URL: ' . $imageUrl);
                return null;
            }

            // Si es Flux, redimensionar la imagen para que sea más consistente
            if ($servicioOrigen === 'flux') {
                // Crear una imagen desde el contenido descargado
                $image = imagecreatefromstring($imageContent);
                if ($image !== false) {
                    // Obtener dimensiones originales
                    $width = imagesx($image);
                    $height = imagesy($image);
                    
                    // Calcular nuevas dimensiones manteniendo proporción
                    $maxDim = 1024; // Tamaño máximo consistente con otros servicios
                    if ($width > $maxDim || $height > $maxDim) {
                        if ($width > $height) {
                            $newWidth = $maxDim;
                            $newHeight = round($height * ($maxDim / $width));
                        } else {
                            $newHeight = $maxDim;
                            $newWidth = round($width * ($maxDim / $height));
                        }
                        
                        // Crear imagen redimensionada
                        $newImage = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        
                        // Guardar la imagen redimensionada
                        imagejpeg($newImage, $filePath, 90); // 90% de calidad
                        
                        // Liberar memoria
                        imagedestroy($image);
                        imagedestroy($newImage);
                    } else {
                        // Si la imagen es más pequeña que el máximo, guardarla tal cual
                        file_put_contents($filePath, $imageContent);
                    }
                } else {
                    // Si no se pudo crear la imagen, guardar el contenido original
                    file_put_contents($filePath, $imageContent);
                }
            } else {
                // Para otros servicios, guardar la imagen tal cual
                file_put_contents($filePath, $imageContent);
            }

            // Obtener el prefijo desde .env
            $prefix = trim(env('APP_PUBLIC_PREFIX', ''), '/');
            $urlPath = ($prefix ? "/$prefix" : '') . "/uploads/image-ia/$filename";

            // Devolver URL completa
            return url($urlPath);

        } catch (Exception $e) {
            Log::error('Error guardando imagen descargada: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Quita una imagen del arreglo de imágenes
     */
    public function quitarImagen($index)
    {
        if (isset($this->imageFiles[$index])) {
            // Crear un nuevo array sin la imagen eliminada
            $newFiles = [];
            foreach ($this->imageFiles as $i => $file) {
                if ($i != $index) {
                    $newFiles[] = $file;
                }
            }
            $this->imageFiles = $newFiles;
        }
    }
    
    /**
     * Limpia el historial de chat
     */
    public function limpiarHistorial()
    {
        // Limpiar el historial local
        $this->chatHistory = [];
        $this->resultados = [];
        
        // Eliminar de la sesión
        session()->forget('imagen_generador_chat_history');
        
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
        ];
        
        // Si hay imágenes múltiples, agregarlas al mensaje (ahora usando URLs)
        if (isset($resultados['images']) && is_array($resultados['images'])) {
            $nuevoMensaje['imagenes'] = [];
            foreach ($resultados['images'] as $imagen) {
                $nuevoMensaje['imagenes'][] = [
                    'url' => $imagen['url']
                ];
            }
        } 
        // Si hay una sola URL de imagen
        elseif (isset($resultados['url'])) {
            $nuevoMensaje['url'] = $resultados['url'];
        }
        
        // Agregar al historial y guardar en sesión
        $this->chatHistory[] = $nuevoMensaje;
        session()->put('imagen_generador_chat_history', $this->chatHistory);
        
        $this->prompt = '';
        $this->imageFiles = [];
    }
    
    public function render()
    {
        return view('livewire.imagen-generador');
    }
} 