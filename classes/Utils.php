<?php

class Utils {
	/**
	 * Read a configurations file
	 */
	public static function getConfigs()
	{
		return parse_ini_file("config/config.ini", true);
	}

	/**
	 * Get the aliases that can be used
	 */
	public static function getActiveAliases()
	{
		$path = $_SERVER['DOCUMENT_ROOT'].'aliases/active/';
		$aliases = array_diff(scandir($path), array('.', '..'));
		return $aliases;
	}

	/**
	 * Transform an Email object to XML
	 */
	public static function saveEmailasXML($email)
	{
		// get images
		$images = "";
		foreach ($email->images as $image) {
			$images .= "<image>$image</image>";
		}

		// get attachments
		$attachments = "";
		foreach ($email->attachments as $attachmet) {
			$attachments .= "<attachmet>$attachmet</attachmet>";
		}

		// create the XML code
		$xml = '
			<?xml version="1.0" encoding="UTF-8" standalone="no"?>
			<email>
				<id>'.$email->id.'</id>
				<messageid>'.$email->messageid.'</messageid>
				<from>'.$email->from.'</from>
				<to>'.$email->to.'</to>
				<subject><![CDATA['.$email->subject.']]></subject>
				<body><![CDATA['.$email->body.']]></body>
				<attachmens>'.$attachments.'</attachmens>
				<images>'.$images.'</images>
				<created>'.$email->created.'</created>
				<error>'.$email->error.'</error>
				<sent>'.$email->sent.'</sent>
				<provider>'.$email->provider.'</provider>
			</email>';

		// save the XML file
		$path = $_SERVER['DOCUMENT_ROOT']."mail/outbox/{$email->id}.xml";
		file_put_contents($path, $xml);
	}

	/**
	 * Save an email as log
	 */
	public static function saveLog($email)
	{
		$path = $_SERVER['DOCUMENT_ROOT']."logs/email.log";
		$today = date("Y-m-d H:i:s");
		file_put_contents($path, "[$today] $email\n", FILE_APPEND);
	}
}
