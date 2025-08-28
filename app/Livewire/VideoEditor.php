<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class VideoEditor extends Component
{
    use WithFileUploads;

    // Propiedades para el editor de videos
    public $videoToEdit = null;
    public $videoFiles = [];
    public $videoUrl = null;
    public $isProcessing = false;
    public $subiendo = false; // Variable para controlar el spinner de carga
    public $errorMessage = '';
    public $editedVideos = [];

    public $transformSettings = [
        'promptText' => '',
        'model' => 'gen4_aleph',
        'ratio' => '1280:720',
        'duration' => 5
    ];

    // Arrays de opciones disponibles - Ratios válidos según RunWay API gen4_aleph
    private $availableRatios = [
        '1280:720' => '16:9 Horizontal',
        '720:1280' => '9:16 Vertical',
        '1104:832' => '4:3 Horizontal',
        '832:1104' => '3:4 Vertical',
        '960:960' => '1:1 Cuadrado',
        '1584:672' => '21:9 Ultra panorámico',
        '848:480' => '16:9 Compacto',
        '640:480' => '4:3 Clásico'
    ];

    private $availableDurations = [
        5 => '5 segundos'
    ];

    public function getAvailableRatios()
    {
        return $this->availableRatios;
    }

    public function getAvailableDurations()
    {
        return $this->availableDurations;
    }

    public function mount()
    {// Hacer scroll al final al entrar al componente
        $this->dispatch('scroll-to-bottom');
        // Inicializar con valores por defecto
        $this->resetVideoEditor();
        // Cargar historial desde sesión si existe
        $this->loadEditedVideosFromSession();
        // Iniciar el polling para verificar estados de videos
        $this->startPolling();
        
    }

    public function startPolling()
    {
        // Verificar si hay videos procesándose
        $hasProcessingVideos = collect($this->editedVideos)->contains('status', 'processing');
        
        if ($hasProcessingVideos) {
            // Programar la verificación cada 15 segundos
            $this->dispatch('start-polling');
        }
    }

    public function checkVideoStatuses()
    {
        $hasChanges = false;
        
        foreach ($this->editedVideos as $index => $video) {
            if ($video['status'] === 'processing' && isset($video['taskId'])) {
                try {
                    $result = \App\Services\RunWayService::checkVideoGenerationStatus($video['taskId']);
                    
                    // Log de debug para ver la respuesta completa
                    Log::info("Respuesta de Runway para taskId: " . $video['taskId'], [
                        'result' => $result,
                        'status' => $result['success'] ? ($result['data']['status'] ?? 'no-status') : 'error'
                    ]);
                    
                    if ($result['success']) {
                        $status = $result['data']['status'] ?? 'processing';
                        
                        if ($status === 'SUCCEEDED') {
                            // Video completado - ahora agregamos la URL final
                            $finalUrl = $result['data']['output'][0] ?? null;
                            
                            if ($finalUrl) {
                                // Actualizar el video existente con la URL final
                                $this->editedVideos[$index]['status'] = 'completed';
                                $this->editedVideos[$index]['finalUrl'] = $finalUrl;
                                $hasChanges = true;
                                $this->dispatch('scroll-to-bottom');
                                
                                Log::info("Video completado con URL final", [
                                    'taskId' => $video['taskId'],
                                    'finalUrl' => $finalUrl
                                ]);
                            } else {
                                Log::error("Video completado pero sin URL final", [
                                    'taskId' => $video['taskId'],
                                    'output' => $result['data']['output'] ?? 'no-output'
                                ]);
                            }
                        } elseif ($status === 'FAILED') {
                            // Video falló
                            $this->editedVideos[$index]['status'] = 'failed';
                            $this->editedVideos[$index]['error'] = $result['data']['error'] ?? 'Error desconocido';
                            $hasChanges = true;
                            
                            Log::error("Video falló", [
                                'taskId' => $video['taskId'],
                                'error' => $result['data']['error'] ?? 'Error desconocido'
                            ]);
                        } elseif ($status === 'RUNNING') {
                            // Video en progreso - log del progreso
                            $progress = $result['data']['progress'] ?? '0';
                            Log::info("Video en progreso", [
                                'taskId' => $video['taskId'],
                                'progress' => $progress . '%'
                            ]);
                        }
                        // Si sigue 'RUNNING', no hacemos nada
                    } else {
                        Log::error("Error al verificar estado del video", [
                            'taskId' => $video['taskId'],
                            'error' => $result['error']
                        ]);
                    }
                } catch (Exception $e) {
                    Log::error("Excepción al verificar estado del video: " . $e->getMessage(), [
                        'taskId' => $video['taskId']
                    ]);
                }
            }
        }
        
        // Si hay cambios, actualizar la vista
        if ($hasChanges) {
            $this->saveEditedVideosToSession();
            $this->dispatch('$refresh');
        }
        
        // Verificar si aún hay videos procesándose
        $stillProcessing = collect($this->editedVideos)->contains('status', 'processing');
        
        if ($stillProcessing) {
            // Programar la siguiente verificación en 15 segundos
            $this->dispatch('schedule-next-check');
        } else {
            // No hay más videos procesándose, detener polling
            Log::info("Polling detenido: todos los videos han terminado de procesarse");
        }
    }

    public function resetVideoEditor()
    {
        $this->videoToEdit = null;
        $this->videoUrl = null;
        $this->isProcessing = false;
        $this->errorMessage = '';
        $this->editedVideos = [];

        $this->transformSettings = [
            'promptText' => '',
            'model' => 'gen4_aleph',
            'ratio' => '1280:720',
            'duration' => 5
        ];
    }

    public function updatedVideoFiles()
    {
        if (!empty($this->videoFiles)) {
            $this->videoToEdit = $this->videoFiles[0];
            $this->errorMessage = '';
            

            // Activar estado de subida inmediatamente
            $this->subiendo = true;
            $this->isProcessing = true;
            
            Log::info("Video seleccionado, iniciando subida automática", [
                'filename' => $this->videoToEdit->getClientOriginalName()
            ]);
            
            // Programar la subida con un pequeño retraso para que se vea el estado
            $this->dispatch('iniciar-subida-delayed');
        }
    }
    
    public function iniciarSubida()
    {
        if (!$this->videoToEdit) {
            $this->errorMessage = 'No hay video seleccionado';
            return;
        }
        
        // Activar estado de subida
        $this->subiendo = true;
        $this->isProcessing = true;
        $this->errorMessage = '';
        
        Log::info("Iniciando subida manual de video a S3");
        
        try {
            $videoUrl = $this->subirVideoAS3($this->videoToEdit);
            
            if ($videoUrl) {
                $this->videoUrl = $videoUrl;
                Log::info("Video subido exitosamente", ['url' => $videoUrl]);
            } else {
                $this->errorMessage = 'Error al subir el video a S3';
                Log::error("No se pudo obtener la URL del video subido");
            }
        } catch (Exception $e) {
            $this->errorMessage = 'Error al procesar el video: ' . $e->getMessage();
            Log::error('Error en iniciarSubida: ' . $e->getMessage());
        } finally {
            $this->subiendo = false;
            $this->isProcessing = false;
        }
    }

    public function procesarVideo()
    {
        
        // if ($this->transformSettings['promptText']){
        //     // $this->errorMessage = 'Debes escribir un prompt para la transformación';
        //     $this->editedVideos[] = [
        //         'type' => 'transform',
        //         'settings' => $this->transformSettings,
        //         'taskId' => "7f0ef0a2-3e95-4df1-97d6-7e738a9f65c5",
        //         'status' => 'processing'
        //     ];
        //     //  dd($this->videoUrl);
        //     Log::info("Transformación de video iniciada", [
        //         'taskId' => $result['data']['id'] ?? null,
        //         'settings' => $this->transformSettings
        //     ]);
        //     $this->dispatch('scroll-to-bottom');
        //     // Iniciar el polling para verificar el estado
        //     $this->startPolling();
        //     return;
        // }
        if (!$this->videoToEdit) {
            $this->errorMessage = 'No hay video seleccionado para editar';
            return;
        }

        $this->isProcessing = true;
        $this->errorMessage = '';

        try {
            // Verificar que tenemos la URL del video
            if (!$this->videoUrl) {
                $this->errorMessage = 'No hay video subido a S3 para transformar';
                return;
            }

            // Verificar que tenemos un prompt
            if (empty($this->transformSettings['promptText'])) {
                $this->errorMessage = 'Debes escribir un prompt para la transformación';
                return;
            }

            // Log de la URL que vamos a enviar a RunWay
            Log::info("Enviando video a RunWay para transformación", [
                'videoUrl' => $this->videoUrl,
                'prompt' => $this->transformSettings['promptText'],
                'model' => $this->transformSettings['model'],
                'ratio' => $this->transformSettings['ratio'],
                'duration' => $this->transformSettings['duration'],
                'url_length' => strlen($this->videoUrl),
                'url_valid' => filter_var($this->videoUrl, FILTER_VALIDATE_URL) !== false,
                'url_accessible' => $this->checkUrlAccessible($this->videoUrl)
            ]);

            // Importar el servicio RunWayService
            $runwayService = new \App\Services\RunWayService();
            
            // Llamar al método de transformación
            $result = \App\Services\RunWayService::generateVideoFromVideo(
                $this->videoUrl,
                $this->transformSettings['promptText'],
                $this->transformSettings['model'],
                4294967295, // seed por defecto
                $this->transformSettings['ratio'],
                [], // references vacío por ahora
                ['publicFigureThreshold' => 'auto'],
                $this->transformSettings['duration']
            );

            if ($result['success']) {
                // Agregar el resultado al historial SOLO cuando tengamos la URL final
                // Por ahora solo guardamos la información básica
                $this->editedVideos[] = [
                    'type' => 'transform',
                    'settings' => $this->transformSettings,
                    'taskId' => $result['data']['id'] ?? null,
                    'status' => 'processing'
                ];
                $this->saveEditedVideosToSession();
                
                Log::info("Transformación de video iniciada", [
                    'taskId' => $result['data']['id'] ?? null,
                    'settings' => $this->transformSettings
                ]);
                
                // Emitir evento para scroll automático
                $this->dispatch('scroll-to-bottom');
                
                // Iniciar el polling para verificar el estado
                $this->startPolling();
            } else {
                $this->errorMessage = 'Error en la transformación: ' . ($result['error'] ?? 'Error desconocido');
                Log::error("Error en procesarTransform", $result);
            }
        } catch (Exception $e) {
            $this->errorMessage = 'Error al procesar la transformación: ' . $e->getMessage();
            Log::error('Error en procesarTransform: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function quitarVideoParaEditar()
    {
        $this->videoToEdit = null;
        $this->videoFiles = [];
        $this->videoUrl = null;
        $this->isProcessing = false;
        $this->errorMessage = '';
        // No limpiar $editedVideos ni reiniciar transformSettings
        $this->saveEditedVideosToSession();
    }

    public function quitarVideoEditado($index)
    {
        if (isset($this->editedVideos[$index])) {
            unset($this->editedVideos[$index]);
            $this->editedVideos = array_values($this->editedVideos);
            $this->saveEditedVideosToSession();
        }
    }

    public function limpiarHistorial()
    {
        $this->editedVideos = [];
        $this->saveEditedVideosToSession();
    }

    public function clearError()
    {
        $this->errorMessage = '';
    }
    
    // Método de prueba para cambiar el estado manualmente
    public function toggleSubiendo()
    {
        $this->subiendo = !$this->subiendo;
        Log::info("Estado de subida cambiado manualmente", ['subiendo' => $this->subiendo]);
    }
    
    // Método helper para verificar si una URL es accesible
    private function checkUrlAccessible($url)
    {
        try {
            $headers = get_headers($url, 1);
            return strpos($headers[0], '200') !== false;
        } catch (\Exception $e) {
            Log::warning("Error verificando accesibilidad de URL: " . $e->getMessage());
            return false;
        }
    }

    private function saveEditedVideosToSession(): void
    {
        session(['video_editor.edited_videos' => $this->editedVideos]);
    }

    private function loadEditedVideosFromSession(): void
    {
        $this->editedVideos = session('video_editor.edited_videos', []);
    }

    /**
     * Sube un video a S3 y retorna la URL pública
     */
    private function subirVideoAS3($file)
    {
        try {
            if (!$file) {
                return null;
            }
            
            // Generar un nombre único para el archivo
            $fileName = 'video-editor-' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ruta en S3
            $filePath = 'genesis/inputs-video/' . $fileName;
            
            // Subir el archivo a S3
            $s3 = Storage::disk('s3');
            $s3->put($filePath, file_get_contents($file->getRealPath()));
            
            // Construir la URL pública del archivo (evita advertencia del analizador por método url())
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            $customBaseUrl = config('filesystems.disks.s3.url');
            $baseUrl = $customBaseUrl ?: "https://{$bucket}.s3.{$region}.amazonaws.com";
            $url = rtrim($baseUrl, '/') . "/{$filePath}";
            Log::info("URL generada para S3", ['url' => $url]);
            
            Log::info("Video subido a S3 correctamente", [
                'url' => $url,
                'fileName' => $fileName,
                'filePath' => $filePath,
                'url_length' => strlen($url),
                'url_valid' => filter_var($url, FILTER_VALIDATE_URL) !== false
            ]);
            
            return $url;
        } catch (\Exception $e) {
            Log::error("Error al subir video a S3: " . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
       
        return view('livewire.video-editor');
    }
} 