<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe permettant la création d'une page html
 * @namespace Library
 * @package Page.class.php
 */
class Page extends ApplicationComponent {

	/**
	 * Variable contenant la page html
	 * @var string
	 */
	protected static $contentFile = null;

	/**
	 * Tableau contenant les variables à transférer dans la page
	 * @var array
	 */
	protected static $vars = array();

	/**
	 * Construction de l'objet Page
	 */
	public function __construct() {
		parent::__construct(__CLASS__);
		$module = (is_object(parent::$route)) ? parent::$route->module() : null;
		if (!static::$vars && !empty($module)) {
			$filename = __DIR__ . '/../Applications/' . parent::$name . '/Modules/' . $module . '/Views/' . parent::$route->action() . '.xml';
			if (file_exists($filename)) {
				$this->loadXML($filename);
			} else {
				$filename = __DIR__ . '/../Applications/' . parent::$name . '/Modules/' . $module . '/config.xml';
				if (file_exists($filename)) {
					$this->loadXML($filename);
				}
			}
		}
	}

	/**
	 * Ajouter une variable dans le tableau des variables
	 * @param string $var Nom de la variable
	 * @param string $value Valeur de la variable
	 * @throws \Library\ArkiumException
	 */
	public function addVar($var, $value) {
		if (!is_string($var) || is_numeric($var) || empty($var))
			throw new \Library\ArkiumException('The variable name must be a string of non-zero character');

		static::$vars[$var] = $value;
	}

	/**
	 * Ajouter un tableau dans le tableau des variables
	 * @param string $var Nom du tableau
	 * @param string $value Valeur à ajouter dans le tableau
	 * @throws \Library\ArkiumException
	 */
	public function addArray($var, $value) {
		if (!is_string($var) || is_numeric($var) || empty($var))
			throw new \Library\ArkiumException('The variable name must be a string of non-zero character');

		if (array_key_exists($var, static::$vars) ) {
			if (!is_array(static::$vars[$var])) {
				static::$vars[$var] = null;
				static::$vars[$var][] = static::$vars[$var];
			}
        }
		static::$vars[$var][] = $value;
	}

	/**
	 * Génération de la page html
	 * @param mixed $template
	 * @throws ArkiumException
	 * @return string
	 */
	public function getGeneratedPage($template = true) {
		try {
			if (!file_exists(static::$contentFile))
				throw new \Library\ArkiumException(static::$contentFile.chr(13).'>> The file to display does not exist! <<', 120);
		}
		catch (\Library\ArkiumException $e) {
			$e->getMsg();
		}
		// Variable $fct : Permettant un accès aux fonctions de la classe Functions
		$fct = new \Library\Functions();
		// Variable $user : Permettant un accès aux données de l'utilisateur
		$user = parent::$user;
		// Variable $css : Liste des fichiers CSS à afficher dans layout.html
		$css = null;
		if (array_key_exists('config_css', static::$vars)) {
			if (static::$vars['config_css'] != null) {
				foreach(static::$vars['config_css'] as $style):
					$css .= '<link rel="stylesheet" href="'.$style.'" />' . chr(13);
				endforeach;
			}
		}
		// Variable $js : Liste des fichiers JS à afficher dans layout.html
		$js = null;
		if (array_key_exists('config_js', static::$vars)) {
			if (static::$vars['config_js'] != null) {
				foreach(static::$vars['config_js'] as $script):
					$js .= '<script type="text/javascript" src="'.$script.'"></script>' . chr(13);
				endforeach;
			}
		}
		// Variable $scriptJS : Liste des scripts à afficher dans layout.html
		$scriptJS = null;
		if (array_key_exists('config_scriptJS', static::$vars)) {
			if (static::$vars['config_scriptJS'] != null) {
				foreach(static::$vars['config_scriptJS'] as $script):
					$scriptJS .= "<script charset=\"UTF-8\">" . $script . "</script>" . chr(13);
				endforeach;
			}
		}

		extract(static::$vars);

		ob_start();
		require static::$contentFile;
		$content = ob_get_clean();

		if ($template) {
			ob_start();
			require dirname(__FILE__) . '/../Applications/' . parent::$name . '/Templates/layout.html';
			return ob_get_clean();
		} else {
			return $content;
		}
	}

	public function setContentFile($contentFile) {
		if (!is_string($contentFile) || empty($contentFile))
			throw new \Library\ArkiumException('The specified view is invalid');

		static::$contentFile = $contentFile;
	}

	private function loadXML($filename) {
		$xml = new \DOMDocument();
		$xml->load($filename);
		$elements = $xml->getElementsByTagName('define');
		foreach ($elements as $element) {
			static::$vars[$element->getAttribute('var')] = $element->getAttribute('value');
		}
		static::$vars['config_css'] = null;
		$elements = $xml->getElementsByTagName('defineCSS');
		foreach ($elements as $element) {
			static::$vars['config_css'][] = $element->getAttribute('value');
		}
		static::$vars['config_js'] = null;
		$elements = $xml->getElementsByTagName('defineJS');
		foreach ($elements as $element) {
			static::$vars['config_js'][] = $element->getAttribute('value');
		}
	}

}