<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Initialisation de l'application
 * @namespace Library
 * @package Application.class.php
 */
abstract class Application extends ApplicationComponent {

	/**
	 * Constructeur de la classe \Library\Application
	 */
	public function __construct() {
		parent::__construct();
		define('_KERNEL_FILE', true);
		date_default_timezone_set('Europe/Berlin');
		parent::$httpRequest = new HTTPRequest();
		parent::$httpResponse = new HTTPResponse();
		parent::$config = new Config();
		if (AK_CLI) {
			parent::$cli = new Cli();
		} else {
			parent::$user = new User();
			parent::$security = new Security();
		}
	}

	/**
	 * Récupérer le contrôleur du module selon l'URL et renvoie son instance
	 * Espace de nom pour récupérer le contrôleur du module :
	 * \Applications\(Nom de l'application)\Modules\(Nom du module)\(Nom du module)Controller
	 * @param string $cmd URL ou commande de la CLI
	 * @throws ArkiumException
	 * @return null|\Library\BackController
	 */
	public function getController(string $cmd): ?\Library\BackController {

		// A vérifier si utile
		define('_DIRMODULES', getcwd().DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.parent::$name.DIRECTORY_SEPARATOR.'Modules');

		try {
			// Initialisation du Router de l'application
			$router = new \Library\Router(parent::$name);

			// Récupérer la route selon l'URL ou la commande CLI
			parent::$route = $router->getRoute($cmd);

			// Vérifier si l'utilisateur est authentifié sauf pour commande CLI
			if (!AK_CLI) {
				if (!parent::$user->isAuthenticated()) {
					// Vérifier si route définie dans config du Framework pour les utilisateurs non authentifiés
					if (!in_array(parent::$route->action(), explode(",", parent::$config->get('kernelAction')))) {
						// Route pour les utilisateurs non authentifiés
						parent::$route = new Route('', parent::$config->get('loginModule'), parent::$config->get('loginAction'));
					}
				}
			}
			// Vérifier si $route est un objet
			if (!is_object(parent::$route) || empty(parent::$route))
				throw new \Library\ArkiumException('La variable $route doit être un objet valide.');

			// TODO: Ici placer le ctrl access module

			// Renvoyer l'instance de la classe à exécuter
			$controllerClass = 'Applications\\' . parent::$name . '\\Modules\\' . parent::$route->module() . '\\' . parent::$route->module() . 'Controller';
			return new $controllerClass();
		}
		catch (\Library\ArkiumException $e) {
			// Afficher le message si erreur dans l'application
			$e->getMsg();
			return null;
		}
	}

	/**
	 * Exécuter l'application
	 */
	abstract public function run();

}