<?php

namespace Applications\Frontend\Modules\TimesheetsHours;

class TimesheetsHoursController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('time_id', $request->getData('id'));

		switch ($_REQUEST["p"]) {
			case 2:
				$this->page->addVar('return', "frmtimesheet?id=".$request->getData('id')."&op=edit&p=2");
				break;
			default:
				$this->page->addVar('return', "frmtimesheet?id=".$request->getData('id')."&op=edit&p=2");
				break;
		}
		//$this->page->addVar('return', "frmtimesheet?id=".$request->getData('id')."&op=edit&p=1");

		$result = $this->title($request);
		$this->page->addVar('first_name', $result['first_name']);
		$this->page->addVar('last_name', $result['last_name']);
		$this->page->addVar('date', $result['date']);
	}

	public function executeList(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
			'start',
			'end',
			'duration'
		);
		$ini->sQuery = "
SELECT SQL_CALC_FOUND_ROWS *
FROM (
	SELECT
	hour_id,
	time_id,
	start,
	end,
	IF(end>start,TIMEDIFF(end, start),'00:00:00') AS duration
	FROM ts_timesheets_hours
) AS view";
		$ini->sWhere = "WHERE time_id = '" . $request->getData("time_id") . "'";
		$ini->sIndexColumn = "hour_id";
		$ini->sTable = "ts_timesheets_hours";
		$ini->sDisplay = array(
				"start" => function ($aRow, $key, $var = '') {
					return date("H:i", strtotime($aRow[$key]));
				},
				"end" => function ($aRow, $key, $var = '') {
					return date("H:i", strtotime($aRow[$key]));
				},
				"duration" => function ($aRow, $key, $var = '') {
					return date("H:i", strtotime($aRow[$key]));
				}
		);
		$list = $this->managers->getManagerOf('TimesheetsHours')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeFrmTimesheetHours(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('op', 'add');
		$this->page->addVar('hour_id', $request->getData('hour_id'));
		$this->page->addVar('time_id', $request->getData('id'));
		$return = (!empty($request->getData('id'))) ? "timesheetshours_index?id=".$request->getData('id'): "timesheets_edit?period_id=".$request->getData('period_id')."&user_id=".$request->getData('user_id')."&p=1";
		$this->page->addVar('return', $return);
		$this->page->addVar('period_id', $request->getData('period_id'));
		$this->page->addVar('user_id', $request->getData('user_id'));
		$this->page->addVar('task_id', $request->getData('task_id'));

		$result = $this->title($request);
		$this->page->addVar('first_name', $result['first_name']);
		$this->page->addVar('last_name', $result['last_name']);
		$this->page->addVar('date', $result['date']);

		// View uniquement
		if ($request->getData('hour_id')) {
			$this->page->addVar('op', 'view');
		}
		// Edition uniquement
		if ($request->getData('op') == 'edit') {
			$this->page->addVar('op', 'edit');
		}
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('TimesheetsHours');
			$db_Timesheets = $this->managers->getManagerOf('Timesheets');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('hour_id'));
					$json[2] = date("H:i", strtotime($json[2]));
					$json[3] = date("H:i", strtotime($json[3]));
					break;
				case 'add' :
					$result = true;
					if (empty($request->postData('time_id')) && !empty($request->postData('period_id')) && !empty($request->postData('date'))) {
						$json = $db_Timesheets->add($request);
						$result = $json['reponse'];
						$request ->postSet('time_id', $json['lastID']);
					}
					if ($result) {
						$json = $selfManager->add($request);
						$json = $selfManager->updateDuration($json, $request->postData('time_id'));
					}
					break;
				case 'edit' :
					$json = $selfManager->modify($request);
					$json = $selfManager->updateDuration($json, $request->postData('time_id'));
					break;
				case 'delete' :
					$json = $selfManager->delete($request->postData('hour_id'));
					$json = $selfManager->updateDuration($json, $request->postData('time_id'));
					if ($json['reponse'] === true) {
						$json['msg'] = _('Le pointage a été supprimé.');
					} else {
						$json['msg'] = _('La mise à jour de la base de données n\'a pas réussie!');
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	/**
	 * Récupérer le Nom, Prénom de l'utisateur + la date de pointage
	 * @param mixed $time_id
	 * @param mixed $user_id
	 * @param mixed $date
	 * @return mixed
	 */
	private function title(\Library\HTTPRequest $request) {
		$result = NULL;
		$time_id = $request->getData('id');
		$hour_id = $request->getData('hour_id');
		$user_id = $request->getData('user_id');
		if ($hour_id != NULL) {
			$query = "SELECT t.date, u.first_name, u.last_name
FROM ts_timesheets AS t
LEFT JOIN ts_users AS u USING (user_id)
LEFT JOIN ts_timesheets_hours AS th USING (time_id)
WHERE th.hour_id='$hour_id'";
			$result = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		} elseif ($time_id != NULL) {
			$query = "SELECT t.date, u.first_name, u.last_name
FROM ts_timesheets AS t
LEFT JOIN ts_users AS u USING (user_id)
WHERE t.time_id='$time_id'";
			$result = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		} elseif ($user_id != NULL) {
			$query = "SELECT u.first_name, u.last_name
FROM ts_users AS u
WHERE u.user_id = '$user_id'";
			$result = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
			$result['date'] = $request->getData('date');
		}
		return $result;
	}

}