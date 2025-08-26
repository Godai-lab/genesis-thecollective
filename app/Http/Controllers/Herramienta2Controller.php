<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Brand;
use App\Models\Field;
use App\Models\Generated;
use App\Services\AnthropicService;
use App\Services\GeminiService;
use App\Services\OpenAiService;
use App\Services\PerplexityService;
use App\Services\ProcessFileContentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class Herramienta2Controller extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('haveaccess','genesis.index');
        $accounts = Account::fullaccess()->get();
        return view('herramienta2.index', compact('accounts'));

    }
   public function generarGenesis(Request $request){
    Log::info('Iniciando generarGenesis', [
        'request_data' => $request->all()
    ]);
    
    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        '360_objective' => 'required|string',
        'account' => 'required|integer',
        'brief' => 'required|integer',
    ]);

    if ($validator->fails()) {
        Log::error('Validación fallida en generarGenesis', [
            'errors' => $validator->errors()
        ]);
        return response()->json(['error' => $validator->errors()]);
    }
    
    ini_set('max_execution_time', 800);
    $objective = $request->input('360_objective');
    $accountId = $request->input('account');
    $idBrief = $request->input('brief');
    
    Log::info('Datos extraídos de la request', [
        'objective' => $objective,
        'accountId' => $accountId,
        'idBrief' => $idBrief
    ]);
    
    try {
        $brief = Generated::find($idBrief)->value;
        Log::info('Brief encontrado', [
            'brief_length' => strlen($brief),
            'brief_preview' => substr($brief, 0, 100) . '...'
        ]);
    } catch (\Exception $e) {
        Log::error('Error al obtener el brief', [
            'idBrief' => $idBrief,
            'error' => $e->getMessage()
        ]);
        return response()->json(['error' => 'Error al obtener el brief']);
    }

    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');
    Log::info('Account data obtenida', [
        'account_data_count' => $accountData->count(),
        'account_data_keys' => $accountData->keys()->toArray()
    ]);

    if ($accountData) {
        $fields = [
            'Brief' => $brief,
            '360_objective' => $objective,
        ];

        foreach ($fields as $key => $value) {
            if (!is_null($value)) {
                Field::updateOrCreate(
                    ['account_id' => $accountId, 'key' => $key],
                    ['value' => $value]
                );
            }
        }
        
        Log::info('Campos guardados en la base de datos');
        
        try {
            Log::info('Iniciando generación de insight');
            $insightgenerado = $this->GenerarInsight($brief,$objective);
            Log::info('Insight generado exitosamente', [
                'insight_data_length' => strlen($insightgenerado['data']),
                'insight_fuentes_count' => is_array($insightgenerado['fuentes']) ? count($insightgenerado['fuentes']) : 'no es array',
                'insight_fuentes_type' => gettype($insightgenerado['fuentes'])
            ]);
            
            $insightdata = $insightgenerado['data'];
            $insightfuentes = $insightgenerado['fuentes'];

            // Verificar si las fuentes existen y convertirlas en enlaces clickeables con comillas dobles
            if (is_array($insightfuentes) && !empty($insightfuentes)) {
                $fuentesHTML = '<p><strong>Fuentes:</strong></p><ul><li>' . implode(
                    '</li><li>',
                    array_map(function ($fuente) {
                        return filter_var($fuente, FILTER_VALIDATE_URL)
                            ? sprintf('<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', htmlspecialchars($fuente, ENT_QUOTES, 'UTF-8'), htmlspecialchars($fuente, ENT_QUOTES, 'UTF-8'))
                            : htmlspecialchars($fuente, ENT_QUOTES, 'UTF-8'); // Escapar si no es URL
                    }, $insightfuentes)
                ) . '</li></ul>';
            } else {
                $fuentesHTML = '<p><strong>Fuentes:</strong> No se encontraron fuentes.</p>';
            }

            Log::info('Fuentes HTML generadas', [
                'fuentes_html_length' => strlen($fuentesHTML)
            ]);

            Field::updateOrCreate(
                [
                    'account_id' => $accountId,
                    'key' => 'fuentesGenesis'
                ],
                [
                    'value' => $fuentesHTML
                ]
            );

            // Preparar las fuentes para la respuesta
            $fuentesResponse = is_string($insightfuentes) ? explode("\n", trim($insightfuentes)) : $insightfuentes;

            $prompt = <<<EOT
En base a la información proporcionada en el brief y el objetivo dado, crea un GÉNESIS completo. 

BRIEF: 
$brief

OBJETIVO: 
$objective

INSIGHTS:
$insightdata

Analiza cuidadosamente la información disponible, además de los Insights poderosos que te servirán para nutrir el resultado, identifica los puntos clave relevantes para cada componente del GÉNESIS, y desarrolla una estrategia coherente y efectiva. Responde en español y presenta solo el resultado final del GÉNESIS sin notas o textos adicionales. Aquí tienes la información para lograrlo:

EOT;

            Log::info('Prompt preparado', [
                'prompt_length' => strlen($prompt),
                'prompt_preview' => substr($prompt, 0, 200) . '...'
            ]);

            $system_prompt = <<<EOT
Eres un experto en planificación estratégica publicitaria especializado en la metodología GÉNESIS (Generación Estratégica Neuroinspirada de Efectividad Sincronizada con Inteligencia Sintética) de god-ai (Objetivo cuantificado, Obstáculo identificado, Insight Aumentado, Desafío creativo). Esta metodología se utiliza para crear estrategias creativas publicitarios efectivas y consiste en los siguientes componentes:
1. Objetivo Cuantificado: Define el propósito de negocio medible y específico.
2. Obstáculo Identificado: Identifica el principal obstáculo para lograr el objetivo. ¿Qué se interpone en el camino para alcanzar ese objetivo? ¿Qué papel juega la comunicación en el cambio de esa conducta? A veces, puedes ver más de un obstáculo, pero trata de identificar la única cosa que, si se supera, hará que todo lo demás caiga en su lugar.
3. Insight Aumentado: Revela una verdad sobre el consumidor que ayuda a superar el obstaculo Identificado. ¿Cuál es la verdad que desbloquea este problema? ¿Qué sabemos sobre el consumidor que nos ayude a crear comunicaciones que superen el problema? Esta información debe provenir del consumidor, no del conocimiento del producto. Un gran insight sorprende a la gente con lo obvio.
4. Desafío Creativo: Plantea un desafío ambicioso y transformador para la comunicación. ¿Cuál es el reto para nuestras comunicaciones? Este debe ser grande, ambicioso y valiente. Puede ser provocador, pero al menos, emocionante. Un gran reto te ayudará a ver claramente y a romper las barreras. Transforma la marca. Nunca es una tarea. Es simple. No está basado en un negocio, un producto o una tarea.
Para crear un GÉNESIS efectivo:
- Mantén cada sección concisa y enfocada.
- Asegúrate de que todas las partes cuenten una historia coherente.
- Enfócate en las personas, no en el producto.
- Busca insights culturales profundos según la info del país.
- El desafío creativo debe ser transformador y no incluir el nombre de la marca.
-Analiza:, ¿Dice una historia simple?
-Menos palabras tienen un mayor impacto; trata de reducir el conteo de palabras a la mitad, casi siempre esto genera una mejora
-Trata de encontrar la línea conductora para que el brief realmente se lea como una historia
-Sé brutalmente honesto con el problema. Incluso si duele
-¿Tu insight se centra en las personas? Los insights más geniales son insights culturales
-Es tentador escribir un insight sobre el producto, pero al final debería regresar a la gente real en el mundo real
-¿Cómo es esto propio? ¿Cómo logro que esta marca se diferencie de la competencia?
- ¡Rompe las reglas!


Puedes usar estos ejemplos como inspiración y guía de formato respetando los saltos de líneas, no debes copiar:
<examples>
<example>
1.- Coca Cola: Sencillo pero resuelve\n\n
**Objetivo Cuantificado:**\n
Aumentar las ventas de Coca-Cola en formato individual en un 10% en los próximos 3 meses.\n\n
**Obstáculo Identificado:**\n
En un mercado saturado, los consumidores tienen múltiples opciones y están optando por alternativas percibidas como más saludables.\n\n
**Insight Aumentado:**\n
Los consumidores buscan pequeños momentos de placer y recompensa en su día a día.\n\n
**Desafío Creativo:**\n
Posicionar la Coca-Cola individual como el compañero ideal para los pequeños momentos de disfrute diario.
</example>

<example>
2.- Ameriprise: Transformador y mantiene a Ameriprise a la vanguardia\n\n
**Objetivo Cuantificado:**\n
Ayudar a crecer la adquisición de nuevos clientes aumentando los niveles de Amor y Respeto por Ameriprise.\n\n
**Obstáculo Identificado:**\n
Ameriprise compite en una categoría intensamente saturada donde todos luchan por lo mismo: la falta de confianza y la inercia. Para combatir esto, más y más competidores están intentando llegar a los consumidores con "plática franca" (Schwab, Smith Barney). Otros están siguiendo los pasos de Ameriprise, alentando a la gente a alcanzar sus sueños de retiro (Allianz, Wachovia, Lincoln). Ahora Ameriprise necesita adoptar una nueva táctica para diferenciarse del grupo una vez más.\n\n
**Insight Aumentado:**\n
Hemos dado en el clavo con la campaña actual. Inspira a la gente a verse como optimista y aventurera. Y esto hace que Ameriprise se vea más progresiva que la competencia. Ahora, a medida que ampliamos el público objetivo para incluir a los Baby Boomers y a la Generación X, necesitamos ir más allá del retiro, para alentar todo tipo de sueños: sueños grandes y pequeños, sueños de hoy y sueños de mañana, sueños egoístas y sueños altruistas, sueños para ti y sueños para tus hijos. Para romper con el cinismo de los asesores financieros, se necesita una representación de los asesores de Ameriprise como una raza aparte: accesibles, atentos y serviciales.\n\n
**Desafío Creativo:**\n
Hacer que Ameriprise marque la diferencia entre los soñadores y los hacedores.
</example>

<example>
3.- Cascade con Bleach Hydro-Clean:\n\n
**Objetivo Cuantificado:**\n
Impulsar el crecimiento de la marca Cascade mediante la migración de los consumidores de Cascade a Cascade Complete.\n\n
**Obstáculo Identificado:**\n
Desde su punto de vista, ella no tiene ninguna razón para pagar más por Cascade Complete, ya que no ve en qué se diferencia de su Cascade regular u otras marcas que usa.\n\n
**Insight Aumentado:**\n
Ella es la mujer que es un poco más germofóbica y, como resultado, es más proactiva en cuanto a que sus platos estén completamente limpios. Para ella, la limpieza necesita ir más allá de la superficie, especialmente cuando se trata de niños pequeños o al trabajar con ciertos alimentos crudos. Por eso, ha recurrido a ciertos comportamientos como lavar en el lavavajillas con agua extra caliente o incluso evitar cortar pollo en la tabla de cortar por completo, solo para asegurarse de que no haya "cosas pegajosas" que queden atrás y que sus platos estén seguros para comer.\n\n
**Desafío Creativo:**\n
Limpiar incluso lo que no se ve.
</example>
</examples>



EOT;

            // $model = "claude-3-7-sonnet-20250219";
            $model = "claude-sonnet-4-20250514";
            
            $temperature = 0.8;
            
            Log::info('Preparando llamada a AnthropicService', [
                'model' => $model,
                'temperature' => $temperature,
                'system_prompt_length' => strlen($system_prompt)
            ]);
            
            Log::info('Iniciando llamada a AnthropicService::TextGeneration');
            $response = AnthropicService::TextGeneration($prompt, $model, $temperature, $system_prompt);
           Log::info('Respuesta de AnthropicService recibida', [
    'response_type' => gettype($response),
    'response_keys' => is_array($response) ? array_keys($response) : 'no es array',
    'response_data_length' => is_array($response) && isset($response['data']) ? strlen($response['data']) : 'no disponible',
    'response_full' => $response // Agregar la respuesta completa para debugging
]);
// Verificar que la respuesta tenga los datos esperados
if (!is_array($response) || !isset($response['data'])) {
    Log::error('Respuesta inesperada de AnthropicService', [
        'response' => $response,
        'response_type' => gettype($response)
    ]);
    
    return response()->json([
        'error' => 'Respuesta inesperada del servicio de IA',
        'details' => 'La respuesta no contiene los datos esperados',
        'goto' => 2
    ]);
}
            Log::info('Preparando respuesta final');
            $finalResponse = [
                'success' => 'Datos procesados correctamente.', 
                'details' => array_merge($response, ['fuentes' => $fuentesResponse]),
                'goto' => 3, 
                'function' => 'generarGenesis'
            ];
            
            Log::info('Respuesta final preparada', [
                'response_keys' => array_keys($finalResponse)
            ]);

            return response()->json($finalResponse);

        } catch (\Exception $e) {
            Log::error('Error al generar Genesis', [
                'message' => $e->getMessage(),
                'accountId' => $accountId,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Ha ocurrido un error al generar el Genesis. Por favor, intenta nuevamente.',
                'details' => env('APP_DEBUG') ? $e->getMessage() : 'Error de conexión con el servicio AI',
                'goto' => 2
            ]);
        }

    } else {
        Log::error('No se encontraron datos de cuenta', [
            'accountId' => $accountId
        ]);
        return response()->json(['error' => 'faltan datos']);
    }
}


public function regenerateGenesis(Request $request){
    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        'genesisgenerado' => 'required|string',
        'account' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }
    ini_set('max_execution_time', 300);

    $genesisgenerado = $request->input('genesisgenerado');
    $accountId = $request->input('account');

    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

    if ($accountData) {
        $brief = $accountData->get('Brief');
        $objective = $accountData->get('360_objective');

        $prompt = <<<EOT

Ahora, basándote en la información proporcionada en el brief y el objetivo dado, crea un GÉNESIS completamente diferente con otro enfoque emocional a:

$genesisgenerado

Usando el brief: 
$brief

Y el objetivo: 
$objective

Analiza cuidadosamente la información disponible, identifica los puntos clave relevantes para cada componente del GÉNESIS, y desarrolla una estrategia coherente y efectiva. Responde en español y presenta solo el resultado final del GÉNESIS sin notas o textos adicionales.

EOT;

    $system_prompt = <<<EOT
Eres un experto en planificación estratégica publicitaria especializado en la metodología GÉNESIS (Generación Estratégica Neuroinspirada de Efectividad Sincronizada con Inteligencia Sintética) de god-ai (Objetivo cuantificado, Obstáculo identificado, Insight Aumentado, Desafío creativo). Esta metodología se utiliza para crear estrategias creativas publicitarios efectivas y consiste en los siguientes componentes:
1. Objetivo Cuantificado: Define el propósito de negocio medible y específico.
2. Obstáculo Identificado: Identifica el principal obstáculo para lograr el objetivo. ¿Qué se interpone en el camino para alcanzar ese objetivo? ¿Qué papel juega la comunicación en el cambio de esa conducta? A veces, puedes ver más de un obstáculo, pero trata de identificar la única cosa que, si se supera, hará que todo lo demás caiga en su lugar.
3. Insight Aumentado: Revela una verdad sobre el consumidor que ayuda a superar el obstaculo Identificado. ¿Cuál es la verdad que desbloquea este problema? ¿Qué sabemos sobre el consumidor que nos ayude a crear comunicaciones que superen el problema? Esta información debe provenir del consumidor, no del conocimiento del producto. Un gran insight sorprende a la gente con lo obvio.
4. Desafío Creativo: Plantea un desafío ambicioso y transformador para la comunicación. ¿Cuál es el reto para nuestras comunicaciones? Este debe ser grande, ambicioso y valiente. Puede ser provocador, pero al menos, emocionante. Un gran reto te ayudará a ver claramente y a romper las barreras. Transforma la marca. Nunca es una tarea. Es simple. No está basado en un negocio, un producto o una tarea.
Para crear un GÉNESIS efectivo:
- Mantén cada sección concisa y enfocada.
- Asegúrate de que todas las partes cuenten una historia coherente.
- Enfócate en las personas, no en el producto.
- Busca insights culturales profundos según la info del país.
- El desafío creativo debe ser transformador y no incluir el nombre de la marca.
-Analiza:, ¿Dice una historia simple?
-Menos palabras tienen un mayor impacto; trata de reducir el conteo de palabras a la mitad, casi siempre esto genera una mejora
-Trata de encontrar la línea conductora para que el brief realmente se lea como una historia
-Sé brutalmente honesto con el problema. Incluso si duele
-¿Tu insight se centra en las personas? Los insights más geniales son insights culturales
-Es tentador escribir un insight sobre el producto, pero al final debería regresar a la gente real en el mundo real
-¿Cómo es esto propio? ¿Cómo logro que esta marca se diferencie de la competencia?
- ¡Rompe las reglas!


Puedes usar estos ejemplos como inspiración y guía de formato respetando los saltos de líneas, no debes copiar:
<examples>
<example>
1.- Coca Cola: Sencillo pero resuelve\n\n
**Objetivo Cuantificado:**\n
Aumentar las ventas de Coca-Cola en formato individual en un 10% en los próximos 3 meses.\n\n
**Obstáculo Identificado:**\n
En un mercado saturado, los consumidores tienen múltiples opciones y están optando por alternativas percibidas como más saludables.\n\n
**Insight Aumentado:**\n
Los consumidores buscan pequeños momentos de placer y recompensa en su día a día.\n\n
**Desafío Creativo:**\n
Posicionar la Coca-Cola individual como el compañero ideal para los pequeños momentos de disfrute diario.
</example>

<example>
2.- Ameriprise: Transformador y mantiene a Ameriprise a la vanguardia\n\n
**Objetivo Cuantificado:**\n
Ayudar a crecer la adquisición de nuevos clientes aumentando los niveles de Amor y Respeto por Ameriprise.\n\n
**Obstáculo Identificado:**\n
Ameriprise compite en una categoría intensamente saturada donde todos luchan por lo mismo: la falta de confianza y la inercia. Para combatir esto, más y más competidores están intentando llegar a los consumidores con "plática franca" (Schwab, Smith Barney). Otros están siguiendo los pasos de Ameriprise, alentando a la gente a alcanzar sus sueños de retiro (Allianz, Wachovia, Lincoln). Ahora Ameriprise necesita adoptar una nueva táctica para diferenciarse del grupo una vez más.\n\n
**Insight Aumentado:**\n
Hemos dado en el clavo con la campaña actual. Inspira a la gente a verse como optimista y aventurera. Y esto hace que Ameriprise se vea más progresiva que la competencia. Ahora, a medida que ampliamos el público objetivo para incluir a los Baby Boomers y a la Generación X, necesitamos ir más allá del retiro, para alentar todo tipo de sueños: sueños grandes y pequeños, sueños de hoy y sueños de mañana, sueños egoístas y sueños altruistas, sueños para ti y sueños para tus hijos. Para romper con el cinismo de los asesores financieros, se necesita una representación de los asesores de Ameriprise como una raza aparte: accesibles, atentos y serviciales.\n\n
**Desafío Creativo:**\n
Hacer que Ameriprise marque la diferencia entre los soñadores y los hacedores.
</example>

<example>
3.- Cascade con Bleach Hydro-Clean:\n\n
**Objetivo Cuantificado:**\n
Impulsar el crecimiento de la marca Cascade mediante la migración de los consumidores de Cascade a Cascade Complete.\n\n
**Obstáculo Identificado:**\n
Desde su punto de vista, ella no tiene ninguna razón para pagar más por Cascade Complete, ya que no ve en qué se diferencia de su Cascade regular u otras marcas que usa.\n\n
**Insight Aumentado:**\n
Ella es la mujer que es un poco más germofóbica y, como resultado, es más proactiva en cuanto a que sus platos estén completamente limpios. Para ella, la limpieza necesita ir más allá de la superficie, especialmente cuando se trata de niños pequeños o al trabajar con ciertos alimentos crudos. Por eso, ha recurrido a ciertos comportamientos como lavar en el lavavajillas con agua extra caliente o incluso evitar cortar pollo en la tabla de cortar por completo, solo para asegurarse de que no haya "cosas pegajosas" que queden atrás y que sus platos estén seguros para comer.\n\n
**Desafío Creativo:**\n
Limpiar incluso lo que no se ve.
</example>
</examples>
EOT;

        try {
            $model = "claude-sonnet-4-20250514";
            $temperature = 0.8;
            $response = AnthropicService::TextGeneration($prompt, $model, $temperature, $system_prompt);

            return response()->json(['success' => 'Datos procesados correctamente.', 'details' => ['newgenesis' => $response['data'], 'oldgenesis' => $genesisgenerado], 'goto' => 3, 'function' => 'regenerateGenesis']);
        } catch (\Exception $e) {
            Log::error('Error al regenerar Genesis', [
                'message' => $e->getMessage(),
                'accountId' => $accountId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Ha ocurrido un error al regenerar el Genesis. Por favor, intenta nuevamente.',
                'details' => env('APP_DEBUG') ? $e->getMessage() : 'Error de conexión con el servicio AI',
                'goto' => 3
            ]);
        }

    }else{
        return response()->json(['error' => 'faltan datos']);
    }
}

public function construccionescenario(Request $request){
    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        'genesisgenerado' => 'required|string',
        'account' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }
    ini_set('max_execution_time', 300);

    $genesisgenerado = $request->input('genesisgenerado');
    $accountId = $request->input('account');
    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

    if ($accountData) {
        // Buscar el registro existente
        $brief = $accountData->get('Brief');
        $objective = $accountData->get('360_objective');

        try {
            // Guarda los datos del génesis
            Field::updateOrCreate(
                ['account_id' => $accountId, 'key' => '360_genesis'],
                ['value' => $genesisgenerado]
            );
            
            // Genera insight usando el génesis
            $insightgenerado = $this->GenerarInsight2($brief,$objective,$genesisgenerado);
            $insightdata = $insightgenerado['data'];
            $insightfuentes = $insightgenerado['fuentes'];

            // Verificar si las fuentes existen y convertirlas en enlaces clickeables con comillas dobles
            if (is_array($insightfuentes) && !empty($insightfuentes)) {
                $fuentesHTML = '<p><strong>Fuentes:</strong></p><ul><li>' . implode(
                    '</li><li>',
                    array_map(function ($fuente) {
                        return filter_var($fuente, FILTER_VALIDATE_URL)
                            ? sprintf('<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', htmlspecialchars($fuente, ENT_QUOTES, 'UTF-8'), htmlspecialchars($fuente, ENT_QUOTES, 'UTF-8'))
                            : htmlspecialchars($fuente, ENT_QUOTES, 'UTF-8'); // Escapar si no es URL
                    }, $insightfuentes)
                ) . '</li></ul>';
            } else {
                $fuentesHTML = '<p><strong>Fuentes:</strong> No se encontraron fuentes.</p>';
            }

            Field::updateOrCreate(
                [
                    'account_id' => $accountId,
                    'key' => 'fuentesEscenario'
                ],
                [
                    'value' => $fuentesHTML
                ]
            );
            
            // Preparar las fuentes para la respuesta
            $fuentesResponse = is_string($insightfuentes) ? explode("\n", trim($insightfuentes)) : $insightfuentes;

            // Genera el escenario
            $prompt = <<<EOT
            Analiza cuidadosamente la información disponible, además de los Insights sociales poderosos que te servirán para nutrir el resultado, identifica los puntos clave relevantes para cada componente creativo. Responde en español y presenta solo el resultado final sin notas o textos adicionales. Aquí tienes la información para lograrlo:

            Génesis:
            $genesisgenerado

            Brief:
            $brief

            Objetivo:
            $objective


            Insights Sociales:
            $insightdata

            EOT;

            $system_prompt = <<<EOT
            Ahora explota tu potencial creativo y debes construir un escenario estratégico creativo con el Objetivo Cuantificado planteado, con el Obstáculo Identificado, para crear el escenario usando el Insight Aumentado y el Desafío Creativo.
            El concepto debe ser replanteado internamente por parte tuya y de 3 opciones que desarrolles, elige mostrar la que sea más original y creativa. Reflexiona internamente para mejorar el resultado y que sea de talla mundial.
            Entrega siempre esta formato sin información adicional y respetando los saltos de líneas:
            **Contexto**\n
            Un relato que amplifique y evidencie el Objetivo Cuantificado (tendencias, hechos, observaciones, etc)
            **Problema**\n
            Dar, mediante storytelling y ojalá desde el punto de vista del target, esa visión que representa esa lucha o frustración. 
            **Solución**\n
            Tomando el Desafío Creativo, se establece la manera en la cual se resolverá
            **Concepto**\n
            Condensación del Desafío Creativo en la frase publicitaria
            REGLAS
            - Mantén cada sección concisa y enfocada.
            - Asegúrate de que todas las partes cuenten una historia coherente.
            - Enfócate en las personas, no en el producto.
            - Busca aspectos culturales profundos según la info del país. Usa los insights sociales para alimentar la creatividad
            - El concepto debe ser transformador y puede no incluir el nombre de la marca si lo ves necesario.
            -Trata de encontrar la línea conductora para que se lea como una historia
            -Sé brutalmente honesto con el problema. Incluso si duele
            -¿Tu solución se centra en las personas? Los insights más geniales son insights culturales, no siempre te bases en tecnología, centraté en solucionar el problema y cumplir con el objetivo
            - ¿Cómo logro que esta marca se diferencie de la competencia? 
            - Para los relatos usa nombres al azar (evitando María, José o Luis) de diferentes personas y asigna el género según la coherencia de los datos del brief, el país y el storytelling. Puedes usar modismos regionales/país.
            - ¡Rompe las reglas!

            EOT;

            $model = "claude-sonnet-4-20250514";
            $temperature = 0.8;
            $response = AnthropicService::TextGeneration($prompt, $model, $temperature, $system_prompt);

            // Field::updateOrCreate(
            //     ['account_id' => $accountId, 'key' => '360_construccionescenario'],
            //     ['value' => $response['data']]
            // );

            return response()->json(
            ['success' => 'Datos procesados correctamente.', 
            'details' =>   array_merge($response, ['fuentes' => $fuentesResponse]), 
            'goto' => 4, 
            'function' => 'construccionescenario'
                ]);

        } catch (\Exception $e) {
            Log::error('Error al generar construcción de escenario', [
                'message' => $e->getMessage(),
                'accountId' => $accountId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Ha ocurrido un error al generar el escenario. Por favor, intenta nuevamente.',
                'details' => env('APP_DEBUG') ? $e->getMessage() : 'Error de conexión con el servicio AI',
                'goto' => 3
            ]);
        }
    } else {
        return response()->json(['error' => 'faltan datos']);
    }
}

public function regenerarConstruccionEscenario(Request $request){
    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        'construccionescenario' => 'required|string',
        'genesisgenerado' => 'required|string',
        'account' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }
    ini_set('max_execution_time', 300);

    $construccionescenario = $request->input('construccionescenario');
    $genesisgenerado = $request->input('genesisgenerado');
    $accountId = $request->input('account');

    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

    if ($accountData) {
        $brief = $accountData->get('Brief');
        $objective = $accountData->get('360_objective');

        try {
            $prompt = <<<EOT
            Analiza cuidadosamente la información disponible, incluyendo el Escenario ya generado previamente. Tu tarea es construir un nuevo escenario creativo que sea distinto al anterior, pero manteniendo coherencia con el objetivo y los insights sociales. No debe ser una repetición, sino una nueva interpretación creativa con un enfoque diferente.

            Aquí tienes la información relevante para lograrlo:

            ESCENARIO ANTERIOR:
            $construccionescenario

            Génesis:
            $genesisgenerado

            Brief:
            $brief

            Objetivo:
            $objective

            Genera un nuevo escenario siguiendo la estructura establecida, pero asegurándote de que tenga un enfoque fresco, innovador y diferenciado del escenario anterior. Responde en español y presenta solo el resultado final del escenario nuevo sin notas o textos adicionales.
            EOT;

            $system_prompt = <<<EOT
            Ahora explota tu potencial creativo y debes construir un escenario estratégico creativo con el Objetivo Cuantificado planteado, con el Obstáculo Identificado, para crear el escenario usando el Insight Aumentado y el Desafío Creativo.
            El concepto debe ser replanteado internamente por parte tuya y de 3 opciones que desarrolles, elige mostrar la que sea más original y creativa. Reflexiona internamente para mejorar el resultado y que sea de talla mundial.
            Entrega siempre esta formato sin información adicional y respetando los saltos de líneas:
            **Contexto**\n
            Un relato que amplifique y evidencie el Objetivo Cuantificado (tendencias, hechos, observaciones, etc)
            **Problema**\n
            Dar, mediante storytelling y ojalá desde el punto de vista del target, esa visión que representa esa lucha o frustración. 
            **Solución**\n
            Tomando el Desafío Creativo, se establece la manera en la cual se resolverá
            **Concepto**\n
            Condensación del Desafío Creativo en la frase publicitaria
            REGLAS
            - Mantén cada sección concisa y enfocada.
            - Asegúrate de que todas las partes cuenten una historia coherente.
            - Enfócate en las personas, no en el producto.
            - Busca aspectos culturales profundos según la info del país. Usa los insights sociales para alimentar la creatividad
            - El concepto debe ser transformador y puede no incluir el nombre de la marca si lo ves necesario.
            -Trata de encontrar la línea conductora para que se lea como una historia
            -Sé brutalmente honesto con el problema. Incluso si duele
            -¿Tu solución se centra en las personas? Los insights más geniales son insights culturales, no siempre te bases en tecnología, centraté en solucionar el problema y cumplir con el objetivo
            - ¿Cómo logro que esta marca se diferencie de la competencia? 
            - Para los relatos usa nombres al azar (evitando María, José o Luis) de diferentes personas y asigna el género según la coherencia de los datos del brief, el país y el storytelling. Puedes usar modismos regionales/país.
            - ¡Rompe las reglas!

            EOT;

            $model = "claude-sonnet-4-20250514";
            $temperature = 0.8;
            $response = AnthropicService::TextGeneration($prompt, $model, $temperature, $system_prompt);

            return response()->json(['success' => 'Datos procesados correctamente.', 'details' => ['newescenario' => $response['data'], 'oldescenario' => $construccionescenario], 'goto' => 4, 'function' => 'regenerarConstruccionEscenario']);
        } catch (\Exception $e) {
            Log::error('Error al regenerar construcción de escenario', [
                'message' => $e->getMessage(),
                'accountId' => $accountId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Ha ocurrido un error al regenerar el escenario. Por favor, intenta nuevamente.',
                'details' => env('APP_DEBUG') ? $e->getMessage() : 'Error de conexión con el servicio AI',
                'goto' => 3
            ]);
        }
    } else {
        return response()->json(['error' => 'faltan datos']);
    }
}
public function eleccioncampania(Request $request){
    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        'construccionescenario' => 'required|string',
        'account' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }
    $construccionescenario = $request->input('construccionescenario');
   
    $accountId = $request->input('account');
    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');
    // $objective = Field::where('key', '360_objective')->where('account_id', $accountId)->first();
    // $problema = Field::where('key', '360_problema')->where('account_id', $accountId)->first();
    // $insight = Field::where('key', '360_insight')->where('account_id', $accountId)->first();
    if ($accountData) {
        // Buscar el registro existente
        $brief = $accountData->get('Brief');
        $objective = $accountData->get('360_objective');
        $genesis = $accountData->get('360_genesis');

       

        // $prompt = "Tu función es generar un CONTEXTO, PROBLEMA, SOLUCIÓN y CONCEPTO usando estos datos. \nObjetivo: $objective\nProblema: $problema\nInsight: $insight\nReto: $reto\nConstrucción de escenario:";
        // $prompt .= "El formato que debes enviar la información es HTML";

        // $response = AnthropicService::TextGeneration($prompt);
        return response()->json(['success' => 'Datos procesados correctamente eleccioncampania.', 'details' => $genesis, 'goto' => 5]);
    }else{
        return response()->json(['error' => 'faltan datos']);
    }
}

public function saveeleccioncampania(Request $request){
    // Validar las URLs y archivos
    
    $validator = Validator::make($request->all(), [
        '360_Tipo_de_campaña' => 'required|string',
        'account' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }

    ini_set('max_execution_time', 300);

    $construccionescenario = $request->input('construccionescenario');
    $construccionescenariofinal = $construccionescenario;
    $accountId = $request->input('account');
    $account = Account::find($accountId);
    // Buscar el registro existente
    Field::updateOrCreate(
        ['account_id' => $accountId, 'key' => '360_construccionescenario'],
        ['value' => $construccionescenariofinal]
    );
    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');
    

    if ($accountData) {
        
       
        $category = $account->category;
    Log::info("Categoria encontrada", [
        'Categoría' => $category,
      
    ]);
        $brief = $accountData->get('Brief');
        $objective = $accountData->get('360_objective');
        $genesis = $accountData->get('360_genesis');
        $fuentesGenesis= $accountData->get('fuentesGenesis');
        $fuentesEscenario= $accountData->get('fuentesEscenario');
        $construccionescenario = $accountData->get('360_construccionescenario');
        $genesiscompleto = $genesis . $construccionescenario;

        $fields = [
            '360_Tipo_de_campaña' => $request->input('360_Tipo_de_campaña'),
        ];

        foreach ($fields as $key => $value) {
            if (!is_null($value)) {
                Field::updateOrCreate(
                    ['account_id' => $accountId, 'key' => $key],
                    ['value' => $value]
                );
            }
        }
        $Tipodecampaña = $request->input('360_Tipo_de_campaña');
        $country = $accountData->get('country');

        $creatividad = $this->generarCreatividad($Tipodecampaña, $objective, $genesiscompleto, $brief, $category);
        $estrategia = $this->generarEstrategia($Tipodecampaña, $objective, $genesiscompleto, $country, $brief);
        $ideasContenido = $this->generarIdeasContenido($Tipodecampaña, $objective, $genesiscompleto, $brief, $creatividad['data'], $estrategia['data'] );

    

        //$innovacion = $this->generarInnovacion($Tipodecampaña, $objective, $construccionescenario, $brief);
        // $estrategia= "estrategia";
        // $creatividad="creatividad";
        // $ideasContenido="ideasContenido";


        $response = ['genesis' =>  $genesis,'fuentesGenesis'=>$fuentesGenesis,'escenario'=>$construccionescenario ,'fuentesEscenario'=>$fuentesEscenario, 'estrategia' => $estrategia, 'creatividad' => $creatividad, 'contenido' => $ideasContenido];

        return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $response, 'goto' => 6, 'function' => 'construccionEstrategiaCreatividadInnovacion']);
        // return response()->json(['success' => 'Datos procesados correctamente.', 'details' => true, 'goto' => 6]);
    }else{
        return response()->json(['error' => 'faltan datos']);
    }
}

public function generateNewCreatividadEstrategiaInnovacion(Request $request){
    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        'account' => 'required|integer',
        'type' => 'required|string',
        'creatividad' => 'required|string',
        'estrategia' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }

    ini_set('max_execution_time', 300);

    $accountId = $request->input('account');
    $account = Account::find($accountId);
    $category = $account->category;

    $type = $request->input('type');
    $creatividad = $request->input('creatividad');
    $estrategia = $request->input('estrategia');
    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

    if ($accountData) {
        // Buscar el registro existente
        $brief = $accountData->get('Brief');
        $objective = $accountData->get('360_objective');
        $genesis = $accountData->get('360_genesis');
        $construccionescenario = $accountData->get('360_construccionescenario');
        $Tipodecampaña = $accountData->get('360_Tipo_de_campaña');
        $country = $accountData->get('country');

        switch ($type) {
            case 'Creatividad':
                $response = $this->generarCreatividad($Tipodecampaña, $objective, $construccionescenario, $brief, $category);
                break;
            case 'Estrategia':
                $response = $this->generarEstrategia($Tipodecampaña, $objective, $construccionescenario, $country, $brief);
                break;
            case 'Contenido':
                $response = $this->generarIdeasContenido($Tipodecampaña, $objective, $construccionescenario, $brief, $creatividad, $estrategia );
                break;
            // case 'Innovacion':
            //     $response = $this->generarInnovacion($Tipodecampaña, $objective, $construccionescenario, $brief);
            //     break;
            default:
            $response = null;
                break;
            }

        return response()->json($response);
    }else{
        return response()->json(['error' => 'faltan datos']);
    }

}
public function generarCreatividad($Tipodecampaña, $objective, $genesiscompleto, $brief, $category)
{
    Log::info("Inicio de generarCreatividad", [
        'Tipo de campaña' => $Tipodecampaña,
        'Objetivo' => $objective,
        'Categoría recibida' => $category
    ]);
    
    $assistantMap = [
        'Alimentación y Bebidas'                   => 'asst_qQGOx0GuidFzGfaSeT9ZnrmC',
        'Moda y Belleza'                           => 'asst_pF5G6eNmnScR0pnZsLl3g9XA',
        'Salud y Bienestar'                        => 'asst_Nax5p5MhFKfIzNAy3HOFgH6f',
        'Tecnología y Electrónica'                 => 'asst_LLzFPTxDBz4WrVVE9O1qIY7V',
        'Educación y Formación'                    => 'asst_5jg3JiYcB9oaOcq1FsXAHOSv',
        'Turismo y Entretenimiento'                => 'asst_2PvGR0EmUTvdR6d8mKB0o1m4',
        'Automotriz y Transporte'                  => 'asst_BaWjivMuhDDM9H5n7ajc9UW3',
        'Bienes Raíces y Construcción'             => 'asst_3NVMRs0oWhCy65l8sKALsevx',
        'Servicios Profesionales'                   => 'asst_sY5pTeWYm6He7tOueGLS4z6o',
        'Deportes y Fitness'                       => 'asst_yOtkV1l3aT9733f1U78Ykj3U',
        'Salud y Medicina'                         => 'asst_d8Qec5aXcbQAOvhiggZSKmJd',
        'E-commerce y Tiendas Online'              => 'asst_uaemeiyxaFYdI1p7WwLnOTpX',
        'Bienestar y Estilo de Vida'               => 'asst_amBE1b5YZSFYCbjAPDXnPBhi',
        'Hogar y Decoración'                       => 'asst_J1YuscRVgZvRuFFg4rIBty3d',
        'Servicios Financieros'                    => 'asst_xy3YrJIuGT8GOh8dGkTZUhYx',
        'Energía y Sostenibilidad'                 => 'asst_vc7ljx4ap3e1cQBmjc9ROti9',
        'Agronegocios y Agroindustria'             => 'asst_VISu1lJA4L7gHCOphX0VXxLa',
        'Medios, Comunicación y Contenido Digital' => 'asst_kqdYi89wvkTKjAk21mEjrFmJ',
        'Logística y Cadena de Suministro'         => 'asst_TOQ8ggRyiODmmZpfSy9L0WvD',
        'Emprendimiento e Innovación'              => 'asst_pJOnxGt9oCMEHZVynWwrqHFG',
        'Arte, Cultura y Creatividad'              => 'asst_FW8JGyhxYi7H2ICAZZejJ3EX',
        'Negocios B2B y Servicios Industriales'    => 'asst_RqZhkeC0xGoCCYSKGJd4kHZr',
        'Gaming y eSports'                         => 'asst_KkeCs6HRWNYyiqWMa4JgSSmo',
    ];
    

    
    if (array_key_exists($category, $assistantMap)) {
        $assistant_idCreatividad = $assistantMap[$category];
        Log::info("Asistente encontrado para la categoría", [
            'Categoría' => $category,
            'Asistente ID' => $assistant_idCreatividad
        ]);
    } else {
         
        // throw new \Exception("No se encontró un asistente para la categoría: $category");
        
        $assistant_idCreatividad = "asst_dBtNQk8BArQyo9GNndD19lEQ";
        Log::warning("Categoría no encontrada en el mapa, asignando asistente por defecto", [
            'Categoría' => $category,
            'Asistente ID' => $assistant_idCreatividad
        ]);
    }

    // prompt

    $promptCreatividad = <<<EOT
Genera las mejores propuestas creativas para una campaña de: $Tipodecampaña. Las propuestas deben considerar este objetivo cuantificable: $objective, el concepto creativo a considerar es: $genesiscompleto. Los datos de marca a considerar para hacer las mejores propuestas son: $brief
EOT;
   Log::info("Asistente enviado id:", [

            'Asistente ID' => $assistant_idCreatividad
        ]);
    return OpenAiService::CompletionsAssistants($promptCreatividad, $assistant_idCreatividad);
}



public function generarEstrategia($Tipodecampaña, $objective, $genesiscompleto, $country, $brief){
    switch ($country) {
        case 'Argentina':
            $assistant_id = 'asst_BvDMPlQNMPiWKGSHi8YgTiyS';
            break;
    
        case 'Bolivia':
            $assistant_id = 'asst_uADcK3IQ89fH2VuaJNzQ1CXD';
            break;
    
        case 'Brasil':
            $assistant_id = 'asst_kt9evA75QLZVeNIrxfdGhN2A';
            break;
    
        case 'Chile':
            $assistant_id = 'asst_DHgTmpCk3d9xfMWDSub6xcSl';
            break;
    
        case 'Colombia':
            $assistant_id = 'asst_tBowMNjPYoLyr9xDvNeu7Saf';
            break;
    
        case 'Costa Rica':
            $assistant_id = 'asst_3lCVysGe7Lc7GeYq1RKIItaU';
            break;
    
        case 'Ecuador':
            $assistant_id = 'asst_iTuGNv1NhNFKYDFdDQmTyqwc';
            break;
    
        case 'El Salvador':
            $assistant_id = 'asst_pO8CsVUw34ru3jrcWF7AdFhy';
            break;
    
        case 'Guatemala':
            $assistant_id = 'asst_qB9F4cwQgBLgQoyCaffeRc1R';
            break;
    
        case 'Honduras':
            $assistant_id = 'asst_VbS87oW41ZCkGTtxv1u8aqkV';
            break;
    
        case 'México':
            $assistant_id = 'asst_1jk0I6nOdAJfHeULJbmWDi4Z';
            break;
    
        case 'Nicaragua':
            $assistant_id = 'asst_qJu94YdEZPFHj3SJnMQj3Ku3';
            break;
    
        case 'Panamá':
            $assistant_id = 'asst_aTXuxujeSDZaE2T5R8hsvlbK';
            break;
    
        case 'Paraguay':
            $assistant_id = 'asst_VQZdR1KDvrW2gGmYCdvXPyqa';
            break;
    
        case 'Perú':
            $assistant_id = 'asst_X9MGseWiwRCkqusXNWen5Fot';
            break;
    
        case 'Puerto Rico':
            $assistant_id = 'asst_HY45bfqa1ulBCYcqLr2eLPUb';
            break;
    
        case 'República Dominicana':
            $assistant_id = 'asst_4g3FitApu6LaJfFYf7bRENjA';
            break;
    
        case 'Uruguay':
            $assistant_id = 'asst_vM1lPc4l7ywEHiB5P7RmnqfA';
            break;
    
        default:
            $assistant_id = 'asst_tBowMNjPYoLyr9xDvNeu7Saf'; // Por defecto el de Colombia
            break;
    }
    

    $prompt = <<<EOT
Genera la mejor estrategia digital para una campaña de: 
$Tipodecampaña 
Para el país: 
$country 
La estrategia debe considerar este objetivo cuantificable: 
$objective, 
Los datos del brief son: 
$brief
El concepto creativo a considerar es: 
$genesiscompleto

EOT;
   Log::info("Asistente encontrado para la estrategia", [
            'País del asistente' => $country,
            'Asistente ID' => $assistant_id
        ]);

    return OpenAiService::CompletionsAssistants($prompt, $assistant_id);
}

public function generarIdeasContenido($Tipodecampaña, $objective, $genesiscompleto, $brief, $creatividad, $estrategia ){

    $promptIdeasContenido = <<<EOT
    Genera las mejores 50 Ideas de contenido respetando el brief, la estrategia digital, las plataformas seleccionadas y el concepto creativo.
    
    Brief: 
    $brief
    
    Las propuestas deben considerar este objetivo cuantificable: 
    $objective
    
    Bajo este concepto creativo y lineamientos: 
    $genesiscompleto 
    
    Para una campaña de: 
    $Tipodecampaña
    
    La bajada de estrategia es:
    $estrategia
    
    La bajada de creatividad es:
    $creatividad
    
    
    EOT;
    

    $assistant_idIdeasContenido = "asst_tQcA7RfVfjt1wnL1uCpjBz1C";

    return OpenAiService::CompletionsAssistants($promptIdeasContenido, $assistant_idIdeasContenido);
}

public function generarInnovacion($Tipodecampaña, $objective, $construccionescenario, $brief){
    $promptInnovacion = <<<EOT
Genera las mejores propuestas innovadoras tecnológicas para una campaña de: $Tipodecampaña. Las propuestas deben considerar este objetivo cuantificable: $objective, el concepto creativo a considerar es: $construccionescenario. Los datos de marca a considerar para hacer las mejores propuestas son: $brief

EOT;

    $assistant_idInnovacion = "asst_uBOwtwTwTL5fLE6smY36FwAP";

    return OpenAiService::CompletionsAssistants($promptInnovacion, $assistant_idInnovacion);
}

public function saveEstrategiaCreatividadInnovacion(Request $request){
    // Validar las URLs y archivos
    $validator = Validator::make($request->all(), [
        'construccionCreatividad' => 'required|string',
        'construccionEstrategia' => 'required|string',
        'construccionIdeasContenido' => 'required|string',
        //'construccionInnovacion' => 'required|string',
        'account' => 'required|integer',
        'file_name' => 'required|string',
        'rating' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }

    // ini_set('max_execution_time', 300);

    $accountId = $request->input('account');
    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

    if ($accountData) {
        // Buscar el registro existente
        $brief = $accountData->get('Brief');
        $objective = $accountData->get('360_objective');
        $genesis = $accountData->get('360_genesis');
        $fuentesGenesis= $accountData->get('fuentesGenesis');
        $fuentesEscenario= $accountData->get('fuentesEscenario');
        $construccionescenario = $accountData->get('360_construccionescenario');
        $estrategia = $request->input('construccionEstrategia');
        $creatividad = $request->input('construccionCreatividad');
        $contenido = $request->input('construccionIdeasContenido');
        //$innovacion = $request->input('construccionInnovacion');

        $fields = [
            'construccionCreatividad' => $creatividad,
            'construccionEstrategia' => $estrategia,
            'construccionIdeasContenido' => $contenido,
            //'construccionInnovacion' => $innovacion,
        ];

        $resultGenesis = $genesis . '<br><br>' .$fuentesGenesis . '<br><br>' .$construccionescenario . '<br><br>'.$fuentesEscenario. '<br><br>'. $creatividad . '<br><br>' . $estrategia . '<br><br>' . $contenido;

        Generated::Create(
            ['account_id' => $accountId, 'key' => 'Genesis', 'name' => $request->input('file_name'), 'value' => $resultGenesis, 'rating' => $request->input('rating')],
        );    

        foreach ($fields as $key => $value) {
            if (!is_null($value)) {
                Field::updateOrCreate(
                    ['account_id' => $accountId, 'key' => $key],
                    ['value' => $value]
                );
            }
        }

        $response = ['genesis' => $genesis,'fuentesGenesis'=>$fuentesGenesis,'escenario'=>$construccionescenario,'fuentesEscenario'=>$fuentesEscenario, 'creatividad' => $creatividad, 'estrategia' => $estrategia, 'contenido' => $contenido];

        return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $response, 'goto' => 7, 'function' => 'mostrarEstrategiaCreatividadInnovacion']);

    }
}

public function download(Request $request)
{
    $validator = Validator::make($request->all(), [
        'account' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()]);
    }

    $accountId = $request->input('account');
    $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

    if ($accountData) {
        // Buscar el registro existentes
        $construccionescenario = $accountData->get('360_construccionescenario');
        $genesis = $accountData->get('360_genesis');
        $fuentesGenesis= $accountData->get('fuentesGenesis');
        $fuentesEscenario= $accountData->get('fuentesEscenario');
        $estrategia = $accountData->get('construccionEstrategia');
        $creatividad = $accountData->get('construccionCreatividad');
        $contenido = $accountData->get('construccionIdeasContenido');
        //$innovacion = $accountData->get('construccionInnovacion');

        $fields = [
            'construccionescenario' => $construccionescenario,
            'genesis'=> $genesis,
            'fuentesGenesis'=>$fuentesGenesis,
            'fuentesEscenario'=> $fuentesEscenario,
            'creatividad' => $creatividad,
            'estrategia' => $estrategia,
            'contenido' => $contenido,
            //'innovacion' => $innovacion,
        ];
    }

    // Cargar la vista Blade que contiene la plantilla PDF
    $pdf = Pdf::setOptions([
        'defaultFont' => 'sans-serif',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true
    ])->loadView('herramienta2.pdf.template', array_merge($fields));
    
    // Obtén la fecha y hora actual
    $now = Carbon::now();

    // Formatea la fecha y hora como una cadena en el formato deseado (por ejemplo, "YYYYMMDD_HHMMSS")
    $timestamp = $now->format('Ymd_His');

    // Descargar el PDF
    return $pdf->download('Genesis_' . $timestamp . '.pdf');

}

public function GenerarInsight($brief, $objective){
    try {
        $prompt = <<<EOT
En base a la siguiente información del brief y objetivo de campaña, encuentras poderosos insights culturales del país asignado en el brief, también si encuentras competidores o campañas similares que se asemejen al objetivo, mencionalos para tomar en cuenta
BRIEF: 
$brief
OBJETIVO: 
$objective

EOT;

        $model = "sonar-reasoning";
        $temperature = 0.7;
    
        $response = PerplexityService::ChatCompletions($prompt, $model, $temperature);

        return array('data' => $response['data'], 'fuentes'=>$response['citations']);
    } catch (\Exception $e) {
        Log::error('Error al generar Insight', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        throw $e; // Re-lanzamos la excepción para que sea capturada en el método principal
    }
}

public function GenerarInsight2($brief, $objective, $genesisgenerado){
    try {
        $prompt = <<<EOT
Usando la siguiente información, busca conversaciones reales, temas, insights sociales sobre los temas relacionados para potenciar cualquier concepto creativo. Hazlo con un enfoque social.
GENESIS:
$genesisgenerado
BRIEF: 
$brief
OBJETIVO: 
$objective

EOT;

        $model = "sonar-reasoning";
        $temperature = 0.7;
    
        $response = PerplexityService::ChatCompletions($prompt, $model, $temperature);

        return array('data' => $response['data'], 'fuentes'=>$response['citations']);
    } catch (\Exception $e) {
        Log::error('Error al generar Insight', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        throw $e; // Re-lanzamos la excepción para que sea capturada en el método principal
    }
}
}
