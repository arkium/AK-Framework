<?php

namespace Cli;

class cli_base extends \Cli\Cli {

	public function __construct() {
		parent::__construct();
	}

	public function getOptions():bool {
		return true;
	}

	public function execute() {
		switch ($this->cmd) {
			case "help":
				$this->getHelp();
				break;
			case "version":
				$this->getVersion();
				break;
			case "":
				$this->getHelp();
		}
	}

	public function getHelp() {
		echo <<<EOT

DESCRIPTION
  Cette commande permet de lancer des scripts PHP en ligne de commande.

  Il est recommandé d'exécuter cette commande dans le répertoire "Cli".

USAGE
  php arkium-cli.php nomduscript arg2 arg3 ...

PARAMETERS
  nomduscript : nom du script PHP à exécuter
  arg2 : argument du script
  arg3 : autre argument du script

  Pour en savoir plus sur les paramètres d'un script PHP, exécutez :
  php arkium-cli.php nomduscript help
EOT;
		exit();
	}

	public function getVersion() {
		echo <<<EOT

AK-Cli v1.0 (based on AK Framework)
Please type 'help' for help. Type 'exit' to quit.
EOT;
		exit();
	}

}