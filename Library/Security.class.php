<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe \Library\Security du framework héritant de la classe \Library\ApplicationComponent permettant la gestion de la séurité du framework
 */
class Security extends ApplicationComponent {

	/**
	 * Constructeur de la classe \Library\Security
	 */
	public function __construct() {
		parent::__construct(__CLASS__);
		$this->security_injection();
	}

	/**
	 * Générer un token et stocker le en session
	 * @param string $module
	 * @throws ArkiumException
	 * @return string
	 */
	public function generer_token($module = 'Arkium') {
		$time = parent::$config->get('expiretoken');
		if (!is_string($module) || empty($module))
			throw new \Library\ArkiumException($module, 1000);
		if (isset($_SESSION[$module . '_token']) && isset($_SESSION[$module . '_token_time']))
			if ($_SESSION[$module . '_token_time'] >= (time() - $time))
				return $_SESSION[$module . '_token'];
		$token = uniqid(rand(), true);
		$_SESSION[$module . '_token'] = $token;
		$_SESSION[$module . '_token_time'] = time();
		return $token;
	}

	/**
	 * Vérifier le token retourné avec le token stocké dans la session pour le module spécifié
	 * @param string $module
	 * @param int $time
	 * @throws ArkiumException
	 * @return bool
	 */
	public function verifier_token($module = 'Arkium', $time = 0) {
		$time = ($time != 0) ? $time : parent::$config->get('expiretoken');
		if (!is_string($module) || empty($module))
			throw new \Library\ArkiumException($module, 1000);
		if (isset($_SESSION[$module . '_token']) && isset($_SESSION[$module . '_token_time']) && isset($_REQUEST['token']))
			if ($_SESSION[$module . '_token'] == $_REQUEST['token'])
				if ($_SESSION[$module . '_token_time'] >= (time() - $time))
					return true;
		throw new \Library\ArkiumException($module, 1001);
	}

	/**
	 * Enregistrer une chaîne dans un fichier log
	 * @param string $logFile
	 * @param string $content
	 * @param int $fileSizeMax
	 * @return bool
	 */
	public static function log_file($logFile, $content, $fileSizeMax = 2000000) {
		if (!is_writable($logFile))
			return false;
		if (@filesize($logFile) > $fileSizeMax) {
        	$info = new \SplFileInfo($logFile);
			if (copy($logFile, "Log/backup_" . $info->getFilename() . "_" . date("Y_m_d") . ".txt")) {
				unlink($logFile);
			} else {
				return false;
			}
	    }
		$fileHandle = fopen($logFile, "a");
		if (!$fileHandle)
			return false;
		fputs($fileHandle, $content . "\r\n");
		fclose($fileHandle);
		return true;
	}

	/**
	 * Vérification de sécurité contre les attaques
	 * @throws ArkiumException
	 */
	private function security_injection() {
		// Disable Protocole HTTP Attacks
		$this->clean_HTTP_request_variables();
		// Disable DOS Attacks
		if ($_SERVER['HTTP_USER_AGENT'] == '')
			throw new \Library\ArkiumException('Error ! Disable DOS Attacks');
		if ($_SERVER['HTTP_USER_AGENT'] == '-')
			throw new \Library\ArkiumException('Error ! Disable DOS Attacks');
		if (array_key_exists("QUERY_STRING", $_SERVER))
			if (stristr($_SERVER["QUERY_STRING"], '%25'))
				throw new \Library\ArkiumException('Error ! Disable Protocole HTTP Attacks');
		// Posting from other servers in not allowed
		if ($_SERVER["REQUEST_METHOD"] == "POST")
			if (strlen($_SERVER["HTTP_REFERER"]) > 0)
				if (!mb_eregi("(http://$_SERVER[HTTP_HOST])", $_SERVER["HTTP_REFERER"]) && !mb_eregi("(https://$_SERVER[HTTP_HOST])", $_SERVER["HTTP_REFERER"]))
					throw new \Library\ArkiumException('Error ! Posting from another server not allowed!');
	}

	/**
	 * Nettoyage de la variable $_REQUEST
	 */
	private function clean_HTTP_request_variables() {
		if (count($_REQUEST) > 0) {
			foreach ($_REQUEST as $Key => $Val) {
				if (!is_array($_REQUEST[$Key]))
					$_REQUEST[$Key] = $this->CleanInput($Val);
			}
		}
	}

	/**
	 * Nettoyage de la variable
	 * @param string $val
	 * @return string
	 */
	private function CleanInput($val) {
		$allowedtags = "<b></b><i></i><h1></h1><a></a><img><ul></ul><li></li><blockquote></blockquote>";
		$notallowedattribs = array(
				"@javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup@si",
				"#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si",
				"#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si"
		);
		$changexssto = '';
		$val = preg_replace($notallowedattribs, $changexssto, $val);
		$val = strip_tags($val, $allowedtags);
		return nl2br($val);
	}

}