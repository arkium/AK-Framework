<?php
error_reporting(E_ALL);

define('AK_CLI', true);
define('AK_CLI_ROOT', __DIR__);
define('AK_CLI_START_MICROTIME', microtime(true));

//echo AK_CLI_START_MICROTIME . "\n";
//echo "répertoire : " . __DIR__ . " :\n";
//echo "fichier existe : " . AK_CLI_ROOT . "/../Ressources/vendors/autoload.php -> ";
//echo (file_exists(AK_CLI_ROOT . "/../Library/AutoLoad.php")) ? "oui\n" : "non\n";

require AK_CLI_ROOT . '/../Library/AutoLoad.php';
if (file_exists(AK_CLI_ROOT . '/../Ressources/vendors/autoload.php')) {
	require AK_CLI_ROOT . '/../Ressources/vendors/autoload.php';
}

new \Cli\cli_createModule();
new \Cli\cli_pointageAtelier();
new \Cli\cli_base();

?>