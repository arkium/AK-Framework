<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe de traitement des requêtes HTML
 */
class HTTPRequest extends ApplicationComponent {

	public function __construct() {
		parent::__construct(__CLASS__);
	}

	/**
	 * Renvoyer la variable de $_COOKIE si la clé $key existe sinon NULL
	 * @param mixed $key
	 * @return null|string
	 */
	public function cookieData($key) {
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
	}

	/**
	 * Renvoyer TRUE si la clé $key existe dans $_COOKIE sinon FALSE
	 * @param string $key
	 * @return bool
	 */
	public function cookieExists($key) {
		return isset($_COOKIE[$key]);
	}

	/**
	 * Renvoyer la variable de $_GET si la clé $key existe sinon NULL
	 * @param string $key
	 * @return null|string
	 */
	public function getData($key) {
		return isset($_GET[$key]) ? (string) $_GET[$key] : null;
	}

	/**
	 * Renvoyer TRUE si la clé $key existe dans $_GET sinon FALSE
	 * @param string $key
	 * @return bool
	 */
	public function getExists($key) {
		return isset($_GET[$key]);
	}

	/**
	 * Ajouter une variable $val à la clé $key dans $_POST
	 * @param string $key
	 * @param null|string|array $val
	 */
	public function postSet($key, $val) {
		$_POST[$key] = $val;
	}

	/**
	 * Renvoyer la variable de $_POST si la clé $key existe sinon NULL
	 * @param string $key
	 * @return null|string|array
	 */
	public function postData($key) {
		return isset($_POST[$key]) ? $_POST[$key] : null;
	}

	/**
	 * Renvoyer TRUE si la clé $key existe dans $_POST sinon FALSE
	 * @param string $key
	 * @return bool
	 */
	public function postExists($key) {
		return isset($_POST[$key]);
	}

	public function requestURI() {
		return $_SERVER['REQUEST_URI'];
	}

	public function getUrlPath() {
		if (dirname($_SERVER['PHP_SELF']) != "/") {
			return str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']);
		} else {
			return $_SERVER['REQUEST_URI'];
		}
	}

	/**
	 * Renvoyer TRUE si la requête HTML est une requête AJAX
	 * @return bool
	 */
	public static function isAJAX() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	public function method() {
		return $_SERVER['REQUEST_METHOD'];
	}

	public static function isGET() {
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}

	public static function isPOST() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
}