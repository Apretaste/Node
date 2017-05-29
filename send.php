<?php

include_once $_SERVER['DOCUMENT_ROOT']."/classes/Utils.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/Email.php";

// get varibles from the config file
$configs = Utils::getConfigs();
$configKey = $configs['general']['key'];

// check if the user has permissions to send
if($configKey != $_POST['key']) die('{"code":"215", "message":"bad authentication"}');

// get variables from the POST
$host = $_POST['host'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$from = $_POST['from'];
$id = empty($_POST['id']) ? "" : $_POST['id'];
$messageid = empty($_POST['messageid']) ? "" : $_POST['messageid'];
$to = $_POST['to'];
$subject = $_POST['subject'];
$body = empty($_POST['body']) ? "" : base64_decode($_POST['body']);
$attachments = empty($_POST['attachments']) ? array() : unserialize($_POST['attachments']);
$images = empty($_POST['images']) ? array() : unserialize($_POST['images']);

// download images to the temp folder
$tmpImags = array();
foreach ($images as $image) {
	$fileName = $_SERVER['DOCUMENT_ROOT']."/temp/".$image->name;
	file_put_contents($fileName, base64_decode($image->content));
	$tmpImags[] = $fileName;
}

// download attachments to the temp folder
$tmpAttachs = array();
foreach ($attachments as $attachment) {
	$fileName = $_SERVER['DOCUMENT_ROOT']."/temp/".$attachment->name;
	file_put_contents($fileName, base64_decode($attachment->content));
	$tmpAttachs[] = $fileName;
}

// Create Email object and send email
try{
	$email = Email::factory($from, $to, $subject, $body, $id, $messageid, $tmpAttachs, $tmpImags);
	$response = $email->send($host, $user, $pass);
} catch (Exception $e) {
	$response = array();
	$response['code'] = "599";
	$response['message'] = $e->getMessage();
	$email->error = $e->getMessage();
}

// save the log
Utils::saveLog($email);

// return response structure
$response['email'] = $email;
echo json_encode($response);
