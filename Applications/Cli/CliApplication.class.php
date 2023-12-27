<?php

namespace Applications\Cli;

define('DIRCLI_TPL_EMAIL', AK_CLI_ROOT. '/Applications/Cli/Templates/tpl_email/');

class CliApplication extends \Library\Application {

	public function __construct() {
		parent::__construct();
		parent::$name = 'Cli';
	}

	public function run() {
		echo <<<EOT

AK-Cli v1.0 (based on AK Framework)

EOT;

		//parent::$cli->setParam();
		$cmd = parent::$cli->cmd;
		if (!empty(parent::$cli->arg[2])) {
			$cmd .= '/' . parent::$cli->arg[2];
		}
		if ($_SERVER['argc'] > 1) {
			$controller = $this->getController($cmd);
			if ($controller !== null) {
                if (!empty(parent::$cli->arg[3])) {
					$controller->param['weekPointage'] = parent::$cli->arg[3];
                }
				$controller->execute();
			} else{
				$this->getHelp();
			}
		} else {
			echo "\nERREUR\n  Veuillez saisir une commmande valide\n";
			$this->getHelp();
		}
	}

	protected function getHelp() {
		echo <<<EOT

DESCRIPTION
  Cette commande permet de lancer des scripts PHP en ligne de commande.

USAGE
  php ak-cli.php nom_commande arg1 arg2 ...

PARAMETERS
  nom_commande : nom de la commande à exécuter
  arg1 : argument 1 de la commande
  arg3 : argument 2 de la commande
  ...

  Pour en savoir plus sur les araguments d'une commande, exécutez :
  php ak-cli.php nom_commande help
EOT;
		exit();
	}

}