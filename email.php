<?php

	$from = $_POST['from'];
	$to = $_POST['to'];
	$subject = $_POST['subject'];
	$message = $_POST['msg'];
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: $from \r\n";
	$headers .= "Reply-To: $from \r\n";
	$headers .= "Return-Path: $from \r\n";
	$headers .= "X-Mailer: PHP \r\n";
	
	if(@mail($to,$subject,$message,$headers)) 
	{
	echo "true";
    } else {
	echo "false";
    }

?>
