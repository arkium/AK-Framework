<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Routeur pour trouver l'action à exécuter
 * @namespace Library
 * @package Router.class.php
 */
class Router extends \Library\ApplicationComponent {

	/**
	 * Tableau contenant les listes des routes
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Short codes for common patterns
	 * @var array
	 */
	private $shortCodes = array(
			'$' => '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*', // php variable
			':' => '[A-Za-z0-9]+', // alphanumeric
			'#' => '[0-9]+', // numeric
			'*' => '(.*)', // wildcard
			'~' => '[a-z]{1,5}', // extension
			'^' => '[A-Za-z0-9\-]+'  // alphanumeric + hyphen
		);

	/**
	 * Characters to replace or escape
	 * @var array
	 */
	private $charMap = array(
			"(" => "(?:",
			")" => ")?",
			"/" => "/",
			"-" => "-",
			"." => "."
	);

	const NO_ROUTE = 1;

	/**
	 * Chargement du fichier de configuration avec les routes
	 * @param string $name Nom de l'application
	 */
	public function __construct($name) {
		// Chargement du fichier route.xml de l'application
		$xml = new \DOMDocument();

		// Chemin du fichier tenant toutes les routes de l'application
		$chemin = __DIR__ . '/../Applications/' . $name . '/Config/routes.xml';

		// Vérifier si le fichier exsite
		if (!file_exists($chemin))
			throw new \Library\ArkiumException("Le fichier des routes.xml n'est pas accessible : " . $chemin, self::NO_ROUTE);

		// Charger le fichier xml
		$xml->load($chemin);

		if (!AK_CLI) {
			// Récupérer les routes pour l'application
			$routesXML = $xml->getElementsByTagName('route');
			foreach ($routesXML as $routeXML) {
				$cmd = $this->routeToRegex($routeXML->getAttribute('url'));
				$this->addRoute(new Route($cmd, $routeXML->getAttribute('module'), $routeXML->getAttribute('action')));
			}
		} else {
			// Récupérer les routes pour l'application en ligne de commande
			$routesXML = $xml->getElementsByTagName('cli');
			foreach ($routesXML as $routeXML) {
				$cmd = $this->routeToRegex($routeXML->getAttribute('cmd'));
				$this->addRoute(new Route($cmd, $routeXML->getAttribute('module'), $routeXML->getAttribute('action')));
			}
		}
	}

	/**
	 * Rechercher dans la liste des routes la commande fournie et renvoyer la première route trouvée
	 * @param string $cmd Nom de la commande
	 * @throws ArkiumException
	 * @return null|\Library\Route
	 */
	public function getRoute(string $cmd): ?\Library\Route {
		if (!AK_CLI) {
			$urlParts = parse_url($cmd);
			if (!isset($urlParts['path']))
				return null;
			$cmdtocheck = $urlParts['path'];
		} else {
			$cmdtocheck = $cmd;
		}

		foreach ($this->routes as $route) {
			// Si la route correspond à la commande
			if (($varsValues = $route->match($cmdtocheck)) !== null) {
				if (!AK_CLI) {
					// On ajoute les variables de l'URL au tableau $_GET
					$_GET = array_merge($_GET, $varsValues);
				}
				// Renvoyer la route correspondante
				return $route;
			}
		}
		throw new \Library\ArkiumException('Aucune route ne correspond à la commande : ' . $cmd, self::NO_ROUTE);
	}

	/**
	* Ajouter une route au tableau des routes
	* @param \Library\Route $route Instance de la classe \Library\Route
	*/
	private function addRoute(\Library\Route $route) {
		if (!in_array($route, $this->routes)) {
			$this->routes[] = $route;
		}
	}

	/**
	 * Convertir une route en expression rationnelle standard
	 * @param string $route
	 * @return string
	 */
	private function routeToRegex($route) {
		$result = '%^';
		$placeholder = null;
		$regex = null;
		$len = strlen($route);

		for($i = 0; $i < $len; $i ++) {
			switch ($route[$i]) {
				case '/' :
				case '(' :
				case ')' :
					if ($placeholder !== null && $regex !== null) {
						$result .= '(?:(' . $placeholder . ')' . $regex . ')';

						$placeholder = null;
						$regex = null;
					}
					$result .= $this->charMap[$route[$i]];
					break;
				case '-' :
					if ($placeholder !== null && $regex !== null) {
						$result .= '(?:(' . $placeholder . ')' . $regex . ')' . $this->charMap[$route[$i]];
					} else {
						$regex .= $route[$i];
					}
					break;
				case '<' :
					$regex = '';
					$result .= '(?:(' . $placeholder . ')';
					$placeholder = null;
					break;
				case '>' :
					if ($regex !== null) {
						$result .= $regex . ')';
						$regex = null;
					}
					break;
				case '$' :
				case ':' :
				case '#' :
				case '*' :
				case '~' :
				case '^' :
					$placeholder = '';
					$regex = $this->shortCodes[$route[$i]];
					break;
				default :
					if ($placeholder !== null) {
						$placeholder .= $route[$i];
					} elseif ($regex !== null) {
						$regex .= $route[$i];
					} else {
						if (array_key_exists($route[$i], $this->charMap)) {
							$result .= $this->charMap[$route[$i]];
						} else {
							$result .= $route[$i];
						}
					}
			}
		}

		if ($placeholder !== null && $regex !== null) {
			$result .= '(?:(' . $placeholder . ')' . $regex . ')';
		}
		$result .= '$%D';
		return $result;
	}
}