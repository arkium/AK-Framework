<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Gestion des erreurs
 * @namespace Library
 * @package ArkiumException.class.php
 */
class ArkiumException extends \Exception {

	/**
	 * Constructeur de la classe \Library\ArkiumException
	 * @param string $message Le message de l'exception
	 * @param int $code Le code de l'exception
	 * @param \Exception $previous
	 */
	public function __construct($message, $code = 0, \Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Représenter l'exception sous la forme d'une chaîne
	 * @return string
	 */
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

	/**
	 * Renvoyer TRUE si erreur via requête AJAX sinon FALSE
	 * @return bool
	 */
	private static function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

	/**
	 * Afficher l'erreur en HTML ou si commande AJAX,
	 * renvoyer l'erreur sous format de fichier JSON
	 */
	public function getMsg() {
		$content = date("Y/m/d H-i-s") . " - " . $this->code . ' ' . $this->message . ' - ' . $this->file . ' ' . $this->line;
		Security::log_file('Log/error.txt', $content);

		if (!static::is_ajax()) {
			$tmpl_file = __DIR__ . '/../Ressources/error/error.html';
			if (is_file($tmpl_file) && (!AK_CLI)) {
				// Affiche le message d'erreur en HTML
				$thefile = implode('', file($tmpl_file));
				$thefile = "\$r_file=\"" . addslashes($thefile) . "\";";
				eval($thefile);
				print $r_file;
				exit();
			} else {
				// Affiche le message d'erreur en CLI
				echo "\nERREUR\n  " . $this->message . "\n";
			}
		} else {
			// Affiche le message d'erreur en JSON
			$output = $this->ArkiumMessage();
			header('Content-Type: text/html; charset=utf-8');
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-Type: application/json');
			echo json_encode($output);
			exit();
		}

	}

	/**
	 * Gérer le message de l'erreur JSON
	 * @return array
	 */
	private function ArkiumMessage(){
		$sEcho = (isset($_REQUEST['sEcho'])) ? (int) $_REQUEST['sEcho'] : '';
		$output = array(
				"sEcho" => $sEcho,
				"iTotalRecords" => 0,
				"iTotalDisplayRecords" => 0,
				"aaData" => array(),
				"reponse" => 'Things do not go well, see the following errors:<br />' . $this->message . '<br />',
				"status" => 'danger'
		);
		switch($this->code) {
			case '1000':
				$output['reponse'] = 'Token: The name of the specified module ' . $this->message . ' is invalid';
				break;
			case '1001':
				$output['reponse'] = 'The security token is incorrect : ' . $this->message . '_token<br/>Thank you to refresh the page by <b>pressing F5</b> on the keyboard.';
				$output['goLogout'] = true;
				break;
		}
		return $output;
	}
}