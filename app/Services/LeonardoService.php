<?php

namespace App\Services;

class LeonardoService
{
    public static function imageGenerationCreate($prompt, $modelId="6b645e3a-d64f-4341-a6d8-7a3690fbf042", $presetStyle="DYNAMIC", $width=512, $height=512, $num_images=1, $transparency="disabled", $contrast=3.5, $alchemy=true, $enhancePrompt=false, $public=false, $negative_prompt=null, $photoReal=true, $styleUUID="111dc692-d470-4eec-b791-3475abac4c46", $seed=null)
    {
        try {
            $url = "https://cloud.leonardo.ai/api/rest/v1/generations";

            $data = [
                "modelId" => $modelId,
                "prompt" => $prompt,
                "width" => $width,
                "height" => $height,
                "num_images" => $num_images,
                "transparency" => $transparency,
                "presetStyle" => $presetStyle,
                "contrast" => $contrast,
                "alchemy" => $alchemy,
                "enhancePrompt" => $enhancePrompt,
                "public" => $public,
                "negative_prompt"  => $negative_prompt,
                "photoReal" => $photoReal,
                "styleUUID" => $styleUUID,
                "seed" => $seed
            ];

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('LEONARDO_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                if (isset($response_data['sdGenerationJob'])) {
                    $choices = $response_data['sdGenerationJob']['generationId'];
                    // return $choices;
                    return array('data' => $choices);
                }else{
                    return array('error' => $response_data);
                    // throw new HttpException(400, $response_data);
                }
            }else{
                return array('error' => $response_data['error']);
                // throw new HttpException(400, $response_data['error']['message']);
            }
        }catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
        }
    }

    public static function imageGenerationGet($generationId)
    {
        try {
            $url = "https://cloud.leonardo.ai/api/rest/v1/generations/$generationId";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('LEONARDO_API_KEY')
            ));
            // Establecer el método de solicitud a GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response_data = json_decode($response, true);
            curl_close($ch);

            if(!isset($response_data['error'])){
                if (isset($response_data['generations_by_pk'])) {
                    $status = $response_data['generations_by_pk']['status'];
                    switch ($status) {
                        case 'COMPLETE':
                            return [
                                'status' => 'complete',
                                'data' => $response_data['generations_by_pk']['generated_images'][0]['url']
                            ];
                        case 'PENDING':
                            return [
                                'status' => 'pending',
                                'message' => 'La imagen aún se está generando'
                            ];
                        case 'FAILED':
                            return [
                                'status' => 'failed',
                                'message' => 'La generación de la imagen falló'
                            ];
                        default:
                            return [
                                'status' => 'unknown',
                                'message' => 'Estado desconocido: ' . $status
                            ];
                    }
                }else{
                    return array('error' => $response_data);
                }
            }else{
                // return array('error' => $response_data['error']);
                // throw new HttpException(400, $response_data['error']['message']);
                return [
                    'status' => 'error',
                    'error' => $response_data['error']
                ];
            }
        }catch (\Exception $e) {
            // return array('error' => $e->getMessage());
            // throw new HttpException(400, $e->getMessage());
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    public static function waitForImageGeneration($generationId, $timeoutSeconds = 300, $intervalSeconds = 5)
    {
        $startTime = time();

        while (true) {
            $result = self::imageGenerationGet($generationId);

            switch ($result['status']) {
                case 'complete':
                    // La imagen está lista
                    return [
                        'status' => 'success',
                        'imageUrl' => $result['data']
                    ];

                case 'pending':
                    // La imagen aún no está lista, verificamos si hemos excedido el tiempo límite
                    if (time() - $startTime > $timeoutSeconds) {
                        return [
                            'status' => 'timeout',
                            'message' => "La generación de la imagen excedió el tiempo límite de {$timeoutSeconds} segundos."
                        ];
                    }
                    // Esperamos antes de la siguiente verificación
                    sleep($intervalSeconds);
                    break;

                case 'failed':
                    return [
                        'status' => 'failed',
                        'message' => $result['message'] ?? 'La generación de la imagen falló.'
                    ];

                case 'error':
                case 'unknown':
                default:
                    return [
                        'status' => 'error',
                        'message' => $result['message'] ?? 'Ocurrió un error desconocido.'
                    ];
            }
        }
    }
}