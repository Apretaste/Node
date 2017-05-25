<?php

// get varibles from the config file
include_once $_SERVER['DOCUMENT_ROOT']."/classes/Utils.php";
$configs = Utils::getConfigs();
$nodeName = $configs['general']['name'];

// welcome message
echo "<h1>Welcome to $nodeName</h1>";
echo "<p>Last 100 transactions [<a href='browse/'>Browse</a>] | [<a href='test.php'>Test</a>] | [<a href='error.php'>Errors</a>]</p>";

// get the error logs file
$logfile = $_SERVER['DOCUMENT_ROOT']."/logs/email.log";

// tail the log file
$numlines = "100";
$cmd = "tail -$numlines '$logfile'";
$errors = explode('<br />', nl2br(shell_exec($cmd)));

// format output to look better
$output = array();
foreach ($errors as $err)
{
	if(strlen($err) < 5) continue;
	$line = htmlentities($err);
	$line = "<b>".substr_replace($line,"]</b>",strpos($line, "]"),1);
	$output[] = $line;
}

// reverse to show latest first
$output = array_reverse($output);

foreach($output as $o){
	// find all the email addresses
	preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $o, $matches);

	foreach($matches[0] as $match){
		// replace addresses with logs
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/sent/$match")) {
			$o = str_replace($match, "<a href='email.php?q=$match'>$match</a>", $o);
		}

		// highlight errors
		if (strpos($o, 'ERROR') !== false) {
			$o = "<span style='background-color:#F9F2F4; color:#CD3853;'>$o</span>";
		}

		// echo the log line
		echo $o . "<br/><br/>";
	}
}
