<?php

namespace App\Services;

class ChatBaseService
{
    public static function completions($text, $chatbot_id, $temperature=1)
    {
        try {
            $url = "https://www.chatbase.co/api/v1/chat";

            $prompt = array(
                array(
                    "role" => "user", 
                    "content" => $text
                )
            );

            $data = array(
                "chatbotId" => $chatbot_id,
                'stream' => true,
                'messages' => $prompt,
                'temperature' => $temperature,
                "model" => "gpt-4",
            );

            $data_string = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.env('CHATBASE_API_KEY')
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response_data = json_decode($response, true);
            curl_close($ch);
            if(!isset($response_data['error'])){
                return $response;
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