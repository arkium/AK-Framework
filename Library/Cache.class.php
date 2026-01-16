<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe de gestion du cache simple
 * Utilise APCu si disponible, sinon file-based cache
 */
class Cache {

	private $useApcu;
	private $cacheDir;

	public function __construct() {
		// Vérifier si APCu est disponible et fonctionnel
		$this->useApcu = extension_loaded('apcu') && ini_get('apc.enabled') && function_exists('apcu_store');
		
		// Test si APCu fonctionne réellement (peut échouer en CLI)
		if ($this->useApcu) {
			$testResult = @apcu_store('_cache_test_', 1, 1);
			if ($testResult === false) {
				$this->useApcu = false;
			} else {
				@apcu_delete('_cache_test_');
			}
		}
		
		// Définir le répertoire de cache file-based
		$this->cacheDir = getcwd() . '/var/cache/';
		
		// Créer le répertoire de cache s'il n'existe pas
		if (!$this->useApcu && !is_dir($this->cacheDir)) {
			mkdir($this->cacheDir, 0755, true);
		}
	}

	/**
	 * Récupérer une valeur du cache
	 * @param string $key La clé du cache
	 * @return mixed La valeur du cache ou null si non trouvé ou expiré
	 */
	public function get($key) {
		if ($this->useApcu) {
			$success = false;
			$value = apcu_fetch($key, $success);
			return $success ? $value : null;
		} else {
			return $this->getFromFile($key);
		}
	}

	/**
	 * Stocker une valeur dans le cache
	 * @param string $key La clé du cache
	 * @param mixed $value La valeur à stocker
	 * @param int $ttl Durée de vie en secondes (défaut: 300s = 5 minutes)
	 * @return bool Succès de l'opération
	 */
	public function set($key, $value, $ttl = 300) {
		if ($this->useApcu) {
			return apcu_store($key, $value, $ttl);
		} else {
			return $this->setToFile($key, $value, $ttl);
		}
	}

	/**
	 * Récupérer une valeur du cache file-based
	 * @param string $key La clé du cache
	 * @return mixed La valeur du cache ou null si non trouvé ou expiré
	 */
	private function getFromFile($key) {
		$filename = $this->getCacheFilename($key);
		
		if (!file_exists($filename)) {
			return null;
		}
		
		$data = @file_get_contents($filename);
		if ($data === false) {
			return null;
		}
		
		$cacheData = @unserialize($data);
		if ($cacheData === false) {
			return null;
		}
		
		// Vérifier l'expiration
		if (time() > $cacheData['expires']) {
			@unlink($filename);
			return null;
		}
		
		return $cacheData['value'];
	}

	/**
	 * Stocker une valeur dans le cache file-based
	 * @param string $key La clé du cache
	 * @param mixed $value La valeur à stocker
	 * @param int $ttl Durée de vie en secondes
	 * @return bool Succès de l'opération
	 */
	private function setToFile($key, $value, $ttl) {
		$filename = $this->getCacheFilename($key);
		
		$cacheData = array(
			'expires' => time() + $ttl,
			'value' => $value
		);
		
		$data = serialize($cacheData);
		$result = @file_put_contents($filename, $data, LOCK_EX);
		
		return $result !== false;
	}

	/**
	 * Obtenir le nom de fichier pour une clé de cache
	 * @param string $key La clé du cache
	 * @return string Le chemin complet du fichier de cache
	 */
	private function getCacheFilename($key) {
		// Utiliser md5 pour créer un nom de fichier sûr
		$safeKey = md5($key);
		return $this->cacheDir . $safeKey . '.cache';
	}

	/**
	 * Supprimer une valeur du cache
	 * @param string $key La clé du cache
	 * @return bool Succès de l'opération
	 */
	public function delete($key) {
		if ($this->useApcu) {
			return apcu_delete($key);
		} else {
			$filename = $this->getCacheFilename($key);
			if (file_exists($filename)) {
				return @unlink($filename);
			}
			return true;
		}
	}

	/**
	 * Vider tout le cache
	 * @return bool Succès de l'opération
	 */
	public function clear() {
		if ($this->useApcu) {
			return apcu_clear_cache();
		} else {
			$files = glob($this->cacheDir . '*.cache');
			if ($files === false) {
				return false;
			}
			foreach ($files as $file) {
				@unlink($file);
			}
			return true;
		}
	}
}
