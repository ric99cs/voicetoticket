<?php
	require('config.php');

	function CallAPI($method, $url, $headers, $data = false)
	{
	
	    $curl = curl_init();
	    echo $url;
	    switch ($method) {
        	case "POST":
	            curl_setopt($curl, CURLOPT_POST, 1);
        	    if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	            break;
        	case "PUT":
	            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
	            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	            break;
	        default:
        	    if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
	    }

	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_VERBOSE, 1);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    $result = curl_exec($curl);

	    return $result;
	}

	function sendMailWithAttachment($to, $file, $subject, $body)
	{
	    $from = Mail_From;
	    $fromName = From_Name;
	    $htmlContent = $body;
	    $headers = "From: $fromName" . " <" . $from . ">";

	    $semi_rand = md5(time());
	    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

	    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

	    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        	"Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

	    if (!empty($file) > 0) {
        	if (is_file($file)) {
	            $message .= "--{$mime_boundary}\n";
	            $data = GetBase64($file);
	            $message .= "Content-Type: application/octet-stream; name=\"" . basename($file) . "\"\n" .
        	        "Content-Description: " . basename($file) . "\n" .
                	"Content-Disposition: attachment;\n" . " filename=\"" . basename($file) . "\"; size=" . filesize($file) . ";\n" .
	                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        	}
	    }

	    $message .= "--{$mime_boundary}--";
	    $returnpath = "-f" . $from;

	    // Send email 
	    $mail = @mail($to, $subject, $message, $headers, $returnpath);

	    // Email sending status 
	    echo $mail ? "<h1>Email Sent Successfully!</h1>" : "<h1>Email sending failed.</h1>";
	}

	function GetBase64($file)
	{
	    $filesize = filesize($file);
	    $fp = fopen($file, 'rb');
	    $binary = fread($fp, $filesize);
	    $base64 = chunk_split(base64_encode($binary));
	    fclose($fp);
	    return $base64;
	}

	function NewUser($cid)
	{
	    $data = '{"phone": "' . $cid . '","roles":["Customer"]}';
	    $result = CallAPI('POST', Zammad_Endpoint.'/users', Zammad_Headers, $data);
	    $json = json_decode($result,true);
	    $userid = $json['id'];
	    return $userid;
	}

	function GetUser($cid)
	{
	    $result = CallAPI('GET', Zammad_Endpoint.'/users/search?query=phone%3A' . $cid . '%20OR%20mobile%3A' . $cid, Zammad_Headers);
	    $json = json_decode($result, true);
	    $userid = array();

	    foreach ($json as $key => $user) {
        	$userid[] = array('id' => $user['id'], 'email' => $user['email']);
	    }

	    usort($userid, function ($a, $b) {
        	return $b['id'] <=> $a['id'];
	    });

	    $email = "";
	    $uid = 0;

	    foreach ($userid as $key => $id) {
        	if ($id['email']) {
	            $email = $id['email'];
        	    $uid = $id['id'];
	            break;
        	}
	    }

	    if ($email) {
	        return $uid;
	    } else {
        	return (count($userid) ? $userid[0]['id'] : 0);
	    }
	}

?>
