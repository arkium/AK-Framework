<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe de traitement de réponse à la requéte HTTP
 */
class HTTPResponse extends ApplicationComponent {

	/**
	 * Instance de la classe Page pour afficher la page
	 * @var \Library\Page
	 */
	private $page;

	public function __construct() {
		parent::__construct(__CLASS__);
	}

	/**
	 * Retourner les données sous forme de tableau au format JSON
	 * @param array $output
	 */
	public function json($output) {
		header('Content-Type: text/html; charset=utf-8');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();
	}

	/**
	 * Envoyer un en-tête HTTP
	 * @param string $header
	 */
	public function addHeader($header) {
		header($header);
	}

	/**
	 * Rediriger à l'adresse demandée
	 * @param string $location
	 */
	public function redirect($location) {
		header('Location: ' . $location);
		exit();
	}

	/**
	 * Afficher la page définie dans le paramètre page
	 * de la classe \Library\HTTPResponse
	 * TRUE = affiche la page dans le fichier template
	 * @param boolean $template
	 */
	public function send($template = true) {
		exit($this->page->getGeneratedPage($template));
	}

	/**
	 * Assigner l'instance de la classe Page à la propriété Page
	 * @param Page $page
	 */
	public function setPage(\Library\Page $page) {
		$this->page = $page;
	}

	/**
	 * Envoyer un cookie
	 * le dernier argument est par défaut à true
	 * @param string $name
	 * @param string $value
	 * @param int $expire
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 * @param bool $httpOnly
	 */
	public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true) {
		setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}

}