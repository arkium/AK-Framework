<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe CLI pour les lignes de commande
 * @namespace Library
 * @package Cli.class.php
 */
class Cli extends \Library\ApplicationComponent {

	/**
	 * Tableau des arguments du CLI
	 * @var array
	 */
	public $arg = '';

	/**
	 * Nom de la commande CLI à exécuter
	 * @var string
	 */
	public $cmd = '';

	public $YesNo = array(
			'y',
			'n'
	);

	/**
	 * Constructeur de la classe \Library\Cli
	 */
	public function __construct() {
		$this->arg = $_SERVER['argv'];
		if ($_SERVER['argc'] > 1) {
			$this->cmd = $this->arg[1];
		}
	}

	public function setParam() {
		//var_dump($_SERVER['argv']);
		//echo $_SERVER['argc'];
	}

	/**
	 * Récupérer la saisie clavier en CLI
	 * @param mixed $prompt Texte à afficher en CLI
	 * @param mixed $valid_inputs Tableau des valeurs attendues ou nom d'un fichier avec extension
	 * @param mixed $default Valeur par défaut si touche ENTER
	 * @return mixed
	 */
	public function getLine($prompt, $valid_inputs, $default = '') {
		while (!isset($input) || (is_array($valid_inputs) && !in_array($input, $valid_inputs)) || ($valid_inputs == 'is_file' && !is_file($input))) {
			echo $prompt;
			$input = strtolower(trim(fgets(STDIN)));
			if (empty($input) && !empty($default)) {
				$input = $default;
			}
		}
		return $input;
	}

	public function write($filename, $content) {
		$fileNameOld = $filename . ".old";
		if (file_exists($filename)) {
			if (is_file($filename)) {
				if (!rename($filename, $fileNameOld)) {
					die('Error: Unable to rename the file (' . $filename . ')');
				}
			} else {
				die('Erreur: le fichier (' . $filename . ') n est pas un fichier');
			}
		}
		if (!$handle = fopen($filename, 'w')) {
			die('Erreur: Unable to open the file (' . $filename . ')');
		}
		if (fwrite($handle, $content) === false) {
			die('Unable to write to file (' . $filename . ')');
		}
		fclose($handle);
		return true;
	}

}