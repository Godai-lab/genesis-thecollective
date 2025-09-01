<?php

namespace App\Livewire;

use App\Services\LumaService;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\On;
use App\Services\OpenAiService;
use App\Services\FluxService;
use App\Services\RunWayService;
use App\Models\Generated;
use App\Models\ServiceUsages;
use Illuminate\Support\Facades\Auth;

class NewGenerador extends Component
{
    use WithFileUploads;
    
    // Propiedades para el generador
    public $tipo = 'imagen';
    public $expandError = '';
    public $modoEdicion = ''; // Valor por defecto
    public $imageToEdit = null;
    public $promptExpansion = ''; 
    public $expandedImages = [];
    
    // Propiedades para rellenar imagen
    public $filledImages = [];
    public $promptFill = '';
    public $fillError = '';
    public $expandConfig = [
        'top' => 0,
        'bottom' => 0,
        'left' => 0,
        'right' => 0
    ];

    public $originalImageDimensions = [
        'width' => 0,
        'height' => 0
    ];

    #[On('imageDimensionsCalculated')]
    public function setImageDimensions($width, $height)
    {
        $this->originalImageDimensions = [
            'width' => $width,
            'height' => $height
        ];
    }

    public function quitarImagenParaEditar()
    {
        $this->imageToEdit = null;
        $this->imageFiles = [];
        $this->expandConfig = [
            'top' => 0,
            'bottom' => 0,
            'left' => 0,
            'right' => 0
        ];
        $this->originalImageDimensions = [
            'width' => 0,
            'height' => 0
        ];
    }

    #[On('updateExpandConfig')]
    public function updateExpandConfig($direction, $value)
    {
        if (isset($this->expandConfig[$direction])) {
            $this->expandConfig[$direction] = max(0, (int)$value);
        }
    }
public function limpiarImagenesTemporales()
{
    $this->temporaryImages = [];
    $this->imageFiles = [];
}
    public function updatedImageFiles()
    {
        if (!empty($this->imageFiles) && $this->tipo === 'editimagen') {
            // Cuando se sube una imagen para editar, la preparamos para el editor
            $this->imageToEdit = $this->imageFiles[0];
            
            // Reiniciar configuración de expansión
            $this->expandConfig = [
                'top' => 0,
                'bottom' => 0,
                'left' => 0,
                'right' => 0
            ];
            
            // Reiniciar dimensiones originales
            $this->originalImageDimensions = [
                'width' => 0,
                'height' => 0
            ];
            
            // Forzar actualización de la vista
            $this->dispatch('imageLoadedForEdit');
        }
    }
    
    public function cambiarModoEdicion($modo)
    {
        if (in_array($modo, ['expand', 'fill'])) {
            $this->modoEdicion = $modo;
        }
    }
    public $duracionVideo = '5s';
    public $duracionesDisponiblesLuma = [
    '5s' => '5 seg.',
    '9s' => '9 seg.'
];
 public $ratioVideo = '16:9';
    
    // Agregar mapeo de ratios disponibles por servicio de video
    public $ratiosVideoDisponibles = [
        'luma' => [
            '1:1' => 'Cuadrado',
            '16:9' => 'Panorámico',
            '9:16' => 'Vertical',
            '4:3' => 'Horizontal',
            '3:4' => 'Vertical clásico',
            '21:9' => 'Ultra panorámico',
            '9:21' => 'Ultra vertical'
        ],
        'luma2' => [
            '1:1' => 'Cuadrado',
            '16:9' => 'Panorámico',
            '9:16' => 'Vertical',
            '4:3' => 'Horizontal',
            '3:4' => 'Vertical clásico',
            '21:9' => 'Ultra panorámico',
            '9:21' => 'Ultra vertical'
        ],
        'gemini' => [
            '16:9' => 'Panorámico',
            '9:16' => 'Vertical'
        ],
        'runway' => [
            '1280:768' => 'Panorámico',
            '768:1280' => 'Vertical'
        ],
        'runway2' => [
        '1280:720'  => 'Panorámico',
        '720:1280'  => 'Vertical',
        '1104:832'  => 'Panorámico',
        '832:1104'  => 'Vertical',
        '960:960'   => 'Cuadrado',
        '1584:672'  => 'Panorámico'
        ]

    ];
    public $prompt = ''; // Prompt para la generación
    public $estilo = ''; 
    public $ratio = '1:1'; // Valor predeterminado compatible
    public $isGenerating = false; 
    public $isTyping = false; // Nuevo indicador para mostrar "escribiendo..."
    public $resultados = []; 
    public $imageFiles = []; // Para almacenar imágenes adjuntas
    public $errorMessage = null; 
    
    // Historial de chat - ahora solo se usa como propiedad temporal
    public $chatHistory = [];
    public function abrirLightbox($imgSrc)
{
    // dd($imgSrc);
    $this->dispatch('abrirLightbox', [
        'imgSrc' => $imgSrc
    ]);
}
    // Opciones disponibles
    public $ratiosDisponibles = [
        '1:1' => 'Cuadrado', 
        '4:3' => 'Horizontal',
        '3:4' => 'Vertical',
        '16:9' => 'Panorámico',
        '9:16' => 'Vertical móvil'
    ];
    
    // Agregar estas propiedades al componente
    public $cantidadImagenes = 1; // Valor predeterminado: 4 imágenes
    
    // Opciones disponibles para cantidad de imágenes
    public $cantidadesDisponibles = [
        1 => '1 imagen',
        2 => '2 imágenes',
        3 => '3 imágenes',
        4 => '4 imágenes'
    ];
    
    // Agrega esta nueva propiedad a la clase
    public $videoGenerating = false;
    
    // Agregar estas propiedades al componente
    public $servicioImagen = 'gemini'; // gemini valor predeterminado
    public $serviciosImagenes=[    
        'gemini4' => 'Image 4',
        'gemini' => 'Image 3',
        'openai' => 'ChatGPT',
        'flux-kontext-max'=>'Flux-Kontext-Max',
        'flux-kontext-pro'=>'Flux-Kontext-Pro',
        'flux' => 'Flux Pro',
        'fluxultra' => 'Flux Ultra'];
    public $serviciosDisponibles = [
       
    ];

    // Agregar esta propiedad para los servicios de video
    public $serviciosVideo = [
        'gemini' => 'Veo2',
        'runway2' => 'Gen4-Turbo',
        'runway' => 'Gen3-AlphaTurbo ',
        'luma2' => 'Ray2',
        'luma' => 'Ray2-flash'
    ];
    public $serviciosPrompt = [
        'openai' => 'ChatGPT'
      
    ];
     public $serviciosEditImagen = [
         'flux-kontext-max'=>'Flux-Kontext-Max',
        'flux-kontext-pro'=>'Flux-Kontext-Pro'
      
    ];
    

    public $calidadImagen = 'auto'; // valores posibles: 'auto', 'high', 'medium', 'low'
    public $calidadesDisponibles = [
        'auto' => 'Automática',
        'high' => 'Alta',
        'medium' => 'Media',
        'low' => 'Baja'
    ];
    
   
    
    // Añadir esta propiedad al inicio de la clase
    public $fluxGenerating = false;
    
    /**
     * Propiedad para las imágenes temporales
     */
    public $temporaryImages = [];
    
    // Añadir esta propiedad junto con las otras propiedades públicas
    public $imagenBaseParaVideo = null; // Para almacenar la URL de la imagen base para video
    
    // Agregar propiedad para el estado de generación con Runway
    public $runwayGenerating = false;
    
    // Agregar estas propiedades al componente
    public $imageFilesStart = []; // Para la imagen de inicio en Runway
    public $imageFilesEnd = []; // Para la imagen de fin en Runway
    public $temporaryImagesStart = []; // Para manejar la carga temporal de imágenes de inicio
    public $temporaryImagesEnd = []; // Para manejar la carga temporal de imágenes de fin
    
    // Agregar esta propiedad al inicio de la clase, junto con las otras propiedades públicas
    public $currentProcessingData = [];
    
    // Agregar esta propiedad al inicio de la clase, junto con las otras propiedades públicas
    public $generatingMessage = '';
    
    // Agregar estas propiedades al inicio de la clase
    public $veo2Generating = false;
    public $veo2TaskId = null;
    
    // propiedades para documentos del generador del prompt
    public $documentos = [];
    public $documentoSeleccionado = null;
    public $documentoInfo = null;
     // Agregar estas propiedades al componente
    public $tamanoOpenAI = 'auto'; // valor predeterminado
    public $tamanosOpenAI = [
    'auto' => 'Automático',
    '1:1' => 'Cuadrado',
    '3:2' => 'Horizontal',
    '2:3' => 'Vertical'
];
public function seleccionarDuracion($duracion)
{
    if (array_key_exists($duracion, $this->duracionesDisponiblesLuma)) {
        $this->duracionVideo = $duracion;
    }
}
    /**
     
     * Método auxiliar para convertir nuestro ratio a los tamaños de OpenAI
     */
   private function mapearAspectRatioAOpenAI($ratio)
{
    // Si estamos usando OpenAI y hay un tamaño seleccionado, usarlo directamente
    if ($this->servicioImagen === 'openai' && $this->tamanoOpenAI !== 'auto') {
        switch ($ratio) {
            case '1:1':
                return '1024x1024';
            case '3:2':
                return '1536x1024';
            case '2:3':
                return '1024x1536';
            default:
                return '1024x1024'; // Valor por defecto 
        }
    }
    
    return '1024x1024'; // Valor por defecto si no se cumple la condición
}
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
     * Observador para cuando se seleccionan nuevas imágenes de inicio
     */
    public function updatedTemporaryImagesStart()
    {
        if (!empty($this->temporaryImagesStart)) {
            Log::info("Actualizando imagen de inicio: " . count($this->temporaryImagesStart) . " archivos");
            $this->imageFilesStart = $this->temporaryImagesStart;
            $this->temporaryImagesStart = [];
             // Si es Veo2, limpiar cualquier imagen base previa
        if ($this->servicioImagen === 'gemini') {
            $this->imagenBaseParaVideo = null;
        }
            // Forzar actualización de la vista
            $this->dispatch('runwayImageUpdated');
        }
    }
    
    /**
     * Observador para cuando se seleccionan nuevas imágenes de fin
     */
    public function updatedTemporaryImagesEnd()
    {
        if (!empty($this->temporaryImagesEnd)) {
            Log::info("Actualizando imagen de fin: " . count($this->temporaryImagesEnd) . " archivos");
            $this->imageFilesEnd = $this->temporaryImagesEnd;
            $this->temporaryImagesEnd = [];
            
            // Forzar actualización de la vista
            $this->dispatch('runwayImageUpdated');
        }
    }
    /**
 * Método para expandir imagen usando Flux Pro
 */
public function expandirImagenFlux($datos)
{
    try {
        // Limpiar errores previos
        $this->expandError = '';
        session()->forget('expand_error');
        Log::info('Iniciando método expandirImagenFlux');
        $this->isGenerating = true;
        $this->fluxGenerating = true;
// dd($this->promptExpansion);
        // Verificar que hay una imagen seleccionada
        if (empty($this->imageFiles) || count($this->imageFiles) === 0) {
            Log::warning('No se ha seleccionado ninguna imagen para expandir');
            throw new \Exception('No se ha seleccionado ninguna imagen para expandir');
        }

        Log::info('Imagen seleccionada correctamente');

        $image = $this->imageFiles[0]; // Tomar la primera imagen
        $imageContent = file_get_contents($image->getRealPath());
        $input_image = base64_encode($imageContent);
        Log::info('Imagen codificada en base64');

        // Calcular dimensiones
        $originalWidth = $datos['imgWidth'];
        $originalHeight = $datos['imgHeight'];
        $expandedWidth = $datos['overlayRealWidth'];
        $expandedHeight = $datos['overlayRealHeight'];

        Log::info("Dimensiones recibidas", [
            'originalWidth' => $originalWidth,
            'originalHeight' => $originalHeight,
            'expandedWidth' => $expandedWidth,
            'expandedHeight' => $expandedHeight
        ]);

        $left = floor(($expandedWidth - $originalWidth) / 2);
        $right = $expandedWidth - $originalWidth - $left;
        $top = floor(($expandedHeight - $originalHeight) / 2);
        $bottom = $expandedHeight - $originalHeight - $top;

        Log::info("Cálculo de expansión en píxeles", [
            'top' => $top,
            'bottom' => $bottom,
            'left' => $left,
            'right' => $right
        ]);

        // Usar el prompt específico para expansión si está disponible, sino usar uno por defecto
        $prompt = !empty($this->promptExpansion) ? $this->promptExpansion : "Expand the scene naturally, continuing the background, environment, and elements in the same artistic style, lighting, and perspective as the original image. Maintain visual consistency and complete any partially visible objects or structures. Do not introduce new unrelated elements.";
        // dd( $prompt);

        // Llamar al servicio Flux
        Log::info("Llamando al servicio Flux");
        $response = FluxService::ExpandImageFluxPro(
            $input_image,
            $top,
            $bottom,
            $left,
            $right,
            $prompt,
            50,
            true,
            null,
            50.75,
            'jpeg',
            2
        );

        Log::info("Respuesta de FluxService", ['response' => $response]);

        if (isset($response['error'])) {
            Log::error("Error recibido de FluxService", ['error' => $response['error']]);
            throw new \Exception('Error con Flux: ' . ($response['error'] ?? 'Error desconocido'));
        }

        if (isset($response['data'])) {
            $generationId = $response['data'];
            Log::info("ID de generación recibido", ['generationId' => $generationId]);

            $this->dispatch('verificarEstadoFlux', [
                'generationId' => $generationId,
                'prompt' => $this->prompt,
                'type' => 'expand',
                'ratio' => $expandedWidth . ':' . $expandedHeight
            ]);
        } else {
            Log::error("Respuesta inesperada de FluxService: no contiene 'data'");
            throw new \Exception('Respuesta inesperada de Flux');
        }
        session()->flash('expand_success', 'Imagen expandida correctamente');

    } catch (\Exception $e) {
        // Capturar y mostrar el error
        $this->expandError = 'Error al expandir la imagen: ' . $e->getMessage();
        session()->flash('expand_error', $this->expandError);
        $this->errorMessage = $e->getMessage();
        Log::error("Excepción capturada en expandirImagenFlux", ['error' => $this->errorMessage]);
        session()->flash('error', 'Error: ' . $this->errorMessage);
        $this->isGenerating = false;
        $this->fluxGenerating = false;
        $this->dispatch('errorOcurrido');
        
        // Emitir evento para ocultar el spinner de expansión
        $this->dispatch('expansion-error');
    }
    
    
}

    /**
     * Inicializar el componente y cargar el historial desde la sesión
     */
    public function mount()
    {
        // Cargar el historial desde la sesión
        $this->chatHistory = session()->get('generador_chat_history', []);
        // Cargar las imágenes expandidas desde la sesión
    $this->expandedImages = collect(session()->get('expanded_images', []))
        ->pluck('url')
        ->toArray();
        
        // Disparar evento para scroll inicial
        $this->dispatch('scrollTo', [
            'target' => '.message-item:last-child',
            'offset' => 0
        ]);
        
        // $this->dispatch('imagenGenerada');
        $this->dispatch('historialActualizado');
        
        // Actualizar servicios disponibles según el tipo
        $this->actualizarServiciosDisponibles();
        $this->cargarDocumentosGenesis();
        // Cargar documentos Genesis si es necesario
        if ($this->tipo === 'gprompt') {
            $this->cargarDocumentosGenesis();
        }
    }
    
/**
     * Carga solo el prompt generado oara generar video o imagen
     */
    public function cargarPromptParaGenerar($contenido,$nuevotipo){

        
        //  $this->tipo = $nuevotipo;
        //  dd($nuevotipo);
         switch ($nuevotipo) {
            case 'video':
                $this->cambiarTipo($nuevotipo);
                 $this->prompt=$contenido;
                break;
                 case 'imagen':
                $this->cambiarTipo($nuevotipo);
                $this->prompt=$contenido;
                break;
            
            default:
                $this->cambiarTipo($nuevotipo);
                 $this->prompt="hola";
                break;
         }
        // Actualizar servicios disponibles según el nuevo tipo
            //$this->actualizarServiciosDisponibles();
        
       
    }
    /**
     * Carga solo los documentos de tipo Genesis
     */
    public function cargarDocumentosGenesis()
    {
        $user = auth()->user();
        // dd("hola");
        
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
    }
    
    /**
     * Cuando se selecciona un documento Genesis
     */
    public function seleccionarDocumentoGenesis()
    {
        if (!$this->documentoSeleccionado) {
            $this->documentoInfo = null;
            return;
        }
        
        $documento = Generated::find($this->documentoSeleccionado);
        
        if ($documento) {
            $user = auth()->user();
            $puedeAcceder = $user->roles->pluck('name')->contains(fn($rol) => in_array($rol, ['Admin', 'Super Admin'])) ||
                           $user->accounts->pluck('id')->contains($documento->account_id);
            
            if ($puedeAcceder) {
                $this->documentoInfo = [
                    'id' => $documento->id,
                    'name' => $documento->name,
                    'fecha' => $documento->created_at->format('d/m/Y')
                ];
            } else {
                $this->documentoInfo = null;
            }
        } else {
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
    // Agregar nuevo método para seleccionar ratio de video
    public function seleccionarRatioVideo($nuevoRatio)
    {
        if (isset($this->ratiosVideoDisponibles[$this->servicioImagen][$nuevoRatio])) {
            $this->ratioVideo = $nuevoRatio;
        }
    }
    /**
     * Cambiar entre modos imagen/video
     */
    public function cambiarTipo($nuevoTipo)
    {
        // Solo cambiar si es diferente al actual
        if ($this->tipo !== $nuevoTipo) {
            $this->tipo = $nuevoTipo;
            $this->modoEdicion = '';
           // $this->prompt = ''; // Limpiar el prompt
            
            // Limpiar todas las imágenes subidas al cambiar de tipo
            $this->imageFiles = [];
            $this->imageFilesStart = [];
            $this->imageFilesEnd = [];
            
            // También limpiar la imagen base para video
            $this->imagenBaseParaVideo = null;
            
    
            switch ($nuevoTipo) {
                case 'imagen':
                    $this->ratio = '1:1';
                    $this->servicioImagen = 'gemini';
                    break;
                case 'video':
                     $this->ratioVideo = '16:9';
                    $this->servicioImagen = 'gemini'; 
                    break;
                case 'gprompt':
                    
                    $this->servicioImagen = 'openai'; 
                    break;
                     case 'editimagen':
                    
                    $this->servicioImagen = 'flux-kontext-max'; 
                    break;
                case 'editvideo':
                    
                    $this->servicioImagen = 'edit-video'; 
                    break;
                
               
            }
            
            // Actualizar servicios disponibles según el nuevo tipo
            $this->actualizarServiciosDisponibles();
            // Verificar límites inmediatamente al cambiar el servicio
            $this->verificarLimitesServicio();
        }
    }

    public function abrirEditorVideo($videoUrl)
    {
        // Cambiar al modo de edición de video
        $this->tipo = 'editvideo';
        
        // Guardar la URL del video para pasarla al editor
        session()->flash('videoUrl', $videoUrl);
    }
    
    /**
     * Actualiza los servicios disponibles según el tipo seleccionado
     */
    public function actualizarServiciosDisponibles()
    {
        switch ($this->tipo ) {
            case 'video':
               $this->serviciosDisponibles = $this->serviciosVideo;
               break;
            case 'imagen':
                 $this->serviciosDisponibles = $this->serviciosImagenes;
                 break;
            case 'gprompt':
            $this->serviciosDisponibles = $this->serviciosPrompt;
            break;
             case 'editimagen':
            $this->serviciosDisponibles = $this->serviciosEditImagen;
            break;
            case 'editvideo':
            $this->serviciosDisponibles = [];
            break;
        default:
            $this->serviciosDisponibles = [];
            break;
            
        
        }
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
        // Si el tipo es editvideo, no permitir cambiar el servicio
        if ($this->tipo === 'editvideo') {
            return;
        }
        
        if (isset($this->serviciosDisponibles[$servicio])) {
            // Si cambiamos de servicio, limpiar las imágenes específicas
            if ($this->servicioImagen !== $servicio) {
                $this->imageFiles = [];
                // $this->imageFilesStart = [];
                $this->imageFilesEnd = [];
                $this->imagenBaseParaVideo = null;
                // Establecer ratio por defecto para el nuevo servicio si es video
                if ($this->tipo === 'video') {
                    $ratiosDisponibles = array_keys($this->ratiosVideoDisponibles[$servicio] ?? []);
                    $this->ratioVideo = !empty($ratiosDisponibles) ? $ratiosDisponibles[0] : '16:9';
                }
            }
            
            $this->servicioImagen = $servicio;
             // Verificar límites inmediatamente al cambiar el servicio
            $this->verificarLimitesServicio();
        }
    }
    
    /**
     * Selecciona la calidad de imagen para OpenAI
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
     * Método principal para generar contenido (imágenes)
     */
    public function generar()
    {
        try {
            // $this->dispatch('historialActualizado');
            Log::info("Iniciando generación. Estado actual - isGenerating: " . ($this->isGenerating ? 'true' : 'false'));
            
            // Validación básica
            if (empty($this->prompt) && $this->tipo !== 'gprompt') {
                session()->flash('error', 'Por favor, ingresa una descripción.');
                $this->dispatch('errorOcurrido');
                return;
            }
             // Verificar límites de uso para el servicio seleccionado
        if ($this->verificarLimitesServicio()) {
            // Si devuelve true, significa que se ha alcanzado el límite
            return;
        }
            
            // Aumentar el tiempo límite para evitar timeouts
            set_time_limit(180); // 3 minutos
            
            // Limpiar mensaje de error anterior
            session()->forget('error');
            $this->errorMessage = null;
            
            // Establecer isGenerating a true INMEDIATAMENTE
            $this->isGenerating = true;
            
            Log::info("Estado de generación actualizado - isGenerating: true");
            
            // Guardar el prompt actual antes de limpiarlo
            $promptActual = $this->prompt;
            
            // Preparar imágenes adjuntas si existen
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
            
            // Agregar el mensaje del usuario al historial
            if ($this->tipo === 'imagen') {
                $ratioTexto = " con relación de aspecto " . $this->ratio;
                $this->chatHistory[] = [
                    'tipo' => 'usuario',
                    'contenido' => $promptActual . $ratioTexto,
                    'imagenes' => $imagenes,
                    'tiempo' => now()->format('H:i')
                ];
            } else {
                // Para videos no incluimos el ratio en el mensaje
                $this->chatHistory[] = [
                    'tipo' => 'usuario',
                    'contenido' => $promptActual,
                    'imagenes' => $imagenes,
                    'tiempo' => now()->format('H:i')
                ];
            }
            
            // Guardar el historial en la sesión
            session()->put('generador_chat_history', $this->chatHistory);
            
            // Limpiar el input inmediatamente
            // $this->prompt = '';
            
            // Activar el indicador de escritura
            $this->isTyping = true;
            
            // Forzar actualización de UI y scroll
            $this->dispatch('historialActualizado');
            $this->dispatch('generacionIniciada', 
            );
            
            // Determinar qué tipo de contenido generar
            if ($this->tipo === 'imagen' ||$this->tipo === 'editimagen') {
                // Para imágenes, usar el evento para iniciar la generación
                $this->dispatch('iniciarGeneracion', [
                    'prompt' => $promptActual,
                    'ratio' => $this->ratio,
                    'tipo' => 'imagen',
                    'cantidad' => $this->cantidadImagenes
                ]);
            } else {
                $this->dispatch('iniciarGeneracion', [
                    'prompt' => $promptActual,
                    'tipo' => 'gprompt',
                    'documento'=>$this->documentoSeleccionado
                    
                ]);
               
            }
             
        } catch (Exception $e) {
            Log::error("Error en generación: " . $e->getMessage());
            $this->isGenerating = false;
            $this->runwayGenerating = false;
            $this->errorMessage = $e->getMessage();
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Método que inicia la generación de imágenes después de que el mensaje del usuario se muestra
     */
    #[On('iniciarGeneracion')]
    public function iniciarGeneracion($datos)
    {
        try {
            // Solo procesar si son imágenes
            if ($datos['tipo'] === 'imagen') {
                $this->generarImagenConDatos($datos);
            }else{
                $this->generarPrompt($datos);
            }
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
     * Método para generar prompt con los datos proporcionados
     */
public function generarPrompt($datos)
    {
         try {
           
            $promptCompleto = $datos['prompt'];
            $documento= $datos['documento'];
            
            Log::info('Generando imagen con prompt: ' . $promptCompleto . ' usando servicio: ' . $this->servicioImagen);
            
            
            switch ($this->servicioImagen) {
                
                case 'openai':
                    return $this->generarPromptOpenAi($promptCompleto, $documento);
                
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
            //$this->dispatch('imagenGenerada');
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
                case 'gemini4':
                    return $this->generarImagenGemini($promptCompleto, $aspectRatio, $datos['cantidad']);
                case 'openai':
                    return $this->generarImagenOpenAI($promptCompleto, $aspectRatio, $datos['cantidad']);
                case 'flux-kontext-max':
                case 'flux-kontext-pro':
                    return $this->generarImagenConFluxKontextPro($promptCompleto, $datos);
                       
                case 'flux':
                   return $this->generarImagenConFlux($promptCompleto, $datos);
                    
                    
                case 'fluxultra':
                   return $this->generarImagenConFluxUltra($promptCompleto, $datos);
                   
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
            //$this->dispatch('imagenGenerada');
        }
    }
    
   private function generarPromptOpenAi($promptCompleto, $documento)
{
    $assistant_id = "asst_A2O3TljT02t6ILUgYhKqGemQ";

    if ($documento) {
        $documentoinfo = Generated::find($documento);

        if ($documentoinfo && $documentoinfo->value) {
            // Adjunta el contenido del documento al prompt
            $promptCompleto .= "\n\nContenido relacionado:\n" . $documentoinfo->value;
        }
    }

    $response = OpenAiService::CompletionsAssistants($promptCompleto, $assistant_id);

    if (isset($response['data'])) {
        $textoGenerado = $response['data'];

        $this->chatHistory[] = [
            'tipo' => 'sistema',
            'contenido' => $textoGenerado,
            'tiempo' => now()->format('H:i')
        ];

        session()->put('generador_chat_history', $this->chatHistory);

        $this->dispatch('historialActualizado'); 

        Log::info('Texto generado:', ['texto' => $textoGenerado]);

    } elseif (isset($response['error'])) {
        Log::error('Error en Assistant:', ['error' => $response['error']]);
        
    }

    return;
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
            // dd($aspectRatio);
            $aspecto=$this->mapearAspectRatioAOpenAI( $this->tamanoOpenAI);
           // $size = $this->tamanoOpenAI === 'auto' ? '1024x1024' : $this->tamanoOpenAI;
            $quality = $this->calidadImagen;
            
            // Comprobar si hay imágenes adjuntas para editar
            if (!empty($this->imageFiles)) {
                // Si hay imágenes, procesarlas individualmente
                $generatedImages = [];
                
                foreach ($this->imageFiles as $image) {
                    $imagePath = $image->getRealPath();
                    
                    // Llamar al servicio de edición de imágenes de OpenAI para cada imagen
                    $response = OpenAiService::editImage($prompt, [$imagePath], 'gpt-image-1', $aspecto, 'auto', 1);
                    
                    if (isset($response['error'])) {
                        // Registrar el error pero continuar con otras imágenes
                        Log::error('Error procesando imagen: ' . $response['error']);
                        continue;
                    }
                    
                    // Procesar respuesta y guardar las imágenes resultantes
                    if (isset($response['data']) && is_array($response['data'])) {
                        //incrementar si la respuesta es exitosa
                         ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
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
                    
                    Log::info('Imágenes editadas con OpenAI: ' . count($generatedImages));
                    
                    // Limpiar las imágenes adjuntas después de una edición exitosa
                    $this->imageFiles = [];
                } else {
                    $this->errorMessage = 'No se pudieron generar imágenes editadas con OpenAI.';
                    session()->flash('error', $this->errorMessage);
                    $this->dispatch('errorOcurrido');
                }
            } else {
                // Si no hay imágenes, usar el endpoint de generación normal
            $response = OpenAiService::generateImage($prompt, 'gpt-image-1', $aspecto, $cantidad, null, null, $quality);
            
            if (isset($response['error'])) {
                $this->errorMessage = 'Error generando imagen con OpenAI: ' . $response['error'];
                session()->flash('error', 'Error: ' . $this->errorMessage);
                $this->isGenerating = false;
                $this->isTyping = false;
                $this->dispatch('errorOcurrido');
                return;
            }
            
                // Procesar respuesta de generación normal
            $generatedImages = [];
            //incrementar si la respuesta es exitosa
                         ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
            foreach ($response['data'] as $image) {
                if (isset($image['b64_json'])) {
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
                    // Si es una URL directa
                    $generatedImages[] = [
                        'url' => $image['url'],
                        'mimeType' => 'image/jpeg'
                    ];
                }
            }
            
            if (!empty($generatedImages)) {
                $resultados = [
                    'images' => $generatedImages,
                    'prompt' => $prompt,
                    'ratio' => $aspectRatio,
                    'text' => ''
                ];
                
                $this->resultados = $resultados;
                $this->agregarRespuestaSistema($resultados);
                
                    Log::info('Imágenes generadas con OpenAI: ' . count($generatedImages));
            } else {
                $this->errorMessage = 'No se pudieron generar imágenes con OpenAI.';
                    session()->flash('error', $this->errorMessage);
                    $this->dispatch('errorOcurrido');
                }
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            session()->flash('error', $this->errorMessage);
            $this->isGenerating = false;
            $this->isTyping = false;
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Genera imágenes usando el servicio de Gemini
     */
    private function generarImagenGemini($prompt, $aspectRatio, $cantidad)
    {
        // Determinar el modelo según la selección del usuario
        $modelo = $this->servicioImagen === 'gemini4'
            ? 'imagen-4.0-generate-preview-06-06'
            : 'imagen-3.0-generate-002';
        // Generar la imagen con Gemini usando el modelo seleccionado
        $response = GeminiService::generateImage(
            $prompt,
            $modelo,
            $cantidad,
            $aspectRatio,
            "ALLOW_ADULT"
        );
        
        if (!$response['success']) {
            $errorMsg = $response['error'] ?? 'Error desconocido al generar la imagen';
            if (is_array($errorMsg) && isset($errorMsg['message'])) {
                $errorMsg = $errorMsg['message'];
            }
            $this->errorMessage = 'Error: ' . $errorMsg;
            $this->isGenerating = false;
            
            Log::error('Error en respuesta de GeminiService', ['error' => $errorMsg]);
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->dispatch('errorOcurrido');
            return;
        }
        
        // Verificar si hay imágenes en la respuesta
        if (isset($response['data']) && is_array($response['data'])) {
            $generatedImages = [];
             //incrementar si la respuesta es exitosa
            ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
            // Procesar cada imagen generada por Gemini
            foreach ($response['data'] as $image) {
                if (isset($image['bytesBase64Encoded']) && isset($image['mimeType'])) {
                    $imageBase64 = $image['bytesBase64Encoded'];
                    $mimeType = $image['mimeType'];
                    
                    // Guardar la imagen en disco
                    $imageUrl = $this->guardarImagenEnDisco($imageBase64, $mimeType, 'gemini');
                    
                    if ($imageUrl) {
                        $generatedImages[] = [
                            'url' => $imageUrl,
                            'mimeType' => $mimeType
                        ];
                    }
                }
            }
            
            if (!empty($generatedImages)) {
                $resultados = [
                    'images' => $generatedImages,
                    'prompt' => $prompt,
                    'ratio' => $aspectRatio,
                    'text' => ''
                ];
                
                $this->resultados = $resultados;
                $this->agregarRespuestaSistema($resultados);
                
                Log::info('Imágenes guardadas en disco con Gemini: ' . count($generatedImages));
            } else {
                $this->errorMessage = 'No se pudieron generar imágenes.';
            }
        } else {
            $this->errorMessage = 'La respuesta no contiene imágenes.';
        }
    }
      private function generarImagenConFluxKontextPro($prompt, $datos)
    {
        // dd($datos);
        $this->isGenerating = true;
     $modelo = $this->servicioImagen === 'flux-kontext-max' ? 'flux-kontext-max' : 'flux-kontext-pro';
        $aspect = $datos['ratio'];
       $input_image = null;
        
        // Convertir la primera imagen a base64 si existe
        if (!empty($this->imageFiles) && count($this->imageFiles) > 0) {
            $image = $this->imageFiles[0]; // Tomar solo la primera imagen
            $imageContent = file_get_contents($image->getRealPath());
            $input_image = base64_encode($imageContent);
        }
        
        
        // Iniciar la generación de la imagen con Flux
        $response = FluxService::GenerateImageKontext(
            $modelo,
            $prompt,
            $aspect,
            $input_image
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
             //incrementar si la respuesta es exitosa
            ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
            // Programar la verificación del estado de la generación
            $this->dispatch('verificarEstadoFlux', [
                'generationId' => $generationId,
                'prompt' => $prompt,
                'type'=>'fluxultra',
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
    // método generarImagenConFlux
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
             //incrementar si la respuesta es exitosa
            ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
            
            // Programar la verificación del estado de la generación
            $this->dispatch('verificarEstadoFlux', [
                'generationId' => $generationId,
                'prompt' => $prompt,
                'type'=>'fluxupro',
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
     private function generarImagenConFluxUltra($prompt, $datos)
    {
        // Determinar dimensiones basadas en la relación de aspecto
        // $dimensions = $this->getDimensionsFromRatio($datos['ratio']);
        // $width = $dimensions['width'];
        // $height = $dimensions['height'];
     
        $aspect = $datos['ratio'];
       // dd($aspect);
        
        // Activar ambos indicadores de generación
        $this->isGenerating = true;
        $this->fluxGenerating = true;
        
        // Iniciar la generación de la imagen con Flux
        $response = FluxService::GenerateImageFluxUltra(
            $prompt,
            $aspect,
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
             //incrementar si la respuesta es exitosa
             ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
            
            // Programar la verificación del estado de la generación
            $this->dispatch('verificarEstadoFlux', [
                'generationId' => $generationId,
                'prompt' => $prompt,
                'type'=>'fluxultra',
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
     * Verifica el estado de una generación de imagen con Flux
     */
    #[On('verificarEstadoFlux')]
    public function verificarEstadoFlux($datos = [])
    {
        try {
            $generationId = $datos['generationId'] ?? null;
            $tipo = $datos['type'];
            
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

            switch ($tipo) {
                case 'fluxupro':
                    // Verificar el estado actual
                    $result = FluxService::GetResult($generationId);
                    break;
                case 'fluxultra':
                case 'expand':
                case 'fill':
                    $result = FluxService::GetResultUltra($generationId);
                    break;
            }
            
            // Verificamos el estado de la generación
            if (isset($result['status'])) {
                switch ($result['status']) {
                    case 'complete':
                    case 'Ready':
                        // La imagen está lista
                        $imageUrl = $result['data'];
                        
                        // Descargar y guardar la imagen localmente
                        $localImageUrl = $this->descargarYGuardarImagen($imageUrl, 'flux');
                        
                        // Si no pudimos guardar la imagen localmente, usar la URL original
                        $finalImageUrl = $localImageUrl ?: $imageUrl;
                        
                        // Crear objeto de resultados 
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
                        
                        // Si es una expansión, agregar la imagen a expandedImages
                        if ($tipo === 'expand') {
                            $this->expandedImages[] = $finalImageUrl;
                            session()->push('expanded_images', [
                                    'url' => $finalImageUrl,
                                    'timestamp' => now(),
                                    'type' => 'expand'
                                ]);
                            
                            // Emitir evento para ocultar el spinner de expansión
                            $this->dispatch('expansion-completed');
                            $this->dispatch('scrollToExpandedImage');
                        } elseif ($tipo === 'fill') {
                            $this->filledImages[] = $finalImageUrl;
                            session()->push('filled_images', [
                                    'url' => $finalImageUrl,
                                    'timestamp' => now(),
                                    'type' => 'fill'
                                ]);
                            
                            // Emitir evento para ocultar el spinner de fill
                            $this->dispatch('fill-completed');
                            $this->dispatch('scrollToFilledImage');
                        } else {
                            // Para otros tipos, usar el comportamiento normal
                            $this->resultados = $resultados;
                            $this->agregarRespuestaSistema($resultados);
                        }
                        
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
                        
                        // Si es una expansión o fill, emitir evento específico para ocultar el spinner
                        if (isset($datos['type']) && $datos['type'] === 'expand') {
                            $this->dispatch('expansion-error');
                        } elseif (isset($datos['type']) && $datos['type'] === 'fill') {
                            $this->dispatch('fill-error');
                        }
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
            
            // Si es una expansión o fill, emitir evento específico para ocultar el spinner
            if (isset($datos['type']) && $datos['type'] === 'expand') {
                $this->dispatch('expansion-error');
            } elseif (isset($datos['type']) && $datos['type'] === 'fill') {
                $this->dispatch('fill-error');
            }
        }
    }
    
    
    public function generarVideoPrincipal(){
        try {
            Log::info("Iniciando generaciónVideo. Estado actual - isGenerating: " . ($this->isGenerating ? 'true' : 'false'));
            
            // Validación básica
            if (empty($this->prompt)&& !($this->tipo === 'video' && $this->servicioImagen === 'luma')) {
                session()->flash('error', 'Por favor, ingresa una descripción.');
                $this->dispatch('errorOcurrido');
                return;
            }
             if ($this->verificarLimitesServicio()) {
            // Si devuelve true, significa que se ha alcanzado el límite
            return;
        }
            
            // Aumentar el tiempo límite para evitar timeouts
            set_time_limit(180); // 3 minutos
            
            // Limpiar mensaje de error anterior
            session()->forget('error');
            $this->errorMessage = null;
            
            // Establecer isGenerating a true INMEDIATAMENTE
            $this->isGenerating = true;
             $this->videoGenerating=true;
            Log::info("Estado de generación actualizado - isGenerating:".$this->isGenerating);
            
            // Guardar el prompt actual antes de limpiarlo
            $promptActual = $this->prompt;
                
             // Limpiar el input inmediatamente
            $this->prompt = '';
            
            // Determinar qué tipo de contenido generar
            if ($this->tipo === 'video') {
                // Para imágenes, usar el evento para iniciar la generación
                $this->dispatch('iniciarGeneracionVideo', [
                    'prompt' => $promptActual,
                    'tipo' => 'video',
                    'ratio'=>$this->ratioVideo
               
                ]);
            } 
        } catch (Exception $e) {
            Log::error("Error en generación: " . $e->getMessage());
            $this->isGenerating = false;
           $this->videoGenerating=false;
            $this->errorMessage = $e->getMessage();
            session()->flash('error', 'Error: ' . $this->errorMessage);
            $this->dispatch('errorOcurrido');
        }

    }
     #[On('iniciarGeneracionVideo')]
    public function iniciarGeneracionVideo($datosvideo)
    {
        try {
            // Solo procesar si son imágenes
            if ($datosvideo['tipo'] === 'video') {
                $this->generarVideo($datosvideo);
            }
        } catch (Exception $e) {
            $this->errorMessage = 'Error inesperado: ' . $e->getMessage();
            $this->isGenerating = false;
            $this->videoGenerating=false;
            
            Log::error('Error en generador: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $e->getMessage());
            $this->dispatch('errorOcurrido');
        }
    }
    
    public function generarVideo($datosvideo)
{
    try {
        $prompt = $datosvideo['prompt'];
        $aspecto=$datosvideo['ratio'];
        
        Log::info("Iniciando generación de video con {$this->servicioImagen}. Prompt: " . $prompt);
        
        $this->chatHistory[] = [
            'tipo' => 'usuario',
            'contenido' => $prompt,
            'tiempo' => now()->format('H:i')
        ];
        
        session()->put('generador_chat_history', $this->chatHistory);
        $this->prompt = '';
        
        $this->dispatch('historialActualizado');
        
        switch ($this->servicioImagen) {
            case 'gemini':
                $this->generarVideoConVeo2($prompt);
                break;
            case 'runway':
            case 'runway2':
                $this->generarVideoConRunway($prompt);
                break;
            case 'luma':
            case 'luma2':
                $this->generarVideoConLuma($prompt,$aspecto);
                break;
            default:
                throw new Exception("Servicio de generación de video no reconocido: {$this->servicioImagen}");
        }
    } catch (Exception $e) {
        $this->isGenerating = false;
       $this->videoGenerating=false;
        $this->errorMessage = $e->getMessage();
        session()->flash('error', 'Error generando video: ' . $e->getMessage());
        Log::error("Error en generarVideo: " . $e->getMessage());
        $this->dispatch('errorOcurrido');

    }
}
   private function generarVideoConLuma($prompt,$aspecto, $modelo = null)
{
    try {
        // dd($this->imageFilesStart);
        // Determinar el modelo a usar basado en el servicio seleccionado
        $modelo = $this->servicioImagen === 'luma2' ? 'ray-2' : 'ray-flash-2';
        $aspecto=$this->ratioVideo;
       
        Log::info("Iniciando generación de video con {$this->servicioImagen}. Prompt: " . $prompt);
        // if($prompt){
        //         $this->dispatch('videoEnviadoALuma', ['taskId' => '9ac85530-188f-44e8-8611-6fc34eab559c']);
        // return;
        //     }
        
        $params = [
            'prompt' => $prompt,
            'aspect_ratio'=>$aspecto,
            'model' => $modelo,
            'resolution' => '720p',
            'duration' => $this->duracionVideo
        ];


 // Procesar imagen de inicio si existe
        // if (!empty($this->imageFilesStart)) {
        //     $imageFile = $this->imageFilesStart[0];
            
        //     if (is_object($imageFile)) {
        //         // Si es un archivo temporal subido manualmente
        //         $imageUrlStart = $this->subirImagenAS3($imageFile);
        //     } else {
        //         // Si es una imagen generada (ya tenemos la URL)
        //         $imageUrlStart = $imageFile['url'];
        //     }
            
        //     if ($imageUrlStart) {
        //         $params['keyframes']['frame0'] = [
        //             'type' => 'image',
        //             'url' => $imageUrlStart
        //         ];
        //         Log::info("Agregando imagen de inicio como keyframe para Luma", ['url' => $imageUrlStart]);
        //     }
        // }

        
        // Procesar imagen de inicio si existe
        if (!empty($this->imageFilesStart)) {
            $imageUrlStart = $this->subirImagenAS3($this->imageFilesStart[0]);
            if ($imageUrlStart) {
                $params['keyframes']['frame0'] = [
                    'type' => 'image',
                    'url' => $imageUrlStart
                ];
                Log::info("Agregando imagen de inicio como keyframe para Luma", ['url' => $imageUrlStart]);
            }
        }
        
        // Procesar imagen de fin si existe
        if (!empty($this->imageFilesEnd)) {
            $imageUrlEnd = $this->subirImagenAS3($this->imageFilesEnd[0]);
            if ($imageUrlEnd) {
                $params['keyframes']['frame1'] = [
                    'type' => 'image',
                    'url' => $imageUrlEnd
                ];
                Log::info("Agregando imagen de fin como keyframe para Luma", ['url' => $imageUrlEnd]);
            }
        }
        
        // Si hay keyframes, usar el método con keyframes
        if (isset($params['keyframes'])) {
            $response = LumaService::generateVideoFromPromptWithKeyframes($params);
        } else {
            $response = LumaService::generateVideoFromPrompt($prompt, $aspecto,$this->duracionVideo, $modelo);
        }

        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Error al iniciar la generación del video con Luma');
        }

        $operationId = $response['data']['id'] ?? null;

        if (empty($operationId)) {
            throw new Exception('No se recibió un ID de operación válido de Luma');
        }

        Log::info("ID de operación Luma:", ['operationId' => $operationId]);
        
        
        $this->isGenerating = true;
        $this->videoGenerating = true;
        $this->currentProcessingData = [
            'prompt' => $prompt,
            'taskId' => $operationId,
            'service' => 'luma',
            'processed' => false,
            'lastCheck' => time() - 10
        ];
        // Registrar el uso después de iniciar la generación
        ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
        $this->dispatch('videoEnviadoALuma', ['taskId' => $operationId]);


    } catch (Exception $e) {
         $this->isGenerating = false;
        $this->videoGenerating = false;
        $this->errorMessage = $e->getMessage();
        session()->flash('error', 'Error generando video con Luma: ' . $e->getMessage());
        Log::error("Error en generarVideoConLuma: " . $e->getMessage());
        $this->dispatch('errorOcurrido');

    }
}
public function verificarEstadoVideoLuma($taskId)
{
    try {
        if (empty($taskId)) {
            Log::error("Error: ID de tarea de Luma es nulo o vacío");
            throw new Exception('ID de tarea de Luma no válido');
        }
        
        Log::info("Verificando estado de video Luma con ID: " . $taskId);
        
        $response = LumaService::getGenerationStatusById($taskId);

        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Error al verificar el estado del video con Luma');
        }

        $estado = $response['data']['state'] ?? null;
        Log::info("Estado actual de video Luma: " . $estado);

        // Actualizar el mensaje de estado
        $this->generatingMessage = "Estado actual: {$estado}";

        if ($estado === 'completed') {
            $videoUrl = $response['data']['assets']['video'] ?? null;
            $thumbnailUrl = $response['data']['assets']['image'] ?? null;
            
            if (empty($videoUrl)) {
                throw new Exception('El video se completó pero no se recibió una URL válida');
            }
            
            Log::info("Video Luma completado. URL: " . $videoUrl);
            
            // IMPORTANTE: Verificar si ya hemos procesado este video
            if (isset($this->currentProcessingData['processed']) && $this->currentProcessingData['processed'] === true) {
                Log::info("Este video ya ha sido procesado, ignorando actualización duplicada");
                return;
            }
            
            // Marcar como procesado para evitar duplicados
            $this->currentProcessingData['processed'] = true;
            
            // Actualizar resultados
            $resultados = [
                'url' => $videoUrl,
                'text' => $this->currentProcessingData['prompt'] ?? 'Video generado con Luma',
                'type' => 'video',
                'esVideo' => true
            ];
            
            // Agregar respuesta al historial de chat
            $this->agregarRespuestaSistema($resultados);
            
            // Actualizar estados
            $this->isGenerating = false;
            $this->videoGenerating = false;
            
            // Notificar al frontend para detener el intervalo
            $this->dispatch('videoGeneradoExitosamente');
            
        } elseif ($estado === 'failed') {
            throw new Exception('La generación del video falló: ' . ($response['data']['failure_reason'] ?? 'Razón desconocida'));
        }

    } catch (Exception $e) {
        $this->isGenerating = false;
        $this->videoGenerating = false;
        $this->errorMessage = $e->getMessage();
        session()->flash('error', 'Error verificando estado del video con Luma: ' . $e->getMessage());
        Log::error("Error en verificarEstadoVideoLuma: " . $e->getMessage());
        $this->dispatch('errorOcurrido');
    }
}

    /**
     * Método para generar video con Veo2
     */
    
    public function generarVideoConVeo2($promptText)
    {
        try {
            $aspecto=$this->ratioVideo;
        
            Log::info("Iniciando generación de video con gemini. Prompt: {$promptText}");
    
            $imageBase64 = null;
    

          // Bloque de prueba: info de la imagen base64 y detener antes de llamar a la API
// if (!empty($imageBase64)) {
//     Log::info('PRUEBA: Imagen base64 generada correctamente', [
//         'base64_length' => strlen($imageBase64),
//         'base64_start' => substr($imageBase64, 0, 50),
//         'base64_end' => substr($imageBase64, -50),
//     ]);
//     session()->flash('mensaje', 'Imagen base cargada y convertida a base64 correctamente (ver logs)');
//     $this->isGenerating = false;
//     $this->videoGenerating=false;
//     $this->veo2Generating=true;
//     $this->dispatch('videoEnviadoAVeo2', ['taskId' => 'd0bavffs8nwg']);
//     return;
// }
 //  Usa imageFilesStart  para la imagen de inicio
        if (!empty($this->imageFilesStart) && count($this->imageFilesStart) > 0) {
            $imageFile = $this->imageFilesStart[0];
            try {
                $imagePath = $imageFile->getRealPath();
                $imageContent = file_get_contents($imagePath);
                $imageBase64 = base64_encode($imageContent);
                Log::info('Imagen base procesada para video (carga manual)', [
                    'originalName' => $imageFile->getClientOriginalName(),
                    'size' => $imageFile->getSize()
                ]);
            } catch (\Exception $e) {
                Log::warning('Error procesando imagen subida para video: ' . $e->getMessage());
            }
             
        }

        
        if (empty($imageBase64)) {
            Log::info('Generando video sin imagen base');
        }

        // Si no pudimos obtener la imagen, notificar al usuario
        if (empty($imageBase64) && !empty($this->imageFilesStart)) {
            session()->flash('error', 'No se pudo cargar la imagen seleccionada. Intente nuevamente.');
            $this->dispatch('errorOcurrido');
            $this->isGenerating = false;
            return;
        }
    
            // Llamada al servicio
            $response = GeminiService::generateVideo(
                $promptText,
                "veo-2.0-generate-001",
                $aspecto,
                $imageBase64
            );
    
            Log::info("Respuesta de generación Veo2:", $response);
    
            if (!$response['success']) {
                throw new Exception($response['error'] ?? 'Error al iniciar la generación del video');
            }
    
            // Extraer el ID de operación correctamente
            $operationId = null;
            if (isset($response['operationName'])) {
                $operationId = basename($response['operationName']);
            } elseif (isset($response['operationId'])) {
                $operationId = $response['operationId'];
            }
    
            if (empty($operationId)) {
                throw new Exception('No se recibió un ID de operación válido');
            }
    
            Log::info("ID de operación Veo2:", ['operationId' => $operationId]);
    
            // Guardar el estado de generación y el ID
            $this->isGenerating = true;
            $this->veo2Generating = true;
            $this->veo2TaskId = $operationId;
            $this->currentProcessingData = [
                'prompt' => $promptText,
                'taskId' => $operationId,
                'service' => 'veo2',
                'processed' => false,
                'lastCheck' => time() - 10 // Para permitir la primera verificación inmediata
            ];
    // Registrar el uso después de iniciar la generación
        ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
            // EMITIR EL EVENTO PARA EL FRONTEND
            $this->dispatch('videoEnviadoAVeo2', ['taskId' => $operationId]);
    
            // Forzar actualización de la UI
            $this->dispatch('generacionIniciada');
    
            
    
        } catch (Exception $e) {
            $this->isGenerating = false;
            $this->videoGenerating=false;
            $this->isTyping = false;
            $this->errorMessage = $e->getMessage();
            session()->flash('error', 'Error generando video: ' . $e->getMessage());
            Log::error("Error en generarVideoConVeo2: " . $e->getMessage());
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Verifica el estado de un video en Veo2
     */
public function verificarEstadoVideoVeo2($taskId = null)
{
    try {
        if (!$this->veo2Generating) {
            Log::info("Verificación de Veo2 ignorada: ya no estamos en modo de generación");
            return;
        }

        $taskId = $taskId ?? $this->currentProcessingData['taskId'] ?? null;
        if (!$taskId) {
            throw new Exception('No hay ID de tarea para verificar');
        }
        $response = GeminiService::getVideoOperation($taskId);
        
        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Error al verificar el estado del video');
        }

        if ($response['done']) {
            // Verificar si hay videos generados
            if (isset($response['response']['response']['generateVideoResponse']['generatedSamples'])) {
                $generatedSamples = $response['response']['response']['generateVideoResponse']['generatedSamples'];
                
                // Verificar si ya procesamos estos videos
                if (isset($this->currentProcessingData['processed']) && $this->currentProcessingData['processed'] === true) {
                    Log::info("Estos videos ya han sido procesados, ignorando actualización duplicada");
                    return;
                }
                
                $this->currentProcessingData['processed'] = true;
                
                // Procesar cada video generado
                foreach ($generatedSamples as $index => $sample) {
                    if (isset($sample['video']['uri'])) {
                        $resultados = [
                            'text' => $this->currentProcessingData['prompt'] ?? "Video " . ($index + 1) . " generado con Veo2",
                            'url' => $sample['video']['uri'],
                            'type' => 'video',
                            'esVideo' => true
                        ];
                        
                        $this->agregarRespuestaSistema($resultados);
                    }
                }
                
                // Actualizar estados
                $this->isGenerating = false;
                $this->veo2Generating = false;
                $this->videoGenerating = false;
                $this->isTyping = false;
                $this->generatingMessage = 'Videos generados correctamente';
                
                $this->dispatch('videoGeneradoExitosamente');
            }
        } else {
            $this->generatingMessage = "Video en proceso... Esto puede tomar hasta 2 minutos.";
        }
        
    } catch (Exception $e) {
        Log::error("Error verificando estado de video Veo2: " . $e->getMessage());
        $this->manejarError($e);
    }
}
    
    /**
     * Función recursiva para buscar la URL del video en una estructura anidada
     */
    private function findVideoUrlRecursive($data, $depth = 0) 
    {
        // Evitar recursión infinita
        if ($depth > 10) return null;
        
        // Si es string y parece una URL de video
        if (is_string($data) && strpos($data, '/files/') !== false && strpos($data, 'download') !== false) {
            return $data;
        }
        
        // Si es array, buscar en cada elemento
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Si encontramos una clave que parece contener una URL de video
                if (is_string($value) && strpos($value, '/files/') !== false && strpos($value, 'download') !== false) {
                    return $value;
                }
                
                // Si es un array, buscar recursivamente
                if (is_array($value)) {
                    $result = $this->findVideoUrlRecursive($value, $depth + 1);
                    if ($result) return $result;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Genera un video utilizando el servicio de IA de Runway
     */
    private function generarVideoConRunway($prompt,$model=null)
    {
        try {
           $modelo = $this->servicioImagen === 'runway' ? 'gen3a_turbo' : 'gen4_turbo';
           $aspecto=$this->ratioVideo;
           
            Log::info("Iniciando generación de video con Runway. Prompt: " . $prompt.$modelo);
            
            // Activar indicadores de generación
            $this->isGenerating = true;
             $this->runwayGenerating = true;
            $this->videoGenerating = true;
            $this->generatingMessage = 'Preparando imágenes para Runway...';
            
            // Forzar actualización de la UI para mostrar el spinner inmediatamente
            $this->dispatch('generacionIniciada');
            
            // Preparar las imágenes
            $promptImages = [];
            
            // Procesar imagen de inicio si existe
            if (!empty($this->imageFilesStart)) {
                Log::info("Procesando imagen de inicio para Runway...");
                $imageDataStart = $this->procesarImagenParaRunway($this->imageFilesStart[0]);
                if ($imageDataStart) {
                    Log::info("Imagen de inicio procesada correctamente");
                    $promptImages[] = [
                        'uri' => $imageDataStart,
                        'position' => 'first'
                    ];
                } else {
                    Log::error("Error procesando imagen de inicio para Runway");
                    throw new Exception('Error procesando la imagen de inicio. Por favor, inténtalo de nuevo.');
                }
            }
            
            // Procesar imagen de fin si existe
            if (!empty($this->imageFilesEnd)) {
                Log::info("Procesando imagen de fin para Runway...");
                $imageDataEnd = $this->procesarImagenParaRunway($this->imageFilesEnd[0]);
                if ($imageDataEnd) {
                    Log::info("Imagen de fin procesada correctamente");
                    $promptImages[] = [
                        'uri' => $imageDataEnd,
                        'position' => 'last'
                    ];
                } else {
                    Log::error("Error procesando imagen de fin para Runway");
                    throw new Exception('Error procesando la imagen de fin. Por favor, inténtalo de nuevo.');
                }
            }
            
            // Verificar que hay al menos una imagen
            if (empty($promptImages)) {
                Log::warning("No se proporcionaron imágenes para Runway");
                throw new Exception('Debes proporcionar al menos una imagen (inicio o fin) para generar el video.');
            }
            
            Log::info("Enviando a Runway " . count($promptImages) . " imágenes con prompt: " . $prompt);
            $this->generatingMessage = 'Enviando solicitud a Runway...';
            
            // Generar el video con Runway
            $response = RunWayService::generateGen3aTurboVideo(
                $prompt,
                $modelo,
                $promptImages,
                $aspecto,
                5
            );
            
            // Verificar si hubo errores en la generación
            if (!isset($response['success']) || $response['success'] !== true) {
                $error = $response['error'] ?? 'Error desconocido al comunicarse con Runway';
                Log::error("Error en respuesta de Runway: " . $error);
                throw new Exception('Error en la comunicación con Runway: ' . $error);
            }
            
            // Extraer el ID de tarea
            if (!isset($response['data']['id'])) {
                throw new Exception('No se recibió ID de tarea en la respuesta de Runway');
            }
            
            $taskId = $response['data']['id'];
            Log::info("Video en proceso, ID de tarea: " . $taskId);
            // Registrar el uso después de iniciar la generación
        ServiceUsages::incrementRequestCount(Auth::id(), $this->getServiceNameForTracking());
            // Guardar datos para uso posterior
            $this->currentProcessingData = [
                'taskId' => $taskId,
                'prompt' => $prompt
            ];
            
            // Actualizar mensaje
            $this->generatingMessage = 'Video en proceso de generación...';
            
            // Emitir evento para iniciar verificación en JavaScript
            $this->dispatch('videoEnviadoARuntimeway', ['taskId' => $taskId]);
            
        } catch (Exception $e) {
            $this->isGenerating = false;
            $this->runwayGenerating = false;
            $this->videoGenerating = false;
            $this->isTyping = false;
            $this->errorMessage = $e->getMessage();
            session()->flash('error', 'Error generando video: ' . $e->getMessage());
            Log::error("Error generando video con Runway: " . $e->getMessage());
            $this->dispatch('errorOcurrido');
        }
    }
    
    /**
     * Procesa una imagen para enviarla a Runway
     */
    private function procesarImagenParaRunway($file)
    {
        try {
            // Detectar el tipo de objeto para depuración
            $fileType = get_class($file);
            Log::info("Tipo de archivo recibido: " . $fileType);
            
            // Si es un objeto de archivo temporal de Livewire
            if ($file instanceof \Livewire\TemporaryUploadedFile) {
                // Obtener la ruta real del archivo
                $realPath = $file->getRealPath();
                Log::info("Ruta real del archivo: " . $realPath);
                
                // Verificar que el archivo existe
                if (!file_exists($realPath)) {
                    Log::error("El archivo no existe en la ruta: " . $realPath);
                    return null;
                }
                
                // Obtener el contenido del archivo directamente
                $content = file_get_contents($realPath);
                if ($content === false) {
                    Log::error("No se pudo leer el contenido del archivo");
                    return null;
                }
                
                // Obtener el tipo MIME del archivo
                $mimeType = $file->getMimeType();
                Log::info("Tipo MIME: " . $mimeType);
                
                // Codificar a base64
                $base64 = base64_encode($content);
                
                // Verificar que tenemos un base64 válido
                if (empty($base64)) {
                    Log::error("Error al codificar la imagen a base64");
                    return null;
                }
                
                // Crear Data URI
                $dataUri = 'data:' . $mimeType . ';base64,' . $base64;
                Log::info("Data URI creado correctamente, longitud: " . strlen($dataUri));
                return $dataUri;
            }
            // Esta parte es crucial - agregamos más verificaciones para objetos temporales
            else if (is_object($file) && method_exists($file, 'getRealPath') && method_exists($file, 'getMimeType')) {
                // Es un objeto de archivo, pero no se detectó correctamente como TemporaryUploadedFile
                Log::info("Archivo detectado como objeto con métodos de archivo, procesando como archivo temporal");
                
                $realPath = $file->getRealPath();
                $content = file_get_contents($realPath);
                $mimeType = $file->getMimeType() ?: 'image/jpeg';
                
                $base64 = base64_encode($content);
                return 'data:' . $mimeType . ';base64,' . $base64;
            }
            // Si es una cadena (url o data uri)
            else if (is_string($file)) {
                if (strpos($file, 'data:image/') === 0 || filter_var($file, FILTER_VALIDATE_URL)) {
                    return $file;
                }
                Log::error("La cadena no es una URL o Data URI válido");
                return null;
            }
            // Si es un array que contiene la información del archivo
            else if (is_array($file) && isset($file['tmp_name'])) {
                $content = file_get_contents($file['tmp_name']);
                $mimeType = $file['type'] ?? 'image/jpeg';
                
                $base64 = base64_encode($content);
                return 'data:' . $mimeType . ';base64,' . $base64;
            }
            
            // Si llegamos aquí, no pudimos procesar el archivo
            Log::error("Tipo de archivo no soportado: " . (is_object($file) ? get_class($file) : gettype($file)));
            return null;
        } catch (\Exception $e) {
            Log::error('Error procesando imagen para Runway: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
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
        $this->chatHistory = [];
        session()->forget('generador_chat_history');
        $this->imageFiles = [];
        $this->imageFilesStart = []; // <--- Limpia imagen de inicio
        $this->imageFilesEnd = [];   // <--- Limpia imagen de fin
        $this->temporaryImages = [];
        $this->temporaryImagesStart = [];
        $this->temporaryImagesEnd = [];
        $this->imagenBaseParaVideo = null;
        $this->documentoSeleccionado = null;
        $this->documentoInfo = null;
        $this->isGenerating = false;
        $this->runwayGenerating = false;
        $this->veo2Generating = false;
        $this->errorMessage = null;
        $this->prompt = '';
        session()->forget('expanded_images');
        $this->expandedImages = [];
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
            'esVideo' => $this->tipo === 'video'
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
        session()->put('generador_chat_history', $this->chatHistory);
        
        // $this->prompt = '';
        $this->imageFiles = [];
        
        // Desactivar indicadores
        $this->isGenerating = false;
        $this->runwayGenerating = false;
        $this->videoGenerating = false;
        $this->isTyping = false;
         $this->imageFilesStart = []; // <--- Limpia imagen de inicio
        $this->imageFilesEnd = [];   // <--- Limpia imagen de fin
        
        // Disparar eventos para actualización y scroll
        $this->dispatch('historialActualizado');
        $this->dispatch('imagenGenerada');
    }
    
    // Agregar este método helper para guardar imágenes en la carpeta pública
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
    
    
    
    // Método para convertir ratio a dimensiones
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
     * Método para descargar una imagen desde una URL y guardarla localmente
     * 
     * @param string $imageUrl URL de la imagen a descargar
     * @param string $servicioOrigen Nombre del servicio que generó la imagen
     * @return string|null URL local de la imagen guardada o null si hubo un error
     */
    // private function descargarYGuardarImagen($imageUrl, $servicioOrigen)
    // {
    //     try {
    //         // Crear carpeta si no existe
    //         $uploadPath = public_path('uploads/image-ia');
    //         if (!file_exists($uploadPath)) {
    //             mkdir($uploadPath, 0755, true);
    //         }

    //         // Nombre de archivo único
    //         $filename = uniqid($servicioOrigen . '_') . '_' . time() . '.jpg';
    //         $filePath = $uploadPath . '/' . $filename;

    //         // Descargar la imagen
    //         $imageContent = file_get_contents($imageUrl);
    //         if ($imageContent === false) {
    //             Log::error('Error descargando imagen desde URL: ' . $imageUrl);
    //             return null;
    //         }

    //         // Si es Flux, redimensionar la imagen para que sea más consistente
    //         if ($servicioOrigen === 'flux') {
    //             // Crear una imagen desde el contenido descargado
    //             $image = imagecreatefromstring($imageContent);
    //             if ($image !== false) {
    //                 // Obtener dimensiones originales
    //                 $width = imagesx($image);
    //                 $height = imagesy($image);
                    
    //                 // Calcular nuevas dimensiones manteniendo proporción
    //                 $maxDim = 1024; // Tamaño máximo consistente con otros servicios
    //                 if ($width > $maxDim || $height > $maxDim) {
    //                     if ($width > $height) {
    //                         $newWidth = $maxDim;
    //                         $newHeight = round($height * ($maxDim / $width));
    //                     } else {
    //                         $newHeight = $maxDim;
    //                         $newWidth = round($width * ($maxDim / $height));
    //                     }
                        
    //                     // Crear imagen redimensionada
    //                     $newImage = imagecreatetruecolor($newWidth, $newHeight);
    //                     imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        
    //                     // Guardar la imagen redimensionada
    //                     imagejpeg($newImage, $filePath, 90); // 90% de calidad
                        
    //                     // Liberar memoria
    //                     imagedestroy($image);
    //                     imagedestroy($newImage);
    //                 } else {
    //                     // Si la imagen es más pequeña que el máximo, guardarla tal cual
    //                     file_put_contents($filePath, $imageContent);
    //                 }
    //             } else {
    //                 // Si no se pudo crear la imagen, guardar el contenido original
    //                 file_put_contents($filePath, $imageContent);
    //             }
    //         } else {
    //             // Para otros servicios, guardar la imagen tal cual
    //             file_put_contents($filePath, $imageContent);
    //         }

    //         // Obtener el prefijo desde .env
    //         $prefix = trim(env('APP_PUBLIC_PREFIX', ''), '/');
    //         $urlPath = ($prefix ? "/$prefix" : '') . "/uploads/image-ia/$filename";

    //         // Devolver URL completa
    //         return url($urlPath);

    //     } catch (Exception $e) {
    //         Log::error('Error guardando imagen descargada: ' . $e->getMessage());
    //         return null;
    //     }
    // }
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
     * Carga una imagen generada para crear un video
     */
    public function cargarImagenParaVideo($imageUrl)
    {
        try {
            // Cambiar a modo video
            $this->cambiarTipo('video');
             // Cambiar servicio a Gemini/Veo2
            // $this->servicioImagen = 'luma';
            // Verificar si la URL es válida
            if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                Log::info("Cargando imagen generada para video: " . $imageUrl);
                
                // Crear un array con la información necesaria
                $imageData = [
                    'is_generated' => true,
                    'url' => $imageUrl,
                    'name' => basename($imageUrl)
                ];
                
                // Limpiar cualquier imagen anterior
                $this->imageFilesStart = [$imageData];
                $this->imagenBaseParaVideo = null;
                
                // Mostrar mensaje informativo
                session()->flash('info', 'Imagen cargada para generar video. Agrega una descripción y presiona "Generar Video".');
                
                // Forzar actualización de la vista
                $this->dispatch('runwayImageUpdated');
            } else {
                Log::warning("URL de imagen no válida: " . $imageUrl);
                session()->flash('error', 'No se pudo cargar la imagen para el video');
            }
        } catch (\Exception $e) {
            Log::error("Error cargando imagen para video: " . $e->getMessage());
            session()->flash('error', 'Error al cargar la imagen: ' . $e->getMessage());
        }
    }
    
    /**
     * Quita la imagen base seleccionada para video
     */
    public function quitarImagenBaseVideo()
    {
        $this->imagenBaseParaVideo = null;
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
     * Depurar la respuesta para verificar su estructura exacta
     */
    private function dumpResponse($response)
    {
        ob_start();
        var_dump($response);
        $output = ob_get_clean();
        Log::debug("Estructura de respuesta Runway: " . $output);
    }

    /**
     * Verifica el estado de un video en generación con Runway
     */
    public function verificarEstadoVideoRunway($taskId = null)
    {
        try {
            // Si no estamos en modo de generación, no hacer nada
            if (!$this->runwayGenerating) {
                Log::info("Verificación de Runway ignorada: ya no estamos en modo de generación");
                return;
            }
            
            // Si se proporciona un nuevo ID, actualizar el almacenado
            if ($taskId) {
                $this->currentProcessingData['taskId'] = $taskId;
            }
            
            // Usar el ID almacenado si no se proporciona uno nuevo
            $taskId = $this->currentProcessingData['taskId'] ?? null;
            
            if (empty($taskId)) {
                $this->runwayGenerating = false;
                $this->videoGenerating = false;
                $this->isGenerating = false;
                $this->errorMessage = 'No hay ID de tarea para verificar';
                session()->flash('error', 'Error verificando estado: No hay ID de tarea');
                Log::error('Intento de verificar estado sin ID de tarea');
                $this->dispatch('errorOcurrido');
                return;
            }
            
            // Evitar verificaciones demasiado frecuentes
            $lastCheck = $this->currentProcessingData['lastCheck'] ?? 0;
            $now = time();
            if ($now - $lastCheck < 1) {  // Al menos 1 segundo entre verificaciones
                return;
            }
            $this->currentProcessingData['lastCheck'] = $now;
            
            Log::info("Verificando estado de video Runway, ID: " . $taskId);
            
            // Obtener el estado desde la API
            $response = RunWayService::checkVideoGenerationStatus($taskId);
            
            // Depurar la respuesta completa con una función más detallada
            $this->dumpResponse($response);
            
            // Si hay error en la respuesta
            if (!isset($response['success']) || $response['success'] !== true) {
                $error = $response['error'] ?? 'Error desconocido al verificar estado';
                Log::error("Error en respuesta de API Runway: " . $error);
                // No lanzar excepción para errores temporales, solo registrar
                $this->generatingMessage = "Error temporal verificando estado. Reintentando...";
                return;
            }
            
            // Obtener el estado del video - NOTA: Asegúrate de que esto coincida con la estructura real
            $status = $response['data']['status'] ?? '';
            Log::info("Estado del video Runway: " . $status);
            
            // Si el video está listo (COMPLETED o SUCCEEDED)
            if ($status === 'COMPLETED' || $status === 'SUCCEEDED') {
                // Verificar TODAS las posibilidades de ubicación de la URL del video
                $videoUrl = null;
                
                // 1. Verificar en output_video_url
                if (isset($response['data']['output_video_url']) && !empty($response['data']['output_video_url'])) {
                    $videoUrl = $response['data']['output_video_url'];
                    Log::debug("URL encontrada en output_video_url");
                } 
                // 2. Verificar en output[0]
                else if (isset($response['data']['output']) && is_array($response['data']['output']) && !empty($response['data']['output'][0])) {
                    $videoUrl = $response['data']['output'][0];
                    Log::debug("URL encontrada en output[0]");
                }
                // 3. Verificar directamente en output (si es string)
                else if (isset($response['data']['output']) && is_string($response['data']['output']) && !empty($response['data']['output'])) {
                    $videoUrl = $response['data']['output'];
                    Log::debug("URL encontrada en output (string)");
                }
                // 4. Si la respuesta tiene una estructura diferente, intentar buscar la URL
                else {
                    // Recorrer recursivamente la respuesta buscando URL
                    $videoUrl = $this->findVideoUrl($response);
                    if ($videoUrl) {
                        Log::debug("URL encontrada mediante búsqueda recursiva");
                    }
                }
                
                // Si no encontramos URL
                if (!$videoUrl) {
                    Log::error("Estado " . $status . " pero sin URL de video", ['response' => $response]);
                    throw new Exception('Video generado pero no se encontró la URL');
                }
                
                Log::info("¡VIDEO GENERADO CORRECTAMENTE CON RUNWAY! URL: " . $videoUrl);
                
                // IMPORTANTE: Verificar si ya hemos procesado este video para evitar duplicados
                if (isset($this->currentProcessingData['processed']) && $this->currentProcessingData['processed'] === true) {
                    Log::info("Este video ya ha sido procesado, ignorando actualización duplicada");
                    // Asegurar que los estados estén correctamente actualizados
                    $this->isGenerating = false;
                    $this->runwayGenerating = false;
                    $this->videoGenerating = false;
                    $this->isTyping = false;
                    return;
                }
                
                // Marcar como procesado para evitar duplicados
                $this->currentProcessingData['processed'] = true;
                
                // Crear resultado y agregar al historial
                $resultados = [
                    'text' => $this->currentProcessingData['prompt'] ?? 'Video generado con Runway',
                    'url' => $videoUrl,
                    'type' => 'video',
                    'esVideo' => true
                ];
                
                // Agregar al historial
                $this->agregarRespuestaSistema($resultados);
                
                // Actualizar estado
                $this->isGenerating = false;
                $this->runwayGenerating = false;
                $this->videoGenerating = false;
                $this->isTyping = false;
                $this->generatingMessage = 'Video generado correctamente';
                
                // Notificar al frontend para detener el intervalo
                $this->dispatch('videoGeneradoExitosamente');
            }
            // Si hubo error
            else if ($status === 'FAILED') {
                $errorMsg = $response['data']['error'] ?? 'Error desconocido en la generación';
                Log::error("Error generando video con Runway: " . $errorMsg);
                throw new Exception('Error en la generación: ' . $errorMsg);
            }
            // Si está procesando
            else if ($status === 'PROCESSING' || $status === 'PENDING') {
                // Actualizar mensaje con progreso si está disponible
                $progress = isset($response['data']['progress']) ? intval($response['data']['progress'] * 100) : null;
                
                if ($progress !== null && $progress > 0) {
                    $this->generatingMessage = "Video en proceso: {$progress}% completado...";
                } else {
                    $this->generatingMessage = "Video en proceso... Esto puede tomar hasta 2 minutos.";
                }
            } else {
                // Estado desconocido
                Log::warning("Estado de video desconocido: " . $status);
                $this->generatingMessage = "Video en proceso (estado: {$status})...";
            }
        } catch (Exception $e) {
            Log::error("Error verificando estado de video Runway: " . $e->getMessage());
            
            // Si el error es fatal, detener la verificación
            if (strpos($e->getMessage(), 'Video generado pero') === false) {
                $this->isGenerating = false;
                $this->runwayGenerating = false;
                $this->isTyping = false;
                $this->errorMessage = $e->getMessage();
                session()->flash('error', 'Error verificando estado: ' . $e->getMessage());
                $this->dispatch('errorOcurrido');
            } else {
                // Para errores no fatales, mostrar mensaje pero continuar
                $this->generatingMessage = "Error temporal: " . $e->getMessage();
            }
        }
    }

    /**
     * Busca recursivamente una URL de video en un array
     */
    private function findVideoUrl($array, $depth = 0) 
    {
        if ($depth > 5) return null; // Evitar recursión infinita
        
        if (is_string($array) && strpos($array, '.mp4') !== false) {
            return $array;
        }
        
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_string($value) && strpos($value, '.mp4') !== false) {
                    return $value;
                }
                if (is_array($value)) {
                    $result = $this->findVideoUrl($value, $depth + 1);
                    if ($result) return $result;
                }
            }
        }
        
        return null;
    }
    
    public function render()
    {
        return view('livewire.new-generador');
    }

    /**
     * Función auxiliar para restablecer los estados después de la generación
     */
    private function resetGenerationStates()
    {
        $this->isGenerating = false;
        $this->videoGenerating = false;
        $this->isTyping = false;
        $this->runwayGenerating = false;
        // Cualquier otro estado que deba restablecerse
    }

    /**
     * Método de prueba para verificar el estado de un video Veo2 con un ID existente
     * Esto evita generar nuevos videos y gastar tokens
     */
    public function probarVerificacionVeo2()
    {
        try {
            // Usar un ID de operación que ya existe
            $operationId = "p8474g5530j0";
            
            Log::info("SIMULACIÓN - Usando ID de operación existente:", ['operationId' => $operationId]);
            
            // Configurar los estados necesarios como si estuviéramos generando
            $this->isGenerating = true;
            $this->veo2Generating = true;
            $this->generatingMessage = "Probando verificación de video...";
            
            // Guardar el ID para futuras verificaciones
            $this->veo2TaskId = $operationId;
            
            // Configurar los datos de procesamiento
            $this->currentProcessingData = [
                'prompt' => 'Simulación de video con Veo2',
                'taskId' => $operationId,
                'service' => 'veo2',
                'processed' => false,
                'lastCheck' => time() - 10 // Restar 10 segundos para asegurar la primera verificación
            ];
            
            // IMPORTANTE: Emitir el evento después de configurar todos los datos
            $this->dispatch('videoEnviadoAVeo2', taskId: $operationId);
            
            // Forzar sincronización con el frontend
            $this->dispatch('generacionIniciada');
            
            // Hacer una verificación inicial
            $this->verificarEstadoVideoVeo2($operationId);
            
            // Mensaje flash para confirmar que el proceso de prueba ha iniciado
            session()->flash('mensaje', 'Prueba de verificación iniciada con ID: ' . $operationId);
            
        } catch (Exception $e) {
            Log::error("Error en probarVerificacionVeo2:", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->isGenerating = false;
            $this->veo2Generating = false;
            $this->errorMessage = $e->getMessage();
            session()->flash('error', 'Error en prueba: ' . $e->getMessage());
            $this->dispatch('errorOcurrido');
        }
    }

   
    /**
 * Sube una imagen a S3 y devuelve la URL
 */
private function subirImagenAS3($file)
{
    try {
        if (!$file) {
            return null;
        }
        
        // Generar un nombre único para el archivo
        $fileName = 'luma-keyframe-' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Ruta en S3
        $filePath = 'genesis/inputs-image/' . $fileName;
        
        // Subir el archivo a S3
        $s3 = \Storage::disk('s3');
        $s3->put($filePath, file_get_contents($file->getRealPath()));
        
        // Obtener la URL del archivo
        $url = $s3->url($filePath);
        
        Log::info("Imagen subida a S3 correctamente", ['url' => $url]);
        
        return $url;
    } catch (\Exception $e) {
        Log::error("Error al subir imagen a S3: " . $e->getMessage());
        return null;
    }
}

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



public function quitarImagenExpandida($index)
{
    if (isset($this->expandedImages[$index])) {
        unset($this->expandedImages[$index]);
        $this->expandedImages = array_values($this->expandedImages);

         // Actualizar la sesión
        $expandedImages = session()->get('expanded_images', []);
        if (isset($expandedImages[$index])) {
            unset($expandedImages[$index]);
            session()->put('expanded_images', array_values($expandedImages));
        }
    }
}

// Método para limpiar el error
public function clearExpandError()
{
    $this->expandError = '';
    session()->forget('expand_error');
}

/**
 * Método para rellenar imagen usando Flux Pro
 */
public function rellenarImagenFlux($datos)
{
    try {
         set_time_limit(180); // 3 minutos
        // Limpiar errores previos
        $this->fillError = '';
        // dd($datos);
//         if($datos){
//  $this->dispatch('verificarEstadoFlux', [
//                 'generationId' => 'acc15502-8e87-44e3-964a-5efd5222c721',
//                 'prompt' => $this->promptFill,
//                 'type' => 'fill'
//             ]);
//             return;
//         }
       
        session()->forget('fill_error');
        
        Log::info('Iniciando método rellenarImagenFlux');
        $this->isGenerating = true;
        $this->fluxGenerating = true;

        // Verificar que tenemos una imagen base y una máscara
        if (empty($datos['imageBase64'])) {
            Log::info('No se encontró imagen base');
            throw new \Exception('No se encontró imagen base');
            
        }

        if (empty($datos['maskBase64'])) {
            Log::info('No se encontró máscara');
            throw new \Exception('No se encontró máscara');
            
        }

        $input_image = $datos['imageBase64'];
        $mask_image = $datos['maskBase64'];
    
        // Usar el prompt específico para fill o uno por defecto
        $prompt = !empty($this->promptFill) ? $this->promptFill : "Fill the masked area naturally, maintaining the same artistic style, lighting, and perspective as the original image.";

        Log::info("Llamando al servicio Flux Fill");
        $response = FluxService::FillImageFluxPro(
            $input_image,
            $mask_image,
            $prompt,
            50,        // steps
            true,      // prompt_upsampling
            null,      // seed
            50.75,     // guidance
            'jpeg',    // output_format
            2          // safety_tolerance
        );

        Log::info("Respuesta de FluxService Fill", ['response' => $response]);

        if (isset($response['error'])) {
            Log::error("Error recibido de FluxService Fill", ['error' => $response['error']]);
            throw new \Exception('Error con Flux Fill: ' . ($response['error'] ?? 'Error desconocido'));
        }

        if (isset($response['data'])) {
            $generationId = $response['data'];
            Log::info("ID de generación Fill recibido", ['generationId' => $generationId]);

            $this->dispatch('verificarEstadoFlux', [
                'generationId' => $generationId,
                'prompt' => $this->promptFill,
                'type' => 'fill'
            ]);
        } else {
            Log::error("Respuesta inesperada de FluxService Fill: no contiene 'data'");
            throw new \Exception('Respuesta inesperada de Flux Fill');
        }

    } catch (\Exception $e) {
        Log::error('Error en expandirImagenFlux', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        $this->fillError = $e->getMessage();
        session()->flash('fill_error', $e->getMessage());
        
        $this->isGenerating = false;
        $this->fluxGenerating = false;
        $this->dispatch('expansion-error');
    }
}

/**
 * Quitar imagen rellenada
 */
public function quitarImagenRellenada($index)
{
    if (isset($this->filledImages[$index])) {
        unset($this->filledImages[$index]);
        $this->filledImages = array_values($this->filledImages);
    }
}

/**
 * Limpiar error de fill
 */
public function clearFillError()
{
    $this->fillError = '';
    session()->forget('fill_error');
}

public function verificarLimitesServicioold()
    {
        // Limpiar cualquier mensaje de error anterior
        session()->forget('error');
        
        // Obtener el nombre del servicio actual
        $serviceName = $this->getServiceNameForTracking();
        
        // Verificar si el usuario ha alcanzado el límite
        if (ServiceUsages::hasReachedLimit(Auth::id(), $serviceName)) {
            $limit = ServiceUsages::getMonthlyLimit(Auth::id(), $serviceName);
            $usage = ServiceUsages::getCurrentUsage(Auth::id(), $serviceName);
            
            $message = "Has alcanzado tu límite mensual de solicitudes para este servicio.";
            // $message .= " Uso actual: $usage solicitudes.";
            
            session()->flash('error', $message);
            $this->dispatch('errorOcurrido');
            return true;
        }
        
        return false;
    }
    public function verificarLimitesServicio()
{
    // Limpiar cualquier mensaje de error anterior
    session()->forget('error');
    
    // Obtener el nombre del servicio actual
    $serviceName = $this->getServiceNameForTracking();
    
    // Para edición de imágenes, usar servicios específicos
    if ($this->modoEdicion === 'expand') {
        $serviceName = 'edicion-flux-expand';
    } elseif ($this->modoEdicion === 'fill') {
        $serviceName = 'edicion-flux-fill';
    }
    
    // Verificar si el usuario ha alcanzado el límite
    if (ServiceUsages::hasReachedLimit(Auth::id(), $serviceName)) {
        $limit = ServiceUsages::getMonthlyLimit(Auth::id(), $serviceName);
        $usage = ServiceUsages::getCurrentUsage(Auth::id(), $serviceName);
        
        $message = "Has alcanzado tu límite mensual de solicitudes para este servicio.";
        // $message .= " Uso actual: $usage solicitudes.";
        
        session()->flash('error', $message);
        $this->dispatch('errorOcurrido');
        return true;
    }
    
    return false;
}
    
     /**
 * Determina el nombre del servicio para registrar el uso
 */
private function getServiceNameForTracking(): string
{
    if ($this->tipo === 'imagen' || $this->tipo === 'editimagen') {
        return match($this->servicioImagen) {
            'gemini4' => 'imagen-gemini4',
            'gemini' => 'imagen-gemini3',
            'openai' => 'imagen-openai',
            'flux-kontext-max' => 'imagen-flux-kontext-max',
            'flux-kontext-pro' => 'imagen-flux-kontext-pro',
            'flux' => 'imagen-flux-pro',
            'fluxultra' => 'imagen-flux-ultra',
            default => 'imagen-generica'
        };
    } elseif ($this->tipo === 'video') {
        return match($this->servicioImagen) {
            'gemini' => 'video-veo2',
            'runway' => 'video-runway-gen3',
            'runway2' => 'video-runway-gen4',
            'luma' => 'video-luma-ray-flash',
            'luma2' => 'video-luma-ray2',
            default => 'video-generico'
        };
    } elseif ($this->tipo === 'gprompt') {
        return 'prompt-generation';
    } elseif ($this->tipo === 'editvideo') {
        return 'edit-video';
    } else {
        return 'servicio-generico';
    }
}

}
