<?php

namespace App\Livewire\Generador\Herramientas;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAiService;
use App\Services\GeminiService;

/**
 * Editor de Imágenes con IA
 *
 * Permite a los usuarios subir imágenes y editarlas mediante prompts
 * usando modelos como GPT y Flux-Kontext
 */
class ImageEditor extends Component
{
    use WithFileUploads;

    /** Texto del prompt para edición */
    #[Validate('required|string|min:3')]
    public string $promptText = '';

    /** Modelo de IA para edición */
    public string $model = 'gpt-image-1';

    /** Relación de aspecto */
    public string $ratio = '1:1';

    /** Cantidad de imágenes a generar */
    #[Validate('integer|min:1|max:4')]
    public int $count = 1;

    /** Estado de procesamiento */
    public bool $isProcessing = false;

    /** Imagen subida por el usuario */
    public $uploadedImage = null;

    /** URL de la imagen subida (después de guardarla) */
    public ?string $imageUrl = null;

    /** Imágenes temporales para manejar la carga */
    #[Validate('max:4', message: 'Máximo 4 imágenes permitidas')]
    public $temporaryImages = [];
    
    /** Imágenes procesadas */
    public $imageFiles = [];

    /** Resultados de edición */
    public array $results = [];
    
    /** Indicador si las imágenes vienen del historial */
    public bool $fromHistory = false;
    
    /** Metadata de la imagen del historial */
    public array $historyMetadata = [];

    /** Propiedades específicas de OpenAI */
    public string $calidadImagen = 'auto';

    public array $calidadesDisponibles = [
        'auto' => 'Automática',
        'high' => 'Alta',
        'medium' => 'Media',
        'low' => 'Baja'
    ];

    /** Catálogo de modelos disponibles para edición */
    public array $availableModels = [
        'gpt-image-1' => [
            'name' => 'ChatGPT',
            'price' => '$0.10',
            'priceUnit' => 'por edición',
            'description' => 'Editor de OpenAI para modificaciones precisas de imágenes',
            'bestFor' => 'Ediciones detalladas, modificaciones específicas, retoque profesional',
            'speed' => 'Medio',
            'quality' => 'Alta'
        ],
        'flux-kontext-max' => [
            'name' => 'Flux-Kontext-Max',
            'price' => '$0.08',
            'priceUnit' => 'por edición',
            'description' => 'Editor Flux de máxima calidad para transformaciones artísticas',
            'bestFor' => 'Transformaciones artísticas, cambios de estilo, ediciones creativas',
            'speed' => 'Medio',
            'quality' => 'Excelente'
        ],
        'flux-kontext-pro' => [
            'name' => 'Flux-Kontext-Pro',
            'price' => '$0.04',
            'priceUnit' => 'por edición',
            'description' => 'Editor Flux equilibrado para uso profesional',
            'bestFor' => 'Ediciones generales, ajustes de contenido, modificaciones rápidas',
            'speed' => 'Rápido',
            'quality' => 'Muy buena'
        ],
        'gemini-2.5-flash-image-preview' => [
        'name' => 'Gemini 2.5 Flash',
        'price' => '$0.039',
        'priceUnit' => 'por imagen',
        'description' => 'Modelo más avanzado con mejor calidad y detalles',
        'bestFor' => 'Imágenes profesionales, arte conceptual, fotografías realistas',
        'speed' => 'Rápido',
        'quality' => 'Excelente'
    ]
    ];

    public array $availableRatios = [
        '1:1' => 'Cuadrado',
        '16:9' => 'Panorámico',
        '9:16' => 'Vertical móvil',
        '4:3' => 'Horizontal',
        '3:4' => 'Vertical',
    ];

    /**
     * Determina si el modelo actual soporta múltiples imágenes editadas
     */
    public function getSupportsMultipleImagesProperty(): bool
    {
        // Modelos que soportan múltiples imágenes en edición
        // OpenAI y Gemini soportan múltiples ediciones
        // Flux modelos solo procesan 1 imagen por request
        return in_array($this->model, [
            'gpt-image-1', // OpenAI soporta múltiples imágenes editadas
            'gemini-2.5-flash-image-preview' // Gemini 2.5 Flash soporta múltiples imágenes editadas
        ]);
    }

    /**
     * Obtiene el nombre amigable del modelo
     */
    private function getModelDisplayName($modelKey): string
    {
        return $this->availableModels[$modelKey]['name'] ?? $modelKey;
    }

    /**
     * Método helper para obtener solo los nombres de los modelos
     */
    public function getModelNamesAttribute(): array
    {
        return collect($this->availableModels)->mapWithKeys(function ($info, $key) {
            return [$key => $info['name']];
        })->toArray();
    }

    /**
     * Listener para cambio de modelo desde el selector (igual que ImageGenerator)
     */
    #[On('image-generator-model-selected')]
    public function updateModel($key)
    {
        $this->model = $key;
        
        // Si el nuevo modelo no soporta múltiples imágenes, resetear a 1
        if (!$this->supportsMultipleImages && $this->count > 1) {
            $this->count = 1;
        }
        
        Log::info('Modelo de edición cambiado a: ' . $key);
    }

    /**
     * Listener para cargar imagen desde el historial
     * Simplificado: reutiliza la misma lógica que las imágenes subidas
     */
    #[On('loadImageFromHistory')]
    public function loadImageFromHistory($imageUrl, $generationId, $originalModel, $originalRatio): void
    {
        try {
            Log::info('🖼️ Cargando imagen del historial para edición', [
                'imageUrl' => $imageUrl,
                'generationId' => $generationId,
                'originalModel' => $originalModel,
                'originalRatio' => $originalRatio
            ]);

            // Limpiar imágenes previas
            $this->clearImage();
            
            // ✅ SIMPLIFICACIÓN: Usar la misma lógica que las imágenes subidas
            // En lugar de crear un sistema paralelo, simulamos que es una imagen "subida"
            $this->imageUrl = $imageUrl;
            $this->fromHistory = true;
            $this->historyMetadata = [
                'imageUrl' => $imageUrl,
                'generationId' => $generationId,
                'originalModel' => $originalModel,
                'originalRatio' => $originalRatio
            ];
            
            // Configurar el ratio basado en la imagen original
            if ($originalRatio && isset($this->availableRatios[$originalRatio])) {
                $this->ratio = $originalRatio;
            }
            
            // Dispatch el mismo evento que las imágenes subidas para compatibilidad
            $this->dispatch('imageUploaded', url: $this->imageUrl);
            
            Log::info('✅ Imagen del historial cargada exitosamente');
            
        } catch (\Exception $e) {
            Log::error('❌ Error cargando imagen del historial: ' . $e->getMessage());
            
            $this->addError('temporaryImages', 'Error al cargar la imagen del historial.');
            
            $this->dispatch('addErrorToList', 
                message: 'Error al cargar imagen del historial: ' . $e->getMessage(), 
                type: 'system', 
                tool: 'image-editor'
            );
        }
    }

    /**
     * Observador para cuando se seleccionan nuevas imágenes temporales
     */
    public function updatedTemporaryImages()
    {
        if (empty($this->temporaryImages)) {
            return;
        }
        
        // 🔄 LIMPIAR IMAGEN DEL HISTORIAL si se suben imágenes manualmente
        if ($this->fromHistory) {
            Log::info('🧹 Limpiando imagen del historial al subir imagen manual');
            $this->fromHistory = false;
            $this->historyMetadata = [];
        }
        
        // Verificar límite total de imágenes (máximo 4)
        $totalImages = count($this->imageFiles) + count($this->temporaryImages);
        if ($totalImages > 4) {
            $this->addError('temporaryImages', 'Máximo 4 imágenes permitidas en total.');
            $this->temporaryImages = [];
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
        
        // Actualizar imageUrl con la primera imagen para compatibilidad
        if (!empty($this->imageFiles)) {
            try {
                $this->imageUrl = $this->imageFiles[0]->temporaryUrl();
            } catch (\Exception $e) {
                Log::error('Error obteniendo URL temporal: ' . $e->getMessage());
            }
        }
        
        // Limpiamos las imágenes temporales
        $this->temporaryImages = [];
        
        // Dispatch evento para notificar que la imagen está lista
        $this->dispatch('imageUploaded', url: $this->imageUrl);
        
        Log::info('Imagen cargada exitosamente en ImageEditor');
    }

    /**
     * Método helper para obtener URL temporal de una imagen
     */
    public function getTemporaryUrl($image)
    {
        try {
            if ($image && method_exists($image, 'temporaryUrl')) {
                return $image->temporaryUrl();
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error al obtener URL temporal: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Método principal para iniciar edición (igual que generate() en ImageGenerator)
     */
    public function editImage(): void
    {
        $this->validate();
        
        // Validar que haya imágenes (subidas o del historial)
        if (empty($this->imageFiles) && !($this->fromHistory && $this->imageUrl)) {
            $errorMessage = 'Debes subir una imagen o seleccionar una del historial primero.';
            
            // Enviar error al componente principal (igual que en VideoGenerator)
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'validation', 
                tool: 'image-editor'
            );
            
            return;
        }

        // 1. ACTIVAR INMEDIATAMENTE el spinner
        $this->isProcessing = true;
        $this->results = [];
        
        // 2. DISPARAR EVENTO para mostrar spinner en frontend
        $this->dispatch('editingStarted');
        
        // 3. DISPARAR EVENTO para iniciar edición REAL (con delay)
        $imagesCount = $this->fromHistory ? 1 : count($this->imageFiles);
        
        $this->dispatch('startImageEditing', [
            'prompt' => $this->promptText,
            'model' => $this->model,
            'count' => $this->count,
            'ratio' => $this->ratio,
            'images_count' => $imagesCount,
            'from_history' => $this->fromHistory,
            'history_metadata' => $this->historyMetadata
        ]);
    }

    // 4. MÉTODO QUE HACE LA EDICIÓN REAL
    #[On('startImageEditing')]
    public function executeEditing($data): void
    {
        try {
            Log::info('Ejecutando edición de imagen', [
                'model' => $data['model'],
                'prompt' => substr($data['prompt'], 0, 50) . '...',
                'ratio' => $data['ratio'],
                'images_count' => $data['images_count']
            ]);

            // Procesar según el modelo seleccionado
            switch ($data['model']) {
                case 'gpt-image-1':
                    $this->editarConOpenAI($data);
                    break;
                case 'flux-kontext-max':
                case 'flux-kontext-pro':
                    $this->editarConFluxKontext($data);
                    break;
                case 'gemini-2.5-flash-image-preview':
                    $this->editarConGemini25Flash($data);
                    break;
                default:
                    throw new \Exception("Modelo de edición no soportado: {$data['model']}");
            }

        } catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            // Enviar error al componente principal
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'image-editor'
            );
            
            $this->dispatch('editingError');
            $this->isProcessing = false; // Solo en caso de error
        }
    }

    /**
     * Limpiar imagen subida
     */
    public function clearImage(): void
    {
        $this->uploadedImage = null;
        $this->imageUrl = null;
        $this->results = [];
        $this->imageFiles = [];
        $this->temporaryImages = [];
        
        // Limpiar datos del historial
        $this->fromHistory = false;
        $this->historyMetadata = [];
        
        $this->dispatch('imageCleared');
    }

    /**
     * Quita una imagen específica del array
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
            
            // Actualizar imageUrl
            if (!empty($this->imageFiles)) {
                try {
                    $this->imageUrl = $this->imageFiles[0]->temporaryUrl();
                } catch (\Exception $e) {
                    $this->imageUrl = null;
                }
            } else {
                $this->imageUrl = null;
            }
        }
    }

    /**
     * Convierte la primera imagen a base64 para envío a APIs (OpenAI)
     * Simplificado: usa imageUrl para ambos casos
     */
    public function getImageAsBase64()
    {
        try {
            if ($this->fromHistory && $this->imageUrl) {
                // Para imágenes del historial, descargar desde S3 y convertir a base64
                Log::info('📥 Descargando imagen del historial para convertir a base64', [
                    'imageUrl' => $this->imageUrl
                ]);
                
                $imageContent = file_get_contents($this->imageUrl);
                if ($imageContent === false) {
                    throw new \Exception('No se pudo descargar la imagen del historial');
                }
                
                Log::info('✅ Imagen del historial convertida a base64', [
                    'imageSize' => strlen($imageContent)
                ]);
                
                return base64_encode($imageContent);
                
            } elseif (!empty($this->imageFiles)) {
                // Para imágenes subidas, leer desde el archivo temporal
                $image = $this->imageFiles[0]; // Tomar la primera imagen
                $imageContent = file_get_contents($image->getRealPath());
                return base64_encode($imageContent);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error convirtiendo imagen a base64: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sube múltiples imágenes a S3 y retorna las URLs para APIs (Flux)
     * Simplificado: usa imageUrl para imágenes del historial
     */
    public function uploadImagesToS3ForFlux()
    {
        try {
            $mainImageUrl = null;
            $additionalImageUrls = [];
            
            if ($this->fromHistory && $this->imageUrl) {
                // ✅ OPTIMIZACIÓN: Las imágenes del historial ya están en S3, reutilizarlas directamente
                Log::info('🚀 Reutilizando imagen del historial (ya en S3)', [
                    'imageUrl' => $this->imageUrl
                ]);
                
                $mainImageUrl = $this->imageUrl; // Imagen del historial va en input_image
                
                Log::info('✅ URL del historial preparada para Flux', [
                    'mainImage' => $mainImageUrl
                ]);
                
            } elseif (!empty($this->imageFiles)) {
                // Para imágenes subidas, subirlas a S3 como antes
                foreach ($this->imageFiles as $index => $image) {
                    $imageContent = file_get_contents($image->getRealPath());
                    
                    // Generar nombre de archivo único para imagen temporal
                    $fileName = 'genesis/temp-images/' . now()->format('Ymd_His') . '_' . uniqid('temp_' . $index . '_') . '.jpg';
                    
                    Log::info('☁️ Subiendo imagen temporal a S3 para Flux', [
                        'index' => $index,
                        'fileName' => $fileName,
                        'imageSize' => strlen($imageContent)
                    ]);
                    
                    // Subir a S3
                    Storage::disk('s3')->put($fileName, $imageContent);
                    
                    // Obtener la URL de S3
                    $url = Storage::disk('s3')->url($fileName);
                    
                    if ($index === 0) {
                        $mainImageUrl = $url; // Primera imagen va en input_image
                    } else {
                        $additionalImageUrls[] = $url; // Imágenes adicionales van en input_image_2, input_image_3, etc.
                    }
                }
                
                Log::info('✅ Imágenes temporales subidas exitosamente a S3', [
                    'mainImage' => $mainImageUrl,
                    'additionalCount' => count($additionalImageUrls)
                ]);
            }
            
            return [
                'main' => $mainImageUrl,
                'additional' => $additionalImageUrls
            ];

        } catch (\Exception $e) {
            Log::error('💥 Error preparando imágenes para Flux: ' . $e->getMessage());
            return ['main' => null, 'additional' => []];
        }
    }

    /**
     * Obtiene información de la imagen (tipo MIME, etc.)
     */
    public function getImageInfo()
    {
        if (empty($this->imageFiles)) {
            return null;
        }

        try {
            $image = $this->imageFiles[0];
            return [
                'mime_type' => $image->getMimeType(),
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'extension' => $image->getClientOriginalExtension()
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo información de imagen: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Edita imágenes usando OpenAI
     */
    private function editarConOpenAI($data): void
    {
        try {
            // Establecer tiempo de ejecución máximo
            set_time_limit(180); // 3 minutos
            
            // Mapear el aspect ratio a formato OpenAI
            $aspecto = $this->mapearAspectRatioAOpenAI($data['ratio']);
            $quality = $this->calidadImagen;
            
            Log::info('Iniciando edición con OpenAI', [
                'prompt' => $data['prompt'],
                'size' => $aspecto,
                'quality' => $quality,
                'count' => $data['count'],
                'images_count' => $data['images_count']
            ]);
            
            // Preparar rutas de imágenes para OpenAI
            $imagePaths = [];
            
            if ($this->fromHistory && $this->imageUrl) {
                // Para imágenes del historial, descargar temporalmente para OpenAI
                $tempFile = tempnam(sys_get_temp_dir(), 'history_image_');
                $imageContent = file_get_contents($this->imageUrl);
                if ($imageContent === false) {
                    throw new \Exception('No se pudo descargar imagen del historial para OpenAI');
                }
                file_put_contents($tempFile, $imageContent);
                $imagePaths[] = $tempFile;
                
            } elseif (!empty($this->imageFiles)) {
                // Para imágenes subidas, usar las rutas temporales
                foreach ($this->imageFiles as $image) {
                    $imagePaths[] = $image->getRealPath();
                }
            }
            
            // Llamar al servicio de edición de OpenAI
            $response = \App\Services\OpenAiService::editImage(
                $data['prompt'], 
                $imagePaths, 
                'gpt-image-1', 
                $aspecto, 
                'auto', 
                $data['count'] // Usar el count del parámetro
            );
            
            Log::info('Respuesta de OpenAI editImage:', $response);
            
            if (isset($response['error'])) {
                $errorMessage = 'Error editando imagen con OpenAI: ' . $response['error'];
                $this->addError('promptText', $errorMessage);
                
                $this->dispatch('addErrorToList', 
                    message: $errorMessage, 
                    type: 'generation', 
                    tool: 'image-editor'
                );
                
                $this->dispatch('editingError');
                return;
            }
            
            // Procesar respuesta y guardar las imágenes resultantes
            if (isset($response['data']) && is_array($response['data'])) {
                $generatedImages = [];
                $generationId = uniqid('edit_openai_');
                
                foreach ($response['data'] as $resultImage) {
                    if (isset($resultImage['b64_json'])) {
                        $imageBase64 = $resultImage['b64_json'];
                        $mimeType = 'image/jpeg';
                        
                        // Guardar la imagen editada en S3
                        $imageUrl = $this->subirImagenEditadaAS3($imageBase64, $mimeType, 'openai');
                        
                        if ($imageUrl) {
                            $generatedImages[] = [
                                'url' => $imageUrl,
                                'mimeType' => $mimeType
                            ];
                        }
                    } else if (isset($resultImage['url'])) {
                        // Si es una URL directa, descargarla y subirla a S3
                        $imageUrl = $this->descargarYSubirAS3($resultImage['url'], 'openai');
                        
                        if ($imageUrl) {
                            $generatedImages[] = [
                                'url' => $imageUrl,
                                'mimeType' => 'image/jpeg'
                            ];
                        }
                    }
                }
                
                if (!empty($generatedImages)) {
                    // Agregar al historial del generador principal
                    $this->dispatch('addToHistory', 
                        type: 'image/edit', 
                        images: $generatedImages, 
                        generationId: $generationId,
                        prompt: $data['prompt'],
                        model: $this->getModelDisplayName($data['model']),
                        ratio: $data['ratio'],
                        count: $data['count']
                    );
                    
                    $this->results = $generatedImages;
                    
                    Log::info('Imágenes editadas con OpenAI: ' . count($generatedImages));
                    
                    // Finalizar procesamiento exitoso
                    $this->dispatch('editingCompleted');
                    
                    // Limpiar el prompt y la vista previa después de edición exitosa
                    $this->promptText = '';
                    $this->clearImage();
                } else {
                    $this->addError('promptText', 'No se pudieron procesar las imágenes editadas.');
                    $this->dispatch('editingError');
                }
            } else {
                $this->addError('promptText', 'Respuesta inválida de OpenAI.');
                $this->dispatch('editingError');
            }

        } catch (\Exception $e) {
            $errorMessage = 'Error editando con OpenAI: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'image-editor'
            );
            
            $this->dispatch('editingError');
        } finally {
            // Limpiar archivos temporales si se crearon para imágenes del historial
            if ($this->fromHistory && $this->imageUrl && isset($imagePaths)) {
                foreach ($imagePaths as $tempFile) {
                    if (file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                }
            }
            
            $this->isProcessing = false;
        }
    }
    
    /**
     * Edita imágenes usando Gemini 2.5 Flash
     */
    private function editarConGemini25Flash($data): void
    {
        try {
            Log::info('🎨 Iniciando edición con Gemini 2.5 Flash', [
                'model' => $data['model'],
                'prompt' => substr($data['prompt'], 0, 50) . '...',
                'ratio' => $data['ratio'],
                'images_count' => $data['images_count']
            ]);
            
            // Establecer tiempo de ejecución máximo
            set_time_limit(180); // 3 minutos
            
            // Preparar imágenes en base64 para Gemini
            $imagesBase64 = [];
            
            if ($this->fromHistory && $this->imageUrl) {
                // Para imágenes del historial, descargar y convertir a base64
                $imageContent = file_get_contents($this->imageUrl);
                if ($imageContent === false) {
                    throw new \Exception('No se pudo descargar imagen del historial para Gemini');
                }
                
                // Determinar MIME type basado en la extensión de la URL
                $mimeType = 'image/jpeg'; // Por defecto
                if (strpos($this->imageUrl, '.png') !== false) {
                    $mimeType = 'image/png';
                }
                
                $imagesBase64[] = [
                    'mime_type' => $mimeType,
                    'data' => base64_encode($imageContent)
                ];
                
            } elseif (!empty($this->imageFiles)) {
                // Para imágenes subidas, leer desde archivos temporales
                foreach ($this->imageFiles as $image) {
                    $imageContent = file_get_contents($image->getRealPath());
                    $mimeType = $image->getMimeType();
                    
                    $imagesBase64[] = [
                        'mime_type' => $mimeType,
                        'data' => base64_encode($imageContent)
                    ];
                }
            }
            
            Log::info('🖼️ Imágenes convertidas a base64 para Gemini', [
                'imagesCount' => count($imagesBase64),
                'firstImageMimeType' => $imagesBase64[0]['mime_type'] ?? 'unknown'
            ]);
            
            // Llamar al servicio Gemini para edición
            $response = GeminiService::generateContentImage(
                prompt: $data['prompt'],
                files: $imagesBase64,
                model: $data['model']
            );
            
            Log::info('📡 Respuesta de GeminiService::generateContentImage', [
                'model' => $data['model'],
                'success' => $response['success'] ?? false,
                'hasError' => isset($response['error']),
                'dataCount' => count($response['data'] ?? []),
                'responseKeys' => array_keys($response)
            ]);
            
            if (!($response['success'] ?? false)) {
                $errorMessage = $response['error']['message'] ?? 'No se pudo editar la imagen con Gemini.';
                Log::error('❌ Error en respuesta de Gemini', [
                    'model' => $data['model'],
                    'error' => $response['error'] ?? 'No error details'
                ]);
                
                $this->addError('promptText', $errorMessage);
                
                $this->dispatch('addErrorToList', 
                    message: $errorMessage, 
                    type: 'generation', 
                    tool: 'image-editor'
                );
                
                $this->dispatch('editingError');
                return;
            }
            
            // Procesar respuesta y guardar las imágenes resultantes
            if (isset($response['data']) && is_array($response['data'])) {
                $generatedImages = [];
                $generationId = uniqid('edit_gemini25_');
                
                Log::info('🔄 Procesando imágenes editadas por Gemini 2.5 Flash', [
                    'generationId' => $generationId,
                    'dataCount' => count($response['data'])
                ]);
                
                foreach ($response['data'] as $index => $resultImage) {
                    if (isset($resultImage['base64'])) {
                        $imageBase64 = $resultImage['base64'];
                        $mimeType = $resultImage['mimeType'] ?? 'image/png';
                        
                        Log::info('🖼️ Procesando imagen editada Gemini 2.5 Flash', [
                            'index' => $index,
                            'base64Length' => strlen($imageBase64),
                            'mimeType' => $mimeType,
                            'generationId' => $generationId
                        ]);
                        
                        // Guardar la imagen editada en S3
                        $imageUrl = $this->subirImagenEditadaAS3($imageBase64, $mimeType, 'gemini25');
                        
                        if ($imageUrl) {
                            $generatedImages[] = [
                                'url' => $imageUrl,
                                'mimeType' => $mimeType
                            ];
                            
                            Log::info('✅ Imagen editada Gemini 2.5 Flash procesada exitosamente', [
                                'index' => $index,
                                'url' => $imageUrl
                            ]);
                        }
                    } else {
                        Log::warning('⚠️ Imagen Gemini sin base64', [
                            'index' => $index,
                            'resultImageKeys' => array_keys($resultImage)
                        ]);
                    }
                }
                
                if (!empty($generatedImages)) {
                    Log::info('📊 Resumen de edición Gemini 2.5 Flash', [
                        'generationId' => $generationId,
                        'totalImages' => count($generatedImages),
                        'successfulImages' => count($generatedImages)
                    ]);
                    
                    // Agregar al historial del generador principal
                    $this->dispatch('addToHistory', 
                        type: 'image/edit', 
                        images: $generatedImages, 
                        generationId: $generationId,
                        prompt: $data['prompt'],
                        model: $this->getModelDisplayName($data['model']),
                        ratio: $data['ratio'],
                        count: $data['count']
                    );
                    
                    $this->results = $generatedImages;
                    
                    Log::info('🎉 Edición Gemini 2.5 Flash completada exitosamente', [
                        'generationId' => $generationId,
                        'imagesCount' => count($generatedImages)
                    ]);
                    
                    // Finalizar procesamiento exitoso
                    $this->dispatch('editingCompleted');
                    
                    // Limpiar el prompt y la vista previa después de edición exitosa
                    $this->promptText = '';
                    $this->clearImage();
                    
                } else {
                    Log::warning('⚠️ No se procesaron imágenes editadas con Gemini 2.5 Flash', [
                        'generationId' => $generationId
                    ]);
                    
                    $this->addError('promptText', 'No se pudieron procesar las imágenes editadas con Gemini.');
                    $this->dispatch('editingError');
                }
            } else {
                Log::error('❌ Respuesta inválida de Gemini 2.5 Flash', [
                    'response' => $response
                ]);
                
                $this->addError('promptText', 'Respuesta inválida de Gemini 2.5 Flash.');
                $this->dispatch('editingError');
            }

        } catch (\Exception $e) {
            Log::error('💥 Error en editarConGemini25Flash', [
                'model' => $data['model'],
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Error editando con Gemini 2.5 Flash: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'image-editor'
            );
            
            $this->dispatch('editingError');
        } finally {
            $this->isProcessing = false;
        }
    }
    
    /**
     * Edita imágenes usando Flux-Kontext
     */
    private function editarConFluxKontext($data): void
    {
        try {
            Log::info('🚀 Iniciando edición con Flux-Kontext', [
                'model' => $data['model'],
                'prompt' => substr($data['prompt'], 0, 50) . '...',
                'ratio' => $data['ratio'],
                'images_count' => $data['images_count']
            ]);
            
            // Subir múltiples imágenes a S3 para obtener las URLs (Flux necesita URLs, no base64)
            $imageUrls = $this->uploadImagesToS3ForFlux();
            if (!$imageUrls['main']) {
                throw new \Exception('No se pudo subir la imagen principal a S3 para Flux');
            }
            
            // Llamar al servicio FluxService para edición con múltiples imágenes
            $response = \App\Services\FluxService::GenerateImageKontext(
                $data['model'],                    // Modelo (flux-kontext-max o flux-kontext-pro)
                $data['prompt'],                   // Prompt de edición
                $data['ratio'],                    // Aspect ratio
                $imageUrls['main'],                // URL de la imagen principal en S3 (input_image)
                false,                             // prompt_upsampling
                null,                              // seed (aleatorio)
                2,                                 // safety_tolerance
                'jpeg',                            // output_format
                null,                              // webhook_url
                null,                              // webhook_secret
                $imageUrls['additional']           // URLs de imágenes adicionales (input_image_2, input_image_3, input_image_4)
            );
            
            Log::info('📝 Respuesta de Flux-Kontext para edición', [
                'response' => $response
            ]);
            
            // Verificar si hubo error en la respuesta inicial
            if (isset($response['error'])) {
                $errorMessage = 'Error con Flux-Kontext: ' . $response['error'];
                $this->addError('promptText', $errorMessage);
                
                $this->dispatch('addErrorToList', 
                    message: $errorMessage, 
                    type: 'generation', 
                    tool: 'image-editor'
                );
                
                $this->dispatch('editingError');
                return;
            }

            // Obtener el ID de generación
            if (!isset($response['data'])) {
                throw new \Exception('Respuesta inesperada de Flux-Kontext');
            }

            $generationId = $response['data'];
            
            // ✅ EMITIR AL FRONTEND para iniciar polling (reutilizamos el evento del generador)
            $this->dispatch('fluxTaskStarted', 
                generationId: $generationId,
                prompt: $data['prompt'],
                model: $data['model'],
                ratio: $data['ratio'],
                count: $data['count'],
                originalImageUrls: $imageUrls  // Para referencia (ahora incluye main + additional)
            );
            
            Log::info('🚀 Evento fluxTaskStarted disparado para edición', [
                'generationId' => $generationId,
                'model' => $data['model'],
                'eventName' => 'fluxTaskStarted'
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error editando con Flux-Kontext: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'image-editor'
            );
            
            $this->dispatch('editingError');
            $this->isProcessing = false; // Solo en caso de error inicial
        }
        // NO hay finally aquí - el isProcessing se mantiene true hasta que termine el polling
    }
    
    /**
     * Verifica el estado de edición de Flux-Kontext (igual que en ImageGenerator)
     */
    #[On('verificarEstadoFluxKontext')]
    public function verificarEstadoFluxEdicion($generationId, $prompt, $model, $ratio, $count, $originalImageUrls = null): void
    {
        try {
            Log::info('🔍 Verificando estado de edición Flux desde frontend', [
                'generationId' => $generationId,
                'model' => $model,
                'prompt' => substr($prompt, 0, 50) . '...',
                'ratio' => $ratio,
                'count' => $count,
                'hasOriginalImages' => !empty($originalImageUrls)
            ]);
            
            // Usar el endpoint Ultra para Flux-Kontext
            $result = \App\Services\FluxService::GetResultUltra($generationId);
            
            Log::info('📡 Respuesta de FluxService::GetResultUltra', [
                'generationId' => $generationId,
                'status' => $result['status'] ?? 'unknown',
                'hasData' => isset($result['data'])
            ]);
            
            // Crear array de datos para compatibilidad
            $datos = [
                'generationId' => $generationId,
                'prompt' => $prompt,
                'model' => $model,
                'ratio' => $ratio,
                'count' => $count,
                'originalImageUrls' => $originalImageUrls
            ];
            
            switch ($result['status']) {
                case 'complete':
                case 'Ready':
                    // ✅ IMAGEN LISTA
                    Log::info('✅ Flux edición completada', ['id' => $generationId]);
                    $this->procesarImagenEditadaFlux($result['data'], $datos);
                    break;
                    
                case 'pending':
                    // ⏳ AÚN PENDIENTE - EMITIR AL FRONTEND PARA NUEVO DELAY
                    Log::info('⏳ Flux edición aún pendiente', ['id' => $generationId]);
                    $this->dispatch('fluxStillPending', 
                        generationId: $generationId,
                        prompt: $prompt,
                        model: $model,
                        ratio: $ratio,
                        count: $count,
                        originalImageUrls: $originalImageUrls
                    );
                    break;
                    
                case 'failed':
                case 'error':
                    // ❌ ERROR
                    Log::error('❌ Flux edición falló', ['id' => $generationId]);
                    $this->isProcessing = false;
                    $this->dispatch('editingError');
                    break;
            }
            
        } catch (\Exception $e) {
            Log::error('💥 Error verificando Flux edición', ['error' => $e->getMessage()]);
            $this->isProcessing = false;
            $this->dispatch('editingError');
        }
    }

    /**
     * Procesa una imagen editada completada por Flux (similar al generador)
     */
    private function procesarImagenEditadaFlux(string $imageUrl, array $datos): void
    {
        try {
            // Descargar la imagen desde la URL de Flux y subirla a S3
            $finalUrl = $this->descargarYSubirAS3($imageUrl, 'flux');

            if (!$finalUrl) {
                throw new \Exception('No se pudo procesar la imagen editada de Flux');
            }

            // Crear datos de la imagen
            $imageData = [
                'url' => $finalUrl,
                'mimeType' => 'image/jpeg'
            ];
            
            $this->results[] = $imageData;

            // Disparar evento de finalización
            $generationId = uniqid('edit_flux_');
            $this->dispatch('addToHistory', 
                type: 'image/edit', 
                images: [$imageData], 
                generationId: $generationId,
                prompt: $datos['prompt'],
                model: $this->getModelDisplayName($datos['model']),
                ratio: $datos['ratio'],
                count: 1 
            );
            
            $this->dispatch('editingCompleted');
            
            // Limpiar la vista previa después de edición exitosa con Flux
            $this->clearImage();
            
        } catch (\Exception $e) {
            $errorMessage = 'Error procesando imagen editada con Flux: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'image-editor'
            );
            
            $this->dispatch('editingError');
        } finally {
            $this->isProcessing = false;
        }
    }
    
    /**
     * Maneja errores durante la edición
     */
    private function handleEditingError(\Exception $e): void
    {
        $errorMessage = 'Error editando imagen: ' . $e->getMessage();
        $this->addError('promptText', $errorMessage);
        
        // Enviar error al componente principal
        $this->dispatch('addErrorToList', 
            message: $errorMessage, 
            type: 'editing', 
            tool: 'image-editor'
        );
        
        $this->dispatch('editingError');
        $this->isProcessing = false;
        
        Log::error('Error en edición de imagen: ' . $e->getMessage());
    }
    
    /**
     * Método auxiliar para convertir nuestro ratio a los tamaños de OpenAI
     */
    private function mapearAspectRatioAOpenAI($ratio)
    {
        switch ($ratio) {
            case '1:1':
                return '1024x1024'; // Cuadrado
            case '16:9':
            case '4:3':
                return '1536x1024'; // Horizontal/Landscape
            case '9:16': 
            case '3:4':
                return '1024x1536'; // Vertical/Portrait
            default:
                return '1024x1024'; // Por defecto cuadrado
        }
    }
    
    /**
     * Sube una imagen editada a S3 (igual que en ImageGenerator)
     */
    private function subirImagenEditadaAS3($base64Image, $mimeType, $servicioOrigen)
    {
        try {
            // Decodificar la imagen base64
            $imageBinary = base64_decode($base64Image);
            
            // Generar nombre de archivo único usando la misma estructura que ImageGenerator
            $fileName = 'genesis/edited-images/' . now()->format('Ymd_His') . '_' . uniqid($servicioOrigen . '_edited_') . '.jpg';
            
            Log::info('Subiendo imagen editada a S3', [
                'fileName' => $fileName,
                'servicioOrigen' => $servicioOrigen,
                'imageSize' => strlen($imageBinary)
            ]);
            
            // Subir a S3
            Storage::disk('s3')->put($fileName, $imageBinary);
            
            // Obtener la URL de S3 (igual que en ImageGenerator)
            $url = Storage::disk('s3')->url($fileName);
            
            Log::info('Imagen editada subida exitosamente a S3', [
                'url' => $url
            ]);
            
            return $url;

        } catch (\Exception $e) {
            Log::error('Error subiendo imagen editada a S3: ' . $e->getMessage(), [
                'servicioOrigen' => $servicioOrigen,
                'error' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Descarga una imagen desde URL y la sube a S3 (para modelos que devuelven URLs)
     */
    private function descargarYSubirAS3($imageUrl, $servicioOrigen)
    {
        try {
            // Descargar la imagen desde la URL
            $imageContent = file_get_contents($imageUrl);
            if ($imageContent === false) {
                throw new \Exception('No se pudo descargar la imagen desde la URL');
            }

            // Generar nombre de archivo único usando la misma estructura que ImageGenerator
            $fileName = 'genesis/edited-images/' . now()->format('Ymd_His') . '_' . uniqid($servicioOrigen . '_edited_') . '.jpg';
            
            Log::info('Descargando y subiendo imagen a S3', [
                'originalUrl' => $imageUrl,
                'fileName' => $fileName,
                'servicioOrigen' => $servicioOrigen,
                'imageSize' => strlen($imageContent)
            ]);

            // Subir a S3
            Storage::disk('s3')->put($fileName, $imageContent);
            
            // Obtener la URL de S3 (igual que en ImageGenerator)
            $finalUrl = Storage::disk('s3')->url($fileName);
            
            Log::info('Imagen descargada y subida exitosamente a S3', [
                'finalUrl' => $finalUrl
            ]);
            
            return $finalUrl;

        } catch (\Exception $e) {
            Log::error('Error descargando y subiendo imagen a S3: ' . $e->getMessage(), [
                'originalUrl' => $imageUrl,
                'servicioOrigen' => $servicioOrigen,
                'error' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function mount()
    {
        // Inicialización básica
        Log::info('ImageEditor component mounted');
    }

    public function render()
    {
        return view('livewire.generador.herramientas.image-editor');
    }
}
