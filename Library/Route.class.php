<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Définition d'un route
 * @namespace Library
 * @package Router.class.php
 */
class Route {

	/**
	 * Commande de la route
	 * @var string
	 */
	private $cmd;

	/**
	 * Module de la route
	 * @var string
	 */
	private $module;

	/**
	 * Action de la route
	 * @var string
	 */
	private $action;

	/**
	 * Initialisation des variables de la route
	 * @param string $cmd Commande de la route
	 * @param string $module Nom du module
	 * @param string $action Nom de l'action
	 */
	public function __construct(string $cmd, string $module, string $action) {
		$this->cmd = $cmd;
		$this->module = $module;
		$this->action = $action;
	}

	/**
	 * Trouver la route de la commande URL ou CLI
	 * @param string $cmd Commande URL ou CLI
	 * @return null|array
	 */
	public function match($cmd): ?array{
		if (preg_match($this->cmd, $cmd, $matches)) {
			if (!empty($matches)) {
				foreach ($matches as $k => $match) {
					if (is_numeric($k)) {
						unset($matches[$k]);
					}
				}
				$matches = array_filter($matches);
			}
			return $matches;
		} else {
			return null;
		}
	}

	/**
	 * Récupérer l'action de la route
	 * @return string
	 */
	public function action() {
		return $this->action;
	}

	/**
	 * Récupérer le module de la route
	 * @return string
	 */
	public function module() {
		return $this->module;
	}

}