<?php

namespace App\Livewire\Generador\Herramientas;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;

/**
 * Generador de Videos
 *
 * Enfoque minimal: solo modelos de video, ratio y cantidad.
 * Guarda los resultados en storage público y emite eventos al historial global.
 */
class VideoGenerator extends Component
{
    use WithFileUploads;

    /** Texto del prompt */
    public string $promptText = '';

    /** Modelo de video seleccionado */
    public string $model = 'veo2';

    /** Relación de aspecto - se inicializará en mount() */
    public string $ratio = '16:9';
    public bool $isGenerating = false;
    
    /** Indica si el ratio está bloqueado por una imagen subida */
    public bool $ratioLocked = false;

    /** Cantidad de videos a generar */
    #[Validate('integer|min:1|max:1')]
    public int $count = 1;
    
    /** Duración del video en segundos (solo para Sora) */
    public int $durationSeconds = 4;

    /** Resultados generados recientemente */
    public array $results = [];

    /** Imágenes de inicio y fin para modelos que las requieren */
    public $imageFilesStart = [];
    public $imageFilesEnd = [];
    public $temporaryImagesStart = [];
    public $temporaryImagesEnd = [];

    /** Indicador si las imágenes vienen del historial */
    public bool $fromHistory = false;
    
    /** Metadata de la imagen del historial */
    public array $historyMetadata = [];
    
    /** URL de la imagen del historial */
    public ?string $imageUrl = null;

    /** Catálogo de modelos disponibles con información detallada */
    public array $availableModels = [
        'veo2' => [
            'name' => 'Veo2',
            'price' => '$0.12',
            'priceUnit' => 'por segundo',
            'description' => 'Modelo más avanzado con mejor calidad y detalles',
            'bestFor' => 'Videos profesionales, contenido creativo, narrativas visuales',
            'speed' => 'Rápido',
            'quality' => 'Excelente'
        ],
        'gen4_turbo' => [
            'name' => 'Gen4-Turbo', 
            'price' => '$0.08',
            'priceUnit' => 'por segundo',
            'description' => 'Modelo equilibrado entre calidad y costo',
            'bestFor' => 'Uso general, prototipos, contenido web',
            'speed' => 'Muy rápido',
            'quality' => 'Buena'
        ],
        'gen3a_turbo' => [
            'name' => 'Gen3-AlphaTurbo',
            'price' => '$0.06',
            'priceUnit' => 'por segundo',
            'description' => 'Modelo AlphaTurbo de alta velocidad',
            'bestFor' => 'Contenido rápido, prototipos, pruebas',
            'speed' => 'Ultra rápido',
            'quality' => 'Buena'
        ],
        'ray2' => [
            'name' => 'Ray2',
            'price' => '$0.10',
            'priceUnit' => 'por segundo',
            'description' => 'Modelo Ray2 de alta calidad',
            'bestFor' => 'Videos artísticos, contenido creativo',
            'speed' => 'Medio',
            'quality' => 'Muy buena'
        ],
        'ray2-flash' => [
            'name' => 'Ray2-Flash',
            'price' => '$0.08',
            'priceUnit' => 'por segundo',
            'description' => 'Modelo Ray2 optimizado para velocidad',
            'bestFor' => 'Contenido rápido, prototipos',
            'speed' => 'Rápido',
            'quality' => 'Buena'
        ],
        'sora-2' => [
            'name' => 'Sora 2',
            'price' => '$0.15',
            'priceUnit' => 'por segundo',
            'description' => 'Modelo Sora de OpenAI de última generación',
            'bestFor' => 'Videos de alta calidad, realismo extremo',
            'speed' => 'Medio',
            'quality' => 'Excelente'
        ],
        'sora-2-pro' => [
            'name' => 'Sora 2 Pro',
            'price' => '$0.20',
            'priceUnit' => 'por segundo',
            'description' => 'Versión Pro de Sora con máxima calidad',
            'bestFor' => 'Producciones profesionales, máxima fidelidad',
            'speed' => 'Medio-Lento',
            'quality' => 'Excepcional'
        ]
    ];

    /** Relaciones de aspecto disponibles para video */
    public array $availableRatios = [
        '16:9' => 'Panorámico',
        '9:16' => 'Vertical móvil',
        '1:1' => 'Cuadrado',
        '4:3' => 'Horizontal',
        '3:4' => 'Vertical',
        '21:9' => 'Ultra panorámico',
    ];

    /**
     * Obtiene los ratios disponibles según el modelo seleccionado
     */
    public function getAvailableRatiosForModel(): array
    {
        switch ($this->model) {
            case 'veo2':
                // ✅ Veo2 (Google): 3 ratios disponibles
                return [
                    '16:9' => 'Panorámico',
                    '9:16' => 'Vertical móvil',
                    
                ];
                
            case 'gen4_turbo':
                // ✅ Gen4-Turbo (Runway): 6 ratios disponibles
                return [
                    '16:9' => 'Panorámico',
                    '9:16' => 'Vertical móvil',
                    '1:1' => 'Cuadrado',
                    '4:3' => 'Horizontal',
                    '3:4' => 'Vertical',
                    '21:9' => 'Ultra panorámico'
                ];
                
            case 'gen3a_turbo':
                // ✅ Gen3-AlphaTurbo (Runway): Solo 2 ratios disponibles
                return [
                    '16:9' => 'Panorámico',
                    '9:16' => 'Vertical móvil'
                ];
                
            case 'ray2':
            case 'ray2-flash':
                // ✅ Ray2 y Ray2-Flash (Luma): Todos los ratios disponibles
                return $this->availableRatios;
                
            case 'sora-2':
            case 'sora-2-pro':
                
                return [
                    '16:9' => 'Panorámico',
                    '9:16' => 'Vertical móvil'
                ];
                
            default:
                // Fallback: todos los ratios para modelos no reconocidos
                return $this->availableRatios;
        }
    }

    /**
     * Obtiene el nombre amigable del modelo
     */
    private function getModelDisplayName($modelKey): string
    {
        return $this->availableModels[$modelKey]['name'] ?? $modelKey;
    }

    /**
     * Método helper para obtener solo los nombres de los modelos (para compatibilidad)
     */
    public function getModelNamesAttribute(): array
    {
        return collect($this->availableModels)->mapWithKeys(function ($info, $key) {
            return [$key => $info['name']];
        })->toArray();
    }

    #[On('video-generator-model-selected')]
    public function updateModel($key)
    {
        $this->model = $key;
        Log::info('🎯 Modelo de video actualizado', [
            'newModel' => $key,
            'currentModel' => $this->model
        ]);
        
        // ✅ Validar que el ratio actual sea compatible con el nuevo modelo
        $this->validarRatioCompatible();
        
        // ✅ VALIDAR IMAGEN si hay una cargada (para cualquier modelo)
        if ($this->tieneImagenCargada()) {
            Log::info('🔍 Modelo cambiado con imagen cargada - Validando imagen', [
                'model' => $key,
                'hasImageFilesStart' => !empty($this->imageFilesStart),
                'fromHistory' => $this->fromHistory,
                'hasImageUrl' => !empty($this->imageUrl)
            ]);
            
            $this->validarImagenParaModelo();
        }
    }

    /**
     * Listener para cargar imagen desde el historial para generar video
     * Similar al ImageEditor pero para video
     */
    #[On('loadImageForVideoFromHistory')]
    public function loadImageForVideoFromHistory($imageUrl, $generationId, $originalModel, $originalRatio): void
    {
        try {
            Log::info('🎬 Cargando imagen del historial para generar video', [
                'imageUrl' => $imageUrl,
                'generationId' => $generationId,
                'originalModel' => $originalModel,
                'originalRatio' => $originalRatio,
                'currentModel' => $this->model,
                'currentPrompt' => substr($this->promptText, 0, 100) . '...'
            ]);

            // Limpiar imágenes previas
            $this->limpiarTodasLasImagenes();
            
            Log::info('🧹 Imágenes previas limpiadas');
            
            // ✅ SIMPLIFICACIÓN: Usar la misma lógica que las imágenes subidas
            // Simulamos que es una imagen "subida" para imagen de inicio
            $this->imageUrl = $imageUrl;
            $this->fromHistory = true;
            $this->historyMetadata = [
                'imageUrl' => $imageUrl,
                'generationId' => $generationId,
                'originalModel' => $originalModel,
                'originalRatio' => $originalRatio
            ];
            
            // ✅ CORRECCIÓN: Usar el ratio seleccionado en la herramienta, NO el de la imagen original
            $ratiosDisponibles = $this->getAvailableRatiosForModel();
            
            // Verificar que el ratio actual sea compatible con el modelo
            if (!array_key_exists($this->ratio, $ratiosDisponibles)) {
                // Si el ratio actual no es compatible, cambiar al primer ratio disponible
                $nuevoRatio = array_key_first($ratiosDisponibles);
                $this->ratio = $nuevoRatio;
                
                Log::info('⚠️ Ratio actual no compatible con modelo, cambiando automáticamente', [
                    'ratioAnterior' => $this->ratio,
                    'nuevoRatio' => $nuevoRatio,
                    'model' => $this->model,
                    'ratiosDisponibles' => array_keys($ratiosDisponibles)
                ]);
            } else {
                Log::info('✅ Ratio actual compatible con modelo', [
                    'ratio' => $this->ratio,
                    'model' => $this->model,
                    'originalRatio' => $originalRatio // Solo para referencia
                ]);
            }
            
            // Dispatch el mismo evento que las imágenes subidas para compatibilidad
            $this->dispatch('imageLoadedForVideo', url: $this->imageUrl);
            
            // 🎯 Validar imagen del historial según el modelo seleccionado
            $this->validarImagenParaModelo();
            
            Log::info('✅ Imagen del historial cargada exitosamente para video', [
                'finalImageUrl' => $this->imageUrl,
                'fromHistory' => $this->fromHistory,
                'finalRatio' => $this->ratio,
                'currentModel' => $this->model,
                'hasPrompt' => !empty(trim($this->promptText)),
                'ratioLocked' => $this->ratioLocked
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error cargando imagen del historial para video: ' . $e->getMessage(), [
                'imageUrl' => $imageUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('addErrorToList', 
                message: 'Error al cargar la imagen del historial para video: ' . $e->getMessage(), 
                type: 'system', 
                tool: 'video-generator'
            );
        }
    }

    /**
     * Valida que el ratio seleccionado sea compatible con el modelo actual
     */
    private function validarRatioCompatible(): void
    {
        $ratiosDisponibles = $this->getAvailableRatiosForModel();
        
        if (!array_key_exists($this->ratio, $ratiosDisponibles)) {
            // ❌ Ratio no compatible, cambiar al primer ratio disponible
            $nuevoRatio = array_key_first($ratiosDisponibles);
            $this->ratio = $nuevoRatio;
            
            Log::info("⚠️ Ratio cambiado automáticamente", [
                'ratioAnterior' => $this->ratio,
                'nuevoRatio' => $nuevoRatio,
                'modelo' => $this->model,
                'razon' => 'Ratio no compatible con el modelo seleccionado'
            ]);
            
            // Notificar al usuario
            $this->addError('ratio', "El ratio seleccionado no es compatible con {$this->getModelDisplayName($this->model)}. Se cambió automáticamente a {$ratiosDisponibles[$nuevoRatio]}.");
        }
    }
    
    /**
     * Verifica si hay una imagen cargada (subida o del historial)
     */
    private function tieneImagenCargada(): bool
    {
        return !empty($this->imageFilesStart) || ($this->fromHistory && !empty($this->imageUrl));
    }
    
    /**
     * Valida la imagen cargada según el modelo seleccionado
     * Se ejecuta cuando se cambia de modelo y ya hay una imagen
     * 
     * 🎯 ESTRUCTURA MODULAR PARA FUTURAS VALIDACIONES:
     * - Cada modelo puede tener sus propias reglas de validación
     * - Fácil agregar nuevos modelos con validaciones específicas
     * - Separación clara de responsabilidades
     */
    private function validarImagenParaModelo(): void
    {
        switch ($this->model) {
            case 'sora-2':
            case 'sora-2-pro':
                // ✅ Sora: Validación estricta de tamaños exactos (1280x720, 720x1280)
                $this->validarImagenParaSora();
                break;
                
            case 'veo2':
                // ✅ Veo2: Validación básica (sin restricciones de tamaño)
                $this->validarImagenBasica();
                break;
                
            case 'gen4_turbo':
            case 'gen3a_turbo':
                // ✅ Runway: Validación básica (sin restricciones de tamaño)
                $this->validarImagenBasica();
                break;
                
            case 'ray2':
            case 'ray2-flash':
                // ✅ Luma: Validación básica (sin restricciones de tamaño)
                $this->validarImagenBasica();
                break;
                
            // 🚀 FUTURAS VALIDACIONES - Ejemplos de cómo agregar nuevos modelos:
            // case 'nuevo-modelo':
            //     $this->validarImagenParaNuevoModelo();
            //     break;
                
            default:
                // ✅ Modelos no reconocidos: Validación básica
                $this->validarImagenBasica();
                break;
        }
    }

    /**
     * Validación básica de imagen para modelos que no requieren restricciones especiales
     * (Veo2, Runway, Luma, etc.)
     * 
     * 🎯 EJEMPLO DE CÓMO AGREGAR VALIDACIONES ESPECÍFICAS:
     * 
     * private function validarImagenParaNuevoModelo(): void
     * {
     *     // 1. Obtener dimensiones de la imagen
     *     $imagePath = $this->obtenerPathImagen();
     *     $imageInfo = getimagesize($imagePath);
     *     $width = $imageInfo[0];
     *     $height = $imageInfo[1];
     *     
     *     // 2. Aplicar reglas específicas del modelo
     *     $tamañosPermitidos = ['1024x1024', '512x512'];
     *     $sizeString = "{$width}x{$height}";
     *     
     *     if (!in_array($sizeString, $tamañosPermitidos)) {
     *         // 3. Manejar error o redimensionar
     *         $this->manejarErrorImagenIncompatible($sizeString, $tamañosPermitidos);
     *         return;
     *     }
     *     
     *     // 4. Bloquear ratio si es necesario
     *     $this->ratioLocked = true;
     *     $this->ratio = $this->detectarRatio($width, $height);
     * }
     */
    private function validarImagenBasica(): void
    {
        try {
            Log::info('📷 Validación básica de imagen', [
                'model' => $this->model,
                'fromHistory' => $this->fromHistory,
                'hasImageFilesStart' => !empty($this->imageFilesStart),
                'hasImageUrl' => !empty($this->imageUrl)
            ]);
            
            // ✅ Para modelos no-Sora: Solo verificar que la imagen existe y es válida
            // No hay restricciones de tamaño específicas
            
            if ($this->fromHistory && $this->imageUrl) {
                Log::info('✅ Imagen del historial válida para modelo no-Sora', [
                    'model' => $this->model,
                    'imageUrl' => $this->imageUrl
                ]);
            } elseif (!empty($this->imageFilesStart)) {
                Log::info('✅ Imagen subida válida para modelo no-Sora', [
                    'model' => $this->model,
                    'count' => count($this->imageFilesStart)
                ]);
            }
            
            // ✅ No bloquear el ratio para modelos no-Sora
            $this->ratioLocked = false;
            
            Log::info('✅ Validación básica completada', [
                'model' => $this->model,
                'ratioLocked' => $this->ratioLocked
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error en validación básica de imagen', [
                'model' => $this->model,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Valida la imagen cargada para modelos Sora
     * Se ejecuta cuando se cambia a modelo Sora y ya hay una imagen
     */
    private function validarImagenParaSora(): void
    {
        try {
            $imagePath = null;
            
            // Obtener el path de la imagen
            if ($this->fromHistory && $this->imageUrl) {
                // Descargar temporalmente para obtener dimensiones
                $imageContent = @file_get_contents($this->imageUrl);
                if ($imageContent) {
                    $tempPath = sys_get_temp_dir() . '/' . uniqid('ratio_detect_') . '.jpg';
                    file_put_contents($tempPath, $imageContent);
                    $imagePath = $tempPath;
                }
            } elseif (!empty($this->imageFilesStart)) {
                $imagePath = $this->imageFilesStart[0]->getRealPath();
            }
            
            if (!$imagePath || !file_exists($imagePath)) {
                Log::warning('⚠️ No se pudo obtener el path de la imagen para validar');
                return;
            }
            
            // Obtener dimensiones
            $imageInfo = @getimagesize($imagePath);
            
            // Limpiar archivo temporal si se creó
            if ($this->fromHistory && isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            
            if (!$imageInfo) {
                Log::warning('⚠️ No se pudieron obtener las dimensiones de la imagen');
                return;
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            Log::info('📐 Validando imagen existente para Sora', [
                'width' => $width,
                'height' => $height,
                'sizeString' => "{$width}x{$height}"
            ]);
            
            // ✅ VALIDAR que el tamaño sea EXACTAMENTE uno de los permitidos por OpenAI
            $tamañosPermitidos = [
                '1280x720' => '16:9',  // Horizontal
                '720x1280' => '9:16',  // Vertical
            ];
            
            $sizeString = "{$width}x{$height}";
            
            if (!array_key_exists($sizeString, $tamañosPermitidos)) {
                // ❌ Tamaño NO permitido - RECHAZAR
                $errorMessage = "La imagen cargada no es compatible con Sora. Debe tener un aspecto similiar a 16:9 o 9:16. Tu imagen es {$width}x{$height}. Por favor, elimina la imagen o cambia a otro modelo.";
                
                // Limpiar la imagen cargada
                $this->limpiarTodasLasImagenes();
                
                // Mostrar error al usuario
                $this->addError('imageFilesStart', $errorMessage);
                
                $this->dispatch('addErrorToList', 
                    message: $errorMessage, 
                    type: 'validation', 
                    tool: 'video-generator'
                );
                
                Log::warning('⚠️ Imagen existente rechazada por tamaño incorrecto para Sora', [
                    'width' => $width,
                    'height' => $height,
                    'sizeString' => $sizeString,
                    'tamañosPermitidos' => array_keys($tamañosPermitidos)
                ]);
                
                return;
            }
            
            // ✅ Tamaño válido - Bloquear y establecer el ratio (solo para Sora-2)
            $ratioDetectado = $tamañosPermitidos[$sizeString];
            $this->ratio = $ratioDetectado;
            
            // Solo bloquear el ratio si el modelo actual es Sora-2
            if (in_array($this->model, ['sora-2', 'sora-2-pro'])) {
                $this->ratioLocked = true;
            }
            
            Log::info('🔒 Imagen existente validada y ratio bloqueado para Sora', [
                'size' => $sizeString,
                'ratio' => $ratioDetectado,
                'modelo' => $this->model,
                'ratioLocked' => $this->ratioLocked
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error validando imagen existente para Sora', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validación personalizada por modelo
     * 
     * @return bool True si la validación pasa, False si hay errores
     */
    private function validarPorModelo(): bool
    {
        // Limpiar errores previos
        $this->resetErrorBag();
        
        $hasErrors = false;
        $errorMessage = '';
        
        if ($this->model === 'veo2') {
            // ✅ Veo2: Requiere prompt obligatorio
            Log::info('🔍 Validando Veo2', [
                'model' => $this->model,
                'promptLength' => strlen(trim($this->promptText)),
                'hasImageFilesStart' => !empty($this->imageFilesStart),
                'hasImageFilesEnd' => !empty($this->imageFilesEnd),
                'fromHistory' => $this->fromHistory,
                'hasImageUrl' => !empty($this->imageUrl),
                'imageUrl' => $this->imageUrl
            ]);
            
            if (empty(trim($this->promptText))) {
                $errorMessage = 'Para Veo2 es necesario escribir un prompt que describa el video a generar.';
                $this->addError('promptText', $errorMessage);
                $hasErrors = true;
                
                Log::info('❌ Validación Veo2: Prompt requerido', [
                    'model' => $this->model,
                    'promptLength' => strlen(trim($this->promptText))
                ]);
            } else {
                Log::info('✅ Validación Veo2: Prompt válido');
            }
        } elseif (in_array($this->model, ['gen4_turbo', 'gen3a_turbo'])) {
            // ✅ Runway: Requiere al menos una imagen (inicio o fin)
            $hasStartImages = !empty($this->imageFilesStart);
            $hasEndImages = !empty($this->imageFilesEnd);
            $hasHistoryImage = $this->fromHistory && $this->imageUrl;
            
            if (!$hasStartImages && !$hasEndImages && !$hasHistoryImage) {
                $errorMessage = "Para modelos Runway es necesario subir al menos una imagen (de inicio o fin) o seleccionar una del historial.";
                $this->addError('imageFilesStart', $errorMessage);
                $hasErrors = true;
                
                Log::info("❌ Validación Runway: Imagen requerida", [
                    'model' => $this->model,
                    'hasStartImages' => $hasStartImages,
                    'hasEndImages' => $hasEndImages,
                    'hasHistoryImage' => $hasHistoryImage
                ]);
            }
        } elseif (in_array($this->model, ['ray2', 'ray2-flash'])) {
            // ✅ Luma: Requiere al menos prompt O imagen (inicio o fin)
            $hasPrompt = !empty(trim($this->promptText));
            $hasStartImages = !empty($this->imageFilesStart);
            $hasEndImages = !empty($this->imageFilesEnd);
            $hasHistoryImage = $this->fromHistory && $this->imageUrl;
            $hasAnyImage = $hasStartImages || $hasEndImages || $hasHistoryImage;
            
            if (!$hasPrompt && !$hasAnyImage) {
                $errorMessage = "Para modelos Luma es necesario escribir un prompt O subir al menos una imagen (de inicio o fin) o seleccionar una del historial.";
                $this->addError('promptText', $errorMessage);
                $hasErrors = true;
                
                Log::info("❌ Validación Luma: Se requiere prompt O imagen", [
                    'model' => $this->model,
                    'hasPrompt' => $hasPrompt,
                    'hasStartImages' => $hasStartImages,
                    'hasEndImages' => $hasEndImages,
                    'hasHistoryImage' => $hasHistoryImage,
                    'hasAnyImage' => $hasAnyImage
                ]);
            }
        } elseif (in_array($this->model, ['sora-2', 'sora-2-pro'])) {
            // ✅ Sora: Requiere prompt obligatorio, imagen es opcional
            Log::info('🔍 Validando Sora', [
                'model' => $this->model,
                'promptLength' => strlen(trim($this->promptText)),
                'hasImageFilesStart' => !empty($this->imageFilesStart),
                'fromHistory' => $this->fromHistory,
                'hasImageUrl' => !empty($this->imageUrl)
            ]);
            
            if (empty(trim($this->promptText))) {
                $errorMessage = 'Para modelos Sora es necesario escribir un prompt que describa el video a generar.';
                $this->addError('promptText', $errorMessage);
                $hasErrors = true;
                
                Log::info('❌ Validación Sora: Prompt requerido', [
                    'model' => $this->model,
                    'promptLength' => strlen(trim($this->promptText))
                ]);
            } else {
                Log::info('✅ Validación Sora: Prompt válido', [
                    'withImage' => !empty($this->imageFilesStart) || ($this->fromHistory && $this->imageUrl)
                ]);
            }
        } else {
            // ✅ Otros modelos: Validación estándar (requiere prompt por ahora)
            if (empty(trim($this->promptText))) {
                $errorMessage = 'Es necesario escribir un prompt que describa el video a generar.';
                $this->addError('promptText', $errorMessage);
                $hasErrors = true;
                
                Log::info('❌ Validación genérica: Prompt requerido', [
                    'model' => $this->model,
                    'promptLength' => strlen(trim($this->promptText))
                ]);
            }
        }
        
        if ($hasErrors) {
            // 🚨 Enviar error al componente principal (igual que en otros métodos)
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'validation', 
                tool: 'video-generator'
            );
            
            Log::info('❌ Validación fallida', [
                'model' => $this->model,
                'errorMessage' => $errorMessage,
                'errors' => $this->getErrorBag()->toArray()
            ]);
            return false;
        }
        
        Log::info('✅ Validación exitosa', [
            'model' => $this->model,
            'hasPrompt' => !empty(trim($this->promptText)),
            'hasStartImages' => !empty($this->imageFilesStart),
            'hasEndImages' => !empty($this->imageFilesEnd),
            'hasHistoryImage' => $this->fromHistory && $this->imageUrl
        ]);
        
        return true;
    }

    public function mount()
    {
        // Inicializar el ratio con el primer ratio disponible del modelo por defecto
        $ratiosDisponibles = $this->getAvailableRatiosForModel();
        $this->ratio = array_key_first($ratiosDisponibles);
        
        Log::info('🎬 VideoGenerator montando...', [
            'model' => $this->model,
            'ratio' => $this->ratio,
            'ratiosDisponibles' => array_keys($ratiosDisponibles),
            'fromHistory' => $this->fromHistory,
            'hasImageUrl' => !empty($this->imageUrl)
        ]);
        
        // ✅ SOLUCIÓN: Dispatch después de un micro delay para asegurar que el componente esté completamente montado
        $this->dispatch('videoGeneratorMounted');
        
        Log::info('✅ VideoGenerator montado exitosamente');
    }

    /**
     * Método para confirmar que el componente está listo y procesar datos pendientes
     */
    #[On('checkComponentReady')]
    public function checkComponentReady(): void
    {
        Log::info('🔍 Verificando si VideoGenerator está listo para procesar eventos');
        
        // Confirmar que el componente está listo
        $this->dispatch('videoGeneratorReady');
        
        Log::info('✅ VideoGenerator confirmado como listo para eventos');
    }

    /**
     * Método para debuggear el estado actual del componente
     */
    public function debugComponentState(): array
    {
        $state = [
            'model' => $this->model,
            'promptText' => substr($this->promptText, 0, 100) . '...',
            'promptLength' => strlen(trim($this->promptText)),
            'ratio' => $this->ratio,
            'count' => $this->count,
            'fromHistory' => $this->fromHistory,
            'imageUrl' => $this->imageUrl,
            'hasImageFilesStart' => !empty($this->imageFilesStart),
            'hasImageFilesEnd' => !empty($this->imageFilesEnd),
            'historyMetadata' => $this->historyMetadata,
            'isGenerating' => $this->isGenerating
        ];
        
        Log::info('🔍 Estado actual del VideoGenerator', $state);
        return $state;
    }

    /**
     * Método para quitar una imagen de inicio
     */
    public function quitarImagenInicio($index)
    {
        if (isset($this->imageFilesStart[$index])) {
            // Crear un nuevo array sin la imagen eliminada
            $newFiles = [];
            foreach ($this->imageFilesStart as $i => $file) {
                if ($i != $index) {
                    $newFiles[] = $file;
                }
            }
            $this->imageFilesStart = $newFiles;
        }
    }
    
    /**
     * Método para quitar una imagen de fin
     */
    public function quitarImagenFin($index)
    {
        if (isset($this->imageFilesEnd[$index])) {
            // Crear un nuevo array sin la imagen eliminada
            $newFiles = [];
            foreach ($this->imageFilesEnd as $i => $file) {
                if ($i != $index) {
                    $newFiles[] = $file;
                }
            }
            $this->imageFilesEnd = $newFiles;
        }
    }

    /**
     * Método para limpiar todas las imágenes (inicio y fin)
     */
    public function limpiarTodasLasImagenes()
    {
        $this->imageFilesStart = [];
        $this->imageFilesEnd = [];
        $this->temporaryImagesStart = [];
        $this->temporaryImagesEnd = [];
        
        // Limpiar datos del historial
        $this->fromHistory = false;
        $this->historyMetadata = [];
        $this->imageUrl = null;
        
        // 🔓 Desbloquear el ratio cuando se limpian las imágenes
        $this->ratioLocked = false;
        
        Log::info('🗑️ Todas las imágenes limpiadas del VideoGenerator');
    }

    /**
     * Valida que la imagen tenga el tamaño EXACTO requerido por Sora
     * OpenAI NO acepta redimensionamiento, debe ser exactamente 1280x720 o 720x1280
     */
    private function detectarYBloquearRatioPorImagen(): void
    {
        try {
            $imagePath = null;
            
            // Obtener el path de la imagen
            if ($this->fromHistory && $this->imageUrl) {
                // Descargar temporalmente para obtener dimensiones
                $imageContent = @file_get_contents($this->imageUrl);
                if ($imageContent) {
                    $tempPath = sys_get_temp_dir() . '/' . uniqid('ratio_detect_') . '.jpg';
                    file_put_contents($tempPath, $imageContent);
                    $imagePath = $tempPath;
                }
            } elseif (!empty($this->imageFilesStart)) {
                $imagePath = $this->imageFilesStart[0]->getRealPath();
            }
            
            if (!$imagePath || !file_exists($imagePath)) {
                Log::warning('⚠️ No se pudo obtener el path de la imagen para detectar dimensiones');
                return;
            }
            
            // Obtener dimensiones
            $imageInfo = @getimagesize($imagePath);
            
            // Limpiar archivo temporal si se creó
            if ($this->fromHistory && isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            
            if (!$imageInfo) {
                Log::warning('⚠️ No se pudieron obtener las dimensiones de la imagen');
                return;
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            Log::info('📐 Dimensiones de imagen detectadas', [
                'width' => $width,
                'height' => $height,
                'sizeString' => "{$width}x{$height}"
            ]);
            
            // ✅ VALIDAR que el tamaño sea EXACTAMENTE uno de los permitidos por OpenAI
            $tamañosPermitidos = [
                '1280x720' => '16:9',  // Horizontal
                '720x1280' => '9:16',  // Vertical
            ];
            
            $sizeString = "{$width}x{$height}";
            
            if (!array_key_exists($sizeString, $tamañosPermitidos)) {
                // ❌ Tamaño NO permitido - RECHAZAR
                $errorMessage = "La imagen debe tener un tamaño exacto de 1280x720 (horizontal) o 720x1280 (vertical). Tu imagen es {$width}x{$height}.";
                
                // Limpiar la imagen subida
                $this->limpiarTodasLasImagenes();
                
                // Mostrar error al usuario
                $this->addError('imageFilesStart', $errorMessage);
                
                $this->dispatch('addErrorToList', 
                    message: $errorMessage, 
                    type: 'validation', 
                    tool: 'video-generator'
                );
                
                Log::warning('⚠️ Imagen rechazada por tamaño incorrecto', [
                    'width' => $width,
                    'height' => $height,
                    'sizeString' => $sizeString,
                    'tamañosPermitidos' => array_keys($tamañosPermitidos)
                ]);
                
                return;
            }
            
            // ✅ Tamaño válido - Bloquear y establecer el ratio (solo para Sora-2)
            $ratioDetectado = $tamañosPermitidos[$sizeString];
            $this->ratio = $ratioDetectado;
            
            // Solo bloquear el ratio si el modelo actual es Sora-2
            if (in_array($this->model, ['sora-2', 'sora-2-pro'])) {
                $this->ratioLocked = true;
            }
            
            Log::info('🔒 Imagen aceptada con tamaño exacto', [
                'size' => $sizeString,
                'ratio' => $ratioDetectado,
                'modelo' => $this->model,
                'ratioLocked' => $this->ratioLocked
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error validando tamaño de imagen', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Actualizar imagen de inicio cuando se sube temporalmente
     */
    public function updatedTemporaryImagesStart()
    {
        if (!empty($this->temporaryImagesStart)) {
            // 🔄 LIMPIAR IMAGEN DEL HISTORIAL si se suben imágenes manualmente
            if ($this->fromHistory) {
                Log::info('🧹 Limpiando imagen del historial al subir imagen manual de inicio');
                $this->fromHistory = false;
                $this->historyMetadata = [];
                $this->imageUrl = null;
            }
            
            Log::info("Actualizando imagen de inicio: " . count($this->temporaryImagesStart) . " archivos");
            
            // ✅ Para modelos NO-Sora: Solo validación básica (sin restricciones de ratio)
            // Los modelos Veo2, Runway, Luma no requieren validación de aspect ratio
            
            $this->imageFilesStart = $this->temporaryImagesStart;
            $this->temporaryImagesStart = [];
            
            Log::info('✅ Imagen validada correctamente', [
                'ratio' => $this->ratio,
                'count' => count($this->imageFilesStart)
            ]);
            
            // 🎯 Validar imagen según el modelo seleccionado
            $this->validarImagenParaModelo();
        }
    }

    /**
     * Actualizar imagen de fin cuando se sube temporalmente
     */
    public function updatedTemporaryImagesEnd()
    {
        if (!empty($this->temporaryImagesEnd)) {
            // 🔄 LIMPIAR IMAGEN DEL HISTORIAL si se suben imágenes manualmente
            if ($this->fromHistory) {
                Log::info('🧹 Limpiando imagen del historial al subir imagen manual de fin');
                $this->fromHistory = false;
                $this->historyMetadata = [];
                $this->imageUrl = null;
            }
            
            Log::info("Actualizando imagen de fin: " . count($this->temporaryImagesEnd) . " archivos");
            $this->imageFilesEnd = $this->temporaryImagesEnd;
            $this->temporaryImagesEnd = [];
        }
    }
    
    public function generate(): void
    {
        Log::info('🎬 Iniciando proceso de generación de video', [
            'model' => $this->model,
            'promptLength' => strlen(trim($this->promptText)),
            'hasImageFilesStart' => !empty($this->imageFilesStart),
            'hasImageFilesEnd' => !empty($this->imageFilesEnd),
            'fromHistory' => $this->fromHistory,
            'hasImageUrl' => !empty($this->imageUrl),
            'ratio' => $this->ratio,
            'count' => $this->count
        ]);
        
        // ✅ Validación personalizada por modelo
        if (!$this->validarPorModelo()) {
            Log::warning('❌ Validación fallida, no se inicia generación');
            return; // No continuar si hay errores de validación
        }
        
        Log::info('✅ Validación exitosa, iniciando generación');
        
        // 1. ACTIVAR INMEDIATAMENTE el spinner
        $this->isGenerating = true;
        $this->results = [];
        
    // 2. DISPARAR EVENTO para mostrar spinner en frontend
    $this->dispatch('videoGenerationStarted');
        
        // 3. DISPARAR EVENTO para iniciar generación REAL (con delay)
        $this->dispatch('startVideoGeneration', [
            'prompt' => $this->promptText,
            'model' => $this->model,
            'count' => $this->count,
            'ratio' => $this->ratio
        ]);
        
        Log::info('🚀 Eventos de generación disparados');
    }

    // 4. MÉTODO QUE HACE LA GENERACIÓN REAL
    #[On('startVideoGeneration')]
    public function executeGeneration($data): void
    {
        try {
            
            Log::info('🎬 executeGeneration llamado exitosamente', [
                'data' => $data,
                'componentState' => [
                    'model' => $this->model,
                    'fromHistory' => $this->fromHistory,
                    'hasImageUrl' => !empty($this->imageUrl),
                    'hasImageFilesStart' => !empty($this->imageFilesStart),
                    'hasImageFilesEnd' => !empty($this->imageFilesEnd),
                    'promptLength' => strlen(trim($this->promptText))
                ]
            ]);
            
        
            // dd($data);
            switch ($data['model']) {
                case 'veo2':
                    $this->generarConVeo2($data);
                    break;
                case 'gen4_turbo':
                    $this->generarConGen4Turbo($data);
                    break;
                case 'gen3a_turbo':
                    $this->generarConGen3AlphaTurbo($data);
                    break;
                case 'ray2':
                    $this->generarConRay2($data);
                    break;
                case 'ray2-flash':
                    $this->generarConRay2Flash($data);
                    break;
                case 'sora-2':
                case 'sora-2-pro':
                    $this->generarConSora($data);
                    break;
                default:
                    throw new \Exception('Modelo no soportado');
            }

        } catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            // Enviar error al componente principal
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
            $this->isGenerating = false; // Solo en caso de error
        }
    }

    /**
     * Métodos placeholder para cada modelo - TÚ LOS IMPLEMENTARÁS
     */
    public function generarConVeo2($data): void
    {
        try {
            Log::info('🚀 Iniciando generación con Veo2', $data);
            
            // Procesar imagen a base64 si está disponible (opcional)
            $imageData = $this->procesarImagenesABase64ParaVeo2();
            
            Log::info('📷 Estado de imagen para Veo2', [
                'tieneImagen' => $imageData !== null,
                'tamañoBase64' => $imageData ? strlen($imageData['base64']) : 0,
                'mimeType' => $imageData ? $imageData['mimeType'] : null
            ]);
            
            // Llamar al servicio Gemini para generar video
            $response = GeminiService::generateVideo(
                prompt: $data['prompt'],
                model: "veo-2.0-generate-001", // Modelo correcto de Veo2
                ratio: $data['ratio'],
                imageBase64: $imageData ? $imageData['base64'] : null, // Imagen opcional en base64
                numberOfVideos: $data['count'],
                durationSeconds: 5, // Por defecto 5 segundos
                personGeneration: "dont_allow",
                imageMimeType: $imageData ? $imageData['mimeType'] : null // Tipo MIME de la imagen
            );
            
            if (!($response['success'] ?? false)) {
                $errorMessage = 'Error con Veo2: ' . ($response['error'] ?? 'Error desconocido');
                $this->addError('promptText', $errorMessage);
                
                $this->dispatch('addErrorToList', 
                    message: $errorMessage, 
                    type: 'generation', 
                    tool: 'video-generator'
                );
                
                $this->dispatch('videoGenerationError');
                $this->isGenerating = false; // ✅ Resetear estado de generación
                return;
            }
            
            // Obtener el ID de operación para el polling
            $operationId = $response['operationId'];
            $operationName = $response['operationName'] ?? null; // Nombre completo de la operación
            
            if (!$operationName) {
                throw new \Exception('No se pudo obtener el nombre de la operación de Veo2');
            }
            
            Log::info('✅ Veo2 iniciado correctamente', [
                'operationId' => $operationId,
                'operationName' => $operationName
            ]);
            
            // Disparar evento para iniciar polling
            $this->dispatch('videoTaskStarted', 
                generationId: $operationName, // Usar el nombre completo para el polling
                prompt: $data['prompt'],
                model: $data['model'],
                ratio: $data['ratio'],
                count: $data['count']
            );
            
        } catch (\Exception $e) {
            $errorMessage = 'Error generando con Veo2: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
            $this->isGenerating = false; // ✅ Resetear estado de generación
        }
    }

    public function generarConGen4Turbo($data): void
    {
        try {
            Log::info('🚀 Iniciando generación con Gen4-Turbo', $data);
            
            // Llamar al método unificado de Runway
            $this->generarConRunway($data, 'gen4_turbo');
            
        } catch (\Exception $e) {
            $errorMessage = 'Error generando con Gen4-Turbo: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
            $this->isGenerating = false; // ✅ Resetear estado de generación
        }
    }

    public function generarConGen3AlphaTurbo($data): void
    {
        try {
            Log::info('🚀 Iniciando generación con Gen3-AlphaTurbo', $data);
            
            // Llamar al método unificado de Runway
            $this->generarConRunway($data, 'gen3a_turbo');
            
        } catch (\Exception $e) {
            $errorMessage = 'Error generando con Gen3-AlphaTurbo: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
            $this->isGenerating = false; // ✅ Resetear estado de generación
        }
    }

    public function generarConRay2($data): void
    {
        try {
            Log::info('🚀 Iniciando generación con Ray2', $data);
            
            // Llamar al método unificado de Luma
            $this->generarConLuma($data, 'ray-2');
            
        } catch (\Exception $e) {
            $errorMessage = 'Error generando con Ray2: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
            $this->isGenerating = false; // ✅ Resetear estado de generación
        }
    }

    public function generarConRay2Flash($data): void
    {
        try {
            Log::info('🚀 Iniciando generación con Ray2-Flash', $data);
            
            // Llamar al método unificado de Luma
            $this->generarConLuma($data, 'ray-flash-2');
            
        } catch (\Exception $e) {
            $errorMessage = 'Error generando con Ray2-Flash: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
            $this->isGenerating = false; // ✅ Resetear estado de generación
        }
    }

    public function generarConSora($data): void
    {
        try {
            Log::info('🚀 Iniciando generación con Sora', [
                'model' => $data['model'],
                'prompt' => $data['prompt'],
                'ratio' => $data['ratio'],
                'hasImageFilesStart' => !empty($this->imageFilesStart),
                'fromHistory' => $this->fromHistory,
                'hasImageUrl' => !empty($this->imageUrl)
            ]);

            // ✅ VALIDACIÓN ADICIONAL: Verificar que la imagen sea válida para el modelo
            if ($this->tieneImagenCargada()) {
                $this->validarImagenParaModelo();
                
                // Si la validación falló, la imagen fue eliminada automáticamente
                // Verificar si aún hay imagen después de la validación
                if (!$this->tieneImagenCargada()) {
                    $errorMessage = 'La imagen no es compatible con ' . $this->getModelDisplayName($this->model) . '.';
                    $this->addError('promptText', $errorMessage);
                    
                    $this->dispatch('addErrorToList', 
                        message: $errorMessage, 
                        type: 'validation', 
                        tool: 'video-generator'
                    );
                    
                    $this->dispatch('videoGenerationError');
                    $this->isGenerating = false;
                    return;
                }
            }

            // Mapear ratio al formato de OpenAI (widthxheight)
            $size = $this->mapearRatioASora($data['ratio']);
            
            // Procesar imagen para Sora si está disponible
            $imageData = $this->procesarImagenParaSora($size);
            
            if ($imageData) {
                Log::info('📷 Imagen procesada para Sora', [
                    'hasImage' => true,
                    'fileName' => $imageData['fileName'] ?? 'unknown',
                    'sizeKB' => round(strlen($imageData['content']) / 1024, 2)
                ]);
            }
            
            // Llamar al servicio OpenAI para crear el video
            $response = \App\Services\OpenAiService::createVideo(
                prompt: $data['prompt'],
                model: $data['model'],
                size: $size,
                seconds: (string)$this->durationSeconds, // Duración en segundos
                imageData: $imageData // Puede ser null si no hay imagen
            );

            if (isset($response['error'])) {
                $errorMessage = 'Error con Sora: ' . $response['error'];
                throw new \Exception($errorMessage);
            }

            // Obtener el ID del video
            $videoId = $response['id'] ?? null;
            if (!$videoId) {
                throw new \Exception('No se recibió ID de video de Sora');
            }

            Log::info("✅ Sora iniciado correctamente", [
                'videoId' => $videoId,
                'model' => $data['model'],
                'status' => $response['status'] ?? 'unknown',
                'withImage' => $imageData !== null
            ]);

            // Disparar evento para iniciar polling
            $this->dispatch('videoTaskStarted', 
                generationId: $videoId,
                prompt: $data['prompt'],
                model: $data['model'],
                ratio: $data['ratio'],
                count: $data['count']
            );

        } catch (\Exception $e) {
            $errorMessage = 'Error generando con Sora: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
            $this->isGenerating = false; // ✅ Resetear estado de generación
        }
    }

    /**
     * Método unificado para generar videos con Luma (Ray2 y Ray2-Flash)
     */
    private function generarConLuma($data, $modelo): void
    {
        try {
// if($data){
//     $imagenesS3 = $this->procesarYSubirImagenesAS3();
//     dd($data,$imagenesS3);
//     $this->dispatch('videoTaskStarted', 
//                 generationId: "c8c4d3fd-9eea-442e-9687-c5f84c5207a7",
//                 prompt: $data['prompt'],
//                 model: $data['model'],
//                 ratio: $data['ratio'],
//                 count: $data['count']
//             );
//     return;
// }

            Log::info("🎬 Iniciando generación con Luma {$modelo}", [
                'prompt' => $data['prompt'],
                'ratio' => $data['ratio'],
                'count' => $data['count']
            ]);

            // Procesar y subir imágenes a S3 si existen
            $imagenesS3 = $this->procesarYSubirImagenesAS3();
            
            // Preparar payload base para Luma
            $payload = [
                'prompt' => $data['prompt'],
                'aspect_ratio' => $data['ratio'],
                'model' => $modelo,
                'resolution' => '720p',
                'duration' => '5s' // Por defecto 5 segundos
            ];

            // Agregar keyframes si hay imágenes
            if (!empty($imagenesS3)) {
                $payload['keyframes'] = [];
                
                // Keyframe 0 (imagen de inicio) - primera imagen
                if (isset($imagenesS3[0])) {
                    $payload['keyframes']['frame0'] = [
                        'type' => 'image',
                        'url' => $imagenesS3[0]
                    ];
                    Log::info("📷 Agregando keyframe0 para Luma", ['url' => $imagenesS3[0]]);
                }
                
                // Keyframe 1 (imagen final) - segunda imagen si existe
                if (isset($imagenesS3[1])) {
                    $payload['keyframes']['frame1'] = [
                        'type' => 'image',
                        'url' => $imagenesS3[1]
                    ];
                    Log::info("📷 Agregando keyframe1 para Luma", ['url' => $imagenesS3[1]]);
                }
                
                Log::info("🎞️ Usando método con keyframes para Luma", [
                    'keyframes_count' => count($payload['keyframes']),
                    'frame0' => isset($payload['keyframes']['frame0']),
                    'frame1' => isset($payload['keyframes']['frame1'])
                ]);
            }

            // Llamar al servicio Luma usando siempre el método con keyframes
            $response = \App\Services\LumaService::generateVideoFromPromptWithKeyframes($payload);

            if (!($response['success'] ?? false)) {
                $errorMessage = 'Error con Luma: ' . ($response['error'] ?? 'Error desconocido');
                throw new \Exception($errorMessage);
            }

            // Obtener el ID de tarea
            $taskId = $response['data']['id'] ?? null;
            if (!$taskId) {
                throw new \Exception('No se recibió ID de tarea de Luma');
            }

            Log::info("✅ Luma iniciado correctamente", [
                'taskId' => $taskId,
                'model' => $modelo
            ]);

            // Disparar evento para iniciar polling
            $this->dispatch('videoTaskStarted', 
                generationId: $taskId,
                prompt: $data['prompt'],
                model: $data['model'],
                ratio: $data['ratio'],
                count: $data['count']
            );

        } catch (\Exception $e) {
            Log::error("❌ Error generando con Luma {$modelo}", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Método unificado para generar videos con Runway (Gen3 y Gen4)
     */
    private function generarConRunway($data, $modelo): void
    {
        try {

    //         if($data){
    //  // Disparar evento para iniciar polling
    // //  dd($data);
    //         $this->dispatch('videoTaskStarted', 
    //             generationId: "e32c8722-261d-482b-a17a-05ad0558a2f6",
    //             prompt: $data['prompt'],
    //             model: $data['model'],
    //             ratio: $data['ratio'],
    //             count: $data['count']
    //         );
    //         return;
    //         }
            Log::info("🎬 Iniciando generación con Runway {$modelo}", [
                'prompt' => $data['prompt'],
                'ratio' => $data['ratio'],
                'count' => $data['count']
            ]);

            // Mapear ratio a formato Runway
            $runwayRatio = $this->mapearRatioARunway($data['ratio'], $modelo);
            
            // Procesar y subir imágenes a S3 si existen
            $imagenesS3 = $this->procesarYSubirImagenesAS3();
            
            // Preparar payload para Runway
            $payload = [
                'promptText' => $data['prompt'],
                'model' => $modelo,
                'ratio' => $runwayRatio,
                'duration' => 5, // Por defecto 5 segundos
                'imagesWithPositions' => []
            ];

            // Agregar imágenes según el modelo
            if (!empty($imagenesS3)) {
                if ($modelo === 'gen4_turbo') {
                    // Gen4 solo acepta imagen en posición 'first'
                    $payload['imagesWithPositions'] = [
                        [
                            'uri' => $imagenesS3[0],
                            'position' => 'first'
                        ]
                    ];
                    Log::info("📷 Agregando imagen para Gen4-Turbo (solo first)");
                } else {
                    // Gen3 acepta imágenes en 'first' y 'last'
                    $payload['imagesWithPositions'] = [];
                    
                    if (isset($imagenesS3[0])) {
                        $payload['imagesWithPositions'][] = [
                            'uri' => $imagenesS3[0],
                            'position' => 'first'
                        ];
                    }
                    
                    if (isset($imagenesS3[1])) {
                        $payload['imagesWithPositions'][] = [
                            'uri' => $imagenesS3[1],
                            'position' => 'last'
                        ];
                    }
                    
                    Log::info("📷 Agregando imágenes para Gen3-AlphaTurbo", [
                        'first' => isset($imagenesS3[0]),
                        'last' => isset($imagenesS3[1])
                    ]);
                }
            }

            // Llamar al servicio Runway
            $response = \App\Services\RunWayService::generateGen3aTurboVideo(
                $payload['promptText'],
                $payload['model'],
                $payload['imagesWithPositions'],
                $payload['ratio'],
                $payload['duration']
            );

            if (!($response['success'] ?? false)) {
                $errorMessage = 'Error con Runway: ' . ($response['error'] ?? 'Error desconocido');
                throw new \Exception($errorMessage);
            }

            // Obtener el ID de tarea
            $taskId = $response['data']['id'] ?? null;
            if (!$taskId) {
                throw new \Exception('No se recibió ID de tarea de Runway');
            }

            Log::info("✅ Runway iniciado correctamente", [
                'taskId' => $taskId,
                'model' => $modelo
            ]);

            // Disparar evento para iniciar polling
            $this->dispatch('videoTaskStarted', 
                generationId: $taskId,
                prompt: $data['prompt'],
                model: $data['model'],
                ratio: $data['ratio'],
                count: $data['count']
            );

        } catch (\Exception $e) {
            Log::error("❌ Error generando con Runway {$modelo}", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Procesa imagen para Sora (OpenAI) - SIN redimensionamiento
     * La imagen ya fue validada para tener el tamaño exacto (1280x720 o 720x1280)
     * Retorna array con 'content' (binario original), 'mimeType', 'fileName' o null
     */
    private function procesarImagenParaSora(string $targetSize): ?array
    {
        try {
            if ($this->fromHistory && $this->imageUrl) {
                // Para imágenes del historial, descargar directamente
                Log::info('📥 Descargando imagen del historial para Sora', [
                    'imageUrl' => $this->imageUrl,
                    'targetSize' => $targetSize
                ]);
                
                $imageContent = file_get_contents($this->imageUrl);
                if ($imageContent === false) {
                    throw new \Exception('No se pudo descargar la imagen del historial');
                }
                
                $fileName = basename($this->imageUrl);
                $mimeType = mime_content_type($this->imageUrl) ?? 'image/jpeg';
                
                Log::info('✅ Imagen del historial lista para Sora (sin redimensionar)', [
                    'fileName' => $fileName,
                    'size' => strlen($imageContent),
                    'sizeKB' => round(strlen($imageContent) / 1024, 2)
                ]);
                
                return [
                    'content' => $imageContent,
                    'mimeType' => $mimeType,
                    'fileName' => $fileName,
                ];
                
            } elseif (!empty($this->imageFilesStart)) {
                // Para imágenes subidas, leer directamente sin modificar
                $image = $this->imageFilesStart[0]; // Tomar solo la primera imagen
                
                try {
                    $imagePath = $image->getRealPath();
                    $fileName = $image->getClientOriginalName();
                    $imageContent = file_get_contents($imagePath);
                    
                    if ($imageContent === false) {
                        throw new \Exception('No se pudo leer el contenido de la imagen');
                    }
                    
                    Log::info("📷 Imagen subida lista para Sora (sin redimensionar)", [
                        'fileName' => $fileName,
                        'size' => strlen($imageContent),
                        'sizeKB' => round(strlen($imageContent) / 1024, 2)
                    ]);
                    
                    return [
                        'content' => $imageContent,
                        'mimeType' => $image->getMimeType(),
                        'fileName' => $fileName,
                    ];
                    
                } catch (\Exception $e) {
                    Log::warning("⚠️ Error procesando imagen para Sora", [
                        'error' => $e->getMessage()
                    ]);
                    return null;
                }
            }
            
            Log::info("📷 No hay imágenes para Sora");
            return null;

        } catch (\Exception $e) {
            Log::error("💥 Error en procesarImagenParaSora", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Valida si una imagen tiene el aspect ratio correcto
     * 
     * @param string $imagePath Ruta de la imagen
     * @param string $ratioRequerido Ratio requerido (ej: '16:9', '9:16', '1:1')
     * @param float $tolerancia Tolerancia en la comparación (default 0.02 = 2%)
     * @return array ['valido' => bool, 'ratioActual' => float, 'ratioRequerido' => float, 'dimensiones' => array]
     */
    private function validarAspectRatioImagen(string $imagePath, string $ratioRequerido, float $tolerancia = 0.02): array
    {
        try {
            // Obtener dimensiones de la imagen
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                throw new \Exception('No se pudieron obtener las dimensiones de la imagen');
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            // Calcular ratio actual
            $ratioActual = $width / $height;
            
            // Convertir ratio requerido a decimal
            $ratioRequeridoDecimal = $this->convertirRatioADecimal($ratioRequerido);
            
            // Comparar con tolerancia
            $diferencia = abs($ratioActual - $ratioRequeridoDecimal);
            $esValido = $diferencia <= $tolerancia;
            
            Log::info('📐 Validación de aspect ratio', [
                'dimensiones' => "{$width}x{$height}",
                'ratioActual' => round($ratioActual, 4),
                'ratioRequerido' => $ratioRequerido,
                'ratioRequeridoDecimal' => round($ratioRequeridoDecimal, 4),
                'diferencia' => round($diferencia, 4),
                'tolerancia' => $tolerancia,
                'esValido' => $esValido
            ]);
            
            return [
                'valido' => $esValido,
                'ratioActual' => $ratioActual,
                'ratioRequerido' => $ratioRequeridoDecimal,
                'dimensiones' => ['width' => $width, 'height' => $height],
                'diferencia' => $diferencia
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Error validando aspect ratio', ['error' => $e->getMessage()]);
            return [
                'valido' => false,
                'ratioActual' => 0,
                'ratioRequerido' => 0,
                'dimensiones' => ['width' => 0, 'height' => 0],
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Convierte un ratio en formato string a decimal
     * 
     * @param string $ratio Ratio en formato '16:9', '9:16', '1:1', etc.
     * @return float Ratio en decimal
     */
    private function convertirRatioADecimal(string $ratio): float
    {
        $ratiosDecimales = [
            '16:9' => 16/9,    // 1.778
            '9:16' => 9/16,    // 0.5625
            '1:1' => 1.0,      // 1.0
            '4:3' => 4/3,      // 1.333
            '3:4' => 3/4,      // 0.75
            '21:9' => 21/9,    // 2.333
        ];
        
        return $ratiosDecimales[$ratio] ?? 1.0;
    }
    
    /**
     * Formatea un ratio decimal a formato legible
     * 
     * @param float $ratio Ratio en decimal
     * @return string Ratio formateado (ej: '16:9', '2.46:1')
     */
    private function formatearRatioParaUsuario(float $ratio): string
    {
        // Intentar encontrar un ratio conocido cercano
        $ratiosConocidos = [
            16/9 => '16:9',
            9/16 => '9:16',
            1.0 => '1:1',
            4/3 => '4:3',
            3/4 => '3:4',
            21/9 => '21:9',
        ];
        
        foreach ($ratiosConocidos as $decimal => $formato) {
            if (abs($ratio - $decimal) < 0.01) {
                return $formato;
            }
        }
        
        // Si no coincide con ninguno conocido, formatear como X:1
        return round($ratio, 2) . ':1';
    }
    
    /**
     * Obtiene las dimensiones target para cada modelo y ratio
     * 
     * @param string $modelo Modelo de video
     * @param string $ratio Ratio seleccionado
     * @return array ['width' => int, 'height' => int]
     */
    private function obtenerDimensionesTargetParaModelo(string $modelo, string $ratio): array
    {
        // Dimensiones estándar por ratio para la mayoría de modelos
        $dimensionesEstandar = [
            '16:9' => ['width' => 1280, 'height' => 720],
            '9:16' => ['width' => 720, 'height' => 1280],
            '1:1' => ['width' => 1024, 'height' => 1024],
            '4:3' => ['width' => 1024, 'height' => 768],
            '3:4' => ['width' => 768, 'height' => 1024],
            '21:9' => ['width' => 1344, 'height' => 576],
        ];
        
        // Para Runway Gen3/Gen4, usar dimensiones específicas
        if (in_array($modelo, ['gen4_turbo', 'gen3a_turbo'])) {
            $dimensionesRunway = [
                'gen4_turbo' => [
                    '16:9' => ['width' => 1280, 'height' => 720],
                    '9:16' => ['width' => 720, 'height' => 1280],
                    '1:1' => ['width' => 960, 'height' => 960],
                    '4:3' => ['width' => 1104, 'height' => 832],
                    '3:4' => ['width' => 832, 'height' => 1104],
                    '21:9' => ['width' => 1584, 'height' => 672]
                ],
                'gen3a_turbo' => [
                    '16:9' => ['width' => 1280, 'height' => 768],
                    '9:16' => ['width' => 768, 'height' => 1280]
                ]
            ];
            
            return $dimensionesRunway[$modelo][$ratio] ?? $dimensionesEstandar[$ratio] ?? ['width' => 1280, 'height' => 720];
        }
        
        return $dimensionesEstandar[$ratio] ?? ['width' => 1280, 'height' => 720];
    }
    
    /**
     * Redimensiona una imagen con CROP inteligente (como Photoshop) - SIN distorsión
     * 
     * @param string $sourcePath Ruta de la imagen origen
     * @param int $targetWidth Ancho objetivo
     * @param int $targetHeight Alto objetivo
     * @return array ['success' => bool, 'imageData' => string|null, 'newDimensions' => array, 'cropInfo' => array]
     */
    private function redimensionarImagenConCropInteligente(string $sourcePath, int $targetWidth, int $targetHeight): array
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new \Exception('No se pudieron obtener las dimensiones de la imagen');
            }
            
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Calcular el ratio objetivo
            $targetRatio = $targetWidth / $targetHeight;
            $originalRatio = $originalWidth / $originalHeight;
            
            // Crear imagen desde el archivo
            $sourceImage = null;
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    throw new \Exception('Formato de imagen no soportado: ' . $mimeType);
            }
            
            if (!$sourceImage) {
                throw new \Exception('No se pudo crear la imagen desde el archivo');
            }
            
            // Calcular dimensiones de crop para mantener el ratio objetivo
            if ($originalRatio > $targetRatio) {
                // La imagen es más ancha que el objetivo - crop horizontal
                $cropHeight = $originalHeight;
                $cropWidth = (int)($originalHeight * $targetRatio);
                $cropX = (int)(($originalWidth - $cropWidth) / 2); // Centrar horizontalmente
                $cropY = 0;
            } else {
                // La imagen es más alta que el objetivo - crop vertical
                $cropWidth = $originalWidth;
                $cropHeight = (int)($originalWidth / $targetRatio);
                $cropX = 0;
                $cropY = (int)(($originalHeight - $cropHeight) / 2); // Centrar verticalmente
            }
            
            // Crear nueva imagen con las dimensiones target
            $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
            
            // Preservar transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $targetWidth, $targetHeight, $transparent);
            }
            
            // Hacer el crop y redimensionar
            imagecopyresampled(
                $newImage, $sourceImage,
                0, 0, $cropX, $cropY,
                $targetWidth, $targetHeight, $cropWidth, $cropHeight
            );
            
            // Guardar en buffer
            ob_start();
            imagejpeg($newImage, null, 95); // 95% de calidad
            $imageData = ob_get_clean();
            
            // Liberar memoria
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            
            $cropInfo = [
                'cropX' => $cropX,
                'cropY' => $cropY,
                'cropWidth' => $cropWidth,
                'cropHeight' => $cropHeight,
                'originalRatio' => round($originalRatio, 4),
                'targetRatio' => round($targetRatio, 4)
            ];
            
            Log::info('✅ Imagen redimensionada con CROP inteligente', [
                'original' => "{$originalWidth}x{$originalHeight}",
                'crop' => "{$cropWidth}x{$cropHeight} desde ({$cropX},{$cropY})",
                'nueva' => "{$targetWidth}x{$targetHeight}",
                'sinDistorsion' => true
            ]);
            
            return [
                'success' => true,
                'imageData' => $imageData,
                'newDimensions' => ['width' => $targetWidth, 'height' => $targetHeight],
                'cropInfo' => $cropInfo,
                'resized' => true
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Error redimensionando imagen con CROP', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'imageData' => null,
                'newDimensions' => ['width' => 0, 'height' => 0],
                'cropInfo' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Redimensiona una imagen FORZANDO un nuevo aspect ratio (con posible distorsión)
     * 
     * @param string $sourcePath Ruta de la imagen origen
     * @param int $targetWidth Ancho objetivo
     * @param int $targetHeight Alto objetivo
     * @return array ['success' => bool, 'imageData' => string|null, 'newDimensions' => array]
     */
    private function redimensionarImagenConNuevoRatio(string $sourcePath, int $targetWidth, int $targetHeight): array
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new \Exception('No se pudieron obtener las dimensiones de la imagen');
            }
            
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Crear imagen desde el archivo
            $sourceImage = null;
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    throw new \Exception('Formato de imagen no soportado: ' . $mimeType);
            }
            
            if (!$sourceImage) {
                throw new \Exception('No se pudo crear la imagen desde el archivo');
            }
            
            // Crear nueva imagen con las dimensiones target
            $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
            
            // Preservar transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $targetWidth, $targetHeight, $transparent);
            }
            
            // Redimensionar (esto PUEDE distorsionar si los ratios son diferentes)
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $originalWidth, $originalHeight);
            
            // Guardar en buffer
            ob_start();
            imagejpeg($newImage, null, 95); // 95% de calidad
            $imageData = ob_get_clean();
            
            // Liberar memoria
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            
            Log::info('✅ Imagen redimensionada con nuevo ratio', [
                'original' => "{$originalWidth}x{$originalHeight}",
                'nueva' => "{$targetWidth}x{$targetHeight}",
                'ratioOriginal' => round($originalWidth / $originalHeight, 4),
                'ratioNuevo' => round($targetWidth / $targetHeight, 4)
            ]);
            
            return [
                'success' => true,
                'imageData' => $imageData,
                'newDimensions' => ['width' => $targetWidth, 'height' => $targetHeight],
                'resized' => true
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Error redimensionando imagen con nuevo ratio', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'imageData' => null,
                'newDimensions' => ['width' => 0, 'height' => 0],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Redimensiona una imagen manteniendo el aspect ratio
     * 
     * @param string $sourcePath Ruta de la imagen origen
     * @param int $maxWidth Ancho máximo permitido
     * @param int $maxHeight Alto máximo permitido
     * @return array ['success' => bool, 'imageData' => string|null, 'newDimensions' => array]
     */
    private function redimensionarImagen(string $sourcePath, int $maxWidth, int $maxHeight): array
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new \Exception('No se pudieron obtener las dimensiones de la imagen');
            }
            
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Si la imagen ya es del tamaño correcto o menor, no redimensionar
            if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
                Log::info('✅ Imagen no requiere redimensionamiento', [
                    'original' => "{$originalWidth}x{$originalHeight}",
                    'maximo' => "{$maxWidth}x{$maxHeight}"
                ]);
                
                return [
                    'success' => true,
                    'imageData' => file_get_contents($sourcePath),
                    'newDimensions' => ['width' => $originalWidth, 'height' => $originalHeight],
                    'resized' => false
                ];
            }
            
            // Calcular nuevas dimensiones manteniendo aspect ratio
            $ratio = $originalWidth / $originalHeight;
            
            if ($originalWidth > $originalHeight) {
                $newWidth = $maxWidth;
                $newHeight = (int)($maxWidth / $ratio);
            } else {
                $newHeight = $maxHeight;
                $newWidth = (int)($maxHeight * $ratio);
            }
            
            // Crear imagen desde el archivo
            $sourceImage = null;
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    throw new \Exception('Formato de imagen no soportado: ' . $mimeType);
            }
            
            if (!$sourceImage) {
                throw new \Exception('No se pudo crear la imagen desde el archivo');
            }
            
            // Crear nueva imagen redimensionada
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preservar transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            // Redimensionar
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            
            // Guardar en buffer
            ob_start();
            imagejpeg($newImage, null, 95); // 95% de calidad
            $imageData = ob_get_clean();
            
            // Liberar memoria
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            
            Log::info('✅ Imagen redimensionada exitosamente', [
                'original' => "{$originalWidth}x{$originalHeight}",
                'nueva' => "{$newWidth}x{$newHeight}",
                'ratio' => round($ratio, 4)
            ]);
            
            return [
                'success' => true,
                'imageData' => $imageData,
                'newDimensions' => ['width' => $newWidth, 'height' => $newHeight],
                'resized' => true
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Error redimensionando imagen', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'imageData' => null,
                'newDimensions' => ['width' => 0, 'height' => 0],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Procesa imágenes a base64 para Veo2
     * Retorna array con 'base64' y 'mimeType' o null si no hay imágenes
     */
    private function procesarImagenesABase64ParaVeo2(): ?array
    {
        try {
            if ($this->fromHistory && $this->imageUrl) {
                // Para imágenes del historial, descargar desde S3 y convertir a base64
                Log::info('📥 Descargando imagen del historial para Veo2', [
                    'imageUrl' => $this->imageUrl
                ]);
                
                $imageContent = file_get_contents($this->imageUrl);
                if ($imageContent === false) {
                    throw new \Exception('No se pudo descargar la imagen del historial');
                }
                
                // Determinar MIME type basado en la extensión de la URL
                $mimeType = 'image/jpeg'; // Por defecto
                if (strpos($this->imageUrl, '.png') !== false) {
                    $mimeType = 'image/png';
                }
                
                $base64Image = base64_encode($imageContent);
                
                Log::info('✅ Imagen del historial convertida a base64 para Veo2', [
                    'imageSize' => strlen($imageContent),
                    'base64Size' => strlen($base64Image),
                    'mimeType' => $mimeType
                ]);
                
                return [
                    'base64' => $base64Image,
                    'mimeType' => $mimeType
                ];
                
            } elseif (!empty($this->imageFilesStart)) {
                // Para imágenes subidas, leer desde el archivo temporal
                $image = $this->imageFilesStart[0]; // Tomar solo la primera imagen
                
                try {
                    $imageContent = file_get_contents($image->getRealPath());
                    $base64Image = base64_encode($imageContent);
                    $mimeType = $image->getMimeType();
                    
                    Log::info("📷 Imagen convertida a base64 para Veo2", [
                        'imageSize' => strlen($imageContent),
                        'base64Size' => strlen($base64Image),
                        'mimeType' => $mimeType,
                        'fileName' => $image->getClientOriginalName()
                    ]);
                    
                    return [
                        'base64' => $base64Image,
                        'mimeType' => $mimeType
                    ];
                    
                } catch (\Exception $e) {
                    Log::warning("⚠️ Error procesando imagen para Veo2", [
                        'error' => $e->getMessage()
                    ]);
                    return null;
                }
            }
            
            Log::info("📷 No hay imágenes para Veo2");
            return null;

        } catch (\Exception $e) {
            Log::error("💥 Error en procesarImagenesABase64ParaVeo2", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Procesa y sube imágenes a S3 para uso en APIs de video
     * Retorna array de URLs de S3
     */
    private function procesarYSubirImagenesAS3(): array
    {
        try {
            $imagenesS3 = [];
            
            if ($this->fromHistory && $this->imageUrl) {
                // ✅ OPTIMIZACIÓN: Las imágenes del historial ya están en S3, reutilizarlas directamente
                Log::info('🚀 Reutilizando imagen del historial (ya en S3)', [
                    'imageUrl' => $this->imageUrl
                ]);
                
                $imagenesS3[] = $this->imageUrl; // Imagen del historial va como imagen de inicio
                
                Log::info('✅ URL del historial preparada para video', [
                    'imageUrl' => $this->imageUrl
                ]);
                
            } else {
                // Verificar si hay imágenes en imageFilesStart (imagen de inicio)
                if (!empty($this->imageFilesStart)) {
                    foreach ($this->imageFilesStart as $index => $image) {
                        try {
                            $imageContent = file_get_contents($image->getRealPath());
                            $fileName = 'genesis/input-videos/' . now()->format('Ymd_His') . '_runway_' . uniqid('img_' . $index . '_') . '.jpg';
                            
                            // Subir a S3
                            Storage::disk('s3')->put($fileName, $imageContent);
                            $url = Storage::disk('s3')->url($fileName);
                            
                            $imagenesS3[] = $url;
                            
                            Log::info("📤 Imagen subida a S3 para Runway", [
                                'index' => $index,
                                'fileName' => $fileName,
                                'url' => $url
                            ]);
                            
                        } catch (\Exception $e) {
                            Log::warning("⚠️ Error procesando imagen {$index} para Runway", [
                                'error' => $e->getMessage()
                            ]);
                            // Continuar con la siguiente imagen
                        }
                    }
                }
                
                // Verificar si hay imágenes en imageFilesEnd (imagen de fin)
                if (!empty($this->imageFilesEnd)) {
                    foreach ($this->imageFilesEnd as $index => $image) {
                        try {
                            $imageContent = file_get_contents($image->getRealPath());
                            $fileName = 'genesis/input-videos/' . now()->format('Ymd_His') . '_runway_' . uniqid('img_end_' . $index . '_') . '.jpg';
                            
                            // Subir a S3
                            Storage::disk('s3')->put($fileName, $imageContent);
                            $url = Storage::disk('s3')->url($fileName);
                            
                            $imagenesS3[] = $url;
                            
                            Log::info("📤 Imagen de fin subida a S3 para Runway", [
                                'index' => $index,
                                'fileName' => $fileName,
                                'url' => $url
                            ]);
                            
                        } catch (\Exception $e) {
                            Log::warning("⚠️ Error procesando imagen de fin {$index} para Runway", [
                                'error' => $e->getMessage()
                            ]);
                            // Continuar con la siguiente imagen
                        }
                    }
                }
            }

            Log::info("📊 Resumen de imágenes procesadas para video", [
                'totalImagenes' => count($imagenesS3),
                'fromHistory' => $this->fromHistory,
                'imagenes' => $imagenesS3
            ]);

            return $imagenesS3;

        } catch (\Exception $e) {
            Log::error("💥 Error en procesarYSubirImagenesAS3", [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Mapea los ratios de aspecto a los tamaños fijos que acepta Runway
     * 
     * @param string $ratio Ratio de aspecto (ej: '16:9', '9:16', '1:1')
     * @param string $modelo Modelo de Runway ('gen4_turbo' o 'gen3a_turbo')
     * @return string Ratio en formato Runway (ej: '1280:720')
     */
    private function mapearRatioARunway(string $ratio, string $modelo): string
    {
        Log::info("📐 Mapeando ratio a formato Runway", [
            'inputRatio' => $ratio,
            'modelo' => $modelo
        ]);

        // Definir mapeo según el modelo
        $mapeoRatios = [];
        
        if ($modelo === 'gen4_turbo') {
            // ✅ Gen4-Turbo: 6 ratios disponibles (según documentación oficial)
            $mapeoRatios = [
                '16:9' => '1280:720',   // Panorámico
                '9:16' => '720:1280',   // Vertical móvil
                '1:1' => '960:960',     // Cuadrado
                '4:3' => '1104:832',    // Horizontal
                '3:4' => '832:1104',    // Vertical
                '21:9' => '1584:672'    // Ultra panorámico
            ];
        } elseif ($modelo === 'gen3a_turbo') {
            // ✅ Gen3-AlphaTurbo: Solo 2 ratios disponibles (según documentación oficial)
            $mapeoRatios = [
                '16:9' => '1280:768',   // Panorámico
                '9:16' => '768:1280'    // Vertical móvil
            ];
        } else {
            // Fallback para modelos no reconocidos
            $mapeoRatios = [
                '16:9' => '1280:720',
                '9:16' => '720:1280',
                '1:1' => '960:960',
                '4:3' => '1104:832',
                '3:4' => '832:1104',
                '21:9' => '1584:672'
            ];
        }

        // Buscar el ratio mapeado
        $runwayRatio = $mapeoRatios[$ratio] ?? null;
        
        if ($runwayRatio === null) {
            // ❌ Ratio no soportado para este modelo
            $errorMessage = "El ratio '{$ratio}' no es soportado por el modelo {$modelo}";
            Log::error("❌ Ratio no soportado", [
                'ratio' => $ratio,
                'modelo' => $modelo,
                'ratiosDisponibles' => array_keys($mapeoRatios)
            ]);
            throw new \Exception($errorMessage);
        }

        Log::info("✅ Ratio mapeado a formato Runway", [
            'inputRatio' => $ratio,
            'outputRatio' => $runwayRatio,
            'modelo' => $modelo
        ]);

        return $runwayRatio;
    }

    /**
     * Mapea los ratios de aspecto al formato que acepta Sora (OpenAI)
     * Solo acepta 4 tamaños específicos según la documentación de OpenAI
     * 
     * @param string $ratio Ratio de aspecto (ej: '16:9', '9:16')
     * @return string Size en formato OpenAI (ej: '1280x720')
     */
    private function mapearRatioASora(string $ratio): string
    {
        Log::info("📐 Mapeando ratio a formato Sora", [
            'inputRatio' => $ratio
        ]);

        // Mapeo de ratios a tamaños de Sora
        // OpenAI solo acepta estos 4 tamaños exactos: 720x1280, 1280x720, 1024x1792, 1792x1024
        $mapeoRatios = [
            '16:9' => '1280x720',   // Panorámico HD (16:9)
            '9:16' => '720x1280',   // Vertical móvil HD (9:16)
        ];

        // Buscar el ratio mapeado
        $soraSize = $mapeoRatios[$ratio] ?? null;
        
        if ($soraSize === null) {
            // ❌ Ratio no soportado
            $errorMessage = "El ratio '{$ratio}' no es soportado por Sora. Solo se permiten 16:9 y 9:16";
            Log::error("❌ Ratio no soportado para Sora", [
                'ratio' => $ratio,
                'ratiosDisponibles' => array_keys($mapeoRatios)
            ]);
            throw new \Exception($errorMessage);
        }

        Log::info("✅ Ratio mapeado a formato Sora", [
            'inputRatio' => $ratio,
            'outputSize' => $soraSize
        ]);

        return $soraSize;
    }

    /**
     * Verifica el estado de generación de video (para modelos asíncronos)
     */
    #[On('verificarEstadoVideo')]
    public function verificarEstadoVideo($generationId, $prompt, $model, $ratio, $count): void
    {
        try {
            // ✅ Aumentar tiempo de ejecución para evitar timeout
            ini_set('max_execution_time', 120);
            
            Log::info('🔍 Verificando estado de video desde frontend', [
                'generationId' => $generationId,
                'model' => $model
            ]);
            
            $datos = [
                'generationId' => $generationId,
                'prompt' => $prompt,
                'model' => $model,
                'ratio' => $ratio,
                'count' => $count
            ];
            
            // ✅ Usar switch para delegar a métodos específicos por modelo
            switch ($model) {
                case 'veo2':
                    $this->verificarEstadoVeo2($generationId, $datos);
                    break;
                    
                case 'gen4_turbo':
                case 'gen3a_turbo':
                    $this->verificarEstadoRunway($generationId, $datos);
                    break;
                    
                case 'ray2':
                    $this->verificarEstadoRay2($generationId, $datos);
                    break;
                    
                case 'ray2-flash':
                    $this->verificarEstadoRay2Flash($generationId, $datos);
                    break;
                    
                case 'sora-2':
                case 'sora-2-pro':
                    $this->verificarEstadoSora($generationId, $datos);
                    break;
                    
                default:
                    $this->verificarEstadoGenerico($generationId, $datos);
                    break;
            }
            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            Log::error('💥 Error verificando estado de video', [
                'error' => $errorMessage,
                'model' => $model,
                'generationId' => $generationId
            ]);
            
            // ✅ Mostrar error en pantalla
            $this->addError('promptText', $errorMessage);
            
            // ✅ Emitir evento para agregar al historial de errores
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'generation', 
                tool: 'video-generator'
            );
            
            $this->isGenerating = false;
            $this->dispatch('videoGenerationError');
        }
    }

    /**
     * Verifica el estado específico de Veo2
     */
    private function verificarEstadoVeo2(string $generationId, array $datos): void
    {
        Log::info('🎬 Consultando estado de Veo2 con GeminiService', [
            'operationName' => $generationId
        ]);
        
        $result = GeminiService::getVideoOperation($generationId);
        
        Log::info('📊 Resultado de verificación Veo2', [
            'success' => $result['success'] ?? false,
            'done' => $result['done'] ?? false,
            'hasResponse' => isset($result['response']),
        ]);
        
        if (!($result['success'] ?? false)) {
            throw new \Exception('Error verificando estado Veo2: ' . ($result['error'] ?? 'Error desconocido'));
        }
        
        if ($result['done'] ?? false) {
            // ✅ VIDEO LISTO - Procesar resultado
            Log::info('✅ Video Veo2 completado', ['id' => $generationId]);
            $this->procesarVideoVeo2($result['response'], $datos);
        } else {
            // ⏳ AÚN PENDIENTE - EMITIR AL FRONTEND PARA NUEVO DELAY
            Log::info('⏳ Video Veo2 aún pendiente', ['id' => $generationId]);
            $this->dispatch('videoStillPending', 
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $datos['model'],
                ratio: $datos['ratio'],
                count: $datos['count']
            );
        }
    }

    /**
     * Verifica el estado específico de modelos Runway
     */
    private function verificarEstadoRunway(string $generationId, array $datos): void
    {
        Log::info('🎬 Consultando estado de Runway', [
            'taskId' => $generationId,
            'model' => $datos['model']
        ]);
        
        $result = \App\Services\RunWayService::checkVideoGenerationStatus($generationId);
        
        Log::info('📊 Resultado de verificación Runway', [
            'success' => $result['success'] ?? false,
            'hasData' => isset($result['data']),
            'dataKeys' => array_keys($result['data'] ?? []),
        ]);
        
        if (!($result['success'] ?? false)) {
            throw new \Exception('Error verificando estado de Runway: ' . ($result['error'] ?? 'Error desconocido'));
        }
        
        // La respuesta real está en result['data']
        $taskData = $result['data'] ?? [];
        $taskStatus = $taskData['status'] ?? 'unknown';
        
        Log::info('📊 Estado de la tarea Runway', [
            'status' => $taskStatus,
            'hasOutput' => isset($taskData['output']),
            'outputCount' => count($taskData['output'] ?? [])
        ]);
        
        if ($taskStatus === 'SUCCEEDED') {
            // ✅ VIDEO LISTO - Procesar resultado
            Log::info('✅ Video Runway completado', ['id' => $generationId]);
            $this->procesarVideoRunway($taskData, $datos);
        } elseif (in_array($taskStatus, ['PENDING', 'RUNNING'])) {
            // ⏳ AÚN PENDIENTE - EMITIR AL FRONTEND PARA NUEVO DELAY
            Log::info('⏳ Video Runway aún pendiente', [
                'id' => $generationId,
                'status' => $taskStatus
            ]);
            $this->dispatch('videoStillPending', 
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $datos['model'],
                ratio: $datos['ratio'],
                count: $datos['count']
            );
        } else {
            // ❌ ERROR O ESTADO DESCONOCIDO
            Log::error('❌ Video Runway falló o estado desconocido', [
                'id' => $generationId,
                'status' => $taskStatus
            ]);
            throw new \Exception('Estado desconocido de Runway: ' . $taskStatus);
        }
    }

    /**
     * Verifica el estado específico de Ray2
     */
    private function verificarEstadoRay2(string $generationId, array $datos): void
    {
        Log::info('🎬 Consultando estado de Ray2', [
            'taskId' => $generationId,
            'model' => $datos['model']
        ]);
        
        $result = \App\Services\LumaService::getGenerationStatusById($generationId);
        
        Log::info('📊 Resultado de verificación Ray2', [
            'success' => $result['success'] ?? false,
            'hasData' => isset($result['data']),
            'state' => $result['data']['state'] ?? 'unknown'
        ]);
        
        if (!($result['success'] ?? false)) {
            throw new \Exception('Error verificando estado de Ray2: ' . ($result['error'] ?? 'Error desconocido'));
        }
        
        $taskData = $result['data'] ?? [];
        $taskState = $taskData['state'] ?? 'unknown';
        
        if ($taskState === 'completed') {
            // ✅ VIDEO LISTO - Procesar resultado
            Log::info('✅ Video Ray2 completado', ['id' => $generationId]);
            $this->procesarVideoLuma($taskData, $datos);
        } elseif (in_array($taskState, ['pending', 'dreaming', 'queued'])) {
            // ⏳ AÚN PENDIENTE - EMITIR AL FRONTEND PARA NUEVO DELAY
            Log::info('⏳ Video Ray2 aún pendiente', [
                'id' => $generationId,
                'state' => $taskState
            ]);
            $this->dispatch('videoStillPending', 
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $datos['model'],
                ratio: $datos['ratio'],
                count: $datos['count']
            );
        } elseif ($taskState === 'failed') {
            // ❌ ERROR EN LA GENERACIÓN
            $failureReason = $taskData['failure_reason'] ?? 'Razón desconocida';
            Log::error('❌ Video Ray2 falló', [
                'id' => $generationId,
                'state' => $taskState,
                'failure_reason' => $failureReason
            ]);
            throw new \Exception('La generación de Ray2 falló: ' . $failureReason);
        } else {
            // ❌ ESTADO DESCONOCIDO
            Log::error('❌ Video Ray2 estado desconocido', [
                'id' => $generationId,
                'state' => $taskState
            ]);
            throw new \Exception('Estado desconocido de Ray2: ' . $taskState);
        }
    }

    /**
     * Verifica el estado específico de Ray2-Flash
     */
    private function verificarEstadoRay2Flash(string $generationId, array $datos): void
    {
        Log::info('🎬 Consultando estado de Ray2-Flash', [
            'taskId' => $generationId,
            'model' => $datos['model']
        ]);
        
        $result = \App\Services\LumaService::getGenerationStatusById($generationId);
        
        Log::info('📊 Resultado de verificación Ray2-Flash', [
            'success' => $result['success'] ?? false,
            'hasData' => isset($result['data']),
            'state' => $result['data']['state'] ?? 'unknown'
        ]);
        
        if (!($result['success'] ?? false)) {
            throw new \Exception('Error verificando estado de Ray2-Flash: ' . ($result['error'] ?? 'Error desconocido'));
        }
        
        $taskData = $result['data'] ?? [];
        $taskState = $taskData['state'] ?? 'unknown';
        
        if ($taskState === 'completed') {
            // ✅ VIDEO LISTO - Procesar resultado
            Log::info('✅ Video Ray2-Flash completado', ['id' => $generationId]);
            $this->procesarVideoLuma($taskData, $datos);
        } elseif (in_array($taskState, ['pending', 'dreaming', 'queued'])) {
            // ⏳ AÚN PENDIENTE - EMITIR AL FRONTEND PARA NUEVO DELAY
            Log::info('⏳ Video Ray2-Flash aún pendiente', [
                'id' => $generationId,
                'state' => $taskState
            ]);
            $this->dispatch('videoStillPending', 
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $datos['model'],
                ratio: $datos['ratio'],
                count: $datos['count']
            );
        } elseif ($taskState === 'failed') {
            // ❌ ERROR EN LA GENERACIÓN
            $failureReason = $taskData['failure_reason'] ?? 'Razón desconocida';
            Log::error('❌ Video Ray2-Flash falló', [
                'id' => $generationId,
                'state' => $taskState,
                'failure_reason' => $failureReason
            ]);
            throw new \Exception('La generación de Ray2-Flash falló: ' . $failureReason);
        } else {
            // ❌ ESTADO DESCONOCIDO
            Log::error('❌ Video Ray2-Flash estado desconocido', [
                'id' => $generationId,
                'state' => $taskState
            ]);
            throw new \Exception('Estado desconocido de Ray2-Flash: ' . $taskState);
        }
    }

    /**
     * Verifica el estado específico de Sora (OpenAI)
     */
    private function verificarEstadoSora(string $generationId, array $datos): void
    {
        // ✅ Aumentar tiempo de ejecución para consultas a OpenAI
        ini_set('max_execution_time', 120);
        
        Log::info('🎬 Consultando estado de Sora', [
            'videoId' => $generationId,
            'model' => $datos['model']
        ]);
        
        $result = \App\Services\OpenAiService::getVideoStatus($generationId);
        
        Log::info('📊 Resultado de verificación Sora', [
            'hasError' => isset($result['error']),
            'status' => $result['status'] ?? 'unknown',
            'progress' => $result['progress'] ?? 0,
            'fullResult' => $result
        ]);
        
        // ❌ Verificar si hay error en la respuesta de la API
        if (isset($result['error'])) {
            // El error viene del servicio (error de comunicación o de la API)
            throw new \Exception($result['error']);
        }
        
        $videoStatus = $result['status'] ?? 'unknown';
        
        if ($videoStatus === 'completed') {
            // ✅ VIDEO LISTO - Procesar resultado
            Log::info('✅ Video Sora completado', ['id' => $generationId]);
            $this->procesarVideoSora($result, $datos);
        } elseif (in_array($videoStatus, ['queued', 'processing', 'in_progress'])) {
            // ⏳ AÚN PENDIENTE - EMITIR AL FRONTEND PARA NUEVO DELAY
            Log::info('⏳ Video Sora aún pendiente', [
                'id' => $generationId,
                'status' => $videoStatus,
                'progress' => $result['progress'] ?? 0
            ]);
            $this->dispatch('videoStillPending', 
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $datos['model'],
                ratio: $datos['ratio'],
                count: $datos['count']
            );
        } elseif ($videoStatus === 'failed') {
            // ❌ ERROR EN LA GENERACIÓN
            // OpenAI puede devolver el mensaje de error en diferentes lugares
            $errorMessage = 'La generación falló';
            
            // Buscar el mensaje de error en diferentes ubicaciones
            if (isset($result['error']) && is_string($result['error'])) {
                $errorMessage = $result['error'];
            } elseif (isset($result['error']['message'])) {
                $errorMessage = $result['error']['message'];
            }
            
            Log::error('❌ Video Sora falló', [
                'id' => $generationId,
                'status' => $videoStatus,
                'error' => $errorMessage,
                'fullResult' => $result
            ]);
            
            throw new \Exception($errorMessage);
        } else {
            // ❌ ESTADO DESCONOCIDO
            Log::error('❌ Video Sora estado desconocido', [
                'id' => $generationId,
                'status' => $videoStatus
            ]);
            throw new \Exception('Estado desconocido: ' . $videoStatus);
        }
    }

    /**
     * Verifica el estado genérico para modelos no reconocidos
     */
    private function verificarEstadoGenerico(string $generationId, array $datos): void
    {
        Log::info('⏳ Video aún pendiente (modelo no implementado)', [
            'id' => $generationId,
            'model' => $datos['model']
        ]);
        
        $this->dispatch('videoStillPending', 
            generationId: $datos['generationId'],
            prompt: $datos['prompt'],
            model: $datos['model'],
            ratio: $datos['ratio'],
            count: $datos['count']
        );
    }

    /**
     * Procesa un video completado de Veo2
     */
    private function procesarVideoVeo2(array $response, array $datos): void
    {
        try {
            Log::info('🎬 Procesando video Veo2 completado', [
                'hasResponse' => isset($response['response']),
                'hasGenerateVideoResponse' => isset($response['response']['generateVideoResponse']),
                'hasGeneratedSamples' => isset($response['response']['generateVideoResponse']['generatedSamples'])
            ]);
            
            // Verificar si hay videos generados en la respuesta
            if (!isset($response['response']['generateVideoResponse']['generatedSamples'])) {
                throw new \Exception('No se encontraron videos en la respuesta de Veo2');
            }
            
            $generatedSamples = $response['response']['generateVideoResponse']['generatedSamples'];
            $totalSamples = count($generatedSamples);
            
            Log::info("📹 Encontrados {$totalSamples} video(s) en la respuesta de Veo2");
            
            $videos = [];
            $processedCount = 0;
            
            foreach ($generatedSamples as $index => $sample) {
                if (isset($sample['video']['uri'])) {
                    $videoUrl = $sample['video']['uri'];
                    
                    try {
                        // Descargar el video desde la URL de Gemini
                        Log::info("📥 Descargando video #{$index} desde Gemini", ['url' => $videoUrl]);
                        $videoContent = file_get_contents($videoUrl);
                        
                        if ($videoContent === false) {
                            Log::warning("⚠️ No se pudo descargar el video #{$index}", ['url' => $videoUrl]);
                            continue;
                        }
                        
                        // Guardar en S3
                        $fileName = 'genesis/output-videos/' . now()->format('Ymd_His') . '_veo2_' . uniqid('video_') . '.mp4';
                        Storage::disk('s3')->put($fileName, $videoContent);
                        $finalUrl = Storage::disk('s3')->url($fileName);
                        
                        Log::info("💾 Video #{$index} guardado en S3", [
                            'fileName' => $fileName,
                            'finalUrl' => $finalUrl,
                            'size' => strlen($videoContent)
                        ]);
                        
                        // Crear datos del video con URL de S3
                        $videoData = [
                            'url' => $finalUrl,
                            'model' => $datos['model'],
                            'ratio' => $datos['ratio'],
                        ];
                        
                        $this->results[] = $videoData;
                        $videos[] = $videoData;
                        $processedCount++;
                        
                        Log::info("✅ Video Veo2 #{$index} procesado y subido a S3", [
                            'originalUrl' => $videoUrl,
                            's3Url' => $finalUrl,
                            'index' => $index + 1,
                            'total' => $totalSamples,
                            'processed' => $processedCount
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error("❌ Error procesando video #{$index}", [
                            'error' => $e->getMessage(),
                            'url' => $videoUrl
                        ]);
                        // Continuar con el siguiente video
                        continue;
                    }
                } else {
                    Log::warning("⚠️ Sample #{$index} no tiene URI de video válida", ['sample' => $sample]);
                }
            }
            
            if (!empty($videos)) {
                $videoCount = count($videos);
                
                Log::info("🎬 Preparando para agregar {$videoCount} video(s) al historial", [
                    'videos' => $videos,
                    'prompt' => $datos['prompt'],
                    'model' => $datos['model']
                ]);
                
                // Disparar evento de finalización
                $this->dispatch('addToHistory', 
                    type: 'video/generate', 
                    images: $videos, // Reutilizamos 'images' para compatibilidad
                    generationId: $datos['generationId'],
                    prompt: $datos['prompt'],
                    model: $this->getModelDisplayName($datos['model']),
                    ratio: $datos['ratio'],
                    count: $videoCount
                );
                
                $this->dispatch('videoGenerationCompleted');
                Log::info("🎉 {$videoCount} video(s) Veo2 agregados exitosamente al historial", [
                    'count' => $videoCount,
                    'generationId' => $datos['generationId']
                ]);
            } else {
                throw new \Exception('No se pudieron procesar los videos de Veo2');
            }
            
        } catch (\Exception $e) {
            $errorMessage = 'Error procesando video Veo2: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Procesa un video completado de Luma
     */
    private function procesarVideoLuma(array $response, array $datos): void
    {
        try {
            Log::info('🎬 Procesando video Luma completado', [
                'hasAssets' => isset($response['assets']),
                'hasVideo' => isset($response['assets']['video']),
                'model' => $datos['model']
            ]);
            
            // Verificar si hay video en la respuesta
            if (!isset($response['assets']['video']) || empty($response['assets']['video'])) {
                throw new \Exception('No se encontró video en la respuesta de Luma');
            }
            
            $videoUrl = $response['assets']['video'];
            
            Log::info("📹 Encontrado video de Luma", ['url' => $videoUrl]);
            
            try {
                // Descargar el video desde la URL de Luma
                Log::info("📥 Descargando video desde Luma", ['url' => $videoUrl]);
                $videoContent = file_get_contents($videoUrl);
                
                if ($videoContent === false) {
                    Log::warning("⚠️ No se pudo descargar el video", ['url' => $videoUrl]);
                    throw new \Exception('No se pudo descargar el video de Luma');
                }
                
                // Guardar en S3
                $fileName = 'genesis/output-videos/' . now()->format('Ymd_His') . '_luma_' . uniqid('video_') . '.mp4';
                Storage::disk('s3')->put($fileName, $videoContent);
                $finalUrl = Storage::disk('s3')->url($fileName);
                
                Log::info("💾 Video guardado en S3", [
                    'fileName' => $fileName,
                    'finalUrl' => $finalUrl,
                    'size' => strlen($videoContent)
                ]);
                
                // Crear datos del video con URL de S3
                $videoData = [
                    'url' => $finalUrl,
                    'model' => $datos['model'],
                    'ratio' => $datos['ratio'],
                ];
                
                $this->results[] = $videoData;
                
                Log::info("✅ Video Luma procesado y subido a S3", [
                    'originalUrl' => $videoUrl,
                    's3Url' => $finalUrl
                ]);
                
            } catch (\Exception $e) {
                Log::error("❌ Error procesando video de Luma", [
                    'error' => $e->getMessage(),
                    'url' => $videoUrl
                ]);
                // Si falla la descarga/subida, usar la URL original
                $videoData = [
                    'url' => $videoUrl,
                    'model' => $datos['model'],
                    'ratio' => $datos['ratio'],
                ];
                $this->results[] = $videoData;
                $finalUrl = $videoUrl;
            }
            
            Log::info("🎬 Preparando para agregar video al historial", [
                'video' => $videoData,
                'prompt' => $datos['prompt'],
                'model' => $datos['model']
            ]);
            
            // Disparar evento de finalización
            $this->dispatch('addToHistory', 
                type: 'video/generate', 
                images: [$videoData], // Reutilizamos 'images' para compatibilidad
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $this->getModelDisplayName($datos['model']),
                ratio: $datos['ratio'],
                count: 1
            );
            
            $this->dispatch('videoGenerationCompleted');
            Log::info("🎉 Video Luma agregado exitosamente al historial", [
                'generationId' => $datos['generationId']
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error procesando video Luma: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Procesa un video completado de Runway
     */
    private function procesarVideoRunway(array $response, array $datos): void
    {
        try {
            Log::info('🎬 Procesando video Runway completado', [
                'hasOutput' => isset($response['output']),
                'outputCount' => count($response['output'] ?? []),
                'model' => $datos['model']
            ]);
            
            // Verificar si hay videos generados en la respuesta
            if (!isset($response['output']) || empty($response['output'])) {
                throw new \Exception('No se encontraron videos en la respuesta de Runway');
            }
            
            $outputUrls = $response['output'];
            $totalVideos = count($outputUrls);
            
            Log::info("📹 Encontrados {$totalVideos} video(s) en la respuesta de Runway");
            
            $videos = [];
            $processedCount = 0;
            
            foreach ($outputUrls as $index => $videoUrl) {
                try {
                    // Descargar el video desde la URL de Runway
                    Log::info("📥 Descargando video #{$index} desde Runway", ['url' => $videoUrl]);
                    $videoContent = file_get_contents($videoUrl);
                    
                    if ($videoContent === false) {
                        Log::warning("⚠️ No se pudo descargar el video #{$index}", ['url' => $videoUrl]);
                        continue;
                    }
                    
                    // Guardar en S3
                    $fileName = 'genesis/output-videos/' . now()->format('Ymd_His') . '_runway_' . uniqid('video_') . '.mp4';
                    Storage::disk('s3')->put($fileName, $videoContent);
                    $finalUrl = Storage::disk('s3')->url($fileName);
                    
                    Log::info("💾 Video #{$index} guardado en S3", [
                        'fileName' => $fileName,
                        'finalUrl' => $finalUrl,
                        'size' => strlen($videoContent)
                    ]);
                    
                    // Crear datos del video con URL de S3
                    $videoData = [
                        'url' => $finalUrl,
                        'model' => $datos['model'],
                        'ratio' => $datos['ratio'],
                    ];
                    
                    $this->results[] = $videoData;
                    $videos[] = $videoData;
                    $processedCount++;
                    
                    Log::info("✅ Video Runway #{$index} procesado y subido a S3", [
                        'originalUrl' => $videoUrl,
                        's3Url' => $finalUrl,
                        'index' => $index + 1,
                        'total' => $totalVideos,
                        'processed' => $processedCount
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error("❌ Error procesando video #{$index}", [
                        'error' => $e->getMessage(),
                        'url' => $videoUrl
                    ]);
                    // Continuar con el siguiente video
                    continue;
                }
            }
            
            if (!empty($videos)) {
                $videoCount = count($videos);
                
                Log::info("🎬 Preparando para agregar {$videoCount} video(s) al historial", [
                    'videos' => $videos,
                    'prompt' => $datos['prompt'],
                    'model' => $datos['model']
                ]);
                
                // Disparar evento de finalización
                $this->dispatch('addToHistory', 
                    type: 'video/generate', 
                    images: $videos, // Reutilizamos 'images' para compatibilidad
                    generationId: $datos['generationId'],
                    prompt: $datos['prompt'],
                    model: $this->getModelDisplayName($datos['model']),
                    ratio: $datos['ratio'],
                    count: $videoCount
                );
                
                $this->dispatch('videoGenerationCompleted');
                Log::info("🎉 {$videoCount} video(s) Runway agregados exitosamente al historial", [
                    'count' => $videoCount,
                    'generationId' => $datos['generationId']
                ]);
            } else {
                throw new \Exception('No se pudieron procesar los videos de Runway');
            }
            
        } catch (\Exception $e) {
            $errorMessage = 'Error procesando video Runway: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Procesa un video completado de Sora (OpenAI)
     */
    private function procesarVideoSora(array $response, array $datos): void
    {
        try {
            // ✅ Aumentar tiempo de ejecución para descarga y procesamiento de video
            ini_set('max_execution_time', 300);
            
            Log::info('🎬 Procesando video Sora completado', [
                'videoId' => $response['id'] ?? 'unknown',
                'status' => $response['status'] ?? 'unknown',
                'model' => $datos['model']
            ]);
            
            // Obtener el ID del video
            $videoId = $response['id'] ?? null;
            if (!$videoId) {
                throw new \Exception('No se encontró ID de video en la respuesta de Sora');
            }
            
            // Obtener el contenido binario del video
            Log::info("📥 Descargando contenido binario del video Sora", ['videoId' => $videoId]);
            $contentResult = \App\Services\OpenAiService::getVideoContent($videoId);
            
            if (isset($contentResult['error'])) {
                throw new \Exception('Error al obtener contenido del video: ' . $contentResult['error']);
            }
            
            if (!isset($contentResult['binary']) || empty($contentResult['binary'])) {
                throw new \Exception('No se recibió contenido binario del video');
            }
            
            $videoBinary = $contentResult['binary'];
            $videoSize = strlen($videoBinary);
            
            Log::info("✅ Contenido binario descargado exitosamente", [
                'videoId' => $videoId,
                'size' => $videoSize,
                'sizeKB' => round($videoSize / 1024, 2),
                'sizeMB' => round($videoSize / 1024 / 1024, 2)
            ]);
            
            // Subir a S3
            $fileName = 'genesis/output-videos/' . now()->format('Ymd_His') . '_sora_' . uniqid('video_') . '.mp4';
            Storage::disk('s3')->put($fileName, $videoBinary);
            $finalUrl = Storage::disk('s3')->url($fileName);
            
            Log::info("💾 Video Sora guardado en S3", [
                'fileName' => $fileName,
                'finalUrl' => $finalUrl,
                'size' => $videoSize
            ]);
            
            // Crear datos del video con URL de S3
            $videoData = [
                'url' => $finalUrl,
                'model' => $datos['model'],
                'ratio' => $datos['ratio'],
            ];
            
            $this->results[] = $videoData;
            
            Log::info("✅ Video Sora procesado y subido a S3", [
                's3Url' => $finalUrl,
                'videoId' => $videoId
            ]);
            
            Log::info("🎬 Preparando para agregar video Sora al historial", [
                'video' => $videoData,
                'prompt' => $datos['prompt'],
                'model' => $datos['model']
            ]);
            
            // Disparar evento de finalización
            $this->dispatch('addToHistory', 
                type: 'video/generate', 
                images: [$videoData], // Reutilizamos 'images' para compatibilidad
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $this->getModelDisplayName($datos['model']),
                ratio: $datos['ratio'],
                count: 1
            );
            
            $this->dispatch('videoGenerationCompleted');
            Log::info("🎉 Video Sora agregado exitosamente al historial", [
                'generationId' => $datos['generationId']
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error procesando video Sora: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            Log::error('❌ Error procesando video Sora', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Procesa un video completado (método genérico para compatibilidad)
     */
    private function procesarVideo(string $videoUrl, array $datos): void
    {
        try {
            // TODO: Implementar lógica de procesamiento de video genérico
            // Por ahora simulamos el flujo, pero cuando se implemente también subirá a S3
            
            Log::info('🎬 Procesando video genérico', [
                'url' => $videoUrl,
                'model' => $datos['model']
            ]);
            
            // TODO: Aquí se implementará la descarga y subida a S3 como en procesarVideoVeo2
            // Por ahora usamos la URL original
            
            $videoData = [
                'url' => $videoUrl,
                'model' => $datos['model'],
                'ratio' => $datos['ratio'],
            ];
            
            $this->results[] = $videoData;

            // Disparar evento de finalización
            $generationId = uniqid('gen_video_');
            $this->dispatch('addToHistory', 
                type: 'video/generate', 
                images: [$videoData], // Reutilizamos 'images' para compatibilidad
                generationId: $generationId,
                prompt: $datos['prompt'],
                model: $this->getModelDisplayName($datos['model']),
                ratio: $datos['ratio'],
                count: 1 
            );
            
            $this->dispatch('videoGenerationCompleted');
            
        } catch (\Exception $e) {
            $errorMessage = 'Error procesando video: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-generator'
            );
            
            $this->dispatch('videoGenerationError');
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Cargar prompt desde el historial para generación de video
     */
    #[On('loadPromptForVideoGeneration')]
    public function loadPromptFromHistory($prompt = null)
    {
        if ($prompt) {
            $this->promptText = $prompt;
            
            Log::info('📝 Prompt cargado desde historial para video', [
                'prompt' => substr($prompt, 0, 100) . '...',
                'length' => strlen($prompt)
            ]);
            
            // Forzar actualización del componente
            $this->dispatch('$refresh');
        }
    }

    public function render()
    {
        return view('livewire.generador.herramientas.video-generator');
    }
}

