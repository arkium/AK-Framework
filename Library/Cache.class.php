<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe de gestion du cache simple (APCu ou file-based)
 */
class Cache {

	private $cacheDir;
	private $useApcu;

	public function __construct() {
		// Vérifier si APCu est disponible
		$this->useApcu = function_exists('apcu_fetch') && apcu_enabled();
		
		// Définir le répertoire de cache file-based
		$this->cacheDir = getcwd() . '/var/cache/';
		
		// Créer le répertoire de cache s'il n'existe pas
		if (!$this->useApcu && !is_dir($this->cacheDir)) {
			if (!mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
				// Si la création échoue et que le répertoire n'existe toujours pas, désactiver le cache
				error_log('Cache: Unable to create cache directory: ' . $this->cacheDir);
				// Fallback: utiliser le répertoire temporaire système
				$this->cacheDir = sys_get_temp_dir() . '/ak-cache/';
				if (!is_dir($this->cacheDir)) {
					mkdir($this->cacheDir, 0755, true);
				}
			}
		}
	}

	/**
	 * Récupérer une valeur du cache
	 * @param string $key Clé du cache
	 * @return mixed|null Retourne la valeur ou null si non trouvée ou expirée
	 */
	public function get($key) {
		if ($this->useApcu) {
			$value = apcu_fetch($key, $success);
			return $success ? $value : null;
		} else {
			return $this->getFromFile($key);
		}
	}

	/**
	 * Stocker une valeur dans le cache
	 * @param string $key Clé du cache
	 * @param mixed $value Valeur à stocker
	 * @param int $ttl Temps de vie en secondes (par défaut 300s = 5min)
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
	 * Supprimer une valeur du cache
	 * @param string $key Clé du cache
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
			return false;
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
			// Supprimer tous les fichiers du répertoire cache
			$files = glob($this->cacheDir . '*.cache');
			foreach ($files as $file) {
				@unlink($file);
			}
			return true;
		}
	}

	/**
	 * Récupérer une valeur depuis le cache file-based
	 * @param string $key
	 * @return mixed|null
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
		
		$cached = @unserialize($data);
		if ($cached === false) {
			return null;
		}
		
		// Vérifier si le cache est expiré
		if (isset($cached['expires']) && $cached['expires'] < time()) {
			@unlink($filename);
			return null;
		}
		
		return isset($cached['value']) ? $cached['value'] : null;
	}

	/**
	 * Stocker une valeur dans le cache file-based
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	private function setToFile($key, $value, $ttl) {
		$filename = $this->getCacheFilename($key);
		
		$cached = array(
			'value' => $value,
			'expires' => time() + $ttl
		);
		
		$data = serialize($cached);
		return @file_put_contents($filename, $data, LOCK_EX) !== false;
	}

	/**
	 * Obtenir le nom de fichier pour une clé de cache
	 * @param string $key
	 * @return string
	 */
	private function getCacheFilename($key) {
		return $this->cacheDir . md5($key) . '.cache';
	}
}
