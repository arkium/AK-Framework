<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

define('FROM_EMAIL', 'No-Reply Gillet <gillet@arkium.eu>');

/**
 * Gestion des emails
 * @namespace Library
 * @package Email.class.php
 */
class Email extends ApplicationComponent {

	public $destinationEmail = '', $subject = '', $data_replace = array();

	private $message = '', $isSent = false, $isHTML = true, $showFrom = true;

	private $from = 'No-Reply Arkium <no-reply@arkium.eu>';

	private $file;

	private $boundary;

	private $LE = "\r\n";

	public function __construct() {
		parent::__construct(__CLASS__);
		$this->boundary = md5(uniqid(time()));
	}

	public function setFilePathMessage($filepath) {
		// Vérifier si le fichier exsite
		if (!file_exists($filepath))
			throw new \Library\ArkiumException("Le fichier template Email n'est pas accessible : " . $filepath);
		$this->message = file_get_contents($filepath);
	}

	public function getfrom() {
		return $this->from;
	}

	public function setfrom($fromEmail) {
		$this->from = $fromEmail;
	}

	public function getIsHTML() {
		return $this->isHTML;
	}

	public function setIsHTML($isHTML) {
		$this->isHTML = $isHTML;
	}

	public function getShowFrom() {
		return $this->showFrom;
	}

	public function setShowFrom($showFrom) {
		$this->showFrom = $showFrom;
	}

	public function getIsSent() {
		return $this->isSent;
	}

	public function getfile() {
		return $this->file;
	}

	public function setfile($file) {
		$this->file = $file;
	}

	/**
	 * Sets the header with the appropriate settings
	 * @param string $add_headers
	 * @return string
	 */
	private function buildHeader() {
		$header = '';
		$header .= "From: " . $this->from . $this->LE;
		$header .= "MIME-Version: 1.0" . $this->LE;
		$header .= "Content-Type: multipart/mixed; boundary=\"". $this->boundary. "\"" . $this->LE;
		$header .= $this->LE;
		return $header;
	}

	public function sendEmail() {

		// Vérifier si serveur email existant
		if (!$this->checkServer())
			return $this->isSent = false;

		reset($this->data_replace);
		foreach ($this->data_replace as $key => $value){
			$this->subject = str_replace("#$key#", $value, $this->subject);
			$this->message = str_replace("#$key#", $value, $this->message);
		}

		$msg = "Je vous informe que ceci est un message au format MIME 1.0 multipart/mixed." . $this->LE;
		$msg .= "--" . $this->boundary . $this->LE;
		$msg .= "Content-type:text/html; charset=iso-8859-1" . $this->LE;
		$msg .= "Content-Transfer-Encoding: 8bit" . $this->LE;
		$msg .= $this->LE;
		$msg .= $this->message . $this->LE;
		$msg .= $this->LE;

		if ($this->file !== NULL) {
			if (!file_exists($this->file))
				throw new \Library\ArkiumException("La pièce jointe de l'email n'est pas accessible : " . $this->file);

			$fileName = basename($this->file);
			$fileSize = filesize($this->file);
			$handle = fopen($this->file, "r");
			$content = fread($handle, $fileSize);
			fclose($handle);
			$content = chunk_split(base64_encode($content));

			$msg .= "--" . $this->boundary . $this->LE;
			$msg .= "Content-Type: application/octet-stream; name=\"" . $fileName . "\"" . $this->LE;
			$msg .= "Content-Transfer-Encoding: base64" . $this->LE;
			$msg .= "Content-Disposition: attachment; filename=\"" . $fileName . "\"" . $this->LE;
			$msg .= $this->LE;
			$msg .= $content . $this->LE;
			$msg .= $this->LE;
			$msg .= $this->LE;
		}
		$msg .= "--" . $this->boundary . "--" . $this->LE;

		// Envoyer l'email
		if (mail($this->destinationEmail, $this->subject, $msg, $this->buildHeader()))
			$this->isSent = true;
		else
			$this->isSent = false;

		// Renvoyer le résultat de l'envoi
		return $this->isSent;
	}

	/**
	 * Vérifier si serveur email actif et si la fonction "mail" existe
	 * @return bool
	 */
	public function checkServer() {
		$mailOn = ini_get('sendmail_path');
		return (!function_exists("mail") || empty($mailOn)) ? false : true;
	}

}