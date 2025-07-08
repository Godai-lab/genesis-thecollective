<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Brand;
use App\Models\Field;
use App\Models\Generated;
use App\Services\ChatBaseService;
use App\Services\GeminiService;
use App\Services\OpenAiService;
use App\Services\ProcessFileContentService;
use App\Services\PerplexityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Herramienta1Controller extends Controller
{
    var $objetivos = [
        ['id' => 'Lanzamiento', 'name' => 'Lanzamiento'],
        ['id' => 'Posicionamiento', 'name' => 'Posicionamiento'],
        ['id' => 'Ventas', 'name' => 'Ventas']
    ];
    var $rangoedad = [
        ['id' => 'más de 18 años', 'name' => 'más de 18 años'],
        ['id' => '18 a 24 años', 'name' => '18 a 24 años'],
        ['id' => '24 a 36 años', 'name' => '24 a 36 años'],
        ['id' => 'más de 34 años', 'name' => 'más de 34 años']
    ];
    public function index(Request $request)
    {
        Gate::authorize('haveaccess','brief.index');
        $accounts = Account::fullaccess()->get();
        $objetivos = $this->objetivos;
        $rangoedad = $this->rangoedad;

        return view('herramienta1.index', compact('accounts','objetivos','rangoedad'));

    }

    public function savefields(Request $request){
        // Validar que los parámetros necesarios existan
        $validator = Validator::make($request->all(), [
            '_token' => 'required',
            'account' => 'required|integer',
            'step' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $accountId = $request->input('account');
        $step = $request->input('step');

        // Filtramos los parámetros para excluir _token y account
        $parameters = $request->except('_token', 'account', 'step');

        foreach ($parameters as $key => $value) {
            // Si el valor es un array, lo convertimos a JSON para guardarlo
            if (is_array($value)) {
                $value = json_encode($value);
            }
    
            // Buscar el registro existente
            $field = Field::where('key', $key)->where('account_id', $accountId)->first();
    
            if ($field) {
                // Si el registro existe, actualizarlo
                $field->value = $value;
                $field->save();
            } else {
                // Si no existe, crear uno nuevo
                Field::create([
                    'key' => $key,
                    'value' => $value,
                    'account_id' => $accountId,
                ]);
            }
        }
        return response()->json(['success' => 'Datos procesados correctamente.', 'details' => true, 'goto' => $step]);
        // return response()->json(['message' => 'Fields saved successfully'], 200);
    }
    
public function datosextras(Request $request){
        
        // Validar las URLs y archivos
        $validator = Validator::make($request->all(), [
            '_token' => 'required',
            'account' => 'required|integer',
            'urls.*' => 'nullable|url',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        ini_set('max_execution_time', 300);
        $accountId = $request->input('account');

        // Obtener las URLs y los archivos validados, filtrando los vacíos
        $urls = array_filter($request->input('urls', []), function($url) {
            return !empty($url);
        });
        $files = array_filter($request->file('files', []), function($file) {
            return $file && $file->isValid();
        });

        // Validar que al menos una URL o un archivo se esté enviando
        if (empty($urls) && empty($files)) {
            return response()->json(['error' => 'Debes enviar al menos una URL o un archivo.']);
        }

        $contentSite = [];
        $contentFile = [];

        // Procesar URLs si existen
        if (!empty($urls)) {
            foreach ($urls as $url) {
                $siteContent = ProcessFileContentService::processUrl($url);
                $contentSite[] = $siteContent;
            }
        }
        // Procesar archivos si existen
        if (!empty($files)) {
            foreach ($files as $file) {
                $filePath = $file->getPathname();
                $fileType = $file->getClientMimeType();
                switch ($fileType) {
                    case 'application/pdf':
                        $fileContent = ProcessFileContentService::processPdf($filePath);
                        break;
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    case 'application/msword':
                        $fileContent = ProcessFileContentService::processWord($filePath);
                        break;
                    case 'application/vnd.ms-excel':
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        $fileContent = ProcessFileContentService::processExcel($filePath);
                        break;
                    case 'text/csv':
                        $fileContent = ProcessFileContentService::processCSV($filePath);
                        break;
                    case 'text/plain':
                        $fileContent = ProcessFileContentService::processTxt($filePath);
                        break;
                    default:
                        $fileContent = "Tipo de archivo no soportado: " . $fileType;
                }
                $contentFile[] = $fileContent;
            }
        }

        if (!empty($contentFile) || !empty($contentSite)) {
            $prompt = "Tu función es extraer la información que se está solicitando en el formato que se solicita desde el contenido que se te está pasando. (responde siempre en español) \n\nExtracción de contenido de la empresa:\n\n";
            if (!empty($contentSite)) {
                $prompt .= "Contenido de sitios: " . json_encode($contentSite) . "\n";
            }
            if (!empty($contentFile)) {
                $prompt .= "Contenido de archivos: " . json_encode($contentFile) . "\n";
            }

            // Detalles específicos que la IA debe buscar en el contenido
            $prompt .= "\nLa información que se está solicitando:\n";
            $prompt .= "Sobre la marca\n";
            $prompt .= "Sobre los productos\n";
            $prompt .= "Sobre la competencia\n";
            $prompt .= "Sobre Estudios de mercado\n";
            $prompt .= "Sobre la ciudad, país, situación económica\n";
            $prompt .= "Necesidades del cliente actual\n";

            $prompt .= "Usando este esquema JSON:\n";
            $prompt .= 'Respuesta={"extraMarca": str, "extraProductos": str, "extraCompetencia": str, "extraEstudiosMercado": str, "extraCiudadPaisEconomia": str, "extraNecesidades": str}';

            $model = "gemini-2.0-flash-exp";
            $temperature = 0.25;
            $response_mime_type = "application/json";

            $response = GeminiService::TextOnlyEntry($prompt,$model,$temperature, $response_mime_type);

            $response = json_decode($response['data'],true);

            $fields = [
                'extraMarca' => $response['extraMarca'],
                'extraProductos' => $response['extraProductos'],
                'extraCompetencia' => $response['extraCompetencia'],
                'extraEstudiosMercado' => $response['extraEstudiosMercado'],
                'extraCiudadPaisEconomia' => $response['extraCiudadPaisEconomia'],
                'extraNecesidades' => $response['extraNecesidades'],
            ];
    
            foreach ($fields as $key => $value) {
                if (!is_null($value)) {
                    Field::updateOrCreate(
                        ['account_id' => $accountId, 'key' => $key],
                        ['value' => $value]
                    );
                }
            }
        }
        
        $response = $this->GenerarBrief($accountId);
        return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $response, 'goto' => 8, 'function' => 'BriefGenerado']);
    }

    public function rellenariasave(Request $request){
        // Validar que los parámetros necesarios existan
        $validator = Validator::make($request->all(), [
            '_token' => 'required',
            'account' => 'required|integer',
            'extraccionIA' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $accountId = $request->input('account');

        // Buscar el registro existente
        $field = Field::where('key', 'extraccionIA')->where('account_id', $accountId)->first();
    
        if ($field) {
            // Si el registro existe, actualizarlo
            $field->value = $request->input('extraccionIA');
            $field->save();
        } else {
            // Si no existe, crear uno nuevo
            Field::create([
                'key' => 'extraccionIA',
                'value' => $request->input('extraccionIA'),
                'account_id' => $accountId,
            ]);
        }

        $response = $this->GenerarBriefGenerateIA($accountId);

        return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $response, 'goto' => 9, 'function' => 'BriefGeneradoFormIA']);

        // return response()->json(['success' => 'Datos procesados correctamente.', 'details' => true, 'goto' => 8]);

    }

    public function datosextrassave(Request $request){
        // Validar que los parámetros necesarios existan
        $validator = Validator::make($request->all(), [
            '_token' => 'required',
            'account' => 'required|integer',
            'extraMarca' => 'string|nullable',
            'extraProductos' => 'string|nullable',
            'extraCompetencia' => 'string|nullable',
            'extraEstudiosMercado' => 'string|nullable',
            'extraCiudadPaisEconomia' => 'string|nullable',
            'extraNecesidades' => 'string|nullable',
        ]);

        ini_set('max_execution_time', 300);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $accountId = $request->input('account');
        $fields = [
            'extraMarca' => $request->input('extraMarca'),
            'extraProductos' => $request->input('extraProductos'),
            'extraCompetencia' => $request->input('extraCompetencia'),
            'extraEstudiosMercado' => $request->input('extraEstudiosMercado'),
            'extraCiudadPaisEconomia' => $request->input('extraCiudadPaisEconomia'),
            'extraNecesidades' => $request->input('extraNecesidades'),
        ];

        foreach ($fields as $key => $value) {
            if (!is_null($value)) {
                Field::updateOrCreate(
                    ['account_id' => $accountId, 'key' => $key],
                    ['value' => $value]
                );
            }
        }
        $response = $this->GenerarBrief($accountId);
        return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $response, 'goto' => 8, 'function' => 'BriefGenerado']);
    }

    public function GenerarBrief($accountId){
        $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

        $country = $accountData->get('country');
        $name = $accountData->get('name');
        $slogan = $accountData->get('slogan');
        $mission = $accountData->get('mission');
        $vision = $accountData->get('vision');
        $valores = $accountData->get('valores');
        $por_que_existe_tu_marca = $accountData->get('por_que_existe_tu_marca');
        $fundacion_marca = $accountData->get('fundacion_marca');
        $hitos_marca = $accountData->get('hitos_marca');
        $diferencia_marca = $accountData->get('diferencia_marca');
        $tono_de_voz = $accountData->get('tono_de_voz');
        $situacion_lugar_marca = $accountData->get('situacion_lugar_marca');
        $archetype = $accountData->get('archetype');
        $tendencias_mercado = $accountData->get('tendencias_mercado');
        $tamano_mercado_y_segmentacion = $accountData->get('tamano_mercado_y_segmentacion');
        $competidores_marca = $accountData->get('competidores_marca');
        $analisis_FODA_competencia = $accountData->get('analisis_FODA_competencia');
        $edad_genero_ubicacion_ingresos_publico_objetivo = $accountData->get('edad_genero_ubicacion_ingresos_publico_objetivo');
        $intereses_valores_estilo_vida_publico_objetivo = $accountData->get('intereses_valores_estilo_vida_publico_objetivo');
        $habitos_compra_lealtad_publico_objetivo = $accountData->get('habitos_compra_lealtad_publico_objetivo');
        $como_utilizan_tu_producto = $accountData->get('como_utilizan_tu_producto');
        $cuando_utilizan_tu_producto = $accountData->get('cuando_utilizan_tu_producto');
        $puntos_contacto_cliente_marca = $accountData->get('puntos_contacto_cliente_marca');
        $canales_comunican_interactuan_marca = $accountData->get('canales_comunican_interactuan_marca');
        $extraMarca = $accountData->get('extraMarca');
        $extraProductos = $accountData->get('extraProductos');
        $extraCompetencia = $accountData->get('extraCompetencia');
        $extraEstudiosMercado = $accountData->get('extraEstudiosMercado');
        $extraCiudadPaisEconomia = $accountData->get('extraCiudadPaisEconomia');
        $extraNecesidades = $accountData->get('extraNecesidades');

        
        // Decodificar los datos del producto
        $products = json_decode($accountData->get('product'), true);

        // Generar la parte de productos del prompt
        $productosTexto = '';
        foreach ($products as $product) {
            $product_name = $product['product_name'];
            $product_slogan = $product['product_slogan'];
            $presentaciones = $product['presentaciones'];
            $characteristics = implode("\nCaracterística: ", $product['product_characteristics']);
            $benefits = implode("\nBeneficio: ", $product['product_benefits']);

            $productosTexto .= <<<EOT
Producto
Nombre del producto: $product_name
Slogan: $product_slogan
Presentaciones: $presentaciones
Características:
Característica: $characteristics
Beneficios:
Beneficio: $benefits

EOT;
        }

        $prompt = <<<EOT
Analiza la información proporcionada por el usuario en el formulario y el documento adicional. Reorganiza y mejora los datos para crear un brief completo y estandarizado, siguiendo este formato:
Información de la marca:
País
Nombre de la marca
Slogan
Misión
Visión
Valores (enumerar hasta 5)
Razón de existencia
Historia de fundación (breve resumen)
Hitos destacados (enumerar hasta 5)
Diferenciación de competidores
Tono de voz
Situación económica, social y cultural del mercado
Información del producto principal:
Nombre del producto
Slogan
Presentaciones
Características (enumerar hasta 5)
Beneficios (enumerar hasta 5)
Arquetipo
Análisis de mercado:
Tendencias relevantes (enumerar hasta 5)
Tamaño y segmentación del mercado
Análisis de competencia:
Principales competidores (enumerar hasta 3)
Análisis FODA de la competencia directa (resumir en puntos clave)
Perfil del público objetivo:
Demografía (edad, género, ubicación, nivel de ingresos)
Psicografía (intereses, valores, estilo de vida)
Comportamientos (hábitos de compra, lealtad, uso del producto)
Puntos de contacto e interacción con el cliente:
Puntos de contacto a lo largo de la experiencia del cliente (enumerar los principales)
Canales de comunicación e interacción (enumerar los principales)
Instrucciones adicionales:
Utiliza la información del formulario como base principal.
Complementa o mejora cada sección con datos relevantes del documento adicional.
Si hay discrepancias entre las fuentes, prioriza la información del formulario.
Mantén un tono profesional y conciso en toda la redacción.
Si falta información en alguna sección, indica 'Información no proporcionada'.
Asegúrate de que cada sección tenga contenido relevante y bien estructurado.
Limita cada punto a un máximo de 2-3 oraciones para mantener la concisión.

FORMULARIO:
País: $country
Marca
Nombre de la marca: $name
Slogan: $slogan
Misión: $mission
Visión: $vision
Valores:
$valores
¿Por qué existe tu marca?: $por_que_existe_tu_marca
¿Cuándo y cómo se fundó la marca?: $fundacion_marca
¿Cuáles son los hitos destacados en la evolución de la marca?: $hitos_marca
¿Qué diferencia tu marca de sus competidores?: $diferencia_marca
Tono de voz: $tono_de_voz
¿Cuál es la situación económica, social y cultural del lugar donde opera tu marca?: $situacion_lugar_marca
$productosTexto
Arquetipo:
$archetype
Mercado
¿Cuáles son las tendencias del mercado relevantes para tu marca?: $tendencias_mercado
¿Cuál es el tamaño del mercado y cómo se segmenta?: $tamano_mercado_y_segmentacion
Competencia
¿Quiénes son los principales competidores de tu marca?: $competidores_marca
¿Cuál es el análisis FODA de tu competencia directa?: 
$analisis_FODA_competencia
Target
Demografía
¿Cuál es la edad, género, ubicación y nivel de ingresos de tu público objetivo?: $edad_genero_ubicacion_ingresos_publico_objetivo
¿Cuáles son los intereses, valores y estilo de vida de tu público objetivo?: $intereses_valores_estilo_vida_publico_objetivo
Comportamientos
¿Cuáles son los hábitos de compra y lealtad a la marca de tu público objetivo?: $habitos_compra_lealtad_publico_objetivo
¿Cómo utilizan tu producto/servicio?: $como_utilizan_tu_producto
¿En qué momento del día lo usan?: $cuando_utilizan_tu_producto
Puntos de contacto del cliente con la marca a lo largo de su experiencia: $puntos_contacto_cliente_marca
Canales en los que los clientes se comunican e interactúan con la marca: $canales_comunican_interactuan_marca

Con estos Datos adicionales:
Sobre tu marca que desees compartir: $extraMarca
Sobre tus productos: $extraProductos
Sobre tu competencia: $extraCompetencia
Estudios de mercado: $extraEstudiosMercado
Sobre tu ciudad, país, situación económica: $extraCiudadPaisEconomia
Para conocer las necesidades del cliente actual: $extraNecesidades
extrae lo más importante y necesario para mejorar el brief y añadelo en el ITEM DE INFORMACIÓN ADICIONAL

Presenta el brief final en un formato claro y fácil de leer, utilizando viñetas y numeración donde sea apropiado. NO añadas ninguna nota adicional al inicio o final. Responde siempre en español.
EOT;

        $model = "gemini-2.0-flash-exp";
        $temperature = 0.25;
        $response_mime_type = "text/plain";
        $response = GeminiService::TextOnlyEntry($prompt,$model,$temperature, $response_mime_type);

        return $response;
    }  


public function GenerarBriefGenerateIA(Request $request)
{
    // Agregamos un log al inicio para saber que se llamó al método
    Log::info('Iniciando método GenerarBriefGenerateIA', ['account' => $request->input('account')]);

    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        '_token' => 'required',
        'account' => 'required|integer',
        'country' => 'required|string',
        'name' => 'required|string',
        'slogan' => 'nullable|string',
        'urls' => 'nullable|array|max:5',
        'urls.*' => 'nullable|url',
        'files' => 'nullable|array|max:5',
        'files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:20480'
    ]);
// Aseguramos que el script tenga 600 segundos de tiempo máximo
set_time_limit(600);
ini_set('max_execution_time', 600);
    if ($validator->fails()) {
        Log::warning('Error de validación', ['errors' => $validator->errors()]);
        return response()->json(['errors' => $validator->errors()]);
    }

    $accountId = $request->input('account');
    // Filtramos los parámetros para excluir _token y account
    $parameters = $request->except('_token', 'account', 'urls', 'files');

    try {
        foreach ($parameters as $key => $value) {
            // Si el valor es un array, lo convertimos a JSON para guardarlo
            if (is_array($value)) {
                $value = json_encode($value);
            }
            // Actualizar o crear el registro en una sola línea
            Field::updateOrCreate(
                ['key' => $key, 'account_id' => $accountId],
                ['value' => $value]
            );
        }
    } catch (\Exception $e) {
        Log::error('Error al guardar parámetros en la base de datos', [
            'exception' => $e->getMessage(),
            'parameters' => $parameters
        ]);
        return response()->json(['error' => 'Error al guardar los datos del formulario.'], 500);
    }

    // Obtener las URLs y los archivos validados, filtrando los vacíos
    $urls = array_filter($request->input('urls', []), function ($url) {
        return !empty($url);
    });
    $files = array_filter($request->file('files', []), function ($file) {
        return $file && $file->isValid();
    });

    // Validar que al menos una URL o un archivo se esté enviando
    if (empty($urls) && empty($files)) {
        Log::warning('No se envió ninguna URL ni archivo');
        return response()->json(['error' => 'Debes enviar al menos una URL o un archivo.']);
    }

    $contentSite = [];
    $contentFile = [];

    // Procesar URLs si existen
    if (!empty($urls)) {
        foreach ($urls as $url) {
            try {
                Log::info('Procesando URL', ['url' => $url]);
                $siteContent = ProcessFileContentService::processUrl($url);
                $contentSite[] = $siteContent;
            } catch (\Exception $e) {
                Log::error('Error al procesar la URL', [
                    'url' => $url,
                    'exception' => $e->getMessage()
                ]);
            }
        }
    }

    // Procesar archivos si existen
    if (!empty($files)) {
        foreach ($files as $file) {
            try {
                $filePath = $file->getPathname();
                $fileType = $file->getClientMimeType();
                Log::info('Procesando archivo', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $fileType
                ]);

                switch ($fileType) {
                    case 'application/pdf':
                        $fileContent = ProcessFileContentService::processPdf($filePath);
                        break;
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    case 'application/msword':
                        $fileContent = ProcessFileContentService::processWord($filePath);
                        break;
                    case 'application/vnd.ms-excel':
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        $fileContent = ProcessFileContentService::processExcel($filePath);
                        break;
                    case 'text/csv':
                        $fileContent = ProcessFileContentService::processCSV($filePath);
                        break;
                    case 'text/plain':
                        $fileContent = ProcessFileContentService::processTxt($filePath);
                        break;
                    default:
                        $fileContent = "Tipo de archivo no soportado: " . $fileType;
                        Log::warning('Tipo de archivo no soportado', ['mime_type' => $fileType]);
                }
                $contentFile[] = $fileContent;
            } catch (\Exception $e) {
                Log::error('Error al procesar el archivo', [
                    'file' => $file->getClientOriginalName(),
                    'exception' => $e->getMessage()
                ]);
            }
        }
    }

    $country = $request->input('country');
    $name = $request->input('name');
    $slogan = $request->input('slogan');
    $callperplexity= $this->callPerplexity($country, $name, $slogan);
    $fuentes=$callperplexity['fuentes'];
    $fuentesFormatted = !empty($fuentes) ? "- " . implode("\n- ", $fuentes) : "No se encontraron fuentes.";
    
    if (!is_array($callperplexity) || isset($callperplexity['error'])) {
        // No hay error en la respuesta
    } 
    $callperplexity = preg_replace('/<think>.*?<\/think>/s', '', $callperplexity['data']);
    


    // Construir el prompt para la IA
    $prompt = <<<EOT
Eres un experto en el armado de Briefs para publicidad y marketing, Analiza detalladamente la información proporcionada (responde siempre en español) y extrae la información clave sobre la marca y sus productos. Organiza los datos en las siguientes categorías, manteniendo el formato y estructura del ejemplo dado:
Mejora la estructura y calidad del contenido para hacerlo más impactante.
Refina la redacción para que tenga un enfoque publicitario y estratégico, manteniendo un tono claro y persuasivo.
Asegura que la información sea precisa y esté alineada con las tendencias actuales de marketing.
Conserva cualquier cita o referencia en el formato original proporcionado, sin alterarlas.
No muestres nada más de lo que se te pide agrega saltos de líneas en donde veas necesario.
**Información de la marca:**
**País:**
***Nombre de la marca:** 
**Slogan:** 
**Misión:** 
**Visión:** 
**Valores:** (enumerar) 
**Razón de existencia:** 
**Historia de fundación:** 
**Hitos destacados:** 
**Diferenciación de competidores*** \n
**Tono de voz**
**Situación económica, social y cultural del mercado**
**Información del producto**:
**Nombre del producto**
**Slogan**
**Presentaciones**
**Características** (enumerar)
**Beneficios** (enumerar)
**Arquetipo**
**Análisis de mercado:**
**Tendencias relevantes**
**Tamaño y segmentación del mercado**
**Análisis de competencia:**
**Principales competidores**
**Análisis FODA de la competencia directa**
**Perfil del público objetivo:**
**Demografía** (edad, género, ubicación, nivel de ingresos)
**Psicografía** (intereses, valores, estilo de vida)
**Comportamientos** (hábitos de compra, lealtad, uso del producto)
**Puntos de contacto e interacción con el cliente:**
**Puntos de contacto a lo largo de la experiencia del cliente**
**Canales de comunicación e interacción**
Si alguna información específica no está disponible en los documentos, indica 'Información no proporcionada' en esa sección. Mantén el formato y la estructura lo más similar posible al ejemplo dado, incluyendo viñetas, saltos de líneas y enumeraciones donde sea apropiado.
Mantén el siguiente país, nombre de la marca y slogan que te estoy pasando:
País: $country \n
 Nombre de la marca: $name \n
Slogan: $slogan \n
Extrae la información para analizar, estructurar y sugerir desde aquí:
EOT;

    if (!empty($contentSite)) {
        $prompt .= "Contenido de sitios actualizados subido por el usuario" . json_encode($contentSite) . "\n";
    }
    if (!empty($contentFile)) {
        $prompt .= "Contenido de archivos actualizados subido por el usuario" . json_encode($contentFile) . "\n";
    }
    if (!empty($callperplexity)) {
        $prompt .= "Contenido de informacion buscado en internet" . json_encode($callperplexity) . "\n";
    }

    $prompt .= "Solo entrega las respuestas sin ninguna nota al inicio o al final y toma como prioridad a la información subida por el usuario.";


    try {
        $model = "gemini-2.0-flash-exp";
        $temperature = 0.7;
        $response = GeminiService::TextOnlyEntry($prompt, $model, $temperature);

        if (!isset($response['data'])) {
            Log::error('La respuesta de ChatCompletions no contiene la clave "data"', [
                'response' => $response,
                'account_id' => $accountId
            ]);
            return response()->json([
                'error' => 'Error al obtener datos de la IA. Por favor, intenta nuevamente.'
            ], 500);
        }


    } catch (\Exception $e) {
        Log::error('Error en la llamada a PerplexityService::ChatCompletions', [
            'exception' => $e->getMessage(),
            'prompt' => $prompt
        ]);
        return response()->json(['error' => 'Error al procesar la solicitud de IA.'], 500);
    }

    try {
        // Guardar o actualizar la respuesta de la IA
        Field::updateOrCreate(
            ['key' => 'extraccionIA', 'account_id' => $accountId],
            ['value' => $response['data']]
        );
    } catch (\Exception $e) {
        Log::error('Error al guardar la respuesta de la IA', [
            'exception' => $e->getMessage(),
            'account_id' => $accountId
        ]);
        return response()->json(['error' => 'Error al guardar los datos de IA.'], 500);
    }

   
    $briefContent = $response['data']. "\n\n## **Fuentes**\n\n" . $fuentesFormatted;

    Log::info('Proceso completado correctamente', [
        'account' => $accountId,
        'briefContent' => $briefContent
    ]);

    return response()->json([
        'success' => 'Datos procesados correctamente.',
        'details' => [
            'data' => $briefContent, 
        ],
        'goto' => 9,
        'function' => 'BriefGeneradoFormIA'
    ]);
    // return response()->json([
    //     'success' => 'Datos procesados correctamente.',
    //     'details' => array_merge([
    //         'data' => $briefContent,
    //     ], ['sonar' => $callperplexity['data']]), 
    //     'goto' => 9,
    //     'function' => 'BriefGeneradoFormIA'
    // ]);
    
}




    public function saveBrief(Request $request){
        // Validar que los parámetros necesarios existan
        $validator = Validator::make($request->all(), [
            '_token' => 'required',
            'account' => 'required|integer',
            'Brief' => 'required|string',
            'file_name' => 'required|string',
            'rating' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $accountId = $request->input('account');
        $fields = [
            'Brief' => $request->input('Brief'),
        ];

        Generated::Create(
            ['account_id' => $accountId, 'key' => 'Brief', 'name' => $request->input('file_name'), 'value' => $request->input('Brief'), 'rating' => $request->input('rating')],
        );

        foreach ($fields as $key => $value) {
            if (!is_null($value)) {
                Field::updateOrCreate(
                    ['account_id' => $accountId, 'key' => $key],
                    ['value' => $value]
                );
            }
        }
        return response()->json(['success' => 'Datos procesados correctamente.', 'details' => true, 'goto' => 10]);
    }

    public function callPerplexity($country, $name, $slogan){
        $prompt = <<<EOT
Realiza una investigación sobre la marca $name que opera en el país $country.
Extrae información actualizada y relevante sobre la empresa y su entorno de mercado.
Tu enfoque debe estar relacionado a Publicidad y marketing.
Investiga la siguiente información:

## Información de la Marca

- Slogan
- Misión
- Visión
- Valores (Mínimo 5)
- Razón de existencia (Propósito de la marca)
- Historia de fundación (Breve resumen de la historia de la empresa)
- Hitos destacados (mínimo 5)
- Diferenciación frente a los competidores: (Factores clave que la diferencian)
- Tono de voz (Definir tono de comunicación)
- Situación económica, social y cultural del mercado: (Breve análisis del mercado en el país)

## Información del Producto Principal
- Nombre del producto (Nombre del Producto)
- Slogan: (Slogan del Producto)
- Presentaciones (Variantes y formatos disponibles)
- Características (Mínimo 5)
- Beneficios (Mínimo 5)
- Arquetipo (Definir arquetipo de marca)

## Análisis de Mercado
- Tendencias relevantes (Mínimo 5)
- Tamaño y segmentación del mercado (Datos sobre el mercado objetivo)

## Análisis de la Competencia
- Principales competidores (Mínimo 3)
- Análisis FODA de la competencia directa
  - Fortalezas:
  - Oportunidades:
  - Debilidades:
  - Amenazas: 

## Perfil del Público Objetivo
- Demografía:
  - Edad: [Rango de edad]
  - Género: [Género predominante]
  - Ubicación: [Ubicaciones clave]
  - Nivel de ingresos: [Rango]
- Psicografía:
  - Intereses: [Principales intereses]
  - Valores: [Valores relevantes]
  - Estilo de vida: [Estilo de vida del público]
- Comportamientos:
  - Hábitos de compra: [Patrones de consumo]
  - Lealtad: [Niveles de lealtad]
  - Uso del producto: [Frecuencia y forma de uso]

## Puntos de Contacto e Interacción con el Cliente
- Puntos de contacto (Mínimo 4)
- Canales de comunicación (Mínimo 4)
EOT;
$system_prompt = <<<EOT
Actúas como un buscador de información de alto valor especializado en analizar documentos para generar briefs de marca efectivos y concisos. Tu objetivo es extraer la información relevante, identificar los puntos clave y elaborar recomendaciones prácticas para crear un brief que refleje la identidad, valores, propósito y ventajas competitivas de la marca.
EOT;

try {
    $model = "sonar-reasoning";
    $temperature = 0.7;
    $response = PerplexityService::ChatCompletions($prompt, $model, $temperature, $system_prompt);

    if (!isset($response['data'])) {
        Log::error('La respuesta de ChatCompletions no contiene la clave "data"', [
            'response' => $response,
        ]);
        return response()->json([
            'error' => 'Error al obtener datos de la IA. Por favor, intenta nuevamente.'
        ], 500);
    }

    return array('data' =>  $response['data'],'fuentes'=>$response['citations']);
} catch (\Exception $e) {
    Log::error('Error en la llamada a PerplexityService::ChatCompletions', [
        'exception' => $e->getMessage(),
        'prompt' => $prompt
    ]);
    return response()->json(['error' => 'Error al procesar la solicitud de IA.'], 500);
}

}

}
