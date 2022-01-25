<?php
        define('Zammad_Endpoint', 'your zammad endpoint');
        define('Zammad_Token', 'token=your zammad token');
        define('Microsoft_Endpoint', 'https://westeurope.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1');
        define('Microsoft_Token', 'your microsoft stt token');
        define('Mail_From', 'example@example.com');
        define('From_Name', 'IP-PBX');

        $ms_headers = array(
            'Ocp-Apim-Subscription-Key: '.Microsoft_Token,
            'Content-Type: audio/wav',
            'Content-Type: application/octet-stream'
        );

        $zammad_headers = array(
           'Content-Type: application/json',
           'Accept: application/json',
           'Authorization: Token '.Zammad_Token
        );

        define('Zammad_Headers',$zammad_headers);
        define('Microsoft_Headers',$ms_headers);

?>
