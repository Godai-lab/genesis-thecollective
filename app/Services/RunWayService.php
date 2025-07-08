<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

class RunWayService
{
    /**
     * Genera un video usando la API de Runway Gen-3a Turbo
     */
//   public static function generateGen3aTurboVideo(
//     string $promptText,
//     string $model,
//     array $imagesWithPositions, // array de [['uri' => ..., 'position' => 'first'|'last']]
//     string $ratio = "1280:768",
//     int $duration = 5
// ) {
//     try {
//         $apiKey = env('RUNWAY_API_KEY');
//         $url = "https://api.dev.runwayml.com/v1/image_to_video";

//         // Log para depuración
//         Log::info("Iniciando generación de video", [
//             'model' => $model,
//             'images_count' => count($imagesWithPositions),
//             'images_data' => $imagesWithPositions
//         ]);

//         // Validaciones específicas por modelo
//         if ($model === 'gen4_turbo') {
//             // Para gen4_turbo, solo permitir una imagen en posición 'first'
//             if (count($imagesWithPositions) > 1 || 
//                 (count($imagesWithPositions) === 1 && $imagesWithPositions[0]['position'] !== 'first')) {
//                 Log::warning("Validación fallida para gen4_turbo", [
//                     'images_count' => count($imagesWithPositions),
//                     'first_image_position' => $imagesWithPositions[0]['position'] ?? 'none'
//                 ]);
//                 return [
//                     'success' => false,
//                     'error' => 'El modelo gen4_turbo solo acepta una imagen en posición "first".'
//                 ];
//             }
            
//             // Validar ratio específico para gen4_turbo
//             if (!in_array($ratio, ['1280:768', '768:1280', '1024:576', '576:1024'])) {
//                 return [
//                     'success' => false,
//                     'error' => 'Ratio no válido para gen4_turbo.'
//                 ];
//             }
//         } else {
//             // Validaciones para gen3a_turbo
//             if (!in_array($ratio, ['1280:768', '768:1280'])) {
//                 return [
//                     'success' => false,
//                     'error' => 'El ratio no es válido para gen3a_turbo.'
//                 ];
//             }
//         }

//         // Validación de duración
//         if (!in_array($duration, [5, 10])) {
//             return [
//                 'success' => false,
//                 'error' => 'La duración debe ser 5 o 10 segundos.'
//             ];
//         }

//         // Validación de posiciones válidas
//         $validPositions = ['first', 'last'];
//         $promptImage = [];
        
//         // Log antes del procesamiento de imágenes
//         Log::info("Procesando imágenes", [
//             'model' => $model,
//             'images_to_process' => $imagesWithPositions
//         ]);

//         foreach ($imagesWithPositions as $img) {
//             if (!isset($img['uri'], $img['position']) || !in_array($img['position'], $validPositions)) {
//                 Log::warning("Imagen inválida encontrada", [
//                     'image_data' => $img
//                 ]);
//                 return [
//                     'success' => false,
//                     'error' => 'Cada imagen debe tener una URI válida y una posición ("first" o "last").'
//                 ];
//             }
            
//             // Para gen4_turbo, solo incluir la imagen si está en posición 'first'
//             if ($model === 'gen4_turbo' && $img['position'] === 'first') {
//                 $promptImage[] = [
//                     'uri' => $img['uri'],
//                     'position' => $img['position']
//                 ];
//                 Log::info("Imagen agregada para gen4_turbo", [
//                     'position' => $img['position']
//                 ]);
//             } 
//             // Para gen3a_turbo, incluir todas las imágenes válidas
//             else if ($model === 'gen3a_turbo') {
//                 $promptImage[] = [
//                     'uri' => $img['uri'],
//                     'position' => $img['position']
//                 ];
//                 Log::info("Imagen agregada para gen3a_turbo", [
//                     'position' => $img['position']
//                 ]);
//             }
//         }

//         // Log después del procesamiento de imágenes
//         Log::info("Resultado del procesamiento de imágenes", [
//             'promptImage_count' => count($promptImage),
//             'promptImage_data' => $promptImage
//         ]);

//         // Verificar que tengamos al menos una imagen
//         if (empty($promptImage)) {
//             Log::error("No se encontraron imágenes válidas después del procesamiento", [
//                 'model' => $model,
//                 'original_images' => $imagesWithPositions
//             ]);
//             return [
//                 'success' => false,
//                 'error' => 'Se requiere al menos una imagen con posición "first".'
//             ];
//         }

//         $payload = [
//             "promptImage" => $promptImage,
//             "promptText" => $promptText,
//             "ratio" => $ratio,
//             "model" => $model,
//             "duration" => $duration
//         ];

//         // Log del payload final
//         Log::info("Payload final para la API", [
//             'model' => $model,
//             'promptImage_count' => count($promptImage),
//             'ratio' => $ratio
//         ]);

//         $data_string = json_encode($payload);

//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_URL, $url);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, [
//             'Content-Type: application/json',
//             'Authorization: Bearer ' . $apiKey,
//             'X-Runway-Version: 2024-11-06'
//         ]);
//         curl_setopt($ch, CURLOPT_POST, 1);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//         $response = curl_exec($ch);
//         $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         curl_close($ch);

//         $responseBody = json_decode($response, true);

//         if ($status >= 200 && $status < 300) {
//             return [
//                 'success' => true,
//                 'data' => $responseBody
//             ];
//         } else {
//             return [
//                 'success' => false,
//                 'error' => $responseBody['error'] ?? 'Error desconocido',
//                 'statusCode' => $status
//             ];
//         }

//     } catch (\Exception $e) {
//         return [
//             'success' => false,
//             'error' => $e->getMessage()
//         ];
//     }
// }
public static function generateGen3aTurboVideo(
    string $promptText,
    string $model,
    array $imagesWithPositions, // array de [['uri' => ..., 'position' => 'first'|'last']]
    string $ratio = "1280:768",
    int $duration = 5
) {
    try {
        $apiKey = env('RUNWAY_API_KEY');
        $url = "https://api.dev.runwayml.com/v1/image_to_video";

        // Validaciones básicas
        // if (!in_array($ratio, ['1280:768', '768:1280'])) {
        //     return [
        //         'success' => false,
        //         'error' => 'El ratio no es válido para gen3a_turbo.'
        //     ];
        // }

        if (!in_array($duration, [5, 10])) {
            return [
                'success' => false,
                'error' => 'La duración debe ser 5 o 10 segundos.'
            ];
        }

        // Validación de posiciones válidas
        $validPositions = ['first', 'last'];
        $promptImage = [];
        foreach ($imagesWithPositions as $img) {
            if (!isset($img['uri'], $img['position']) || !in_array($img['position'], $validPositions)) {
                return [
                    'success' => false,
                    'error' => 'Cada imagen debe tener una URI válida y una posición ("first" o "last").'
                ];
            }
            $promptImage[] = [
                'uri' => $img['uri'],
                'position' => $img['position']
            ];
        }

        $payload = [
            "promptImage" => $promptImage,
            "promptText" => $promptText,
            "ratio" => $ratio,
            "model" => $model,
            "duration" => $duration
        ];

        $data_string = json_encode($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
            'X-Runway-Version: 2024-11-06'
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
                'data' => $responseBody
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseBody['error'] ?? 'Error desconocido',
                'statusCode' => $status
            ];
        }

    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

    public static function generateVideoFromImage(
    string $promptText,
    array $images, // array de 1 o 2 imágenes: ['data:image/jpeg;base64,...', ...] o URLs
    int $duration = 5,
    string $ratio = "1280:720",
    string $model = "gen4_turbo",
    int $seed = 4294967295
) {
    try {
        $apiKey = env('RUNWAY_API_KEY');
        $url = "https://api.dev.runwayml.com/v1/image_to_video";

        // Construcción del array de imágenes con posición
        $promptImage = [];
        if (count($images) >= 1) {
            $promptImage[] = [
                "uri" => $images[0],
                "position" => "first"
            ];
        }
        if (count($images) === 2) {
            $promptImage[] = [
                "uri" => $images[1],
                "position" => "last"
            ];
        }

        $payload = [
            "promptImage" => $promptImage,
            "seed" => $seed,
            "model" => $model,
            "promptText" => $promptText,
            "duration" => $duration,
            "ratio" => $ratio
        ];

        $data_string = json_encode($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
            'X-Runway-Version: 2024-11-06'
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
                'data' => $responseBody
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseBody['error'] ?? 'Error desconocido',
                'statusCode' => $status
            ];
        }

    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Verifica el estado de generación de un video
 */
public static function checkVideoGenerationStatus($taskId)
{
    try {
        // Verificar que el ID no esté vacío
        if (empty($taskId)) {
            Log::error("ID de tarea vacío en checkVideoGenerationStatus");
            return [
                'success' => false,
                'error' => 'ID de tarea no válido'
            ];
        }
        
        $apiKey = env('RUNWAY_API_KEY');
        
        // Usar la URL correcta para verificar el estado
        $url = "https://api.dev.runwayml.com/v1/tasks/{$taskId}";
        
        Log::info("Consultando estado de tarea con URL: " . $url);
        
        // Configurar la solicitud cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'X-Runway-Version: 2024-11-06'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Ejecutar la solicitud
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Verificar si hubo error en la solicitud
        if ($response === false) {
            $curlError = curl_error($ch);
            Log::error("Error en solicitud cURL: " . $curlError);
            curl_close($ch);
            
            return [
                'success' => false,
                'error' => 'Error en la conexión: ' . $curlError
            ];
        }
        
        curl_close($ch);
        
        // Decodificar la respuesta
        $responseBody = json_decode($response, true);
        
        Log::info("Respuesta de checkVideoGenerationStatus", [
            'statusCode' => $status,
            'body' => $responseBody
        ]);
        
        // Comprobar si la solicitud tuvo éxito
        if ($status >= 200 && $status < 300) {
            return [
                'success' => true,
                'data' => $responseBody
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseBody['error'] ?? 'Error desconocido al verificar estado',
                'statusCode' => $status
            ];
        }
    } catch (\Exception $e) {
        Log::error("Error en checkVideoGenerationStatus: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

}