<?php
namespace App\Services;

class LumaService
{
    /**
     * Genera un video con la API de Luma Labs Dream Machine.
     *
     * @param string $prompt Texto del prompt para generar el video.
     * @param string $model Modelo a usar (por defecto: ray-2).
     * @param string $resolution Resolución del video (por defecto: 720p).
     * @param string $duration Duración del video (por defecto: 5s).
     * @return array Retorna un array con 'success' => true|false, y 'data' o 'error'.
     *
     * Ejemplo de respuesta de Luma:
     * {
     *     "id": "c446311f-94f8-497c-b84a-3bb0cebdc895",
     *     "generation_type": "video",
     *     "state": "queued",
     *     "failure_reason": null,
     *     "created_at": "2025-05-16T18:53:39.062856Z",
     *     "assets": null,
     *     "model": "ray-flash-2",
     *     "request": {
     *         "generation_type": "video",
     *         "prompt": "an old lady laughing underwater, wearing a scuba diving suit",
     *         "aspect_ratio": "16:9",
     *         "loop": false,
     *         "keyframes": null,
     *         "callback_url": null,
     *         "model": "ray-flash-2",
     *         "resolution": "720p",
     *         "duration": "5s",
     *         "concepts": null
     *     }
     * }
     */
    public static function generateVideoFromPrompt(
        string $prompt,
        string $aspect_ratio='16:9',
        string $duration = '5s',
        string $model = 'ray-2',
        string $resolution = '720p'
        
    ) {
        try {
            $apiKey = env('LUMA_API_KEY');
            $url = 'https://api.lumalabs.ai/dream-machine/v1/generations';

            $payload = [
                'prompt' => $prompt,
                'aspect_ratio'=>$aspect_ratio,
                'model' => $model,
                'resolution' => $resolution,
                'duration' => $duration,
                
            ];

            $data_string = json_encode($payload);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseBody = json_decode($response, true);

            if ($status >= 200 && $status < 300) {
                return [
                    'success' => true,
                    'data' => $responseBody,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseBody['error'] ?? 'Error desconocido',
                    'statusCode' => $status,
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }


    /**
 * Genera un video con la API de Luma Labs Dream Machine con soporte para keyframes.
 *
 * @param array $params Parámetros para la generación del video.
 * @return array Retorna un array con 'success' => true|false, y 'data' o 'error'.
 */
public static function generateVideoFromPromptWithKeyframes(array $params)
{
    try {
        $apiKey = env('LUMA_API_KEY');
        $url = 'https://api.lumalabs.ai/dream-machine/v1/generations';

        $payload = $params;

        $data_string = json_encode($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseBody = json_decode($response, true);

        if ($status >= 200 && $status < 300) {
            return [
                'success' => true,
                'data' => $responseBody,
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseBody['error'] ?? 'Error desconocido',
                'statusCode' => $status,
            ];
        }

    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
}
        /**
     * Consulta el estado de una generación de video por su ID.
     *
     * @param string $id ID de la generación devuelta por Luma en el POST inicial.
     * @return array Retorna un array con 'success' => true|false, y 'data' o 'error'.
     *
     * Ejemplo de respuesta cuando el video está completado:
     * {
     *     "id": "15b7abb1-3741-4b7e-9615-1f9a16c7442d",
     *     "generation_type": "video",
     *     "state": "completed",
     *     "failure_reason": null,
     *     "created_at": "2025-05-19T13:51:42.302000Z",
     *     "assets": {
     *         "video": "https://.../video.mp4",
     *         "image": "https://.../thumb.jpg",
     *         "progress_video": null
     *     },
     *     "model": "ray-flash-2",
     *     "request": {
     *         "generation_type": "video",
     *         "prompt": "an old lady laughing underwater, wearing a scuba diving suit",
     *         "aspect_ratio": "16:9",
     *         "loop": false,
     *         "keyframes": null,
     *         "callback_url": null,
     *         "model": "ray-flash-2",
     *         "resolution": "720p",
     *         "duration": "5s",
     *         "concepts": null
     *     }
     * }
     */
    public static function getGenerationStatusById(string $id)
    {
        try {
            $apiKey = env('LUMA_API_KEY');
            $url = "https://api.lumalabs.ai/dream-machine/v1/generations/{$id}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Authorization: Bearer ' . $apiKey,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseBody = json_decode($response, true);

            if ($status >= 200 && $status < 300) {
                return [
                    'success' => true,
                    'data' => $responseBody,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseBody['error'] ?? 'Error desconocido',
                    'statusCode' => $status,
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

}
