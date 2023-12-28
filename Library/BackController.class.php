<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Contrôleur de l'application
 * @namespace Library
 * @package BackController.class.php
 */
abstract class BackController extends ApplicationComponent {

	/**
	 * Instance de la classe \Library\Managers permettant d'accéder à la DB
	 * @var \Library\Managers
	 */
	protected $managers;

	/**
	 * Instance de la classe \Library\Page permettant l'affichage de la page demandée
	 * @var \Library\Page
	 */
    public $page;

	/**
	 * Constructeur de la classe \Library\BackController
	 */
	public function __construct() {
		parent::__construct(__CLASS__);
		parent::$dao = PDOFactory::getMysqlConnexion(parent::$name);
		$this->managers = new \Library\Managers('PDO');
		if (!AK_CLI) {
			$this->page = new \Library\Page();
			$this->setView(parent::$route->action());
		}
	}

	/**
	 * Exécuter l'action demandée en passant les paramètres de l'instance parent::$httpRequest
	 * @throws ArkiumException
	 */
	public function execute() {
		$method = 'execute' . ucfirst(parent::$route->action());
		$toCheck = array(
				$this,
				$method
		);
		try {
			// Vérifier si l'action est définie
			if (!is_callable($toCheck))
				throw new \Library\ArkiumException('"' . parent::$route->action() . '" : Action non définie pour ce module');
			// Exécute l'action
			if (!AK_CLI) {
				$this->$method(parent::$httpRequest);
			} else {
				$this->$method();
			}
		}
		catch (\Library\ArkiumException $e) {
			$e->getMsg();
		}
	}

	/**
	 * Assigner le chemin complet du fichier à afficher dans la propriété $contentFile de la classe Page
	 * @param string $view Nom du fichier
	 * @throws ArkiumException
	 */
	private function setView($view) {
		if (!is_string($view) || empty($view))
			throw new \Library\ArkiumException('The view name must be a valid string');
		$this->page->setContentFile(__DIR__ . '/../Applications/' . parent::$name . '/Modules/' . parent::$route->module() . '/Views/' . $view . '.php');
	}

}