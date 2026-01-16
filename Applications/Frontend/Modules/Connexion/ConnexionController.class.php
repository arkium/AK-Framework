<?php

namespace Applications\Frontend\Modules\Connexion;

class ConnexionController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		if ($request->postExists('action')) {
			parent::$security->verifier_token('login');
			try {
				$login = new \Library\Login();
				if ($request->postData('action') == 'login') {
					$login->username_login = $request->postData('username');
					$login->password_login = $request->postData('password');
					$login->remember = $request->postData('remember');
					$query = "
					SELECT DISTINCT
						u.user_id,
						u.username,
						u.password,
						ur.level,
						ur.modules,
						u.code,
						u.first_name,
						u.last_name,
						u.update_time
					FROM ts_users u
					INNER JOIN ts_users_roles ur ON u.level = ur.role_id
					WHERE u.username = :username
						AND u.status='1'
					LIMIT 1";
					$stmt = parent::$dao->prepare($query);
					$stmt->bindValue(':username', $login->username_login, \PDO::PARAM_STR);
					$stmt->execute();
					$row = $stmt->fetch(\PDO::FETCH_ASSOC);
					
					// Verify password using password_verify if password is hashed, or plain text comparison for legacy
					if ($row !== false) {
						// Check if password is hashed using password_get_info() for reliable detection
						$passwordInfo = password_get_info($row['password']);
						if ($passwordInfo['algo'] !== null && $passwordInfo['algo'] !== 0) {
							// Modern hashed password - use password_verify
							if (password_verify($login->password_login, $row['password'])) {
								$login->getData($row);
								$output = $login->authenticate();
							} else {
								// Password verification failed
								$login->getData(array('user_id' => null));
								$output = $login->authenticate();
							}
						} else {
							// Legacy plain text password - getData() loads the password and 
							// authenticate() will compare it with $login->password_login
							$login->getData($row);
							$output = $login->authenticate();
						}
					} else {
						// User not found
						$login->getData(array('user_id' => null));
						$output = $login->authenticate();
					}
					
					if ($output['reponse'] === true) {
						parent::$user->permissions = parent::$user->getPermissions($output['level']);
						$output['url'] = (parent::$user->permissions['admin']) ? '.' : parent::$config->get('redirection');
					}
				} elseif ($request->postData('action') == 'forgot') {
					$query = "
					SELECT DISTINCT
						user_id
					FROM ts_users
					WHERE username = :username";
					$stmt = parent::$dao->prepare($query);
					$stmt->bindValue(':username', $request->postData('username2'), \PDO::PARAM_STR);
					$stmt->execute();
					$row = $stmt->fetch(\PDO::FETCH_ASSOC);

					$output = $login->forgot($row);
					if ($output['reponse'] === true) {
						if ($this->forgot_password_email($row['user_id'])) {
							$output['reponse'] = "Your password has been sent to your email address.";
							$output['status'] = "success";
						} else {
							$output['reponse'] = "The mail server failed to send you an e-mail.";
							$output['status'] = "warning";
						}
					}
				} elseif ($request->postData('action') == 'change') {
					$login->id = $request->postData('id');
					$login->token_change = $request->postData('token_change');
					$login->password = $request->postData('password3');
					$login->password2 = $request->postData('password2');
					$query = "
					SELECT DISTINCT
						user_id,
						password
					FROM ts_users
					WHERE user_id = :user_id";
					$stmt = parent::$dao->prepare($query);
					$stmt->bindValue(':user_id', $login->id, \PDO::PARAM_INT);
					$stmt->execute();
					$row = $stmt->fetch(\PDO::FETCH_ASSOC);

					$output = $login->change($row);
					if ($output['reponse'] === true) {
						$query = "
						UPDATE
							ts_users
						SET password = :password
						WHERE user_id = :user_id";
						$stmt = parent::$dao->prepare($query);
						$stmt->bindValue(':password', $login->password, \PDO::PARAM_STR);
						$stmt->bindValue(':user_id', $login->id, \PDO::PARAM_INT);
						$output['reponse'] = ($stmt->execute() !== false) ? true : 'The update of the database is not successful!<br/>Please contact your administrator.';
						if ($output['reponse'] === true) {
							$login->getData(parent::$user->data);
							$login->data['token'] = $login->token_change;
							$login->login('member');
							$this->new_password_email($login->id);
							// Définir la page de destination
							parent::$user->permissions = parent::$user->getPermissions($output['level']);
							$output['url'] = (parent::$user->permissions['admin']) ? '.' : parent::$config->get('redirection');
						}
					}
				}
			} catch (\PDOException $e) {
				$output['reponse'] = 'The database is not able to be read!<br/>';
				$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
				$output['reponse'] .= "Syntax Error: " . $e->getMessage();
				$output['status'] = "warning";
			}
			parent::$httpResponse->json($output);
		} else {
			$this->page->addVar('page_token', parent::$security->generer_token('login'));
		}
	}

	public function executeLogout(\Library\HTTPRequest $request) {
		parent::$user->logout();
		parent::$httpResponse->redirect('.');
	}

	public function executeKeepalive(\Library\HTTPRequest $request) {
		$login = new \Library\Login();
		$login->getData(parent::$user->data);
		parent::$httpResponse->json($login->keepAlive());
	}

	public function executeTimeremaining() {
		$login = new \Library\Login();
		$login->getData(parent::$user->data);
		parent::$httpResponse->json($login->timeRemaining());

	}

	private function forgot_password_email($user_id) {
		try {
			$query = "
			SELECT
				email_address,
				first_name,
				username,
				password
			FROM ts_users
			WHERE user_id = :user_id";
			$stmt = parent::$dao->prepare($query);
			$stmt->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
			$stmt->execute();
			$data_user = $stmt->fetch(\PDO::FETCH_ASSOC);
			$data_replace["first_name"] = $data_user['first_name'];
			$data_replace["username"] = $data_user['username'];
			$data_replace["password"] = $data_user['password'];
		} catch (\PDOException $e) {
			$output['reponse'] = 'The database is not able to be read!<br/>';
			$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
			$output['reponse'] .= "Syntax Error: " . $e->getMessage();
			$output['status'] = "warning";
		}

		$email = new \Library\Email();
		$email->setfrom(FROM_EMAIL);
		$email->data_replace = $data_replace;
		$email->subject = 'Account details for #username# at gillet.arkium.eu';
		$email->setFilePathMessage(getcwd() . DIR_TPL_EMAIL . 'forgot_password.html');
		$email->destinationEmail = $data_user['email_address'];
		return $email->sendEmail();
	}

	private function new_password_email($user_id) {
		try {
			$query = "
			SELECT
				email_address,
				first_name,
				username,
				password
			FROM ts_users
			WHERE user_id = :user_id";
			$stmt = parent::$dao->prepare($query);
			$stmt->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
			$stmt->execute();
			$data_user = $stmt->fetch(\PDO::FETCH_ASSOC);
			$data_replace["first_name"] = $data_user['first_name'];
			$data_replace["username"] = $data_user['username'];
			$data_replace["password"] = $data_user['password'];
		} catch (\PDOException $e) {
			$output['reponse'] = 'The database is not able to be read!<br/>';
			$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
			$output['reponse'] .= "Syntax Error: " . $e->getMessage();
			$output['status'] = "warning";
		}

		$email = new \Library\Email();
		$email->setfrom(FROM_EMAIL);
		$email->data_replace = $data_replace;
		$email->subject = 'Account details for #username# at gillet.arkium.eu';
		$email->setFilePathMessage(getcwd() . DIR_TPL_EMAIL . 'new_password.html');
		$email->destinationEmail = $data_user['email_address'];
		return $email->sendEmail();
	}
}