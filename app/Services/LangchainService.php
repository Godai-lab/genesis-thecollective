<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LangchainService
{
    /**
     * Envía un mensaje al API de Langchain con Flask
     * 
     * @param string $message El mensaje a enviar
     * @param string $model El modelo a utilizar (openai, claude, etc.)
     * @param string|null $session_id ID de la sesión para mantener el contexto
     * @return array Respuesta con datos o error
     */
    public static function chat($message, $model = "openai", $session_id = "")
    {
        try {
            // URL de la API local de Flask
            $url = "http://127.0.0.1:5000/api/v1/chat";

            // Preparar los datos para enviar
            $data = [
                'model' => $model,
                'message' => $message,
                'session_id' => $session_id
            ];

            $data_string = json_encode($data);

            // Inicializar cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Timeout de 2 minutos
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // Timeout de conexión de 30 segundos

            // Ejecutar la petición
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Verificar si hubo error en cURL
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close($ch);
                Log::error('Error de cURL en LangchainService', [
                    'error' => $error_msg
                ]);
                return ['error' => 'Error de conexión: ' . $error_msg];
            }
            
            curl_close($ch);

            // Decodificar la respuesta JSON
            $response_data = json_decode($response, true);

            // Verificar el código HTTP
            if ($http_code >= 200 && $http_code < 300) {
                // Verificar que la respuesta sea exitosa
                if (isset($response_data['success']) && $response_data['success'] === true) {
                    return [
                        'data' => $response_data['response'],
                        'session_id' => $response_data['session_id'] ?? $session_id,
                        'request_id' => $response_data['request_id'] ?? null,
                        'model' => $response_data['model'] ?? $model,
                        'timestamp' => $response_data['timestamp'] ?? null
                    ];
                } else {
                    // Si la API devuelve success: false
                    Log::error('Error en respuesta de Langchain API', [
                        'response' => $response_data
                    ]);
                    return [
                        'error' => $response_data['error'] ?? 'Error desconocido en la API'
                    ];
                }
            } else {
                // Error HTTP
                Log::error('Error HTTP en LangchainService', [
                    'http_code' => $http_code,
                    'response' => $response
                ]);
                return [
                    'error' => $response_data['error'] ?? "Error HTTP: $http_code"
                ];
            }

        } catch (\Exception $e) {
            Log::error('Excepción en LangchainService', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Inicia una nueva conversación (crea un nuevo session_id)
     * 
     * @param string $message El mensaje inicial
     * @param string $model El modelo a utilizar
     * @return array Respuesta con datos o error
     */
    public static function startConversation($message, $model = "openai")
    {
        return self::chat($message, $model, "");
    }

    /**
     * Continúa una conversación existente
     * 
     * @param string $message El mensaje a enviar
     * @param string $session_id ID de la sesión existente
     * @param string $model El modelo a utilizar
     * @return array Respuesta con datos o error
     */
    public static function continueConversation($message, $session_id, $model = "openai")
    {
        return self::chat($message, $model, $session_id);
    }
}

