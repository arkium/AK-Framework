<?php

// Définir la période selon la date du jour + vérifier si période ouverte sinon pas de saisie

namespace Applications\Frontend\Modules\TimesheetsDirect;

class TimesheetsDirectController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		parent::$param['data_period_id'] = $this->managers->getManagerOf('Timesheets')->getPeriods();
		parent::$param['period_id'] = isset($_REQUEST["period_id"]) ? $_REQUEST["period_id"] : $this->checkPeriod();
		parent::$param['user_id'] = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : parent::$user->data['user_id'];
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('return', 'logout');
		$this->page->addVar('op', 'add');
		$this->page->addVar('time_id', '');
		$this->page->addVar('hour_id', '');
		// Vérifier si tableau avec des périodes existe
		$task = (array_key_exists('code',parent::$param['data_period_id'])) ? $this->managers->getManagerOf('Timesheets')->getTasks(parent::$param['data_period_id']) : null;
		parent::$param['data_task_id'] = $task;
		// Visualisation uniquement
		if ($request->getData('id')) {
			$this->page->addVar('op', 'view');
			$this->page->addVar('time_id', $request->getData('id'));
		}
		// Vérifier si un pointage n'est pas ouvert
		$lastID = $this->managers->getManagerOf('Timesheets')->lastTimeDirect(parent::$param['user_id']);
		if ($lastID['reponse']) {
			$this->page->addVar('return', 'timeclock');
			$this->page->addVar('op', 'edit');
			$this->page->addVar('time_id', $lastID['time_id']);
			$this->page->addVar('hour_id', $lastID['hour_id']);
		}
	}

	public function executeAdd(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('return', 'timeclock_add');
		$this->page->addVar('op', 'addtime');
		// Vérifier si tableau avec des périodes existe
		$task = (array_key_exists('code',parent::$param['data_period_id'])) ? $this->managers->getManagerOf('Timesheets')->getTasks(parent::$param['data_period_id'], null, null, true) : null;
		parent::$param['data_task_id'] = $task;
		// Liste des utilisateurs pour ajouter des pointages
		$query = "SELECT user_id, code, first_name, last_name, status FROM ts_users WHERE user_id>0 ORDER BY code";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_user_id']['code'][] = $row['user_id'];
			parent::$param['data_user_id']['name'][] = $row['code'] . ' - ' . $row['last_name'] . ', ' . $row['first_name'];
			parent::$param['data_user_id']['status'][] = ($row['status'] == '0') ? false : true;
			parent::$param['data_user_id']['option'][] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Timesheets');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getTimeDirect($request->postData('hour_id'));
					break;
				case 'add' :
					$json = $selfManager->saveTimeDirect($request);
					break;
				case 'edit' :
					$json = $selfManager->saveTimeDirect($request);
					break;
				case 'note' :
					$json = $selfManager->noteTask($request);
					break;
				case 'addtime' :
					$json = $selfManager->addTime($request);
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	private function checkPeriod() {
		$date = date("Y-m-d", time());
		// Récupération de la période en cours selon la date d'aujourd'hui
		$query = "SELECT period_id
FROM ts_periods	WHERE (end_date > '$date' AND start_date < '$date') OR end_date='$date' OR start_date='$date'
ORDER BY start_date DESC LIMIT 1";
		$lastDate = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		return $lastDate['period_id'];
	}
}