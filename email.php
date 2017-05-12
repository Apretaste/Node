<h1><?php echo $_GET['q']; ?></h1>

<p>Last 100 transactions [<a href="browse/">Browse</a> | <a href="/">Back</a>]</p>

<?php

// get the error logs file
$logfile = $_SERVER['DOCUMENT_ROOT']."/sent/". $_GET['q'];

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
	// highlight errors
	if (strpos($o, 'ERROR') !== false) {
		$o = "<span style='background-color:#F9F2F4; color:#CD3853;'>$o</span>";
	}

	// echo the log line
	echo $o . "<br/><br/>";
}
