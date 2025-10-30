<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Field;
use App\Models\Generated;
use App\Services\OpenAiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Supports\ContentCategory;
class AsistenteCreativoController extends Controller
{
    //
    public function index()
    {
        Gate::authorize('haveaccess','asistentecreativo.index');
        $accounts = Account::fullaccess()->get();

       
        $categories = ContentCategory::all();

        return view('asistenteCreativo.index', compact('accounts', 'categories'));
    }
    public function generarPrompt(Request $request)
    {
        try {
           
            Log::info("🔹 Iniciando generarPrompt", [
                'request' => $request->all()
            ]);
    
            $validator = Validator::make($request->all(), [
                '_token' => 'required',
                'account' => 'required|integer',
                'brief' => 'nullable|required_without:genesis|integer',
                'genesis' => 'nullable|required_without:brief|integer',
                'asistenteCreativoPrompt' => 'required|string',
                'categories' => 'nullable|array',
            ]);
    
            if ($validator->fails()) {
                Log::warning("⚠️ Validación fallida", [
                    'errors' => $validator->errors()
                ]);
                return response()->json(['error' => $validator->errors()]);
            }
    
            ini_set('max_execution_time', 300);
    
            $accountId = $request->input('account');
            $briefID = $request->input('brief');
            $genesisID = $request->input('genesis');
    
            Log::info("📌 Parámetros recibidos", [
                'accountId' => $accountId,
                'briefID' => $briefID,
                'genesisID' => $genesisID
            ]);
    
            if ($briefID && $genesisID) {
                Log::error("❌ Error: Se enviaron brief y genesis al mismo tiempo");
                return response()->json([
                    'error' => 'Solo puedes seleccionar un brief o un genesis, no ambos'
                ]);
            }
    
            $fileGenerated = "";
            if ($briefID) {
                $fileGenerated = Generated::where('id', $briefID)->first()->value;
                Log::info("📄 FileGenerated desde brief", [
                    'value' => $fileGenerated
                ]);
            }
            if ($genesisID) {
                $fileGenerated = Generated::where('id', $genesisID)->first()->value;
                Log::info("📄 FileGenerated desde genesis", [
                    'value' => $fileGenerated
                ]);
            }
    
            $asistenteCreativoPrompt = $request->input('asistenteCreativoPrompt');
            
           
            $categoryVectorStores = ContentCategory::vectorMap();
            
            // Procesar categorías seleccionadas
            $selectedCategories = $request->input('categories', []);
            $vectorIds = [];
            
            if (!empty($selectedCategories)) {
                foreach ($selectedCategories as $category) {
                    if (isset($categoryVectorStores[$category])) {
                        $vectorIds[] = $categoryVectorStores[$category];
                    }
                }
            } else {
                // Si no hay categorías seleccionadas, usar el vector store por defecto
                $vectorIds[] = 'vs_WIikAxBR2wfrELhu6On7ALVt';
            }
            
            Log::info("📂 Vector stores seleccionados", [
                'categories' => $selectedCategories,
                'vectorIds' => $vectorIds,
                'is_default' => empty($selectedCategories)
            ]);
    
            $options = [
                'model' => 'gpt-5',
                'prompt' => [
                    'id' => 'pmpt_68dafbf1014c8195a6f3afca452f954103267248dc0692ae',
                    'variables' => [
                        'asistentecreativoprompt' => $asistenteCreativoPrompt,
                        'filegenerated' => $fileGenerated
                    ]
                ],
                'tools' => [
                    [
                        'type' => 'file_search',
                        'vector_store_ids' => $vectorIds
                    ]
                ],
                'background' => false,
            ];
    
            Log::info("📤 Enviando request a OpenAI", [
                'options' => $options
            ]);
    
            $response = OpenAiService::createModelResponse($options);
    
            Log::info("📥 Respuesta cruda OpenAI", [
                'response' => $response
            ]);
    
            if (isset($response['error'])) {
                throw new \Exception($response['error']);
            }
    
            // Extraer respuesta final
            $textoFinal = '';
            if (isset($response['data']['output']) && is_array($response['data']['output'])) {
                foreach ($response['data']['output'] as $block) {
                    if (
                        ($block['type'] === 'message' || $block['type'] === 'assistant') &&
                        isset($block['content'][0]['text'])
                    ) {
                        $textoFinal = $block['content'][0]['text'];
                        break;
                    }
                }
            }
    
            Log::info("✅ Texto final extraído", [
                'textoFinal' => $textoFinal
            ]);
    
            return response()->json([
                'success' => 'Datos procesados correctamente.',
                'details' => [
                    'data' => $textoFinal
                ],
                'goto' => 3,
                'function' => 'asistenteCreativoGenerate'
            ]);
    
        } catch (\Exception $e) {
            Log::error("❌ Excepción en generarPrompt", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
    
 
    public function generarPromptold(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                '_token' => 'required',
                'account' => 'required|integer',
                'brief' => 'nullable|required_without:genesis|integer',
                'genesis' => 'nullable|required_without:brief|integer',
                'asistenteCreativoPrompt' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }

            ini_set('max_execution_time', 300);

            $accountId = $request->input('account');
            //solo puede llegar uno o brief o genesis
            $briefID = $request->input('brief');
            $genesisID = $request->input('genesis');
            if($briefID && $genesisID){
                return response()->json(['error' => 'Solo puedes seleccionar un brief o un genesis, no ambos']);
            }
            $fileGenerated = "";
            if($briefID){
                $fileGenerated = Generated::where('id',$briefID)->first()->value;
            }
            if($genesisID){
                $fileGenerated = Generated::where('id',$genesisID)->first()->value;
            }

            $asistenteCreativoPrompt = $request->input('asistenteCreativoPrompt');

            $prompt = <<<EOT
Genera las mejores propuestas creativas para realizar lo siguiente: $asistenteCreativoPrompt.
Las propuestas deben ser creativas y estratégicas en base a esta información: 

$fileGenerated
EOT;

            // $assistant_idCreatividad = "asst_TKFWxh6excONKyUMDg9dDKPw";
           $assistant_idCreatividad = "asst_NxsQ9fW4jhAUG3Ba2ugwdzi7";

            $response = OpenAiService::CompletionsAssistants($prompt, $assistant_idCreatividad);

            if (isset($response['error'])) throw new Exception($response['error']);

            return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $response, 'goto' => 3, 'function' => 'asistenteCreativoGenerate']);

        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }
    }
public function guardarGenerado(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer',
            'file_name' => 'required|string',
            'asistenteCreativoGenerateInput' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $generated = Generated::create([
            'account_id' => $request->input('account_id'),
            'key' => 'Asistente Creativo',
            'name' => $request->input('file_name'),
            'value' => $request->input('asistenteCreativoGenerateInput'),
            'rating' => $request->input('rating'),
        ]);

        return response()->json(['success' => 'Datos guardados correctamente.']);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}
    public function download(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|integer',
            'asistenteCreativoGenerateContainer' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $fields = [
            'asistenteCreativoGenerateContainer' => $request->input('asistenteCreativoGenerateContainer'),
        ];

        // Cargar la vista Blade que contiene la plantilla PDF
        $pdf = Pdf::setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView('asistenteCreativo.pdf.template', array_merge($fields));
        
        // Obtén la fecha y hora actual
        $now = Carbon::now();

        // Formatea la fecha y hora como una cadena en el formato deseado (por ejemplo, "YYYYMMDD_HHMMSS")
        $timestamp = $now->format('Ymd_His');

        // Descargar el PDF
        return $pdf->download('AsistenteCreativo_' . $timestamp . '.pdf');

    }

}
