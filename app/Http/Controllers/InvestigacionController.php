<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Generated;
use App\Services\PerplexityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class InvestigacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('haveaccess','investigacion.index');
        $accounts = Account::fullaccess()->get();
        return view('herramienta1.dashboard.investigacion.index',compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Guardando investigación', [
                'account_id' => $request->input('account')
            ]);
    
            $accountId = $request->input('account');
            $investigacionData = $request->input('investigaciongenerada');
            
            // Validar que el account_id existe
            if (!$accountId) {
                throw new \Exception('No se ha proporcionado un ID de cuenta válido');
            }
    
            // Crear el registro solo con la investigación (HTML puro)
            $generated = Generated::create([
                'account_id' => $accountId,
                'key' => 'Investigacion',
                'name' => $request->input('file_name'),
                'value' => $investigacionData, // Aquí se guarda solo el HTML
                'rating' => $request->input('rating'),
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Investigación guardada con éxito',
                'generated_id' => $generated->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar investigación: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'error' => 'Error al guardar la investigación: ' . $e->getMessage()
            ], 500);
        }
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function generarInvestigacion(Request $request)
{
    $accountId = $request->input('account');
    $country = $request->input('country');
    $brand = $request->input('brand');
    $instruccion = $request->input('instruccion');

    set_time_limit(600);
    ini_set('max_execution_time', 600);

    try {
        $respuestaper = $this->callPerplexity($country, $brand, $instruccion);
        
        // VALIDAR SI HAY ERROR
        if (isset($respuestaper['error']) && !empty($respuestaper['error'])) {
            Log::error('Error en callPerplexity', [
                'error' => $respuestaper['error']
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $respuestaper['error']
            ], 500);
        }
        
        // VALIDAR SI HAY DATA
        if (!isset($respuestaper['data']) || empty($respuestaper['data'])) {
            return response()->json([
                'success' => false,
                'error' => 'No se recibió contenido de la investigación. Por favor, intenta nuevamente.'
            ], 500);
        }
        
        $fuentes = $respuestaper['fuentes'] ?? [];

        // Convertimos las fuentes a una lista numerada con enlaces en líneas separadas
        $fuentesFormatted = !empty($fuentes)
        ? "<p>" . implode("</p><p>", array_map(fn($fuente, $index) => ($index + 1) . ". <a href=\"$fuente\" target=\"_blank\">$fuente</a>", $fuentes, array_keys($fuentes))) . "</p>"
        : "<p>No se encontraron fuentes.</p>";

        // Limpiar el contenido dentro de las etiquetas <think>
        $investigacionData = preg_replace('/<think>.*?<\/think>/s', '', $respuestaper['data']);
        $investigacionData = trim($investigacionData);

        $investContent = $investigacionData . "<p><strong>Fuentes:</strong><br>" . $fuentesFormatted . "</p>";

        return response()->json([
            'success' => true,
            'details' => $investContent,
            'goto' => 3
        ]);
    } catch (\Exception $e) {
        Log::error('Error en generarInvestigacion', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => 'Error inesperado al generar la investigación. Por favor, intenta nuevamente más tarde.'
        ], 500);
    }
}

   
    public function callPerplexity($country, $brand, $instruccion){
        

 $prompt = <<<EOT
Conduct a comprehensive advertising and marketing research report on the brand "$brand," operating in "$country," addressing the following user request: "$instruccion." The report should be professionally structured, detailed, and easy to navigate, containing updated and relevant data. The deliverable should include:

- Executive Summary
- Background and Market Context
- Brand Analysis (current market positioning, advertising strategies, and competitive landscape)
- Key Insights and Recommendations directly responding to the user's specific instruction

Ensure the report meets professional advertising research standards similar to those produced by leading agencies such as Kantar.

Your response must be formatted **strictly in Markdown** using the following rules:

1. Use `#` for main titles.
2. Use `##` for subtitles.
3. Use standard paragraph text for regular content (no extra formatting unless necessary).
4. Use **numbered lists** where needed for structured points.
5. Do **not** include any HTML tags or code formatting.
6. The response must be clear, clean, and easy to parse using Markdown renderers (GitHub, Notion, PDF converters, etc.)
7. All sections must be written in **Spanish** in a professional, research-oriented tone.
EOT;


        
$system_prompt = <<<EOT
You are a specialist researcher in advertising and marketing whose task is to conduct detailed online research and deliver responses in a professional, advertising-research format in Spanish.

Rules:

- Always respond exclusively in Spanish, using a professional and research-driven tone.
- Present the final answer strictly in the form of an advertising research report, avoiding any explanation of how the information was obtained.
- Format the response strictly in Markdown using this structure:
  1. Use `#` for main titles.
  2. Use `##` for subtitles.
  3. Write regular text as simple paragraphs.
  4. For enumerations, use numbered Markdown lists.
  5. Avoid any HTML or code formatting.
  6. The entire response must be well-structured, clean, and professionally presented for easy export to PDF or other formats.
  7. Ensure the result is aligned with the standards of high-end advertising research agencies like Kantar.

Process:

1. Clearly identify the parameters provided by the user: country, brand, and specific request.
2. Conduct thorough online research using those parameters.
3. Curate the most relevant and updated information.
4. Write the report in a structured, professional format.
5. Deliver the response in Markdown, and **nothing else**.
EOT;

        
          

try {
    $model = "sonar-deep-research";
    $temperature = 0.5;
    $response = PerplexityService::ChatCompletions($prompt, $model, $temperature, $system_prompt);
    
    // Log completo de la respuesta de Perplexity
    Log::info('Respuesta PerplexityService::ChatCompletions', [
        'response' => $response
    ]); 
    
    // VALIDAR PRIMERO SI HAY ERROR
    if (isset($response['error'])) {
        Log::error('Error en respuesta de Perplexity', [
            'error' => $response['error'],
        ]);
        // DEVOLVER ARRAY, NO JsonResponse
        return [
            'error' => 'Error al obtener datos de la IA: ' . 
                      ($response['error'] ?? 'Respuesta inesperada del servicio'),
            'data' => null,
            'fuentes' => []
        ];
    }
    
    // VALIDAR SI EXISTE DATA
    if (!isset($response['data']) || empty($response['data'])) {
        Log::error('La respuesta de ChatCompletions no contiene datos válidos', [
            'response' => $response,
        ]);
        // DEVOLVER ARRAY, NO JsonResponse
        return [
            'error' => 'No se recibieron datos válidos de la IA. Por favor, intenta nuevamente.',
            'data' => null,
            'fuentes' => []
        ];
    }

    return [
        'data' => $response['data'],
        'fuentes' => $response['citations'] ?? [],
        'error' => null
    ];
    
} catch (\Exception $e) {
    Log::error('Error en la llamada a PerplexityService::ChatCompletions', [
        'exception' => $e->getMessage(),
        'prompt' => substr($prompt, 0, 200) // Solo los primeros 200 chars
    ]);
    // DEVOLVER ARRAY, NO JsonResponse
    return [
        'error' => 'Error al procesar la solicitud: ' . $e->getMessage(),
        'data' => null,
        'fuentes' => []
    ];
}

}

/**
 * Descargar la última investigación generada para una cuenta.
 */
public function downloadLast($accountId)
{
    try {
        // Buscar el último documento generado para esta cuenta
        $generated = Generated::where('account_id', $accountId)
            ->where('key', 'Investigacion')
            ->latest('id')
            ->first();
        
        if (!$generated) {
            return redirect()->route('dashboard')
                ->with('error', 'No se encontró ninguna investigación para esta cuenta.');
        }
        
        Log::info('Descargando última investigación', [
            'account_id' => $accountId,
            'generated_id' => $generated->id,
            'name' => $generated->name
        ]);

        $fields = [
            'generated' => $generated->value,
        ];

        $pdf = Pdf::setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView('generated.pdf.template', array_merge($fields));
        
        $now = Carbon::now();
        $timestamp = $now->format('Ymd_His');

        return $pdf->download('Investigacion_' . $generated->name . '_' . $timestamp . '.pdf');
    } catch (\Exception $e) {
        Log::error('Error al descargar investigación', [
            'error' => $e->getMessage(),
            'account_id' => $accountId
        ]);
        
        return redirect()->route('dashboard')
            ->with('error', 'Error al generar el PDF: ' . $e->getMessage());
    }
}
}
