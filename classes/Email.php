<?php

include $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/Utils.php";

use Nette\Mail\Message;

class Email {
	public $id;
	public $messageid;
	public $from;
	public $to;
	public $subject;
	public $body;
	public $attachments;
	public $images;
	public $created; // auto
	public $error = false; // when sent
	public $sent = false; // when sent

	/**
	 * FACTORY: Create a new email object and the XML file
	 */
	public static function factory($from, $to, $subject, $body, $id, $messageid="", $attachs=array(), $imags=array())
	{
		// create new Email object
		$email = new Email();
		$email->id = $id;
		$email->messageid = $messageid;
		$email->from = $from;
		$email->to = $to;
		$email->subject = $subject;
		$email->body = $body;
		$email->attachments = $attachs;
		$email->images = $imags;
		$email->created = date("Y-m-d H:i:s");

		// return new Email object
		return $email;
	}

	/**
	 * Send an email using Gmail
	 * @return String error message
	 */
	public function send($host, $user, $pass)
	{
		// create mailer
		$mailer = new Nette\Mail\SmtpMailer([
			'host' => $host,
			'username' => $user,
			'password' => $pass,
			'secure' => 'ssl'
		]);

		// create message
		$mail = new Message;
		$mail->setFrom($this->from);
		$mail->addTo($this->to);
		$mail->setSubject($this->subject);
		$mail->setHtmlBody($this->body);
		$mail->setReturnPath($this->from);
		$mail->setHeader('X-Mailer', '');
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
			$this->error = $e->getMessage();
			return array("code"=>"500", "message"=>$this->error);
		}

		// return ok message
		$this->sent = date("Y-m-d H:i:s");
		return array("code"=>"200", "message"=>"Email sent");
	}

	/**
	 * Create the Email as String
	 * @return String error message
	 */
	public function __toString()
	{
		$result = empty($this->error) ? "SENT:{$this->sent}" : "ERROR:{$this->error}";
		return "ID:{$this->id}, FROM:{$this->from}, TO:{$this->to}, SUBJECT:{$this->subject}, MESSAGEID:{$this->messageid}, $result";
	}
}
