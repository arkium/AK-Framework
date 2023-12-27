<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe \PDOFactory permettant la création d'une connexion à la base de données
 * @namespace Library
 * @package PDOFactory.class.php
 */
class PDOFactory {

	/**
	 * Renvoi l'instance de la connexion à la base de données
	 * @param string $name Nom de l'application
	 * @throws ArkiumException
	 * @return \PDO
	 */
	public static function getMysqlConnexion($name) {
		try {
			$file = __DIR__ . '/../Applications/' . $name . '/Config/PDOConfig.ini';
			if (!$settings = parse_ini_file($file, true))
				throw new \Library\ArkiumException('Unable to open ' . $file . '.');

			$driver = $settings['database']['driver'] . ':host=' . $settings['database']['host'] . ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') . ';dbname=' . $settings['database']['schema'];

			$options = array(
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			);

			$db = new \PDO($driver, $settings['database']['username'], $settings['database']['password'], $options);
			$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			return $db;
		}
		catch (\PDOException $e) {
			$error = new \Library\ArkiumException($e->getMessage(), $e->getCode());
			$error->getMsg();
			return false;
		}
	}

}