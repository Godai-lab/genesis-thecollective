<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;
class OpenAiService
{
    public static function completions($text, $model="gpt-4-0125-preview", $temperature=1)
    {
        try {
            $url = "https://api.openai.com/v1/chat/completions";

            $prompt = array(
                array(
                    "role" => "user", 
                    "content" => $text
                )
            );

            $data = array(
                'model' => $model,
                'messages' => $prompt,
                'temperature' => $temperature
            );

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY')
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
                    // return $choices;
                    return array('data' => $choices);
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

    public static function CreateThreadAndRun($text, $assistant_id, $model=null, $instructions=null, $tools=null)
    {
        try {
            $url = "https://api.openai.com/v1/threads/runs";

            $thread = array(
                'messages' => array(
                    array(
                        "role" => "user", 
                        "content" => $text
                    )
                )
            );

            $data = array(
                'assistant_id' => $assistant_id,
                'thread' => $thread,
                "stream" => true,
                'model' => $model,
                'instructions' => $instructions,
                'tools' => $tools
            );

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY'),
                'OpenAI-Beta: assistants=v2'
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code >= 200 && $http_code < 300) {

                // Analizar la respuesta para obtener el mensaje completo
                $lines = explode("\n", trim($response));
                $full_message = '';

                foreach ($lines as $line) {
                    if (strpos($line, 'data: ') === 0) {
                        $json_data = substr($line, strlen('data: '));
                        $event_data = json_decode($json_data, true);

                        // Busca el mensaje completo en thread.message.completed
                        if (isset($event_data['object']) && $event_data['object'] === 'thread.message' && $event_data['status'] === 'completed') {
                            $full_message = $event_data['content'][0]['text']['value'];
                            break;
                        }

                         // Manejar errores si hay un fallo en la ejecución del hilo o paso
                        if ((isset($event_data['object']) && $event_data['object'] === 'thread.run' && $event_data['status'] === 'failed') ||
                            (isset($event_data['object']) && $event_data['object'] === 'thread.run.step' && $event_data['status'] === 'failed')) {
                            $error_message = isset($event_data['last_error']) ? $event_data['last_error']['message'] : 'An unknown error occurred';
                            break;
                        }
                    }
                }

                if (!empty($full_message)) {
                    // echo "Mensaje completo: " . $full_message;
                    return array('data' => $full_message);
                } elseif (!empty($error_message)) {
                    // echo "Error: " . $error_message;
                    return array('error' => $error_message);
                } else {
                    return array('error' => $response);
                }
                
            }else{
                $response_data = json_decode($response, true);
                if(isset($response_data['error'])){
                    return array('error' => $response_data['error']['message']);
                    // throw new HttpException(400, $response_data['error']['message']);
                }
            }
            
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
        }
    }

    public static function RetrieveRun($thread_id, $run_id)
    {
        try {
            $url = "https://api.openai.com/v1/threads/$thread_id/runs/$run_id";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY'),
                'OpenAI-Beta: assistants=v2'
            ));
            // Establecer el método de solicitud a GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                // return $response_data;
                return array('data' => $response_data);
            }else{
                return array('error' => $response_data['error']['message']);
                // throw new HttpException(400, $response_data['error']['message']);
            }
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
        }
    }

    public static function ListRunSteps($thread_id, $run_id)
    {
        try {
            $url = "https://api.openai.com/v1/threads/$thread_id/runs/$run_id/steps";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY'),
                'OpenAI-Beta: assistants=v2'
            ));
            // Establecer el método de solicitud a GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                // return $response_data;
                return array('data' => $response_data);
            }else{
                return array('error' => $response_data['error']['message']);
                // throw new HttpException(400, $response_data['error']['message']);
            }
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
        }
    }

    public static function RetrieveMessage($thread_id, $message_id)
    {
        try {
            $url = "https://api.openai.com/v1/threads/$thread_id/messages/$message_id";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY'),
                'OpenAI-Beta: assistants=v2'
            ));
            // Establecer el método de solicitud a GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                $completion = $response_data['content'][0]['text']['value'];
                // return $completion;
                return array('data' => $completion);
            }else{
                return array('error' => $response_data['error']['message']);
                // throw new HttpException(400, $response_data['error']['message']);
            }
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
        }
    }

    public static function ListMessages($thread_id, $params = [])
    {
        try {
            // Parámetros por defecto
            $default_params = [
                'order' => 'asc',
                'limit' => 20
            ];
            
            // Combinar con los parámetros proporcionados
            $params = array_merge($default_params, $params);
            
            // Construir la URL con query params
            $query = http_build_query($params);
            $url = "https://api.openai.com/v1/threads/{$thread_id}/messages?{$query}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY'),
                'OpenAI-Beta: assistants=v2'
            ]);
            // Establecer el método de solicitud a GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            Log::debug('Respuesta de ListMessages', [
                'http_code' => $http_code,
                'params' => $params
            ]);
            
            if ($http_code >= 200 && $http_code < 300) {
                $response_data = json_decode($response, true);
                return ['data' => $response_data];
            } else {
                Log::error('Error en ListMessages', [
                    'http_code' => $http_code,
                    'response' => $response
                ]);
                return ['error' => 'Error al listar mensajes'];
            }
        } catch (\Exception $e) {
            Log::error('Excepción en ListMessages', [
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    public static function CompletionsAssistants($text, $assistant_id)
    {
        try {
            $model = null;
            $instructions=null; 
            $tools=null;
            $CreateThreadAndRun = self::CreateThreadAndRun($text, $assistant_id);
            if(!isset($CreateThreadAndRun['error'])){
                return $CreateThreadAndRun;
            }else{
                return array('error' => $CreateThreadAndRun['error']);
            }
            
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
        }
    }

    public static function CreateImage($prompt, $model="dall-e-3", $size="1024x1024", $response_format="url")
    {
        try {
            $url = "https://api.openai.com/v1/images/generations";

            $data = array(
                'prompt' => $prompt,
                'model' => $model,
                'n' => 1,
                'size' => $size,
                'response_format' => $response_format,
            );

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY_GENERATE_IMAGE')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                return $response_data;
            }else{
                return array('error' => $response_data['error']['message']);
                // throw new HttpException(400, $response_data['error']['message']);
            }
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
        }
    }

    public static function CompletionsAssistantsWithFunctions($text, $assistant_id, $functions_config = [])
    {
        try {
            ini_set('max_execution_time', 500);
            
            // URL para crear un thread y ejecutarlo
            $url = "https://api.openai.com/v1/threads/runs";

            // Preparar el mensaje del usuario
            $thread = [
                'messages' => [
                    [
                        "role" => "user", 
                        "content" => $text
                    ]
                ]
            ];

            // Configuración básica para la ejecución
            $data = [
                'assistant_id' => $assistant_id,
                'thread' => $thread,
            ];
            
            // Convertir a JSON
            $data_string = json_encode($data);

            // Configurar la solicitud cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer '.env('OPENAI_API_KEY'),
                'OpenAI-Beta: assistants=v2'
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Agregar log para depuración
            Log::info('Respuesta inicial de creación de thread', [
                'http_code' => $http_code,
                'response' => $response
            ]);
            
            // Procesar respuesta
            if ($http_code >= 200 && $http_code < 300) {
                $response_data = json_decode($response, true);
                
                if (!isset($response_data['error'])) {
                    // Obtener IDs necesarios para seguimiento
                    $thread_id = $response_data['thread_id'];
                    $run_id = $response_data['id'];
                    
                    Log::info('Thread y Run creados correctamente', [
                        'thread_id' => $thread_id,
                        'run_id' => $run_id
                    ]);
                    
                    // Esperar a que se complete la ejecución
                    $run_status = 'in_progress';
                    $max_attempts = 60; // 5 minutos máximo
                    $attempt = 0;
                    
                    // Seguir comprobando hasta que termine o falle
                    while ($run_status == 'in_progress' || $run_status == 'queued' || $run_status == 'requires_action') {
                        sleep(2); // Esperar 2 segundos entre comprobaciones
                        $attempt++;
                        
                        // Obtener estado actual
                        $run_status_response = self::RetrieveRun($thread_id, $run_id);
                        
                        Log::debug('Comprobación de estado del run', [
                            'attempt' => $attempt,
                            'status_response' => $run_status_response
                        ]);
                        
                        if (isset($run_status_response['data']['status'])) {
                            $run_status = $run_status_response['data']['status'];
                            
                            Log::info('Estado actual del run', [
                                'attempt' => $attempt,
                                'status' => $run_status
                            ]);
                            
                            // Si requiere acción (llamada a función)
                            if ($run_status == 'requires_action') {
                                // Procesar la llamada a función
                                $tool_calls = $run_status_response['data']['required_action']['submit_tool_outputs']['tool_calls'];
                                $tool_outputs = [];
                                
                                foreach ($tool_calls as $tool_call) {
                                    $function_name = $tool_call['function']['name'];
                                    $function_args = json_decode($tool_call['function']['arguments'], true);
                                    
                                    Log::info('Llamada a función detectada', [
                                        'function' => $function_name,
                                        'args' => $function_args
                                    ]);
                                    
                                    // Llamada a la API para ejecutar la función
                                    $function_response = self::executeFunction($function_name, $function_args);
                                    
                                    $tool_outputs[] = [
                                        'tool_call_id' => $tool_call['id'],
                                        'output' => json_encode($function_response)
                                    ];
                                }
                                
                                // Enviar las salidas de las herramientas
                                $submit_url = "https://api.openai.com/v1/threads/{$thread_id}/runs/{$run_id}/submit_tool_outputs";
                                $submit_data = [
                                    'tool_outputs' => $tool_outputs
                                ];
                                
                                $submit_ch = curl_init();
                                curl_setopt($submit_ch, CURLOPT_URL, $submit_url);
                                curl_setopt($submit_ch, CURLOPT_HTTPHEADER, [
                                    'Content-Type: application/json',
                                    'Authorization: Bearer '.env('OPENAI_API_KEY'),
                                    'OpenAI-Beta: assistants=v2'
                                ]);
                                curl_setopt($submit_ch, CURLOPT_POST, 1);
                                curl_setopt($submit_ch, CURLOPT_POSTFIELDS, json_encode($submit_data));
                                curl_setopt($submit_ch, CURLOPT_RETURNTRANSFER, true);
                                
                                $submit_response = curl_exec($submit_ch);
                                $submit_http_code = curl_getinfo($submit_ch, CURLINFO_HTTP_CODE);
                                curl_close($submit_ch);
                                
                                Log::info('Respuesta de envío de herramientas', [
                                    'http_code' => $submit_http_code,
                                    'response' => $submit_response
                                ]);
                                
                                // Continuar con el bucle para verificar el estado actualizado
                                $run_status = 'in_progress';
                            }
                        } else {
                            Log::error('Error al obtener el estado del run', [
                                'response' => $run_status_response
                            ]);
                            return ['error' => 'Error al obtener el estado de la ejecución'];
                        }
                        
                        // Salir si se alcanza el máximo de intentos
                        if ($attempt >= $max_attempts) {
                            Log::warning('Tiempo de espera agotado para el run', [
                                'thread_id' => $thread_id,
                                'run_id' => $run_id,
                                'attempts' => $attempt
                            ]);
                            return ['error' => 'Se agotó el tiempo de espera'];
                        }
                    }
                    
                    // Comprobar si se completó correctamente
                    if ($run_status == 'completed') {
                        Log::info('Run completado, recuperando mensajes', [
                            'thread_id' => $thread_id
                        ]);
                        
                        // Esperar un momento para asegurar que los mensajes estén disponibles
                        sleep(1);
                        
                        // Obtener los mensajes del thread
                        $messages_response = self::ListMessages($thread_id);
                        
                        Log::debug('Respuesta de mensajes', [
                            'messages_response' => $messages_response
                        ]);
                        
                        if (isset($messages_response['data']['data']) && !empty($messages_response['data']['data'])) {
                            // Obtener el mensaje del asistente (debería ser el más reciente)
                            $assistant_messages = array_filter($messages_response['data']['data'], function($msg) {
                                return $msg['role'] === 'assistant';
                            });
                            
                            if (!empty($assistant_messages)) {
                                // Usar el primer mensaje del asistente encontrado
                                $assistant_message = reset($assistant_messages);
                                
                                Log::info('Mensaje del asistente encontrado', [
                                    'message_id' => $assistant_message['id']
                                ]);
                                
                                if (isset($assistant_message['content'][0]['text']['value'])) {
                                    $completion = $assistant_message['content'][0]['text']['value'];
                                    return ['data' => $completion];
                                } else {
                                    Log::warning('Estructura de mensaje inesperada', [
                                        'message' => $assistant_message
                                    ]);
                                    
                                    // Intento alternativo de extraer contenido
                                    if (isset($assistant_message['content']) && is_array($assistant_message['content'])) {
                                        $content_texts = [];
                                        foreach ($assistant_message['content'] as $content_item) {
                                            if (isset($content_item['text']['value'])) {
                                                $content_texts[] = $content_item['text']['value'];
                                            }
                                        }
                                        
                                        if (!empty($content_texts)) {
                                            return ['data' => implode("\n", $content_texts)];
                                        }
                                    }
                                }
                            } else {
                                Log::warning('No se encontraron mensajes del asistente', [
                                    'messages' => $messages_response['data']['data']
                                ]);
                            }
                            
                            // Si llegamos aquí, vamos a intentar obtener cualquier mensaje
                            foreach ($messages_response['data']['data'] as $message) {
                                if (isset($message['content'][0]['text']['value'])) {
                                    Log::info('Recuperando primer mensaje con contenido disponible', [
                                        'role' => $message['role']
                                    ]);
                                    return ['data' => $message['content'][0]['text']['value']];
                                }
                            }
                        }
                        
                        // Último intento: obtener el mensaje directamente
                        try {
                            // A veces el mensaje no está inmediatamente disponible en ListMessages
                            // Intentamos recuperar mensajes individuales
                            $latest_messages = self::ListMessages($thread_id, ['limit' => 1, 'order' => 'desc']);
                            
                            if (isset($latest_messages['data']['data'][0]['id'])) {
                                $latest_message_id = $latest_messages['data']['data'][0]['id'];
                                $message_details = self::RetrieveMessage($thread_id, $latest_message_id);
                                
                                if (isset($message_details['data'])) {
                                    return ['data' => $message_details['data']];
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error('Error en el último intento de recuperar mensaje', [
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        Log::error('No se pudo extraer el contenido del mensaje', [
                            'thread_id' => $thread_id,
                            'run_id' => $run_id
                        ]);
                        
                        return ['error' => 'No se pudo obtener una respuesta válida'];
                    } else {
                        Log::warning('El run finalizó con estado inesperado', [
                            'status' => $run_status
                        ]);
                        return ['error' => "El asistente no pudo completar la tarea. Estado: $run_status"];
                    }
                } else {
                    Log::error('Error en la creación del thread', [
                        'error' => $response_data['error']['message']
                    ]);
                    return ['error' => $response_data['error']['message']];
                }
            } else {
                Log::error('Error HTTP en la creación del thread', [
                    'http_code' => $http_code,
                    'response' => $response
                ]);
                $response_data = json_decode($response, true);
                return ['error' => $response_data['error']['message'] ?? "Error HTTP: $http_code"];
            }
        } catch (\Exception $e) {
            Log::error('Excepción no controlada', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Ejecuta una función externa basada en el nombre y argumentos
     */
    private static function executeFunction($function_name, $function_args)
    {
        try {
            Log::info('Ejecutando función', [
                'function' => $function_name,
                'args' => $function_args
            ]);
            
            switch ($function_name) {
                case 'search_perplexity':
                    // Ejecutar directamente sin hacer llamadas HTTP
                    try {
                        $query = $function_args['query'] ?? '';
                        Log::info('Iniciando búsqueda directa en Perplexity', ['query' => $query]);
                        
                        if (empty($query)) {
                            Log::warning('Consulta vacía');
                            return ['result' => 'La consulta no puede estar vacía'];
                        }
                        
                        // Crear el prompt para la búsqueda
                        $prompt = "Busca información actualizada sobre: $query";
                        $model = "sonar-reasoning";
                        $temperature = 0.7;
                        
                        Log::info('Llamando a PerplexityService con ChatCompletionsChat', [
                            'prompt' => $prompt,
                            'model' => $model
                        ]);
                        
                        // Usar el nuevo método específico para el chat
                        $response = \App\Services\PerplexityService::ChatCompletionsChat($prompt, $model, $temperature);
                        
                        Log::info('Respuesta recibida de PerplexityService', [
                            'response_keys' => array_keys($response)
                        ]);
                        
                        if (!isset($response['data'])) {
                            Log::error('Error en respuesta de Perplexity', [
                                'response' => $response
                            ]);
                            return ['result' => 'Error al buscar información: ' . ($response['error'] ?? 'Error desconocido')];
                        }
                        
                        Log::info('Búsqueda exitosa', [
                            'data_length' => strlen($response['data'])
                        ]);
                        
                        return [
                            'result' => $response['data'],
                            'citations' => $response['citations'] ?? []
                        ];
                        
                    } catch (\Exception $e) {
                        Log::error('Error en búsqueda directa de Perplexity', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        return ['result' => 'Error al buscar información: ' . $e->getMessage()];
                    }
                    
                default:
                    return ['result' => 'Función desconocida: '.$function_name];
            }
        } catch (\Exception $e) {
            Log::error('Error ejecutando función', [
                'function' => $function_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ['result' => 'Error ejecutando la función: '.$e->getMessage()];
        }
    }

   public static function generateImage($prompt, $model = 'gpt-image-1', $size = '1024x1024', $n = 1, $background = null, $output_format = null, $quality = null) {
    try {
        $url = "https://api.openai.com/v1/images/generations";

        $data = [
            'model' => $model,
            'prompt' => $prompt,
            'n' => $n,
            'size' => $size
        ];

        if ($background !== null) {
            $data['background'] = $background;
        }
        if ($output_format !== null) {
            $data['output_format'] = $output_format;
        }
        if ($quality !== null) {
            $data['quality'] = $quality;
        }

        $data_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . env('OPENAI_API_KEY_GENERATE_IMAGE')
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        // Validar que sea un array
        if (!is_array($response_data)) {
            Log::error('Respuesta malformada de OpenAI: ' . $response);
            return ['error' => 'Respuesta malformada de OpenAI'];
        }

        // Verificar si hay error
        if (isset($response_data['error'])) {
            Log::error('Error OpenAI Image Gen:', $response_data);
            return ['error' => $response_data['error']['message']];
        }

        // Verificar si data está correctamente estructurada
        if (isset($response_data['data'][0]['b64_json'])) {
            return [
                'data' => $response_data['data'],
                'created' => $response_data['created'] ?? null,
                'usage' => $response_data['usage'] ?? null
            ];
        } else {
            Log::error('Respuesta de OpenAI no contiene b64_json:', $response_data);
            return ['error' => 'La respuesta de OpenAI no contiene datos de imagen válidos'];
        }

    } catch (\Exception $e) {
        Log::error('Excepción al generar imagen:', ['exception' => $e]);
        return ['error' => 'Excepción al generar imagen: ' . $e->getMessage()];
    }
}
    public static function editImage($prompt, array $imagePaths, $model = 'gpt-image-1', $size = '1024x1024', $background = 'auto', $n = 1)
{
    try {
        $url = "https://api.openai.com/v1/images/edits";

        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;
        $eol = "\r\n";

        $body = '';

        // Parámetros de texto
        $params = [
            'model' => $model,
            'prompt' => $prompt,
            'size' => $size,
            'background' => $background,
            'n' => $n
        ];

        foreach ($params as $key => $value) {
            $body .= "--$delimiter$eol";
            $body .= "Content-Disposition: form-data; name=\"$key\"$eol$eol";
            $body .= "$value$eol";
        }

        // Adjuntar múltiples imágenes
        foreach ($imagePaths as $path) {
            if (file_exists($path)) {
                $filename = basename($path);
                $mimeType = mime_content_type($path);
                $fileContents = file_get_contents($path);

                $body .= "--$delimiter$eol";
                $body .= "Content-Disposition: form-data; name=\"image[]\"; filename=\"$filename\"$eol";
                $body .= "Content-Type: $mimeType$eol$eol";
                $body .= $fileContents . $eol;
            } else {
                return ['error' => "File not found: $path"];
            }
        }

        $body .= "--$delimiter--$eol";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: multipart/form-data; boundary=$delimiter",
            'Authorization: Bearer ' . env('OPENAI_API_KEY_GENERATE_IMAGE')
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

        $response = curl_exec($ch);
        $response_data = json_decode($response, true);
        curl_close($ch);

        if (!isset($response_data['error'])) {
            return [
                'data' => $response_data['data'],
                'created' => $response_data['created'],
            ];
        } else {
            return ['error' => $response_data['error']['message']];
        }
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}


    /**
     * Crea una respuesta del modelo usando el endpoint /v1/responses
     * 
     * @param array $options Opciones para la creación de la respuesta
     * @return array Respuesta con datos o error
     */
    public static function createModelResponse($options = [])
    {
        try {
            $url = "https://api.openai.com/v1/responses";

            // No Configurar opciones por defecto
            $default_options = [
                'background' => 'false',
            ];

            // Combinar opciones por defecto con las proporcionadas
            $data = array_merge($default_options, $options);

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('OPENAI_API_KEY')
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code >= 200 && $http_code < 300) {
                $response_data = json_decode($response, true);
                
                if (!isset($response_data['error'])) {
                    // Si background es true, esperar hasta que la respuesta esté lista
                    // if (isset($data['background']) && $data['background'] === true) {
                    //     return self::waitForBackgroundResponse($response_data['id']);
                    // }
                    return ['success' => true, 'data' => $response_data];
                } else {
                    Log::error('Error en respuesta de OpenAI', [
                        'error' => $response_data['error']
                    ]);
                    return ['success' => false, 'error' => $response_data['error']['message']];
                }
            } else {
                Log::error('Error HTTP en createModelResponse', [
                    'http_code' => $http_code,
                    'response' => $response
                ]);
                
                $response_data = json_decode($response, true);
                return ['success' => false, 'error' => $response_data['error']['message'] ?? "Error HTTP: $http_code"];
            }

        } catch (\Exception $e) {
            Log::error('Excepción en createModelResponse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtiene una respuesta de modelo de OpenAI por ID.
     * Realiza una consulta GET correctamente estructurada.
     *
     * @param string $responseId
     * @return array
     */
    public static function getModelResponse($responseId)
    {
        try {
            $url = "https://api.openai.com/v1/responses/$responseId";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('OPENAI_API_KEY')
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
                    Log::error('Error en respuesta de OpenAI', [
                        'error' => $response_data['error']
                    ]);
                    return ['success' => false, 'error' => $response_data['error']['message']];
                }
            } else {
                Log::error('Error HTTP en getModelResponse', [
                    'http_code' => $http_code,
                    'response' => $response
                ]);

                $response_data = json_decode($response, true);
                return ['success' => false, 'error' => $response_data['error']['message'] ?? "Error HTTP: $http_code"];
            }

        } catch (\Exception $e) {
            Log::error('Excepción en getModelResponse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Espera activamente hasta que una respuesta en background esté lista
     * 
     * @param string $responseId ID de la respuesta
     * @param int $maxWaitTime Tiempo máximo de espera en segundos (por defecto 300 = 5 minutos)
     * @param int $pollInterval Intervalo entre consultas en segundos (por defecto 2)
     * @return array Respuesta con datos o error
     */
    private static function waitForBackgroundResponse($responseId, $maxWaitTime = 600, $pollInterval = 2)
    {
        $startTime = time();
        $url = "https://api.openai.com/v1/responses/$responseId";

        Log::info('Iniciando espera para respuesta en background', [
            'response_id' => $responseId,
            'max_wait_time' => $maxWaitTime,
            'poll_interval' => $pollInterval
        ]);

        while (time() - $startTime < $maxWaitTime) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('OPENAI_API_KEY')
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code >= 200 && $http_code < 300) {
                $response_data = json_decode($response, true);
                
                if (!isset($response_data['error'])) {
                    // Verificar el estado de la respuesta
                    $status = $response_data['status'] ?? 'unknown';

                    // Si la respuesta está lista
                    if ($status === 'completed') {
                        return ['data' => $response_data];
                    }
                    
                    // Si hay un error
                    if ($status === 'failed' || $status === 'cancelled') {
                        Log::error('Respuesta en background falló', [
                            'response_id' => $responseId,
                            'status' => $status,
                            'response' => $response_data
                        ]);
                        return ['error' => "La respuesta falló con estado: $status"];
                    }
                    
                    // Si aún está en proceso, esperar y continuar
                    if (in_array($status, ['queued', 'in_progress'])) {
                        sleep($pollInterval);
                        continue;
                    }
                } else {
                    Log::error('Error al consultar respuesta en background', [
                        'response_id' => $responseId,
                        'error' => $response_data['error']
                    ]);
                    return ['error' => $response_data['error']['message']];
                }
            } else {
                Log::error('Error HTTP al consultar respuesta en background', [
                    'response_id' => $responseId,
                    'http_code' => $http_code,
                    'response' => $response
                ]);
                return ['error' => "Error HTTP: $http_code"];
            }
        }

        // Si se agotó el tiempo de espera
        Log::error('Tiempo de espera agotado para respuesta en background', [
            'response_id' => $responseId,
            'max_wait_time' => $maxWaitTime
        ]);
        return ['error' => 'Tiempo de espera agotado. La respuesta puede estar aún procesándose.'];
    }

 /**
     * 1️⃣ Inicia la generación de video
     * Crea un nuevo trabajo de video en OpenAI usando multipart/form-data.
     * 
     * @param string $prompt Descripción del video a generar
     * @param string $model Modelo a usar (sora-2 o sora-2-pro)
     * @param string $size Tamaño del video (ej: 720x1280)
     * @param string $seconds Duración en segundos
     * @param array|null $imageData Array con ['content', 'mimeType', 'fileName'] para input_reference (ya redimensionada)
     */
    public static function createVideo($prompt, $model = 'sora-2', $size = '720x1280', $seconds = "4", $imageData = null)
    {
        try {
            $url = "https://api.openai.com/v1/videos";

            // Generar boundary para multipart/form-data
            $boundary = uniqid();
            $delimiter = '-------------' . $boundary;
            $eol = "\r\n";

            $body = '';

            // Agregar campos de texto
            $fields = [
                'model' => $model,
                'prompt' => $prompt,
                'size' => $size,
                'seconds' => $seconds
            ];

            foreach ($fields as $key => $value) {
                $body .= "--$delimiter$eol";
                $body .= "Content-Disposition: form-data; name=\"$key\"$eol$eol";
                $body .= "$value$eol";
            }

            // Agregar archivo de imagen si existe (input_reference)
            // La imagen ya viene redimensionada y lista para usar desde el controlador
            if ($imageData && !empty($imageData['content'])) {
                $imageContent = $imageData['content'];
                $mimeType = $imageData['mimeType'] ?? 'image/jpeg';
                $fileName = $imageData['fileName'] ?? 'image.jpg';
                
                $body .= "--$delimiter$eol";
                $body .= "Content-Disposition: form-data; name=\"input_reference\"; filename=\"$fileName\"$eol";
                $body .= "Content-Type: $mimeType$eol$eol";
                $body .= $imageContent . $eol;
                
                Log::info('📷 Agregando input_reference a la solicitud', [
                    'fileName' => $fileName,
                    'mimeType' => $mimeType,
                    'targetSize' => $size,
                    'imageFileSize' => strlen($imageContent),
                    'imageSizeKB' => round(strlen($imageContent) / 1024, 2)
                ]);
            }

            $body .= "--$delimiter--$eol";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: multipart/form-data; boundary=$delimiter",
                'Authorization: Bearer ' . env('OPENAI_API_KEY_GENERATE_VIDEO')
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 180);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('Respuesta de OpenAI createVideo', [
                'http_code' => $http_code,
                'response_length' => strlen($response),
                'model' => $model,
                'withImage' => $imageData !== null
            ]);

            $response_data = json_decode($response, true);

            if (!is_array($response_data)) {
                Log::error('Respuesta malformada al crear video', ['response' => $response]);
                return ['error' => 'Respuesta malformada de OpenAI'];
            }

            if (isset($response_data['error'])) {
                Log::error('Error al crear video OpenAI', $response_data);
                return ['error' => $response_data['error']['message']];
            }

            return $response_data; // Contiene id, status, etc.

        } catch (\Exception $e) {
            Log::error('Excepción al crear video OpenAI', ['exception' => $e]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * 2️⃣ Consulta el estado del video (progreso, status)
     */
    public static function getVideoStatus($videoId)
    {
        try {
            
            ini_set('max_execution_time', 180); 
            
            $url = "https://api.openai.com/v1/videos/{$videoId}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('OPENAI_API_KEY_GENERATE_VIDEO')
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // ✅ Aumentar timeout a 2 minutos
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // ✅ Aumentar timeout de conexión a 30 segundos

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);

            
            if ($response === false) {
                Log::error('Error cURL al consultar estado video', [
                    'videoId' => $videoId,
                    'curl_error' => $curl_error,
                    'curl_errno' => $curl_errno,
                    'http_code' => $http_code
                ]);
                return ['error' => 'Error de conexión: ' . ($curl_error ?: 'Error desconocido de cURL')];
            }

            // ✅ Verificar que la respuesta no esté vacía
            if (empty($response)) {
                Log::error('Respuesta vacía al consultar estado video', [
                    'videoId' => $videoId,
                    'http_code' => $http_code,
                    'curl_error' => $curl_error
                ]);
                return ['error' => 'Respuesta vacía de la API'];
            }

            $data = json_decode($response, true);

            Log::info('Respuesta de OpenAI getVideoStatus', [
                'http_code' => $http_code,
                'videoId' => $videoId,
                'response_length' => strlen($response),
                'status' => $data['status'] ?? 'unknown',
                'hasError' => isset($data['error']),
                'curl_error' => $curl_error
            ]);

            // ✅ Verificar que json_decode fue exitoso
            if ($data === null) {
                Log::error('Error al decodificar JSON del estado video', [
                    'response' => $response,
                    'videoId' => $videoId,
                    'json_error' => json_last_error_msg()
                ]);
                return ['error' => 'Error al decodificar respuesta JSON'];
            }

            if (!is_array($data)) {
                Log::error('Respuesta malformada al obtener estado video', [
                    'response' => $response,
                    'videoId' => $videoId,
                    'data_type' => gettype($data)
                ]);
                return ['error' => 'Respuesta malformada'];
            }

            if (isset($data['error'])) {
                // ✅ Capturar el mensaje de error completo
                $errorMessage = $data['error']['message'] ?? 'Error desconocido';
                
                Log::error('Error al obtener estado video OpenAI', [
                    'error' => $data['error'],
                    'errorMessage' => $errorMessage,
                    'videoId' => $videoId,
                    'fullResponse' => $data
                ]);
                
                return ['error' => $errorMessage];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Error al consultar estado video OpenAI', [
                'exception' => $e->getMessage(),
                'videoId' => $videoId
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * 3️⃣ Obtiene el contenido binario del video final (MP4)
     * Devuelve los bytes del video para que el controlador decida qué hacer (guardar local o subir a S3).
     */
    public static function getVideoContent($videoId, $variant = null)
    {
        try {
            // ✅ Aumentar tiempo de ejecución para descarga de video
            ini_set('max_execution_time', 300);
            
            $url = "https://api.openai.com/v1/videos/{$videoId}/content";
            if ($variant) {
                $url .= "?variant={$variant}";
            }

            Log::info('Solicitando contenido binario del video', [
                'videoId' => $videoId,
                'url' => $url,
                'variant' => $variant
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . env('OPENAI_API_KEY_GENERATE_VIDEO')
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

            $binary = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            Log::info('Respuesta de contenido binario recibida', [
                'videoId' => $videoId,
                'httpCode' => $httpCode,
                'contentType' => $contentType,
                'binarySize' => strlen($binary),
                'binarySizeKB' => round(strlen($binary) / 1024, 2),
                'binarySizeMB' => round(strlen($binary) / 1024 / 1024, 2)
            ]);

            if ($curlError) {
                Log::error('Error CURL al obtener contenido binario', [
                    'videoId' => $videoId,
                    'error' => $curlError
                ]);
                return ['error' => 'Error CURL: ' . $curlError];
            }

            if ($httpCode !== 200) {
                Log::error('Error HTTP al obtener contenido binario', [
                    'videoId' => $videoId,
                    'httpCode' => $httpCode,
                    'responseLength' => strlen($binary)
                ]);
                return ['error' => 'Error HTTP: ' . $httpCode];
            }

            return ['success' => true, 'binary' => $binary];

        } catch (\Exception $e) {
            Log::error('Excepción al obtener contenido del video', [
                'exception' => $e->getMessage(),
                'videoId' => $videoId,
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

}