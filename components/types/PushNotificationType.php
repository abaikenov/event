<?php namespace app\types\components; 
                class PushNotificationType extends NotificationType
                {
                    function doExecute()
                    {
                        
                        curl_setopt_array($ch = curl_init(), array(
  CURLOPT_URL => "https://api.jeapie.com/v1/send/message.json",
  CURLOPT_POSTFIELDS => array(
    "token" => "APP_TOKEN",
    "user" => "USER_KEY",
    "message" => "Hello World",
  )));
curl_exec($ch);
curl_close($ch);
                    }
            }