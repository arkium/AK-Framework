<?php
error_reporting(E_ALL);

//define('AK_CLI', PHP_SAPI === 'cli');
define('AK_CLI', true);
define('AK_CLI_ROOT', __DIR__);
define('AK_CLI_START_MICROTIME', microtime(true));

require AK_CLI_ROOT . '/Library/AutoLoad.php';
if (file_exists(AK_CLI_ROOT . '/Ressources/vendors/autoload.php')) {
	require AK_CLI_ROOT . '/Ressources/vendors/autoload.php';
}

$app = new \Applications\Cli\CliApplication();
$app->run();