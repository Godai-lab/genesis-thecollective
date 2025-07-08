<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Field;
use App\Services\OpenAiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AsistenteInnovacionController extends Controller
{
    public function index()
    {
        Gate::authorize('haveaccess','asistenteinnovacion.index');
        $accounts = Account::fullaccess()->get();

        return view('asistenteInnovacion.index', compact('accounts'));
    }

    public function generarPrompt(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                '_token' => 'required',
                'account' => 'required|integer',
                'asistenteInnovacionPrompt' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }

            ini_set('max_execution_time', 300);

            $accountId = $request->input('account');
            $asistenteInnovacionPrompt = $request->input('asistenteInnovacionPrompt');

            $accountData = Field::where('account_id', $accountId)->pluck('value', 'key');

            $prompt = $asistenteInnovacionPrompt;

            $assistant_idCreatividad = "asst_xKGJYOMzhBlNtQENHgwYFSba";

            $response = OpenAiService::CompletionsAssistants($prompt, $assistant_idCreatividad);

            if (isset($response['error'])) throw new Exception($response['error']);

            return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $response, 'goto' => 3, 'function' => 'asistenteInnovacionGenerate']);

        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }
    }

    public function download(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|integer',
            'asistenteInnovacionGenerateContainer' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $fields = [
            'asistenteInnovacionGenerateContainer' => $request->input('asistenteInnovacionGenerateContainer'),
        ];

        // Cargar la vista Blade que contiene la plantilla PDF
        $pdf = Pdf::setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView('asistenteInnovacion.pdf.template', array_merge($fields));
        
        // Obtén la fecha y hora actual
        $now = Carbon::now();

        // Formatea la fecha y hora como una cadena en el formato deseado (por ejemplo, "YYYYMMDD_HHMMSS")
        $timestamp = $now->format('Ymd_His');

        // Descargar el PDF
        return $pdf->download('asistenteInnovacion_' . $timestamp . '.pdf');

    }
}
