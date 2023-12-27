<?php

//Permettre la mise à jour de la base de données

class updateDB {

	private $dbOrig, $dbDest, $conv, $status = array(
			"INACTIVE" => "0",
			"ACTIVE" => "1"
	), $family = array(
			'Statutory audit project',
			'Other attest projects',
			'Non attest project',
			'Administration',
			'Office/practice management',
			'HR related',
			'Business Development',
			'Training/teaching',
			'NA'
	), $chargeable = array(
			'Chargeable' => '1',
			'Non-chargeable' => '0',
			'Absence' => '0'
	), $submit = array(
			'SUBMIT' => '0',
			'APPROVAL' => '1'
	);

	public $driverOrig, $usernameOrig, $passwordOrig, $driverDest, $usernameDest, $passwordDest;

	private $YesNo = array(
			'y',
			'n'
	);

	public function __construct() {
		$this->getOptions();
		$this->getVersion();
		if ($this->getLine("Do you want to continue? (y/n) : ", $this->YesNo, 'y') == 'n')
			exit();
	}

	private function getOptions() {
		if (isset($_SERVER['argv'][1]))
			if ($_SERVER['argv'][1] == 'help') {
				$this->getHelp();
				exit();
			}
	}

	private function getHelp() {
		echo <<<EOT
USAGE
  php arkium.php className tableName

DESCRIPTION
  This command allows you to automatically generate
  new controllers, views and data models for the tables
  in your database.

  It is recommended that you run this command
  in the "CLI" directory.

PARAMETERS
  className : Name of the class to create
  TableName : Name of the table to be converted into class
EOT;
	}

	private function getVersion() {
		echo <<<EOT
Arkium Tool v1.0 (based on Arkium Framework v)
Please type 'help' for help. Type 'exit' to quit.
EOT;
	}

	private function getLine($prompt, $valid_inputs, $default = '') {
		while (!isset($input) || (is_array($valid_inputs) && !in_array($input, $valid_inputs)) || ($valid_inputs == 'is_file' && !is_file($input))) {
			echo "\n\n" . $prompt;
			$input = strtolower(trim(fgets(STDIN)));
			if (empty($input) && !empty($default)) {
				$input = $default;
			}
		}
		return $input;
	}

	public function getDBConnexionOrig() {
		try {
			$options = array(
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			);
			$this->dbOrig = new \PDO($this->driverOrig, $this->usernameOrig, $this->passwordOrig, $options);
			$this->dbOrig->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			die('Error ! ' . $e->getCode() . ' -> ' . $e->getMessage());
		}
	}

	public function getDBConnexionDest() {
		try {
			$options = array(
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			);
			$this->dbDest = new \PDO($this->driverDest, $this->usernameDest, $this->passwordDest, $options);
			$this->dbDest->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			die('Error ! ' . $e->getCode() . ' -> ' . $e->getMessage());
		}
	}

	public function updateClientToEntities() {
		echo "\nVidange de la table : ts_entities";
		$this->dbDest->exec('TRUNCATE TABLE ts_entities');
		echo "\nConnexion aux tables : ts_client and ts_entities";
		$query = "SELECT * FROM ts_client";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$query = "INSERT INTO ts_entities SET
			entity_id='" . $row['client_id'] . "',
			entity_type_id=2,
			code='" . $row['code'] . "',
			organisation='" . addslashes($row['organisation']) . "',
			entity_group_id='" . $row['clientgroup_id'] . "',
			address1='" . addslashes($row['address1']) . "',
			address2='" . addslashes($row['address2']) . "',
			postal_code='" . $row['postal_code'] . "',
			city='" . addslashes($row['city']) . "',
			state='" . addslashes($row['state']) . "',
			country='" . addslashes($row['country']) . "',
 			http_url='" . $row['http_url'] . "',
 			inception_date=NULL,
 			legal_form=NULL,
 			juridiction=NULL,
			opportunity='0',
			dateLastRiskAssessment='',
			direct='0',
			indirect='1',
			note='" . $row['note'] . "',
			status='1',
			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_entities.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateClientgroupToEntitiesgroups() {
		echo "\nVidange de la table : ts_entities_groups";
		$this->dbDest->exec('TRUNCATE TABLE ts_entities_groups');
		echo "\nConnexion aux tables : ts_clientgroup and ts_entities_groups";
		$query = "SELECT * FROM ts_clientgroup";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$query = "INSERT INTO ts_entities_groups SET
			entity_group_id=" . $row['clientgroup_id'] . ",
			name='" . addslashes($row['group_name']) . "',
			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_entities_groups.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateCompagnyToEntities() {
		echo "\nConnexion aux tables : ts_compagny and ts_entities";
		$query = "SELECT * FROM ts_compagny";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			// entity_id='" . $row['compagny_id'] . "',
			$query = "INSERT INTO ts_entities SET
			entity_type_id=1,
			code='" . $row['code'] . "',
			organisation='" . addslashes($row['organisation']) . "',
			entity_group_id=NULL,
			address1='" . addslashes($row['address1']) . "',
			address2='" . addslashes($row['address2']) . "',
			postal_code='" . $row['postal_code'] . "',
			city='" . addslashes($row['city']) . "',
			state='" . addslashes($row['state']) . "',
			country='" . addslashes($row['country']) . "',
 			http_url='" . $row['http_url'] . "',
 			inception_date=NULL,
 			legal_form=NULL,
 			juridiction=NULL,
			opportunity='0',
			dateLastRiskAssessment='',
			direct='0',
			indirect='1',
			note='" . $row['note'] . "',
			status='1',
			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$this->conv['compagny_id'][$row['compagny_id']] = $this->dbDest->lastInsertId();
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_entities.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updatePeriodToPeriods() {
		echo "\nVidange de la table : ts_periods";
		$this->dbDest->exec('TRUNCATE TABLE ts_periods');
		echo "\nConnexion aux tables : ts_period and ts_periods";
		$query = "SELECT * FROM ts_period";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$query = "INSERT INTO ts_periods SET
			start_date='" . $row['start_date'] . "',
			end_date='" . $row['end_date'] . "',
			status=" . $this->status[$row['status']] . ",
			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_periods.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateUserToUsers() {
		echo "\nVidange de la table : ts_users";
		$this->dbDest->exec('TRUNCATE TABLE ts_users');
		echo "\nConnexion aux tables : ts_user and ts_users";
		$query = "SELECT * FROM ts_user WHERE user_id>0";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$query = "INSERT INTO ts_users SET
			user_id='" . $row['user_id'] . "',
			code='" . $row['code'] . "',
			first_name='" . addslashes($row['first_name']) . "',
			last_name='" . addslashes($row['last_name']) . "',
			email_address='" . addslashes($row['email_address']) . "',
			company_id='" . $this->conv['compagny_id'][$row['invoicing_entity_id']] . "',
			level='0',
			username='" . addslashes($row['username']) . "',
			password='" . addslashes($row['password']) . "',
			contract='" . addslashes($row['contract']) . "',
			status=" . $this->status[$row['status']] . ",
 			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_users.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateTasks_families() {
		echo "\nVidange de la table : ts_tasks_families";
		$this->dbDest->exec('TRUNCATE TABLE ts_tasks_families');
		echo "\nConnexion aux tables : ts_tasks_families";
		$i = 0;
		foreach ($this->family as $key => $val) {
			$query = "INSERT INTO ts_tasks_families SET
			name='" . addslashes($val) . "',
			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$this->conv['family'][$val] = $this->dbDest->lastInsertId();
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_tasks_families.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateProject_typeToTasks_types() {
		echo "\nVidange de la table : ts_tasks_types";
		$this->dbDest->exec('TRUNCATE TABLE ts_tasks_types');
		echo "\nConnexion aux tables : ts_project_type and ts_tasks_types";
		$query = "SELECT * FROM ts_project_type";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$query = "INSERT INTO ts_tasks_types SET
			task_type_id='" . $row['project_type_id'] . "',
			code='" . $row['code'] . "',
			name='" . addslashes($row['name']) . "',
			task_family_id='" . $this->conv['family'][$row['family']] . "',
			chargeable='" . $this->chargeable[$row['type']] . "',
			note='" . addslashes($row['description']) . "',
			status=" . $this->status[$row['status']] . ",
 			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$this->conv['project_type']['name'][$row['project_type_id']] = $row['name'];
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_tasks_types.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateProject_codeToTasks() {
		echo "\nVidange de la table : ts_tasks";
		$this->dbDest->exec('TRUNCATE TABLE ts_tasks');
		echo "\nConnexion aux tables : ts_project_code and ts_tasks";
		$query = "SELECT * FROM ts_project_code";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$code = $this->newCode($row);
			$query = "INSERT INTO ts_tasks SET
			task_id='" . $row['project_code_id'] . "',
			code='$code',
			name='" . addslashes($this->conv['project_type']['name'][$row['project_type_id']]) . "',
			invoicing_entity_id='" . $this->conv['compagny_id'][$row['invoicing_entity_id']] . "',
			customer_id='" . $row['client_id'] . "',
			task_type_id='" . $row['project_type_id'] . "',
			closing_date='" . $row['closing_date'] . "',
			intermediate_id='" . $row['type'] . "',
			num_proj='" . $row['num_project_code'] . "',
			start_date='" . $row['start_date'] . "',
			end_date='" . $row['end_date'] . "',
			project_proposal='0',
			note=NULL,
			status='1',
 			created_time=NOW()";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_tasks.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateProject_typeToTasks() {
		echo "\nConnexion aux tables : ts_project_type and ts_tasks";
		$query = "SELECT * FROM ts_project_type WHERE type<>'Chargeable'";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$code = $this->newCode2($row);
			$query = "INSERT INTO ts_tasks SET
			code='$code',
			name='" . addslashes($this->conv['project_type']['name'][$row['project_type_id']]) . "',
			invoicing_entity_id='" . $this->conv['compagny_id'][1] . "',
			customer_id='" . $this->conv['compagny_id'][1] . "',
			task_type_id='" . $row['project_type_id'] . "',
			closing_date='2050-12-31',
			intermediate_id='0',
			num_proj='0',
			start_date='2010-01-01',
			end_date='2050-12-31',
			project_proposal='0',
			note=NULL,
			status='1',
 			created_time=NOW()";
			$this->dbDest->exec($query);
			$this->conv['project_type']['list'][$row['project_type_id']] = $this->dbDest->lastInsertId();
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_tasks_types.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	private function newCode($row) {
		$query = "SELECT code FROM ts_entities WHERE entity_id='" . $this->conv['compagny_id'][$row['invoicing_entity_id']] . "'";
		$result = $this->dbDest->query($query);
		list($codecompany) = $result->fetch();
		$codecompany = (empty($codecompany)) ? '????' : $codecompany;

		$query = "SELECT code FROM ts_entities WHERE entity_id='" . $row['client_id'] . "'";
		$result = $this->dbDest->query($query);
		list($codecustomer) = $result->fetch();
		$codecustomer = (empty($codecustomer)) ? '????' : $codecustomer;

		$query = "SELECT code, name FROM ts_tasks_types WHERE task_type_id='" . $row['project_type_id'] . "'";
		$result = $this->dbDest->query($query);
		list($codetask, $nametask) = $result->fetch();
		$codetask = (empty($codetask)) ? '????' : $codetask;

		$date = $row['closing_date'];
		$datetime3 = new \DateTime($date);
		$closing_date_text = (empty($date)) ? '????' : $datetime3->format('Y/m');

		$query = "SELECT code FROM ts_entities WHERE entity_id='" . $row['type'] . "'";
		$result = $this->dbDest->query($query);
		list($codeintermediate) = $result->fetch();
		$codeintermediate = (empty($codeintermediate)) ? 'DIRECT' : $codeintermediate;

		$num_proj = $row['num_project_code'];
		$num_proj = (empty($num_proj)) ? '????' : sprintf("%04d", $num_proj);

		// Création du code projet
		return $codecompany . '-' . $codecustomer . '-' . $codetask . '-' . $closing_date_text . '-' . $codeintermediate . '-' . $num_proj;
	}

	private function newCode2($row) {
		$query = "SELECT code FROM ts_entities WHERE entity_id='" . $this->conv['compagny_id'][1] . "'";
		$result = $this->dbDest->query($query);
		list($codecompany) = $result->fetch();
		$codecompany = (empty($codecompany)) ? '????' : $codecompany;

		$query = "SELECT code, name FROM ts_tasks_types WHERE task_type_id='" . $row['project_type_id'] . "'";
		$result = $this->dbDest->query($query);
		list($codetask, $nametask) = $result->fetch();
		$codetask = (empty($codetask)) ? '????' : $codetask;

		// Création du code projet
		return $codecompany . '-' . $codetask . '-' . $nametask;
	}

	public function updateProject_code_userToTasks_users() {
		echo "\nVidange de la table : ts_tasks_users";
		$this->dbDest->exec('TRUNCATE TABLE ts_tasks_users');
		echo "\nConnexion aux tables : ts_project_code_user and ts_tasks_users";
		$query = "SELECT * FROM ts_project_code_user";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			if (empty($row['project_code_id']) && empty($row['user_id']))
				continue;
			$query = "INSERT INTO ts_tasks_users SET
			task_id='" . $row['project_code_id'] . "',
			user_id='" . $row['user_id'] . "',
 			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_tasks_users.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateProject_typeToTasks_users() {
		echo "\nConnexion aux tables : ts_tasks_users";
		$query = "SELECT * FROM ts_users WHERE status='1' AND user_id<>'0'";
		$result = $this->dbDest->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			reset($this->conv['project_type']['list']);
			foreach ($this->conv['project_type']['list'] as $key => $val) {
				echo $key . ' ' . $val;
				$query = "INSERT INTO ts_tasks_users SET
				task_id='" . $val . "',
				user_id='" . $row['user_id'] . "',
	 			created_time=NOW()";
				echo $query . "\n\n";
				$this->dbDest->exec($query);
				$i ++;
			}
		}
		echo "\nAjout de $i lignes dans ts_tasks_users.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateTimesToTimesheets() {
		echo "\nVidange de la table : ts_timsheets";
		$this->dbDest->exec('TRUNCATE TABLE ts_timesheets');
		echo "\nConnexion aux tables : ts_times and ts_timesheets";
		$query = "SELECT * FROM ts_times";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			if ($row['duration'] == '00:00:00')
				echo $row['duration'] . ' ' . $row['log_message'];
			if ($row['duration'] == '00:00:00' && $row['log_message'] == '')
				continue;
			$query = "INSERT INTO ts_timesheets SET
			user_id='" . $row['user_id'] . "',
			period_id='" . $row['period_id'] . "',
			date='" . $row['date'] . "',
			duration='" . $row['duration'] . "',";
			if ($row['project_code_id'] == '0') {
				$query .= "task_id='" . $this->conv['project_type']['list'][$row['project_type_id']] . "',";
			} else {
				$query .= "task_id='" . $row['project_code_id'] . "',";
			}
			$query .= "comment='" . addslashes($row['log_message']) . "',
			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_timesheets.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function updateTimesToTimesheets_users() {
		echo "\nVidange de la table : ts_timsheets_users";
		$this->dbDest->exec('TRUNCATE TABLE ts_timesheets_users');
		echo "\nConnexion aux tables : ts_period_user and ts_timesheets_users";
		$query = "SELECT * FROM ts_period_user";
		$result = $this->dbOrig->query($query);
		$i = 0;
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$query = "INSERT INTO ts_timesheets_users SET
			period_id='" . $row['period_id'] . "',
			user_id='" . $row['user_id'] . "',
			submit_date='" . $row['submit_date'] . "',
			approval_date='" . $row['approval_date'] . "',
			status='" . $this->submit[$row['status']] . "',
			created_time=NOW()";
			// echo $query . "\n\n";
			$this->dbDest->exec($query);
			$i ++;
		}
		echo "\nAjout de $i lignes dans ts_timesheets.";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}
}

$db = new updateDB();
$db->driverOrig = 'mysql:host=localhost;dbname=timesheet';
$db->usernameOrig = 'root';
$db->passwordOrig = '';
$db->getDBConnexionOrig();
$db->driverDest = 'mysql:host=localhost;dbname=test';
$db->usernameDest = 'root';
$db->passwordDest = '';
$db->getDBConnexionDest();
$db->updateClientToEntities();
$db->updateClientgroupToEntitiesgroups();
$db->updateCompagnyToEntities();
$db->updatePeriodToPeriods();
// $db->updateUserToUsers();
$db->updateTasks_families();
$db->updateProject_typeToTasks_types();
$db->updateProject_codeToTasks();
$db->updateProject_typeToTasks();
$db->updateProject_code_userToTasks_users();
$db->updateProject_typeToTasks_users();
$db->updateTimesToTimesheets();
$db->updateTimesToTimesheets_users();
echo "\nProcess is complete\n";
