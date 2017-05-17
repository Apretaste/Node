<?php if(empty($_POST['to'])) { ?>

<h1>Test sending an email</h1>

<p>Type your email [<a href='index.php'>Back</a>]</p>

<form action="test.php" method="post">
	<h2>Server details</h2>
	<input id="host" name="host" placeholder="host" type="text"/>
	<input id="user" name="user" placeholder="user" type="text"/>
	<input id="pass" name="pass" placeholder="pass" type="text"/>

	<h2>Details of the email</h2>
	<input id="from" name="from" placeholder="from" type="email"/>
	<br/><br/>

	<input id="to" name="to" placeholder="to" type="email"/>
	<br/><br/>

	<input id="subject" name="subject" placeholder="subject" type="text"/>
	<br/><br/>

	<textarea id="body" name="body"  placeholder="body (optional)" rows="10" cols="40"></textarea>
	<br/><br/>

	<input type="submit" value="Send!"/>
</form>

<?php } else {

	// get variables from the POST
	$host = $_POST['host'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];
	$messageid = "";

	include_once $_SERVER['DOCUMENT_ROOT']."/classes/Email.php";

	// Create Email object and send email
	$email = Email::factory($from, $to, $subject, $body, "", "", array(), array());
	$response = $email->send($host, $user, $pass);

	// return response structure
	$response['email'] = $email;
	print_r($response);
} ?>
