<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\GeminiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AsistenteExperimentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('haveaccess','asistentegrafica.index');
        $accounts = Account::fullaccess()->get();

        return view('asistenteGrafica.experimental', compact('accounts'));
    }

    public function generarExperimental(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                '_token' => 'required',
                'message' => 'required|string',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }

            $prompt = $request->input('message');

            // Inicializar la variable files como null
            $files = null;

            if($request->hasFile('images')){
                // Crear el array files con el formato requerido
                $files = [];
                
                foreach($request->file('images') as $image){
                    // Obtener contenido de la imagen
                    $imageContent = file_get_contents($image->getRealPath());
                    
                    // Convertir a base64
                    $base64Image = base64_encode($imageContent);
                    
                    // Agregar al array files con el formato requerido
                    $files[] = [
                        'mime_type' => $image->getMimeType(),
                        'data' => $base64Image
                    ];
                    
                }
            }

            $model = "gemini-2.0-flash-exp-image-generation";
            $temperature = 1.0;
            $response_modalities = ["Text","Image"];

            $generateContent = GeminiService::generateContent($prompt, $files, $model, $temperature, $response_modalities);

            if (!$generateContent['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $generateContent['error']['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Datos procesados correctamente.',
                'data' => $generateContent['data']
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
