<?php

namespace App\Services;

class AnthropicService
{
    public static function TextGeneration($text, $model="claude-3-5-sonnet-20240620", $temperature=1.0, $system_prompt = null)
    {
        try {
            $url = "https://api.anthropic.com/v1/messages";

            $data = [
                "model" => $model,
                "max_tokens" => 8192,
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $text
                    ]
                ],
            ];

            if ($system_prompt) {
                $data['system'] = $system_prompt;
            }

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-api-key: '.env('ANTHROPIC_API_KEY'),
                'anthropic-version: 2023-06-01'
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                if (isset($response_data['content'])) {
                    $choices = $response_data['content'][0]['text'];
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

    
}