<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class PerplexityService
{
    public static function ChatCompletions($text, $model="sonar", $temperature=1.0, $system_prompt = null)
    {
        try {
            // Verificar que la clave API existe
            if (empty(env('PERPLEXITY_API_KEY'))) {
                Log::error('PerplexityService::ChatCompletions - Falta la clave API');
                return ['error' => 'Falta configurar la clave API de Perplexity'];
            }
            
            $url = "https://api.perplexity.ai/chat/completions";

            $prompt = [];

            // Agregar el prompt del sistema si existe
            if (!empty($system_prompt)) {
                $prompt[] = [
                    "role" => "system",
                    "content" => $system_prompt
                ];
            }

            // Agregar el mensaje del usuario
            $prompt[] = [
                "role" => "user",
                "content" => $text
            ];

            $data = array(
                'model' => $model,
                'messages' => $prompt,
                'temperature' => $temperature
                // 'max_tokens' => 100000 
            );

            $data_string = json_encode($data);
            
            // Log: Request
            Log::info('PerplexityService::ChatCompletions - Enviando solicitud', [
                'model' => $model,
                'temperature' => $temperature,
                'text_length' => strlen($text)
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('PERPLEXITY_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Timeout dinámico según el modelo
            // sonar-deep-research necesita más tiempo porque hace investigación exhaustiva
            $timeout = ($model === 'sonar-deep-research') ? 900 : 300; // 15 min para deep-research, 5 min para otros
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            
            Log::info('PerplexityService::ChatCompletions - Timeout configurado', [
                'timeout_seconds' => $timeout,
                'timeout_minutes' => round($timeout / 60, 1)
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            // Log: Response básico
            Log::info('PerplexityService::ChatCompletions - Respuesta recibida', [
                'http_code' => $http_code,
                'curl_error' => $curl_error ? $curl_error : 'Sin errores',
                'response_length' => strlen($response)
            ]);
            
            // Verificar errores de cURL
            if ($curl_error) {
                Log::error('PerplexityService::ChatCompletions - Error de cURL', [
                    'error' => $curl_error
                ]);
                return ['error' => 'Error de conexión: ' . $curl_error];
            }
            
            // Verificar si la respuesta está vacía
            if (empty($response)) {
                Log::error('PerplexityService::ChatCompletions - Respuesta vacía', [
                    'http_code' => $http_code
                ]);
                return ['error' => 'La API devolvió una respuesta vacía'];
            }

            $response_data = json_decode($response, true);
            
            // Verificar si el JSON es válido
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('PerplexityService::ChatCompletions - Error al decodificar JSON', [
                    'json_error' => json_last_error_msg(),
                    'response_preview' => substr($response, 0, 500)
                ]);
                return ['error' => 'Error al decodificar la respuesta de la API'];
            }
            
            // Log: Response data completo
            Log::info('PerplexityService::ChatCompletions - Respuesta decodificada', [
                'has_choices' => isset($response_data['choices']),
                'has_error' => isset($response_data['error']),
                'http_code' => $http_code
            ]);
            
            // Manejar códigos HTTP de error
            if ($http_code >= 400) {
                $error_msg = 'Error HTTP ' . $http_code;
                if (isset($response_data['error']['message'])) {
                    $error_msg .= ': ' . $response_data['error']['message'];
                } elseif (isset($response_data['error'])) {
                    $error_msg .= ': ' . json_encode($response_data['error']);
                }
                
                Log::error('PerplexityService::ChatCompletions - Error HTTP', [
                    'http_code' => $http_code,
                    'error' => $error_msg,
                    'response_data' => $response_data
                ]);
                
                return ['error' => $error_msg];
            }
            
            // Verificar estructura de respuesta exitosa
            if (isset($response_data['choices'][0]['message']['content'])) {
                $choices = $response_data['choices'][0]['message']['content'];
                $citations = $response_data['citations'] ?? [];
                
                Log::info('PerplexityService::ChatCompletions - Éxito', [
                    'content_length' => strlen($choices),
                    'citations_count' => count($citations)
                ]);
                
                return array('data' => $choices, 'citations' => $citations);
            } else {
                // Respuesta inesperada sin error pero sin data
                Log::error('PerplexityService::ChatCompletions - Estructura inesperada', [
                    'response_data' => $response_data,
                    'http_code' => $http_code
                ]);
                return array('error' => 'Estructura de respuesta inesperada de la API. Por favor, intenta nuevamente.');
            }
            
        }catch (\Exception $e) {
            Log::error('PerplexityService::ChatCompletions - Excepción', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return array('error' => 'Error inesperado: ' . $e->getMessage());
        }
    }
    public static function ChatCompletionsChat($text, $model="sonar-reasoning", $temperature=1)
    {
        try {
            Log::info('PerplexityService: Iniciando llamada', [
                'text_length' => strlen($text),
                'model' => $model
            ]);
            
            // Verificar que la clave API existe
            if (empty(env('PERPLEXITY_API_KEY'))) {
                Log::error('PerplexityService: Falta la clave API');
                return ['error' => 'Falta configurar la clave API de Perplexity'];
            }
            
            $url = "https://api.perplexity.ai/chat/completions";
            
            $data = [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $text]
                ],
                'temperature' => $temperature
            ];
            
            Log::info('PerplexityService: Enviando solicitud', [
                'url' => $url,
                'data' => $data
            ]);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer '.env('PERPLEXITY_API_KEY')
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            Log::info('PerplexityService: Respuesta recibida', [
                'http_code' => $http_code,
                'curl_error' => $curl_error ? $curl_error : 'Sin errores'
            ]);
            
            if ($curl_error) {
                Log::error('PerplexityService: Error de cURL', [
                    'error' => $curl_error
                ]);
                return ['error' => 'Error de conexión: ' . $curl_error];
            }
            
            if ($http_code >= 200 && $http_code < 300) {
                $response_data = json_decode($response, true);
                
                if (isset($response_data['choices'][0]['message']['content'])) {
                    $content = $response_data['choices'][0]['message']['content'];
                    $citations = [];
                    
                    // Extraer citas si existen
                    if (isset($response_data['choices'][0]['message']['context']['citations'])) {
                        $citations = $response_data['choices'][0]['message']['context']['citations'];
                    }
                    
                    Log::info('PerplexityService: Contenido extraído correctamente', [
                        'content_length' => strlen($content),
                        'citations_count' => count($citations)
                    ]);
                    
                    return [
                        'data' => $content,
                        'citations' => $citations
                    ];
                } else {
                    Log::warning('PerplexityService: Estructura de respuesta inesperada', [
                        'response_data' => $response_data
                    ]);
                    return ['error' => 'Estructura de respuesta inesperada'];
                }
            } else {
                Log::error('PerplexityService: Error HTTP', [
                    'http_code' => $http_code,
                    'response' => $response
                ]);
                
                $response_data = json_decode($response, true);
                $error_msg = isset($response_data['error']) ? $response_data['error']['message'] : "Error HTTP: $http_code";
                
                return ['error' => $error_msg];
            }
        } catch (\Exception $e) {
            Log::error('PerplexityService: Excepción no controlada', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => $e->getMessage()];
        }
    }
    
}