<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2021, Arkium SCS
 */
namespace Library;

/**
 * Classe \Library\User du framework héritant de la classe \Library\ApplicationComponent
 */
class User extends ApplicationComponent {

	/**
	 * Variable contenant le COOKIE de l'utilisateur
	 * @var string
	 */
	public $cookie;

	/**
	 * Données du cookie
	 * @var mixed
	 */
	public $data;

	/**
	 * Tableau des modules accessibles de l'application
	 * @var array
	 */
	public $modules = "";

	/**
	 * Tableau des permissions par module
	 * @var array
	 */
	public $permModules;

	/**
	 * Tableau des permissions pour les paramètres :
	 * view, add, edit, delete, approval, admin
	 * @var array
	 */
	public $permissions = array(
			"view" => false,
			"add" => false,
			"edit" => false,
			"delete" => false,
			"approval" => false,
			"admin" => false
	);

	/**
	 * Constructeur de la classe \Library\User
	 */
	public function __construct() {
		parent::__construct(__CLASS__);
		session_name("ArkiumID");
		session_start();
		$this->cookie = isset($_COOKIE['ArkiumUser']) ? $_COOKIE['ArkiumUser'] : null;
		$this->data = $this->cookieDecode($this->cookie);
		$this->permissions = ($this->data != false) ? $this->getPermissions($this->data['level']) : $this->permissions ;
		$this->modules = ($this->data != false) ? explode(',', $this->data['modules']) : $this->modules ;
	}

	/**
	 * Décode le COOKIE encodé en base_64
	 * @param string $cookie Valeur du COOKIE à décoder
	 * @return mixed
	 */
	private function cookieDecode($cookie) {
		return unserialize(base64_decode($cookie));
	}

	/**
	 * Vérifier si l'utilisateur est authentifié
	 * @return bool
	 */
	public function isAuthenticated() {
		$remember = ($this->data != false) ? $this->data['remember'] : false;
		$expire = ($remember) ? 60 * 60 * 24 * 30 : parent::$config->get('expire'); // 30 jours si remember
		if (isset($_SESSION['kernel_token']) && isset($_SESSION['kernel_token_time']))
			if ($_SESSION['kernel_token'] == $this->data['token'])
				if ($_SESSION['kernel_token_time'] >= (time() - $expire))
					return true;
		return false;
	}

	public function isAuthorized($module) {
		if ($this->permissions['admin'])
			return true;
		if (in_array($module, $this->modules))
			return true;
		return false;
	}

	/**
	 * Déconnecter l'utilisateur
	 * @return bool
	 */
	public function logout() {
		setcookie('ArkiumUser', '', 0);
		setcookie(session_name(), '', 0, '/');
		session_destroy();
		return true;
	}

	/**
	 * Renvoyer l'attribue défini dans les sessions
	 * @param string $attr Attribue de la session à renvoyer
	 * @return null|string
	 */
	public function getAttribute($attr) {
		return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
	}

	/**
	 * Définir la valeur d'attribue dans les sessions
	 * @param string $attr Attribue de la session à définir
	 * @param mixed $value
	 */
	public function setAttribute($attr, $value) {
		$_SESSION[$attr] = $value;
	}

	/**
	 * Décoder et renvoyer l'état des permissions selon la valeur du masque
	 * @param int $bitMask Valeur du masque à décoder
	 * @return array Tableau contenant $permissions
	 */
	public function getPermissions($bitMask = 0) {
		$i = 0;
		foreach ($this->permissions as $key => $value) {
			$this->permissions[$key] = (($bitMask & pow(2, $i)) != 0) ? true : false;
			$i ++;
		}
		return $this->permissions;
	}

	/**
	 * Coder les permissions selon la valeur du masque
	 * @return int
	 */
	public function toBitmask() {
		$bitmask = 0;
		$i = 0;
		foreach ($this->permissions as $key => $value) {
			if ($value) {
				$bitmask += pow(2, $i);
			}
			$i ++;
		}
		return $bitmask;
	}

}