<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class PerplexityService
{
    public static function ChatCompletions($text, $model="sonar", $temperature=1.0, $system_prompt = null)
    {
        try {
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
                'temperature' => $temperature,
                'max_tokens' => 64000 
            );

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('PERPLEXITY_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                if (isset($response_data['choices'])) {
                    $choices = $response_data['choices'][0]['message']['content'];
                    $citations = $response_data['citations'];
                    // return $choices;
                    return array('data' => $choices, 'citations' => $citations);
                }else{
                    return array('error' => $response_data);
                    // throw new HttpException(400, $response_data);
                }
            }else{
                return array('error' => $response_data['error']['message']);
                // throw new HttpException(400, $response_data['error']['message']);
            }
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
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

    public static function ChatCompletionsAsync($text, $model="sonar", $temperature=1.0, $system_prompt = null)
    {
        // return ['success' => true, 'data' => ["id"=>"e2a4c46c-69c3-4353-bebb-b4a82cbfd7ec"]];
        try {
            $url = "https://api.perplexity.ai/async/chat/completions";

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

            $data = [
                'request' => [
                    'model' => $model,
                    'messages' => $prompt,
                    'temperature' => $temperature
                ]
            ];

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('PERPLEXITY_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);

            curl_close($ch);
            if(!isset($response_data['error'])){
                 //verificar si existe un id en la respuesta
                if(isset($response_data['id'])){
                    return ['success' => true, 'data' => $response_data];
                }else{
                    return ['success' => false, 'error' => $response_data];
                    // throw new HttpException(400, $response_data);
                }
            }else{
                return ['success' => false, 'error' => $response_data['error']['message']];
                // throw new HttpException(400, $response_data['error']['message']);
            }
        }catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
            // throw new HttpException(400, $e->getMessage());
        }
    }

    /* 
    curl --location 'https://api.perplexity.ai/async/chat/completions/{REPLACE_WITH_REQUEST_ID}' \
  --header "Authorization: Bearer <token>"
    */
    public static function ChatCompletionsAsyncGet($requestId)
    {
        try {
            $url = "https://api.perplexity.ai/async/chat/completions/$requestId";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('PERPLEXITY_API_KEY')
            ]);
            // En cURL, una petición GET es el valor por defecto, así que no es necesario establecer CURLOPT_CUSTOMREQUEST ni CURLOPT_HTTPGET explícitamente.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code >= 200 && $http_code < 300) {
                $response_data = json_decode($response, true);

                if (!isset($response_data['error'])) {
                    return ['success' => true, 'data' => $response_data];
                } else {
                    Log::error('Error en respuesta de Perplexity', [
                        'error' => $response_data['error']
                    ]);
                    return ['success' => false, 'error' => $response_data['error']['message']];
                }
            } else {
                Log::error('Error HTTP en ChatCompletionsAsyncGet', [
                    'http_code' => $http_code,
                    'response' => $response
                ]);

                $response_data = json_decode($response, true);
                return ['success' => false, 'error' => $response_data['error']['message'] ?? "Error HTTP: $http_code"];
            }

        } catch (\Exception $e) {
            Log::error('Excepción en ChatCompletionsAsyncGet', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}