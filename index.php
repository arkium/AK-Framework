<?php
error_reporting(E_ALL);

define('AK_CLI', false);
define('AK_ROOT', __DIR__);
define('AK_START_MICROTIME', microtime(true));

require AK_ROOT . '/Library/AutoLoad.php';
if (file_exists(AK_ROOT . '/Ressources/vendors/autoload.php')) {
	require AK_ROOT . '/Ressources/vendors/autoload.php';
}
$app = new \Applications\Frontend\FrontendApplication();
$app->run();