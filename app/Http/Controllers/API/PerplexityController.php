<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PerplexityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerplexityController extends Controller
{
    /**
     * Buscar información en Perplexity
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            
            Log::info('Solicitud de búsqueda recibida', [
                'query' => $query
            ]);
            
            if (empty($query)) {
                Log::warning('Consulta vacía recibida');
                return response()->json(['error' => 'La consulta no puede estar vacía'], 400);
            }
            
            // Crear el prompt para la búsqueda
            $prompt = "Busca información actualizada sobre: $query";
            $model = "sonar-reasoning";
            $temperature = 0.7;
            
            Log::info('Llamando a PerplexityService', [
                'prompt' => $prompt,
                'model' => $model
            ]);
            
            // Realizar la búsqueda con Perplexity
            $response = PerplexityService::ChatCompletionsChat($prompt, $model, $temperature);
            
            Log::info('Respuesta de PerplexityService', [
                'response_keys' => array_keys($response)
            ]);
            
            if (!isset($response['data'])) {
                Log::error('Error en respuesta de Perplexity', [
                    'response' => $response
                ]);
                return response()->json(['error' => 'Error al buscar en Perplexity', 'details' => $response], 500);
            }
            
            // Devolver los resultados
            $result = [
                'result' => $response['data'],
                'citations' => $response['citations'] ?? []
            ];
            
            Log::info('Devolviendo resultados de búsqueda', [
                'result_length' => strlen($result['result'])
            ]);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de Perplexity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error en el servicio de búsqueda', 'details' => $e->getMessage()], 500);
        }
    }
} 