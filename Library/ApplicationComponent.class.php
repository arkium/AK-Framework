<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Permettant la liaison entre les différentes classes du framework
 * @namespace Library
 * @package ApplicationComponent.class.php
 */
class ApplicationComponent {

	/**
	 * Nom de l'application
	 * @var string
	 */
	protected static $name = '';

	/**
	 * Instance de la classe \Library\HTTPRequest
	 * @var \Library\HTTPRequest
	 */
	protected static $httpRequest;

	/**
	 * Instance de la classe \Library\HTTPResponse
	 * @var \Library\HTTPResponse
	 */
	protected static $httpResponse;

	/**
	 * Instance de la classe \Library\User
	 * @var \Library\User
	 */
	protected static $user;

	/**
	 * Instance de la classe \Library\Config
	 * @var \Library\Config
	 */
	protected static $config;

	/**
	 * Instance de la classe \Library\Security
	 * @var \Library\Security
	 */
	protected static $security;

	/**
	 * Instance de la classe \PDO permmetant la connexion à la base de données créée par la classe \Library\PDOFactory
	 * @var \PDO
	 */
	protected static $dao;

	/**
	 * Instance de la classe \Library\Route permettant de définir les routes de l'application
	 * @var \Library\Route
	 */
	protected static $route;

	/**
	 * Instance de la classe \Library\Cli permettant de gérer la ligne de commande
	 * @var \Library\CLi
	 */
	protected static $cli;

	/**
	 * Tableau des variables de l'application
	 * @var array
	 */
	protected $param = array();

	/**
	 * Liste des classes construites pour le déboguer l'application
	 * @var string
	 */
	protected static $appel = '';

	/**
	 * Constructeur de la classe \Library\ApplicationComponent
	 * @param string $class Nom de la classe
	 */
	public function __construct($class = '') {
		self::$appel .= $class . '<br />';
	}

}