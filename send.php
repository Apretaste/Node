<?php

include_once $_SERVER['DOCUMENT_ROOT']."classes/Utils.php";
include_once $_SERVER['DOCUMENT_ROOT']."classes/Email.php";

// get varibles from the config file
$configs = Utils::getConfigs();
$configKey = $configs['general']['key'];

// check if the user has permissions to send
if($configKey != $_POST['key']) die('{"code":"215", "message":"bad authentication"}');

// get variables from the POST
$messageid = $_POST['messageid'];
$to = $_POST['to'];
$subject = $_POST['subject'];
$body = empty($_POST['body']) ? "" : base64_decode($_POST['body']);
$attachments = empty($_POST['attachments']) ? array() : unserialize($_POST['attachments']);
$images = empty($_POST['images']) ? array() : unserialize($_POST['images']);

// download images to the temp folder
$tempImages = array();
foreach ($images as $image) {
	$fileName = $_SERVER['DOCUMENT_ROOT']."temp/".$image->name;
	file_put_contents($fileName, base64_decode($image->content));
	$tempImages[] = $fileName;
}

// download attachments to the temp folder
$tempAttachments = array();
foreach ($attachments as $attachment) {
	$fileName = $_SERVER['DOCUMENT_ROOT']."temp/".$attachment->name;
	file_put_contents($fileName, base64_decode($attachment->content));
	$tempAttachments[] = $fileName;
}

// Create Email object and send email
$email = Email::newEmail($to, $subject, $body, $messageid, $tempAttachments, $tempImages);
$response = $email->send();

// save the log
Utils::saveLog($response['email']);

// return response structure
echo json_encode($response);
