<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Classe de gestion des logins
 */
class Login extends ApplicationComponent {

	public $username_login, $password_login, $remember;

	public $id, $token_change, $password, $password2;

	public $data = array(
			'user_id' => null,
			'username' => null,
			'password' => null,
			'level' => null,
			'modules' => null,
			'token' => null,
			'code' => null,
			'first_name' => null,
			'last_name' => null,
			'time' => null,
			'remember' => null
	);

	private $loginTries, $loginBanTime, $update_time, $kernel;

	public function __construct() {
		parent::__construct(__CLASS__);
		$this->loginTries = (isset($_SESSION['loginTries'])) ? $_SESSION['loginTries'] : null;
		$this->loginBanTime = (isset($_SESSION['loginBanTime'])) ? $_SESSION['loginBanTime'] : null;
		$this->kernel = &parent::$config;
	}

	/**
	 * Création du cookie
	 * @param string $var
	 */
	public function login($var) {
		if ($var == 'guest') {
			$_SESSION['loginTries'] = $this->loginTries;
			$_SESSION['loginBanTime'] = $this->loginBanTime;
		} else {
			unset($_SESSION['loginTries']);
			unset($_SESSION['loginBanTime']);
			$string = serialize($this->data);
			$expire = ($this->data['remember']) ? 60 * 60 * 24 * 20 : parent::$config->get('expire'); // 20 jours si remember sinon 10 min
			parent::$user->cookie = base64_encode($string);
			setcookie('ArkiumUser', parent::$user->cookie, time() + $expire);
		}
	}

	public function getData($array) {
		if (is_array($array)) {
			foreach ($this->data as $key => $value) {
				if (array_key_exists($key, $array)) {
					$this->data[$key] = $array[$key];
				}
			}
			if (array_key_exists('update_time', $array)) {
				$this->update_time = $array['update_time'];
			}
		}
	}

	/**
	 * Archiver la connection dans le fichier log
	 */
	private function log_user() {
		$ip = $_SERVER['REMOTE_ADDR'];
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$user_id = (isset($this->data['username'])) ? $this->data['username'] : 'guest';
		$content = date("Y/m/d H-i-s") . " - " . $user_id . " - " . $ip . " - " . $browser;
		parent::$security->log_file('Log/login.txt', $content);
	}

	public function authenticate() {
		$this->data['remember'] = (!empty($this->remember)) ? $this->remember : false;
		$checked = false;
		// autorise jusqu'à x essai pour se connecter et attendre x min avant de pouvoir relogguer
		if (($this->loginTries < ($this->kernel->get('loginTries')) || (time() - $this->loginBanTime) > $this->kernel->get('loginBanTime'))) {
			if ($this->data['user_id'] !== null) { // utilisateur validé
				if ($this->password_login == $this->data['password']) {
					$this->data['token'] = parent::$security->generer_token('kernel');
					$this->data['time'] = date("Y/m/d H-i-s"); // $ucookie[8]
					// $expire = 3600 * 24 * 60; // 60 jours avant de modifier le mot de passe
					$expire = parent::$config->get('changepassword');
					if ((strtotime($this->update_time) >= (time() - $expire)) || $this->data['user_id'] == 0) {
						$output['reponse'] = true; // Login
						$output['level'] = $this->data['level'];
					} else { // Change le mot de passe après 60 jours
						$output['reponse'] = 'change';
						$output['user_id'] = $this->data['user_id'];
						$output['token'] = $this->data['token'];
						$this->data['token'] = '';
					}
					$this->login('member');
					$this->log_user(); // Archive la connection de l'user dans le fichier log
					$checked = true;
				}
			}
			if (!$checked) {
				$this->loginTries = (isset($this->loginTries)) ? $this->loginTries + 1 : 1; // Login Tries
				$this->loginBanTime = (isset($this->loginBanTime)) ? $this->loginBanTime : time(); // Time
				if ((time() - $this->loginBanTime) > $this->kernel->get('loginBanTime')) {
					$this->loginTries = 1;
					$this->loginBanTime = time();
				}
				$this->login('guest');
				if ($this->loginTries < $this->kernel->get('loginTries')) {
					$a = $this->kernel->get('loginTries') - $this->loginTries;
					$output['reponse'] = "Username or Password incorrect!<br /> You have $a attempts before trying again in 10 minutes..";
					$output['status'] = "warning";
				} else {
					$output['reponse'] = 'Please wait 10 minutes before logging.';
					$output['status'] = "warning";
				}
			}
		} else {
			$output['reponse'] = 'Please wait 10 minutes before logging.';
			$output['status'] = "warning";
		}
		return $output;
	}

	public function keepAlive() {
		$output['reponse'] = false;
		if (isset($_SESSION['kernel_token']) && isset($_SESSION['kernel_token_time'])) {
			if ($_SESSION['kernel_token'] == $this->data['token']) {
				$this->data['token'] = parent::$security->generer_token('kernel');
				$_SESSION['kernel_token_time'] = time();
				$this->login('member');
				$output['reponse'] = true;
			}
		}
		return $output;
	}

	public function timeRemaining() {
		$output['reponse'] = false;
		if (isset($_SESSION['kernel_token']) && isset($_SESSION['kernel_token_time'])) {
			if ($_SESSION['kernel_token'] == $this->data['token']) {
				$expire = ($this->data['remember']) ? 60 * 60 * 24 * 20 : $this->kernel->get('expire'); // 20 jours si remember sinon 10 min
				$redirafter = ($_SESSION['kernel_token_time'] + $expire) - time();
				$redirafter = ($redirafter < 0) ? 0 : $redirafter;
				$warnafter = ($redirafter < 5 * 60) ? 0 : $redirafter - (5 * 60);
				$output['redirafter'] = $redirafter * 1000;
				$output['warnafter'] = $warnafter * 1000;
				$output['reponse'] = true;
			}
		}
		return $output;
	}

	public function forgot($row) {
		if ($this->loginTries < ($this->kernel->get('loginTries')) || 		// autorise jusqu'à x essai pour se connecter
		(time() - $this->loginBanTime) > $this->kernel->get('loginBanTime')) { // attendre x min avant de pouvoir relogguer
			$output['type'] = "danger";
			if ($row !== false) {
				$output['reponse'] = true;
			} else {
				$this->loginTries = (isset($this->loginTries)) ? $this->loginTries + 1 : 1; // Login Tries
				$this->loginBanTime = (isset($this->loginBanTime)) ? $this->loginBanTime : time(); // Time
				if ((time() - $this->loginBanTime) > $this->kernel->get('loginBanTime')) {
					$this->loginTries = 1;
					$this->loginBanTime = time();
				}
				$user = $this->login('guest');
				if ($this->loginTries < $this->kernel->get('loginTries')) {
					$a = $this->kernel->get('loginTries') - $this->loginTries;
					$output['reponse'] = "Username or Password incorrect!<br /> You have $a attempts before trying again in 10 minutes..";
					$output['status'] = "warning";
				} else {
					$output['reponse'] = 'Please wait 10 minutes before logging.';
					$output['status'] = "warning";
				}
			}
		} else {
			$output['reponse'] = 'Please wait 10 minutes before logging.';
			$output['status'] = "warning";
		}
		return $output;
	}

	/**
	 * Vérifier l'encodage du mot de passe et renvoyer si un message d'erreur sinon TRUE
	 * @param mixed $row
	 * @return array
	 */
	public function change($row) {
		if ($row !== false) {
			if ($this->password == "") {
				$output['reponse'] = "Passwords is blank.";
			} elseif ($this->password == $this->password2) {
				if ($row['password'] == $this->password) {
					$output['reponse'] = "Passwords identical but please do not use your last password.";
					$output['status'] = "warning";
				} else {
					$output['reponse'] = true;
				}
			} else {
				$output['reponse'] = "Passwords are not identical.";
				$output['status'] = "warning";
			}
		} else {
			$output['reponse'] = 'We identify a problem connecting to your account.<br/>Please contact your administrator.';
			$output['status'] = "warning";
		}
		return $output;
	}

	/**
	 * Créer un mot de passe
	 * Longueur par défaut du mot de passe = 7
	 * @param int $lenght Longueur du mot de passe
	 * @return string
	 */
	public function create_password($lenght = 7) {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double) microtime() * 1000000);
		$pass = '';
		for($i = 0; $i < $lenght; $i ++) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
		}
		return $pass;
	}

}