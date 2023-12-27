<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe du managers du Framework
 */
class Managers extends ApplicationComponent {

	/**
	 * Type de base de données exemple: 'PDO'
	 * @var string
	 */
	protected $api;

	/**
	 * Ensemble des instances permettant l'accès aux modèles
	 * @var array
	 */
	protected $managers = array();

	/**
	 * Constructeur de la classe \Library\Managers
	 * @param string $api Type de base de données
	 */
	public function __construct($api) {
		parent::__construct(__CLASS__);
		$this->api = $api;
	}

	/**
	 * Renvoyer l'instance du modèle selon le module demandé
	 * @param string $module Nom du module
	 * @throws \InvalidArgumentException
	 * @return \Library\{nom du modèle}
	 */
	public function getManagerOf($module) {
		if (!is_string($module) || empty($module))
			throw new \Library\ArkiumException('The name of the specified module is invalid');

		if (!isset($this->managers[$module])) {
			$filename = __DIR__ . '/../Applications/' . parent::$name . '/Modules/' . $module . '/' . $module . 'ManagerExtends_' . $this->api . '.class.php';
			if (file_exists($filename)) {
				$manager = 'Applications\\' . parent::$name . '\\Modules\\' . $module . '\\' . $module . 'ManagerExtends_' . $this->api;
			} else {
				$manager = '\\Library\\Models\\' . $module . 'Manager_' . $this->api;
			}
			$this->managers[$module] = new $manager();
		}

		return $this->managers[$module];
	}

}