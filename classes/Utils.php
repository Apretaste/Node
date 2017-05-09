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
	 * Save an email as log
	 */
	public static function saveLog($email)
	{
		// save simple log
		$path = $_SERVER['DOCUMENT_ROOT']."/logs/email.log";
		$today = date("Y-m-d H:i:s");
		file_put_contents($path, "[$today] $email\n", FILE_APPEND);

		// save detailed log
		$path = $_SERVER['DOCUMENT_ROOT']."/sent/".$email->to;
		file_put_contents($path, "[$today] $email\n", FILE_APPEND);
	}
}
