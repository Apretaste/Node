<?php

include $_SERVER['DOCUMENT_ROOT']."vendor/autoload.php";
include_once $_SERVER['DOCUMENT_ROOT']."classes/Utils.php";

use Nette\Mail\Message;

class Email {
	public $id; // auto
	public $messageid;
	public $from; // auto
	public $to;
	public $subject;
	public $body;
	public $attachments;
	public $images;
	public $created; // auto
	public $error; // auto
	public $sent; // when sent
	public $provider; // when sent

	/**
	 * FACTORY: Create a new email object and the XML file
	 */
	public static function newEmail($to, $subject, $body, $messageid="", $attachments=array(), $images=array())
	{
		// get the ID of the email
		$id = date("ymdHis") . rand();

		// get the alias to use to send the email
		$percent = 0;
		$user = str_replace(array(".","+"), "", explode("@", $to)[0]);
		$aliases = Utils::getActiveAliases();
		foreach ($aliases as $alias) {
			$temp = explode("+", $alias)[1];
			similar_text ($temp, $user, $p);
			if($p > $percent) {
				$percent = $p;
				$from = $alias;
			}
		}

		// create new Email object
		$email = new Email();
		$email->id = $id;
		$email->messageid = $messageid;
		$email->from = $from;
		$email->to = $to;
		$email->subject = $subject;
		$email->body = $body;
		$email->attachments = $attachments;
		$email->images = $images;
		$email->created = date("Y-m-d H:i:s");

		// save XML document
		Utils::saveEmailasXML($email);

		// return new Email object
		return $email;
	}

	/**
	 * Send an email using Gmail
	 * @return String error message
	 */
	public function send()
	{
		// path of the XML file must exist
		$pathFrom = $_SERVER['DOCUMENT_ROOT']."mail/outbox/{$this->id}.xml";
		if( ! file_exists($pathFrom)) return "Email file does not exist";

		// get varibles from the config file
		$configs = Utils::getConfigs();
		$limit = $configs['gmail']['limit'];
		$host = $configs['gmail']['host'];
		$username = $configs['gmail']['user'];
		$password = $configs['gmail']['pass'];

		// check limits before sending
		$sentToday = 100; // @TODO
		if($sentToday > $limit) return array("code"=>"215", "message"=>"Daily limit reached", "email"=>$this);

		// get the username and password from
		// create mailer
		$mailer = new Nette\Mail\SmtpMailer([
			'host' => $host,
			'username' => $username,
			'password' => $password,
			'secure' => 'ssl'
		]);

		// create message
		$mail = new Message;
		$mail->setFrom($this->from);
		$mail->addTo($this->to);
		$mail->setSubject($this->subject);
		$mail->setHtmlBody($this->body);
		$mail->setReturnPath($this->from);
		$mail->setHeader('Sender', $this->from);
		$mail->setHeader('In-Reply-To', $this->messageid);
		$mail->setHeader('References', $this->messageid);

		// add images to the template
		foreach ($this->images as $image) {
			$mail->addEmbeddedFile($image);
		}

		// add attachments
		foreach ($this->attachments as $attachment) {
			$mail->addAttachment($attachment);
		}

		// send email
		try{
			$mailer->send($mail, false);
		} catch (Exception $e) {
			// add error to the XML file
			$this->error = $e->getMessage();
			$this->provider = "gmail";
			Utils::saveEmailasXML($this);

			// move to the error folder
			$pathTo = $_SERVER['DOCUMENT_ROOT']."mail/error/{$this->id}.xml";
			rename($pathFrom, $pathTo);

			// return error message
			return array("code"=>"500", "message"=>$this->error, "email"=>$this);
		}

		// update the XML file
		$this->sent = date("Y-m-d H:i:s");
		$this->provider = "gmail";
		Utils::saveEmailasXML($this);

		// move the email to the folder sent
		$pathTo = $_SERVER['DOCUMENT_ROOT']."mail/sent/{$this->id}.xml";
		rename($pathFrom, $pathTo);

		// return ok message
		return array("code"=>"200", "message"=>"Email sent", "email"=>$this);
	}

	/**
	 * Create the Email as String
	 * @return String error message
	 */
	public function __toString()
	{
		$status = empty($sent) ? "WAITING" : (empty($error) ? "SENT" : "ERROR");
		return "ID:{$this->id}, STATUS:$status, FROM:{$this->from}, TO:{$this->to}, SUBJECT:{$this->subject}";
	}
}
