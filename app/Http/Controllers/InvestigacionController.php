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
        $fuentes = $respuestaper['fuentes'] ?? [];

        // Convertimos las fuentes a una lista numerada con enlaces
       // Convertimos las fuentes a una lista numerada con enlaces en líneas separadas
        $fuentesFormatted = !empty($fuentes)
        ? "<p>" . implode("</p><p>", array_map(fn($fuente, $index) => ($index + 1) . ". <a href=\"$fuente\" target=\"_blank\">$fuente</a>", $fuentes, array_keys($fuentes))) . "</p>"
        : "<p>No se encontraron fuentes.</p>";


        if (!isset($respuestaper['data'])) {
            throw new \Exception('La respuesta no contiene la clave \"data\".');
        }

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
        return response()->json([
            'success' => false,
            'error' => 'Error al generar la investigación: ' . $e->getMessage()
        ], 500);
    }
}

   
    public function callPerplexity($country, $brand, $instruccion){
        

        $prompt = <<<EOT
Conduct a comprehensive advertising and marketing research report on the brand "$brand," operating in "$country," addressing the following user request: "$instruccion." The report should be professionally structured, detailed, and easy to navigate, containing updated and relevant data. The deliverable should include:
                
Executive Summary
                
Background and Market Context
                
Brand Analysis (current market positioning, advertising strategies, and competitive landscape)
                
Key Insights and Recommendations directly responding to the user's specific instruction
                
Ensure the report meets professional advertising research standards similar to those produced by leading agencies such as Kantar.
                
Your response must be formatted strictly in HTML with the following specific format:
1. For main titles, use: `<p><strong>TITLE TEXT</strong></p>`
2. For subtitles, use: `<p><strong>SUBTITLE TEXT</strong></p>`
3. For normal paragraphs, use: `<p>PARAGRAPH TEXT</p>`
4. Instead of using `<ul>` or `<ol>` lists, format enumerations as numbered items in separate paragraphs, using bold for the number:
   - `<p><strong>1.</strong> First item text</p>`
   - `<p><strong>2.</strong> Second item text</p>`
5. Do NOT use `<h1>`, `<h2>`, or any heading tags
6. IMPORTANT: **Absolutely no blank lines, line breaks, or unnecessary spaces between HTML tags. The response should be a single continuous text block without interruptions.**
7. **All text must be enclosed within proper HTML tags with no extra spaces or line breaks between them.**  
EOT;

        
$system_prompt = <<<EOT
You are a specialist researcher in advertising and marketing whose task is to conduct detailed online research and deliver responses in a professional, advertising-research format in Spanish.

Rules:

- Always respond exclusively in Spanish, using a professional research tone.
- Present the final answer strictly in the form of an advertising research report, avoiding any explanation of how the information was obtained.
- Format your response strictly as HTML with this specific structure:
  1. All main titles must be wrapped in `<p><strong>TITLE TEXT</strong></p>` tags.
  2. All subtitles must be wrapped in `<p><strong>SUBTITLE TEXT</strong></p>` tags.
  3. Regular text must use `<p>PARAGRAPH TEXT</p>` tags.
  4. Never use `<h1>`, `<h2>`, or any heading tags.
  5. Instead of using `<ul>` or `<ol>` lists, format enumerations as numbered items in separate paragraphs, using bold for the number:
     - `<p><strong>1.</strong> First item text</p>`
     - `<p><strong>2.</strong> Second item text</p>`
  6. CRITICAL: **Ensure there are NO blank lines, NO line breaks, and NO extra spaces between HTML tags. The response must be a single, continuous text block.**
  7. **Do NOT use `<em></em>` for separation—just maintain proper HTML structure without unnecessary whitespace.**
  8. The final output must be fully structured as valid HTML without markdown syntax, ensuring compatibility with systems requiring clean HTML formatting.

Steps to conduct and deliver the research:

1. Clearly identify the parameters provided by the user: country, category, brand, and the specific request.
2. Conduct thorough online research using the provided parameters to gather updated, detailed, and relevant information.
3. Evaluate the collected data carefully, selecting only the most relevant, updated, and insightful information to accurately fulfill the request.
4. Prepare the deliverable to match the quality and depth expected from professional advertising research, akin to a Kantar-style report.
5. Deliver only the final response in the chosen format, ensuring clarity, depth, and professional structure worthy of industry-leading advertising research standards.
EOT;

        
          

try {
    $model = "sonar-deep-research";
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
