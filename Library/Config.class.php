<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe de configuration de l'application
 * @namespace Library
 * @package Config.class.php
 */
class Config extends ApplicationComponent {

	/**
	 * Tableau contenant l'ensemble des parametres de l'application en provenance du fichier app.xml dans le répertoire \Applications\{Nom de l'application}\Config
	 * @var array
	 */
	protected $vars = array();

	public function __construct() {
		parent::__construct(__CLASS__);
	}

	/**
	 * Retourner la valeur du paramètre
	 * @param string $var
	 * @return string|null
	 */
	public function get($var) {
		if (!$this->vars) {
			$xml = new \DOMDocument();
			$xml->load(__DIR__ . '/../Applications/' . parent::$name . '/Config/app.xml');
			$elements = $xml->getElementsByTagName('define');
			foreach ($elements as $element) {
				$this->vars[$element->getAttribute('var')] = (string) $element->getAttribute('value');
			}
		}
		if (isset($this->vars[$var]))
			return $this->vars[$var];
		return null;
	}

}