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

sendMailWithAttachment($recipient, $file, 'New Message at your mailbox', $htmlContent);

?>
