<?php 
namespace App\Services;

class FluxService
{
    public static function FillImageFluxPro(
    string $input_image,
    string $mask_image,
    string $prompt,
    int $steps = 50,
    bool $prompt_upsampling = true,
    ?int $seed = null,
    float $guidance = 50.75,
    string $output_format = "jpeg",
    int $safety_tolerance = 2,
    ?string $webhook_url = null,
    ?string $webhook_secret = null
) {
    try {
        $url = "https://api.bfl.ai/v1/flux-pro-1.0-fill";

        $data = [
            "image" => $input_image,
            "mask" => $mask_image,
            "prompt" => $prompt,
            "steps" => $steps,
            "prompt_upsampling" => $prompt_upsampling,
            "guidance" => $guidance,
            "output_format" => $output_format,
            "safety_tolerance" => $safety_tolerance,
        ];

        if (!is_null($seed)) {
            $data["seed"] = $seed;
        }

        if (!empty($webhook_url)) {
            $data["webhook_url"] = $webhook_url;
        }

        if (!empty($webhook_secret)) {
            $data["webhook_secret"] = $webhook_secret;
        }

        $data_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-key: ' . env('FLUXPRO_API_KEY')
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (!isset($response_data['error'])) {
            if (isset($response_data['id'])) {
                return ['data' => $response_data['id']];
            } else {
                return ['error' => $response_data];
            }
        } else {
            return ['error' => $response_data['error']];
        }
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

    public static function ExpandImageFluxPro(
    string $input_image,
    int $top,
    int $bottom,
    int $left,
    int $right,
    string $prompt,
    int $steps = 50,
    bool $prompt_upsampling = false,
    ?int $seed = null,
    float $guidance = 50.75,
    string $output_format = "png",
    int $safety_tolerance = 2,
    ?string $webhook_url = null,
    ?string $webhook_secret = null
) {
    try {
        $url = "https://api.bfl.ai/v1/flux-pro-1.0-expand";

        $data = [
            "image" => $input_image,
            "top" => $top,
            "bottom" => $bottom,
            "left" => $left,
            "right" => $right,
            "prompt" => $prompt,
            "steps" => $steps,
            "prompt_upsampling" => $prompt_upsampling,
            "guidance" => $guidance,
            "output_format" => $output_format,
            "safety_tolerance" => $safety_tolerance,
        ];

        if (!is_null($seed)) {
            $data["seed"] = $seed;
        }

        if (!empty($webhook_url)) {
            $data["webhook_url"] = $webhook_url;
        }

        if (!empty($webhook_secret)) {
            $data["webhook_secret"] = $webhook_secret;
        }

        $data_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-key: ' . env('FLUXPRO_API_KEY')
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (!isset($response_data['error'])) {
            if (isset($response_data['id'])) {
                return ['data' => $response_data['id']];
            } else {
                return ['error' => $response_data];
            }
        } else {
            return ['error' => $response_data['error']];
        }
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

    public static function GenerateImageKontext(
    $modelo= null,   
    $prompt,
    $aspect_ratio = "16:9",
    $input_image = null,
    $prompt_upsampling = false,
    $seed = null,
    $safety_tolerance = 2,
    $output_format = "jpeg",
    $webhook_url = null,
    $webhook_secret = null,
    $additional_images = [] // Nuevo parámetro para múltiples imágenes
) {
    try {
        $url = "https://api.us1.bfl.ai/v1/{$modelo}";

        $data = [
            "prompt" => $prompt,
            "prompt_upsampling" => $prompt_upsampling,
            "aspect_ratio" => $aspect_ratio,
            "safety_tolerance" => $safety_tolerance,
            "output_format" => $output_format,
        ];

        if (!is_null($seed)) {
            $data["seed"] = $seed;
        }

        // Si se proporciona imagen principal, agregarla (compatible hacia atrás)
        if (!empty($input_image)) {
            $data["input_image"] = $input_image;
        }

        // Si se proporcionan imágenes adicionales, agregarlas con campos enumerados
        if (!empty($additional_images) && is_array($additional_images)) {
            foreach ($additional_images as $index => $imageUrl) {
                if ($index === 0) {
                    // La primera imagen adicional va en input_image_2
                    $data["input_image_2"] = $imageUrl;
                } elseif ($index === 1) {
                    $data["input_image_3"] = $imageUrl;
                } elseif ($index === 2) {
                    $data["input_image_4"] = $imageUrl;
                }
                // Máximo 4 imágenes totales (input_image + 3 adicionales)
                if ($index >= 2) break;
            }
        }

        if (!empty($webhook_url)) {
            $data["webhook_url"] = $webhook_url;
        }

        if (!empty($webhook_secret)) {
            $data["webhook_secret"] = $webhook_secret;
        }

        $data_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Key: ' . env('FLUXPRO_API_KEY')
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (!isset($response_data['error'])) {
            if (isset($response_data['id'])) {
                return ['data' => $response_data['id']];
            } else {
                return ['error' => $response_data];
            }
        } else {
            return ['error' => $response_data['error']];
        }
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

    public static function GenerateImageFluxUltra(
    $prompt,
    $aspect_ratio = "16:9",
    $prompt_upsampling = false,
    $seed = null,
    $safety_tolerance = 2,
    $output_format = "png",
    $raw = false,
    $image_prompt = null,
    $image_prompt_strength = 0.1,
    $webhook_url = null,
    $webhook_secret = null
) {
    try {
        $url = "https://api.us1.bfl.ai/v1/flux-pro-1.1-ultra";

        $data = [
            "prompt" => $prompt,
            "prompt_upsampling" => $prompt_upsampling,
            "aspect_ratio" => $aspect_ratio,
            "safety_tolerance" => $safety_tolerance,
            "output_format" => $output_format,
            "raw" => $raw,
            "image_prompt_strength" => $image_prompt_strength,
        ];

        if (!is_null($seed)) {
            $data["seed"] = $seed;
        }

        if (!empty($image_prompt)) {
            $data["image_prompt"] = $image_prompt;
        }

        if (!empty($webhook_url)) {
            $data["webhook_url"] = $webhook_url;
        }

        if (!empty($webhook_secret)) {
            $data["webhook_secret"] = $webhook_secret;
        }

        $data_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Key: ' . env('FLUXPRO_API_KEY')
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (!isset($response_data['error'])) {
            if (isset($response_data['id'])) {
                return ['data' => $response_data['id']];
            } else {
                return ['error' => $response_data];
            }
        } else {
            return ['error' => $response_data['error']];
        }
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

    public static function GenerateImageFlux($prompt, $width=1024, $height=768, $prompt_upsampling=false, $seed=null, $safety_tolerance=2)
    {
        try {
            $url = "https://api.bfl.ml/v1/flux-pro-1.1";

            $data = [
                "prompt" => $prompt,
                "width" => $width,
                "height" => $height,
                "prompt_upsampling" => $prompt_upsampling,
                "safety_tolerance" => $safety_tolerance,
            ];

            if ($seed) {
                $data['seed'] = $seed;
            }

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Key: '.env('FLUXPRO_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                if (isset($response_data['id'])) {
                    $choices = $response_data['id'];
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
 public static function GetResultUltra($generationId)
    {
        try {
            $url = "https://api.us1.bfl.ai/v1/get_result?id=$generationId";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Key: '.env('FLUXPRO_API_KEY')
            ));
            // Establecer el método de solicitud a GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response_data = json_decode($response, true);
            curl_close($ch);

            if(!isset($response_data['error'])){
                if (isset($response_data['status'])) {
                    $status = $response_data['status'];
                    switch ($status) {
                        case 'Ready':
                            return [
                                'status' => 'complete',
                                'data' => $response_data['result']['sample']
                            ];
                        case 'Pending':
                        case 'Request Moderated':
                        case 'Content Moderated':
                            return [
                                'status' => 'pending',
                                'message' => 'La imagen aún se está generando'
                            ];
                        case 'Error':
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
    public static function GetResult($generationId)
    {
        try {
            $url = "https://api.bfl.ml/v1/get_result?id=$generationId";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-Key: '.env('FLUXPRO_API_KEY')
            ));
            // Establecer el método de solicitud a GET
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response_data = json_decode($response, true);
            curl_close($ch);

            if(!isset($response_data['error'])){
                if (isset($response_data['status'])) {
                    $status = $response_data['status'];
                    switch ($status) {
                        case 'Ready':
                            return [
                                'status' => 'complete',
                                'data' => $response_data['result']['sample']
                            ];
                        case 'Pending':
                        case 'Request Moderated':
                        case 'Content Moderated':
                            return [
                                'status' => 'pending',
                                'message' => 'La imagen aún se está generando'
                            ];
                        case 'Error':
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
            $result = self::GetResult($generationId);

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