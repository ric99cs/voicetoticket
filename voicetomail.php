#!/usr/bin/php -q
<?php

require('config.php');
include('functions.php');

$file = $argv[1];
$cid = $argv[2];
$recipient = $argv[3];

$url = Microsoft_Endpoint;
$result = CallAPI('POST',$url,Microsoft_Headers,file_get_contents($file));
$jdecode = json_decode($result);

if ($jdecode->RecognitionStatus == 'Success') {
    $htmlContent = "There is a new voicemail from the caller " . $cid . "<br />The following message was detected:<br><br><strong>" . $jdecode->DisplayText . "</strong><br /><br />The voicemail is attached as audio file.";
} else {
    $htmlContent = "There is a new voicemail from the caller " . $cid . "<br />The message text could not be detected!<br /><br />The voicemail is attached as audio file.";
}

$userid = GetUser($cid);
$jdecode = json_decode($result);
$data = GetBase64($file);
$list[] = array('filename' => basename($file), 'data' => $data, 'mime-type' => 'audio/wav');

if ($userid == 0) {
    $userid = NewUser($cid);
}

$postdata = array(
    "title" => "Neue Nachricht auf dem Anrufbeantworter",
    "customer_id" => $userid,
    "group_id" => 14,
    "article" => array(
        "subject" => "Neue Nachricht",
        "body" => $htmlContent,
        "content_type" => "text/html",
        "type" => "note",
        "internal" => false,
        "attachments" => $list
    )
);
$jsondata = json_encode($postdata);
$final = CallAPI('POST', Zammad_Endpoint.'/tickets',Zammad_Headers, $jsondata);

?>
