<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class GeminiService
{
    public static function TextOnlyEntry($text, $model="gemini-1.5-flash", $temperature=1.0, $response_mime_type="text/plain")
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent";

            $data = [
                "contents" => [
                    [
                        "parts" => [
                            [
                                "text" => $text
                            ]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "temperature" => $temperature,
                    "response_mime_type"=> $response_mime_type,
                ]
            ];

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-goog-api-key: '.env('GEMINI_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                if (isset($response_data['candidates'])) {
                    $choices = $response_data['candidates'][0]['content']['parts'][0]['text'];
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

    public static function generateContent($text, $files=null, $model="gemini-1.5-flash", $temperature=1.0,$response_modalities=null)
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent";

            $generationConfig = [
                "temperature" => $temperature
            ];

            if ($response_modalities !== null) {
                $generationConfig["responseModalities"] = $response_modalities;
            }

            $parts = [
                [
                    "text" => $text
                ]
            ];

            if ($files !== null) {
                if (!is_array($files)) {
                    $files = [$files];
                }
                
                foreach ($files as $file) {
                    // Verificar si el archivo tiene la estructura correcta
                    if (is_array($file) && isset($file['mime_type']) && isset($file['data'])) {
                        $parts[] = [
                            "inline_data" => [
                                "mime_type" => $file['mime_type'],
                                "data" => $file['data']
                            ]
                        ];
                    } else {
                        // Si es solo una cadena base64, asumimos que es una imagen JPEG
                        $parts[] = [
                            "inline_data" => [
                                "mime_type" => "image/jpeg",
                                "data" => $file
                            ]
                        ];
                    }
                }
            }

            $data = [
                "contents" => [
                    [
                        "parts" => $parts
                    ]
                ],
                "generationConfig" => $generationConfig
            ];

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-goog-api-key: '.env('GEMINI_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $response = curl_exec($ch);
            
            if ($response === false) {
                throw new \Exception('Error en la conexión cURL: ' . curl_error($ch));
            }

            $response_data = json_decode($response, true);
            curl_close($ch);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error al decodificar la respuesta JSON: ' . json_last_error_msg());
            }

            if (isset($response_data['error'])) {
                throw new \Exception('Error de la API Gemini: ' . $response_data['error']['message']);
            }

            if (!isset($response_data['candidates'][0]['content']['parts'][0])) {
                throw new \Exception('Respuesta inesperada de la API');
            }

            return [
                'success' => true,
                'data' => $response_data['candidates'][0]['content']['parts'][0]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public static function generateImage($prompt, $model="imagen-3.0-generate-002", $numberOfImages=4, $aspectRatio="1:1", $personGeneration="ALLOW_ADULT")
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:predict";

            $data = [
                "instances" => [
                    [
                        "prompt" => $prompt
                    ]
                ],
                "parameters" => [
                    //"numberOfImages" => $numberOfImages,
                    "sampleCount" => $numberOfImages,
                    "aspectRatio" => $aspectRatio,
                    "personGeneration" => $personGeneration
                ]
            ];  

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-goog-api-key: '.env('GEMINI_API_KEY_GENERATE_IMAGE')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

            $response = curl_exec($ch);
            curl_close($ch);

            $response_data = json_decode($response, true);

            if (isset($response_data['error'])) {
                throw new \Exception('Error de la API Gemini: ' . $response_data['error']['message']);
            }
            // verificar si existe por lo menos una imagen en la respuesta
            if (!isset($response_data['predictions'][0]['bytesBase64Encoded'])) {
                throw new \Exception('Respuesta inesperada de la API');
            }   
            // devolver todas las imagenes
            return [
                'success' => true,
                'data' => $response_data['predictions']
            ];
            // ejemplo de respuesta
            // {
            //     "success": true,
            //     "data": [
            //         {
            //             "mimeType": "image/png",
            //             "bytesBase64Encoded": "..."
            //         },   
            //         {    
            //             "mimeType": "image/png",
            //             "bytesBase64Encoded": "..."
            //         },
            //         ...
            //     ]
            // }
        } catch (\Exception $e) {   
            return [
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
        }
    }      

    /**
     * # Use curl to send a POST request to the predictLongRunning endpoint
# The request body includes the prompt for video generation
curl "${BASE_URL}/models/veo-2.0-generate-001:predictLongRunning?key=${GOOGLE_API_KEY}" \
  -H "Content-Type: application/json" \
  -X "POST" \
  -d '{
    "instances": [{
        "prompt": "Panning wide shot of a calico kitten sleeping in the sunshine"
      }
    ],
    "parameters": {
      "aspectRatio": "16:9",
      "personGeneration": "dont_allow",
    }
  }' | tee result.json | jq .name | sed 's/"//g' > op_name

  Parámetros del modelo Veo

(Las convenciones de nombres varían según el lenguaje de programación).

    prompt: Es la instrucción de texto del video. Cuando está presente, el parámetro image es opcional.
    image: Es la imagen que se usará como primer fotograma del video. Cuando está presente, el parámetro prompt es opcional.
    negativePrompt: Es una cadena de texto que describe todo lo que deseas que el modelo desaconseje generar.
    aspectRatio: Cambia la relación de aspecto del video generado. Los valores admitidos son "16:9" y "9:16". El valor predeterminado es "16:9".
    personGeneration: Permite que el modelo genere videos de personas. Se admiten los siguientes valores:
        Generación de texto a video:
            "dont_allow": No permite la inclusión de personas ni rostros.
            "allow_adult": Genera videos que incluyan adultos, pero no niños.
        Generación de imágenes a video:
            No se permite. El servidor rechazará la solicitud si se usa el parámetro.
    numberOfVideos: Videos de salida solicitados, ya sea 1 o 2.
    durationSeconds: Es la duración de cada video de salida en segundos, entre 5 y 8.
    enhance_prompt: Habilita o inhabilita el reescribidor de instrucciones. Está habilitado de forma predeterminada.

    ejemplo javascript

    import { GoogleGenAI } from "@google/genai";
import { createWriteStream } from "fs";
import { Readable } from "stream";

const ai = new GoogleGenAI({ apiKey: "GOOGLE_API_KEY" });

async function main() {
  // get image bytes from Imagen, as shown above

  let operation = await ai.models.generateVideos({
    model: "veo-2.0-generate-001",
    prompt: "Panning wide shot of a calico kitten sleeping in the sunshine",
    image: {
      imageBytes: response.generatedImages[0].image.imageBytes, // response from Imagen
      mimeType: "image/png",
    },
    config: {
      aspectRatio: "16:9",
      numberOfVideos: 2,
    },
  });

  while (!operation.done) {
    await new Promise((resolve) => setTimeout(resolve, 10000));
    operation = await ai.operations.getVideosOperation({
      operation: operation,
    });
  }

  operation.response?.generatedVideos?.forEach(async (generatedVideo, n) => {
    const resp = await fetch(
      `${generatedVideo.video?.uri}&key=GOOGLE_API_KEY`, // append your API key
    );
    const writer = createWriteStream(`video${n}.mp4`);
    Readable.fromWeb(resp.body).pipe(writer);
  });
}

main();
     */

    /**
     * Genera un video a partir de un prompt
     * 
     * @param string $prompt Descripción del video a generar
     * @param string $model Modelo a utilizar (por defecto veo-2.0-generate-001)
     * @param string $ratio Relación de aspecto (16:9, 9:16, etc.)
     * @return array Respuesta con el ID de operación
     */
    public static function generateVideo(
        string $prompt,
        string $model = "veo-2.0-generate-001",
        string $ratio = "16:9",
        ?string $imageBase64 = null, // imagen opcional en base64
        int $numberOfVideos = 1,
        int $durationSeconds = 5,
        string $personGeneration = "dont_allow",
        ?string $imageMimeType = null // tipo MIME de la imagen opcional
    ) {
        try {
            $apiKey = env('GEMINI_API_KEY_GENERATE_IMAGE');
            $baseUrl = "https://generativelanguage.googleapis.com/v1beta";
            $url = "{$baseUrl}/models/{$model}:predictLongRunning?key={$apiKey}";
    
            // Registrar si se está enviando imagen
            Log::info('Generando video con Gemini', [
                'conImagen' => $imageBase64 !== null,
                'longitudImagen' => $imageBase64 !== null ? strlen($imageBase64) : 0,
                'ratio' => $ratio
            ]);
    
            // Construir instancia con prompt
            $instance = [
                "prompt" => $prompt
            ];
    
            // Agregar imagen si se proporciona
            if ($imageBase64 !== null) {
                // Usar el tipo MIME proporcionado o detectar automáticamente
                $mimeType = $imageMimeType ?? "image/jpeg"; // Por defecto JPEG
                
                $instance["image"] = [
                    "bytesBase64Encoded" => $imageBase64,
                    "mimeType" => $mimeType
                ];
                Log::info('Agregando imagen a la solicitud de video', [
                    'mimeType' => $mimeType,
                    'imageSize' => strlen($imageBase64)
                ]);
            }
    
            // Construir parámetros adicionales, excluyendo personGeneration y numberOfVideos si hay imagen
            $parameters = [
                "aspectRatio" => $ratio,
                "durationSeconds" => $durationSeconds
            ];
            
            // Solo incluir estos parámetros si NO hay imagen
            if ($imageBase64 === null) {
                $parameters["personGeneration"] = $personGeneration;
                // $parameters["numberOfVideos"] = $numberOfVideos;
                Log::info('Configurando parámetros para solicitud sin imagen');
            } else {
                Log::info('Omitiendo parámetros personGeneration y numberOfVideos por ser solicitud con imagen');
            }
    
            $payload = [
                "instances" => [$instance],
                "parameters" => $parameters
            ];
    
            $data_string = json_encode($payload);
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
    
            $responseBody = json_decode($response, true);
    
            if ($status >= 200 && $status < 300 && isset($responseBody['name'])) {
                return [
                    'success' => true,
                    'operationName' => $responseBody['name'],
                    'operationId' => basename($responseBody['name'])
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseBody['error']['message'] ?? 'Error desconocido',
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
     * Verifica el estado de una operación de generación de video
     * 
     * @param string $operationId ID de la operación
     * @return array Estado de la operación y URL del video si está completo
     */
    // public static function getVideoOperation(string $operationId)
    // {
    //     try {
    //         // dd("llega".$operationId);
    //         $apiKey = env('GEMINI_API_KEY');
    //         $baseUrl = "https://generativelanguage.googleapis.com/v1beta";
            
    //         // URL con API key incluida
    //         $url = "{$baseUrl}/models/veo-2.0-generate-001/operations/{$operationId}?key={$apiKey}";
            
    //         Log::info('Consultando estado de operación de video', [
    //             'operationId' => $operationId,
    //             'url' => $url
    //         ]);
            
    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, $url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
    //         $response = curl_exec($ch);
    //         $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         curl_close($ch);
            
    //         $responseBody = json_decode($response, true);
            
    //         Log::debug('Video operation status', [
    //             'statusCode' => $status,
    //             'response' => $responseBody
    //         ]);
            
    //         if ($status >= 200 && $status < 300) {
    //             // Si la respuesta incluye "done":true, significa que la operación ha finalizado
    //             $done = isset($responseBody['done']) && $responseBody['done'] === true;
                
    //              // Si hay un video generado, agregar la API key a la URL
    //         if ($done && isset($responseBody['response']['generatedVideos'][0]['video']['uri'])) {
    //             // $apiKey = env('GEMINI_API_KEY');
    //             $videoUrl = $responseBody['response']['generatedVideos'][0]['video']['uri'];
    //             dd($videoUrl);
    //             // Log de la URL original
    //             Log::info('URL original del video:', ['url' => $videoUrl]);
    //             if (!str_contains($videoUrl, 'key=')) {
    //                 $videoUrl .= (str_contains($videoUrl, '?') ? '&' : '?') . 'key=' . $apiKey;
    //             }
    //             // Log de la URL modificada
    //             Log::info('URL final del video con API key:', ['url' => $videoUrl]);
    //             $responseBody['response']['generatedVideos'][0]['video']['uri'] = $videoUrl;
    //         }else {
    //             // Log si no hay video disponible
    //             Log::info('Estado de la operación:', [
    //                 'done' => $done,
    //                 'tieneVideo' => isset($responseBody['response']['generatedVideos'][0]['video']['uri']),
    //                 'responseBody' => $responseBody
    //             ]);
    //         }
                
    //             // Devolver la respuesta completa para facilitar el debug
    //             return [
    //                 'success' => true,
    //                 'done' => $done,
    //                 'name' => $responseBody['name'] ?? '',
    //                 'response' => $responseBody
    //             ];
    //         } else {
    //             return [
    //                 'success' => false,
    //                 'error' => $responseBody['error'] ?? 'Error desconocido al verificar el estado del video',
    //                 'statusCode' => $status
    //             ];
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Error verificando estado del video: ' . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         return [
    //             'success' => false,
    //             'error' => $e->getMessage()
    //         ];
    //     }
    // }
    public static function getVideoOperation(string $operationName)
{
    try {
        $apiKey = env('GEMINI_API_KEY_GENERATE_IMAGE');
        
        // El operationName ya viene completo, solo agregamos la API key
        $url = "https://generativelanguage.googleapis.com/v1beta/{$operationName}?key={$apiKey}";
        
        Log::info('Consultando estado de operación de video', [
            'operationName' => $operationName,
            'url' => $url
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $responseBody = json_decode($response, true);
        
        Log::debug('Respuesta completa de la API:', ['response' => $responseBody]);
        
        if ($status >= 200 && $status < 300) {
            $done = isset($responseBody['done']) && $responseBody['done'] === true;
            
            // Si la operación está completa y hay videos generados
            if ($done && isset($responseBody['response']['generateVideoResponse']['generatedSamples'])) {
                $generatedSamples = $responseBody['response']['generateVideoResponse']['generatedSamples'];
                
                // Procesar cada video en la respuesta
                foreach ($generatedSamples as $index => $sample) {
                    if (isset($sample['video']['uri'])) {
                        $videoUrl = $sample['video']['uri'];
                        Log::info("URL original del video {$index}:", ['url' => $videoUrl]);
                        
                        // Agregar API key si no está presente
                        if (!str_contains($videoUrl, 'key=')) {
                            $videoUrl .= (str_contains($videoUrl, '?') ? '&' : '?') . 'key=' . $apiKey;
                        }
                        
                        Log::info("URL final del video {$index} con API key:", ['url' => $videoUrl]);
                        $generatedSamples[$index]['video']['uri'] = $videoUrl;
                    }
                }
                
                // Actualizar la respuesta con las URLs modificadas
                $responseBody['response']['generateVideoResponse']['generatedSamples'] = $generatedSamples;
            }
            
            return [
                'success' => true,
                'done' => $done,
                'name' => $responseBody['name'] ?? '',
                'response' => $responseBody
            ];
        }
        
        return [
            'success' => false,
            'error' => $responseBody['error'] ?? 'Error desconocido al verificar el estado del video',
            'statusCode' => $status
        ];
        
    } catch (\Exception $e) {
        Log::error('Error en getVideoOperation:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
        }
    }
    public static function generateContentImage($prompt, $files = [], $model = "gemini-2.5-flash-image-preview")
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent";
    
            $parts = [
                ["text" => $prompt]
            ];
    
            if (!empty($files)) {
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    if (is_array($file) && isset($file['mime_type']) && isset($file['data'])) {
                        $parts[] = ["inline_data" => ["mime_type" => $file['mime_type'], "data" => $file['data']]];
                    } else {
                        $parts[] = ["inline_data" => ["mime_type" => "image/jpeg", "data" => $file]];
                    }
                }
            }
    
            $data = ["contents" => [["parts" => $parts]]];
            $data_string = json_encode($data);
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'x-goog-api-key: ' . env('GEMINI_API_KEY_GENERATE_IMAGE')
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
            $response = curl_exec($ch);
            if ($response === false) throw new \Exception('Error cURL: ' . curl_error($ch));
            curl_close($ch);
    
            $response_data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) throw new \Exception('Error JSON: ' . json_last_error_msg());
            if (isset($response_data['error'])) throw new \Exception('API Gemini: ' . $response_data['error']['message']);
    
            $images = [];
            foreach ($response_data['candidates'] ?? [] as $candidate) {
                foreach ($candidate['content']['parts'] ?? [] as $part) {
                    if (isset($part['inlineData']['data'])) {
                        $images[] = [
                            'base64' => $part['inlineData']['data'],
                            'mimeType' => $part['inlineData']['mimeType'] ?? 'image/png'
                        ];
                    }
                }
            }
    
            if (empty($images)) throw new \Exception('No se encontró imagen en la respuesta de Gemini.');
    
            return [
                'success' => true,
                'data' => $images
            ];
    
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
        }
    }
    

}