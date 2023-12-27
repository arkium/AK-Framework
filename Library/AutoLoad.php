<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */

define('CORE_PATH', __DIR__);

spl_autoload_register(function ($class) {
	// Retourner TRUE si $class est static
	if (strpos($class, 'static::') === 0)
		return true;

	// Vérifier si $class est vide
	if (empty($class))
		die(_("Erreur ! Impossible de charger une classe vide : " . $class));

	// Vérifier si serveur Windows
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		// Adapter les séparateurs pour Windows
		$filename = str_replace('/', '\\', $class) . '.class.php';
		if (!file_exists(__DIR__ . $filename)) {
			$filename = '\\..\\' . str_replace('/', '\\', $class) . '.class.php';
		}
	} else {
		// Adapter les séparateurs pour Linux
		$filename = str_replace('\\', '/', $class) . '.class.php';
		if (!file_exists(__DIR__ . $filename)) {
			$filename = '/../' . str_replace('\\', '/', $class) . '.class.php';
		}
	}

	// Vérifier si $filename existe
	if (!file_exists(__DIR__ . $filename))
		die(_("Erreur ! Le fichier de la classe n'a été trouvé : ") . __DIR__ . $filename);

	// Charger $filename
	require __DIR__ . $filename;

// Vérifier si le require a déclaré la classe
	if (!class_exists($class, false))
		trigger_error(_("Erreur ! La classe n'a pas été chargée : ") . $class, E_USER_WARNING);
});