<?php

namespace App\Livewire\Generador\Herramientas;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use App\Services\RunWayService;

/**
 * Editor de Videos
 *
 * Permite transformar videos existentes usando diferentes modelos de IA.
 * Estructura modular y escalable para agregar nuevos modelos fácilmente.
 */
class VideoEditor extends Component
{
    use WithFileUploads;

    /** Video a editar */
    public $videoFile = null;
    public $videoUrl = null;

    /** Texto del prompt para la transformación */
    public string $promptText = '';

    /** Modelo de edición seleccionado */
    public string $model = 'gen4_aleph';

    /** Configuración de transformación */
    public string $ratio = '1280:720';
    public int $duration = 5;

    /** Estados de procesamiento */
    public bool $isGenerating = false;
    public bool $isUploading = false;

    /** Resultados de edición */
    public array $results = [];

    /** Indicador si el video viene del historial */
    public bool $fromHistory = false;
    
    /** Metadata del video del historial */
    public array $historyMetadata = [];

    /** Catálogo de modelos disponibles para edición */
    public array $availableModels = [
        'gen4_aleph' => [
            'name' => 'Gen4-Aleph',
            'price' => '$0.10',
            'priceUnit' => 'por segundo',
            'description' => 'Modelo avanzado para transformaciones de video de alta calidad',
            'bestFor' => 'Ediciones profesionales, efectos complejos, cambios de estilo',
            'speed' => 'Medio',
            'quality' => 'Excelente'
        ]
    ];

    /** Ratios disponibles para edición según el modelo */
    public array $availableRatios = [
        '1280:720' => '16:9 Horizontal',
        '720:1280' => '9:16 Vertical',
        '1104:832' => '4:3 Horizontal',
        '832:1104' => '3:4 Vertical',
        '960:960' => '1:1 Cuadrado',
        '1584:672' => '21:9 Ultra panorámico',
        '848:480' => '16:9 Compacto',
        '640:480' => '4:3 Clásico'
    ];

    /** Duraciones disponibles */
    public array $availableDurations = [
        5 => '5 segundos'
    ];

    /**
     * Obtiene los ratios disponibles según el modelo seleccionado
     */
    public function getAvailableRatiosForModel(): array
    {
        // Por ahora todos los modelos soportan todos los ratios
        // En el futuro se puede filtrar según el modelo
        return $this->availableRatios;
    }

    /**
     * Obtiene las duraciones disponibles según el modelo seleccionado
     */
    public function getAvailableDurationsForModel(): array
    {
        // Por ahora todos los modelos soportan las mismas duraciones
        return $this->availableDurations;
    }

    /**
     * Obtiene el nombre amigable del modelo
     */
    private function getModelDisplayName($modelKey): string
    {
        return $this->availableModels[$modelKey]['name'] ?? $modelKey;
    }

    #[On('video-editor-model-selected')]
    public function updateModel($key)
    {
        $this->model = $key;
        Log::info('🎯 Modelo de editor actualizado', [
            'newModel' => $key,
            'currentModel' => $this->model
        ]);
        
        // Validar que el ratio actual sea compatible con el nuevo modelo
        $this->validarRatioCompatible();
    }

    /**
     * Valida que el ratio seleccionado sea compatible con el modelo actual
     */
    private function validarRatioCompatible(): void
    {
        $ratiosDisponibles = $this->getAvailableRatiosForModel();
        
        if (!array_key_exists($this->ratio, $ratiosDisponibles)) {
            // Cambiar al primer ratio disponible
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
     * Validación personalizada por modelo
     */
    private function validarPorModelo(): bool
    {
        // Limpiar errores previos
        $this->resetErrorBag();
        
        $hasErrors = false;
        $errorMessage = '';
        
        // Validar que hay un video seleccionado (subido o del historial)
        if (!$this->videoFile && !$this->videoUrl) {
            $errorMessage = 'Es necesario seleccionar un video para editar o elegir uno del historial.';
            $this->addError('videoFile', $errorMessage);
            $hasErrors = true;
        }
        
        // Validar que hay un prompt
        if (empty(trim($this->promptText))) {
            $errorMessage = 'Es necesario escribir un prompt que describa la transformación deseada.';
            $this->addError('promptText', $errorMessage);
            $hasErrors = true;
        }
        
        if ($hasErrors) {
            // Enviar error al componente principal
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'validation', 
                tool: 'video-editor'
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
            'hasVideo' => !empty($this->videoFile) || !empty($this->videoUrl),
            'fromHistory' => $this->fromHistory
        ]);
        
        return true;
    }

    /**
     * Listener para cargar video desde el historial para edición
     */
    #[On('loadVideoFromHistory')]
    public function loadVideoFromHistory($videoUrl, $generationId, $originalModel, $originalRatio): void
    {
        try {
            Log::info('🎬 Cargando video del historial para edición', [
                'videoUrl' => $videoUrl,
                'generationId' => $generationId,
                'originalModel' => $originalModel,
                'originalRatio' => $originalRatio
            ]);

            // Limpiar video previo
            $this->quitarVideo();
            
            // ✅ SIMPLIFICACIÓN: Usar la misma lógica que los videos subidos
            // Simulamos que es un video "subido" pero desde el historial
            $this->videoUrl = $videoUrl;
            $this->fromHistory = true;
            $this->historyMetadata = [
                'videoUrl' => $videoUrl,
                'generationId' => $generationId,
                'originalModel' => $originalModel,
                'originalRatio' => $originalRatio
            ];
            
            // Configurar el ratio basado en el video original
            if ($originalRatio && isset($this->availableRatios[$originalRatio])) {
                $this->ratio = $originalRatio;
            }
            
            // Dispatch el mismo evento que los videos subidos para compatibilidad
            $this->dispatch('videoLoadedForEditing', url: $this->videoUrl);
            
            Log::info('✅ Video del historial cargado exitosamente para edición');
            
        } catch (\Exception $e) {
            Log::error('❌ Error cargando video del historial para edición: ' . $e->getMessage());
            
            $this->dispatch('addErrorToList', 
                message: 'Error al cargar el video del historial para edición: ' . $e->getMessage(), 
                type: 'system', 
                tool: 'video-editor'
            );
        }
    }

    public function mount()
    {
        // Inicialización del componente
        Log::info('🎬 VideoEditor component mounted');
        
        // Notificar que el componente está listo
        $this->dispatch('videoEditorReady');
    }

    /**
     * Observer para cuando se selecciona un video
     */
    public function updatedVideoFile()
    {
        if ($this->videoFile) {
            // 🔄 LIMPIAR VIDEO DEL HISTORIAL si se sube video manualmente
            if ($this->fromHistory) {
                Log::info('🧹 Limpiando video del historial al subir video manual');
                $this->fromHistory = false;
                $this->historyMetadata = [];
            }
            
            $this->videoUrl = null; // Limpiar URL previa
            Log::info("Video seleccionado para editar", [
                'filename' => $this->videoFile->getClientOriginalName(),
                'size' => $this->videoFile->getSize()
            ]);
            
            // Iniciar subida automática
            $this->dispatch('iniciarSubidaVideo');
        }
    }

    /**
     * Subir video a S3 para procesamiento
     */
    #[On('iniciarSubidaVideo')]
    public function subirVideoAS3(): void
    {
        if (!$this->videoFile) {
            $this->addError('videoFile', 'No hay video seleccionado para subir');
            return;
        }
        
        try {
            $this->isUploading = true;
            
            Log::info("Iniciando subida de video a S3");
            
            // Generar nombre único
            $fileName = 'genesis/input-videos/' . now()->format('Ymd_His') . '_editor_' . uniqid() . '.' . $this->videoFile->getClientOriginalExtension();
            
            // Subir a S3
            $videoContent = file_get_contents($this->videoFile->getRealPath());
            Storage::disk('s3')->put($fileName, $videoContent);
            
            // Construir la URL pública del archivo
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            $customBaseUrl = config('filesystems.disks.s3.url');
            $baseUrl = $customBaseUrl ?: "https://{$bucket}.s3.{$region}.amazonaws.com";
            $this->videoUrl = rtrim($baseUrl, '/') . "/{$fileName}";
            
            Log::info("Video subido exitosamente a S3", [
                'fileName' => $fileName,
                'url' => $this->videoUrl,
                'size' => strlen($videoContent)
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error al subir el video: ' . $e->getMessage();
            $this->addError('videoFile', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'upload', 
                tool: 'video-editor'
            );
            
            Log::error('Error subiendo video a S3: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    /**
     * Quitar video seleccionado
     */
    public function quitarVideo(): void
    {
        $this->videoFile = null;
        $this->videoUrl = null;
        
        // Limpiar datos del historial
        $this->fromHistory = false;
        $this->historyMetadata = [];
        
        $this->resetErrorBag(['videoFile']);
        
        Log::info("Video removido del editor");
    }



    /**
     * Método principal para procesar/editar el video
     */
    public function processVideo(): void
    {
        // Validación personalizada por modelo
        if (!$this->validarPorModelo()) {
            return; // No continuar si hay errores de validación
        }
        
        // Activar inmediatamente el spinner
        $this->isGenerating = true;
        $this->results = [];
        
        // Disparar evento para mostrar spinner en frontend
        $this->dispatch('videoEditStarted');
        
        // Disparar evento para iniciar edición real (con delay)
        $this->dispatch('startVideoEditing', [
            'prompt' => $this->promptText,
            'model' => $this->model,
            'ratio' => $this->ratio,
            'duration' => $this->duration,
            'videoUrl' => $this->videoUrl
        ]);
    }

    /**
     * Método que hace la edición real
     */
    #[On('startVideoEditing')]
    public function executeEditing($data): void
    {
        try {
            // Delegar según el modelo
            switch ($data['model']) {
                case 'gen4_aleph':
                    $this->editarConGen4Aleph($data);
                    break;
                default:
                    throw new \Exception('Modelo no soportado para edición');
            }

        } catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            // Enviar error al componente principal
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-editor'
            );
            
            $this->dispatch('videoEditError');
            $this->isGenerating = false;
        }
    }

    /**
     * Editar video con Gen4-Aleph (Runway)
     */
    private function editarConGen4Aleph($data): void
    {
        try {
        //     dd($data);
        //     if($data){
        //           // Disparar evento para iniciar polling
        //     $this->dispatch('videoEditTaskStarted', 
        //     generationId: "33c53e6a-6825-455b-ae9c-6960d0c28f1a",
        //     prompt: $data['prompt'],
        //     model: $data['model'],
        //     ratio: $data['ratio'],
        //     count: 1 // Para compatibilidad
        // );
        //         return;
        //     }
            Log::info('🚀 Iniciando edición con Gen4-Aleph', $data);
            
            // Llamar al servicio Runway para transformación de video
            $response = RunWayService::generateVideoFromVideo(
                $data['videoUrl'],
                $data['prompt'],
                'gen4_aleph',
                4294967295, // Seed por defecto
                $data['ratio'],
                [], // Referencias vacías por ahora
                ['publicFigureThreshold' => 'auto'],
                $data['duration']
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
            
            Log::info("✅ Edición Runway iniciada correctamente", [
                'taskId' => $taskId,
                'model' => $data['model']
            ]);
            
            // Disparar evento para iniciar polling
            $this->dispatch('videoEditTaskStarted', 
                generationId: $taskId,
                prompt: $data['prompt'],
                model: $data['model'],
                ratio: $data['ratio'],
                count: 1 // Para compatibilidad
            );
            
        } catch (\Exception $e) {
            $errorMessage = 'Error editando con Gen4-Aleph: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-editor'
            );
            
            $this->dispatch('videoEditError');
            $this->isGenerating = false;
        }
    }

    /**
     * Verifica el estado de edición de video
     */
    #[On('verificarEstadoVideoEditor')]
    public function verificarEstadoVideoEditor($generationId, $prompt, $model, $ratio, $count): void
    {
        try {
            Log::info('🔍 Verificando estado de edición de video', [
                'generationId' => $generationId,
                'model' => $model
            ]);
            
            $datos = [
                'generationId' => $generationId,
                'prompt' => $prompt,
                'model' => $model,
                'ratio' => $ratio,
                'count' => $count // Para compatibilidad con VideoGenerator
            ];
            
            // Delegar según el modelo
            switch ($model) {
                case 'gen4_aleph':
                    $this->verificarEstadoRunway($generationId, $datos);
                    break;
                default:
                $this->verificarEstadoRunway($generationId, $datos);
                    break;
            }
            
        } catch (\Exception $e) {
            Log::error('💥 Error verificando estado de edición', [
                'error' => $e->getMessage(),
                'model' => $model,
                'generationId' => $generationId
            ]);
            $this->isGenerating = false;
            $this->dispatch('videoEditError');
        }
    }

    /**
     * Verifica el estado específico de Runway para edición
     */
    private function verificarEstadoRunway(string $generationId, array $datos): void
    {
        Log::info('🎬 Consultando estado de edición Runway', [
            'taskId' => $generationId,
            'model' => $datos['model']
        ]);
        
        $result = RunWayService::checkVideoGenerationStatus($generationId);
        
        if (!($result['success'] ?? false)) {
            throw new \Exception('Error verificando estado de Runway: ' . ($result['error'] ?? 'Error desconocido'));
        }
        
        $taskData = $result['data'] ?? [];
        $taskStatus = $taskData['status'] ?? 'unknown';
        
        Log::info('📊 Estado de la tarea de edición Runway', [
            'status' => $taskStatus,
            'hasOutput' => isset($taskData['output']),
            'outputCount' => count($taskData['output'] ?? [])
        ]);
        
        if ($taskStatus === 'SUCCEEDED') {
            // Video editado listo - Procesar resultado
            Log::info('✅ Edición Runway completada', ['id' => $generationId]);
            $this->procesarVideoEditado($taskData, $datos);
        } elseif (in_array($taskStatus, ['PENDING', 'RUNNING'])) {
            // Aún pendiente - Emitir al frontend para nuevo delay
            Log::info('⏳ Edición Runway aún pendiente', [
                'id' => $generationId,
                'status' => $taskStatus
            ]);
            $this->dispatch('videoEditStillPending', 
                generationId: $datos['generationId'],
                prompt: $datos['prompt'],
                model: $datos['model'],
                ratio: $datos['ratio'],
                count: $datos['count']
            );
        } else {
            // Error o estado desconocido
            Log::error('❌ Edición Runway falló o estado desconocido', [
                'id' => $generationId,
                'status' => $taskStatus
            ]);
            throw new \Exception('Estado desconocido de edición Runway: ' . $taskStatus);
        }
    }

    /**
     * Verifica el estado genérico para modelos no reconocidos
     * Para el editor, por ahora solo soportamos gen4_aleph, así que este método
     * debería manejar casos no implementados correctamente
     */
    private function verificarEstadoGenerico(string $generationId, array $datos): void
    {
        Log::warning('⚠️ Modelo no soportado para edición', [
            'id' => $generationId,
            'model' => $datos['model']
        ]);
        
        // Para modelos no soportados, marcar como error
        $this->isGenerating = false;
        $this->dispatch('videoEditError');
        
        $this->dispatch('addErrorToList', 
            message: "El modelo {$datos['model']} no está soportado para edición de videos", 
            type: 'system', 
            tool: 'video-editor'
        );
    }

    /**
     * Procesa un video editado completado
     */
    private function procesarVideoEditado(array $response, array $datos): void
    {
        try {
            Log::info('🎬 Procesando video editado completado', [
                'hasOutput' => isset($response['output']),
                'outputCount' => count($response['output'] ?? []),
                'model' => $datos['model']
            ]);
            
            // Verificar si hay videos en la respuesta
            if (!isset($response['output']) || empty($response['output'])) {
                throw new \Exception('No se encontraron videos editados en la respuesta');
            }
            
            $outputUrls = $response['output'];
            $totalVideos = count($outputUrls);
            
            Log::info("📹 Encontrados {$totalVideos} video(s) editado(s)");
            
            $videos = [];
            $processedCount = 0;
            
            foreach ($outputUrls as $index => $videoUrl) {
                try {
                    // Descargar el video editado
                    Log::info("📥 Descargando video editado #{$index}", ['url' => $videoUrl]);
                    $videoContent = file_get_contents($videoUrl);
                    
                    if ($videoContent === false) {
                        Log::warning("⚠️ No se pudo descargar el video editado #{$index}", ['url' => $videoUrl]);
                        continue;
                    }
                    
                    // Guardar en S3
                    $fileName = 'genesis/output-videos/' . now()->format('Ymd_His') . '_edited_' . uniqid('video_') . '.mp4';
                    Storage::disk('s3')->put($fileName, $videoContent);
                    
                    // Construir la URL pública del archivo
                    $bucket = config('filesystems.disks.s3.bucket');
                    $region = config('filesystems.disks.s3.region');
                    $customBaseUrl = config('filesystems.disks.s3.url');
                    $baseUrl = $customBaseUrl ?: "https://{$bucket}.s3.{$region}.amazonaws.com";
                    $finalUrl = rtrim($baseUrl, '/') . "/{$fileName}";
                    
                    Log::info("💾 Video editado #{$index} guardado en S3", [
                        'fileName' => $fileName,
                        'finalUrl' => $finalUrl,
                        'size' => strlen($videoContent)
                    ]);
                    
                    // Crear datos del video editado
                    $videoData = [
                        'url' => $finalUrl,
                        'model' => $datos['model'],
                        'ratio' => $datos['ratio'],
                        'prompt' => $datos['prompt'],
                        'status' => 'completed',
                        'created_at' => now()->toISOString()
                    ];
                    
                    $this->results[] = $videoData;
                    $videos[] = $videoData;
                    $processedCount++;
                    
                    Log::info("✅ Video editado #{$index} procesado", [
                        'originalUrl' => $videoUrl,
                        's3Url' => $finalUrl,
                        'index' => $index + 1,
                        'total' => $totalVideos,
                        'processed' => $processedCount
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error("❌ Error procesando video editado #{$index}", [
                        'error' => $e->getMessage(),
                        'url' => $videoUrl
                    ]);
                    continue;
                }
            }
            
            if (!empty($videos)) {
                $videoCount = count($videos);
                
                Log::info("🎬 Preparando para agregar {$videoCount} video(s) editado(s) al historial", [
                    'videos' => $videos,
                    'prompt' => $datos['prompt'],
                    'model' => $datos['model']
                ]);
                
                // Disparar evento de finalización
                $this->dispatch('addToHistory', 
                    type: 'video/generate', 
                    images: $videos,
                    generationId: $datos['generationId'],
                    prompt: $datos['prompt'],
                    model: $this->getModelDisplayName($datos['model']),
                    ratio: $datos['ratio'],
                    count: $videoCount
                );
                
                $this->dispatch('videoEditCompleted');
                Log::info("🎉 {$videoCount} video(s) editado(s) agregados exitosamente al historial", [
                    'count' => $videoCount,
                    'generationId' => $datos['generationId']
                ]);
            } else {
                throw new \Exception('No se pudieron procesar los videos editados');
            }
            
        } catch (\Exception $e) {
            $errorMessage = 'Error procesando video editado: ' . $e->getMessage();
            $this->addError('promptText', $errorMessage);
            
            $this->dispatch('addErrorToList', 
                message: $errorMessage, 
                type: 'system', 
                tool: 'video-editor'
            );
            
            $this->dispatch('videoEditError');
        } finally {
            $this->isGenerating = false;
        }
    }



    public function render()
    {
        return view('livewire.generador.herramientas.video-editor');
    }
}
