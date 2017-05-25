<h1>Error log</h1>

<p>Last 100 errors [<a href="/">Back</a>]</p>

<?php

// get log file from the config
include_once $_SERVER['DOCUMENT_ROOT']."/classes/Utils.php";
$configs = Utils::getConfigs();
$logfile = $configs['general']['logfile'];

// tail the log file
$cmd = "tail -100 '$logfile'";
$errors = explode('<br />', nl2br(shell_exec($cmd)));

// reverse to show latest first
$errors = array_reverse($errors);

// format output and print
foreach ($errors as $err)
{
	if(strlen($err) < 5) continue;
	$line = htmlentities($err);
	$line = "<b>".substr_replace($line,"]</b>",strpos($line, "]"),1);

	echo $line . "<br/><br/>";
}
