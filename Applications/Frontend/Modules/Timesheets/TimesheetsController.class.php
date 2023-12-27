<?php

namespace Applications\Frontend\Modules\Timesheets;

class TimesheetsController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des status_filter
		parent::$param['status_filter']['code'][0] = 'Closed';
		parent::$param['status_filter']['name'][0] = 'Closed';
		parent::$param['status_filter']['code'][1] = 'Open';
		parent::$param['status_filter']['name'][1] = 'Open';

		parent::$param['data_period_id'] = $this->managers->getManagerOf('Timesheets')->getPeriods();
		parent::$param['period_id'] = isset($_REQUEST["period_id"]) ? $_REQUEST["period_id"] : parent::$param['data_period_id']['code'][0];
		parent::$param['user_id'] = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : parent::$user->data['user_id'];
	}

	private function DatesColonnes($periods, $period_id) {
		$currentDayDate = strtotime($periods['start_date'][$period_id]);
		$datetime1 = new \DateTime($periods['start_date'][$period_id]);
		$datetime2 = new \DateTime($periods['end_date'][$period_id]);
		$interval = $datetime1->diff($datetime2);
		$max = (!empty($period_id)) ? $interval->format('%d') + 1 : 0;
		$input = array();
		for($i = 0; $i < $max; $i ++) {
			$input['date'][] = $currentDayDate;
			$input['input'][] = date("m-d-Y", $currentDayDate);
			// $input['option']['code'][] = date("Ymd", $currentDayDate);
			// $input['option']['name'][] = date("d/m/Y", $currentDayDate);
			$currentDayDate = strtotime(date("d M Y H:i:s", $currentDayDate) . " +1 days");
		}
		return $input;
	}

	public function executeView(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		parent::$param['data_date_colonne'] = $this->DatesColonnes(parent::$param['data_period_id'], parent::$param['period_id']);
		parent::$param['return'] = isset($_REQUEST["p"]) ? "timesheetsapprove_index?period_id=" . parent::$param['period_id'] : "timesheetssummary_index";
		parent::$param['data_times'] = $this->managers->getManagerOf('Timesheets')->getTimes();
	}

	public function executeEdit(\Library\HTTPRequest $request) {
		// Check if timesheet isn't submitted or approved
		if ($this->checkStatusTimesheet() !== false)
			parent::$httpResponse->redirect("timesheets_view?period_id=" . parent::$param['period_id']);
		parent::$param['return'] = isset($_REQUEST["p"]) ? "timesheetsapprove_index?period_id=" . parent::$param['period_id'] : "timesheetssummary_index";
		$this->page->addVar('page_token', parent::$security->generer_token());
		parent::$param['data_date_colonne'] = $this->DatesColonnes(parent::$param['data_period_id'], parent::$param['period_id']);
		parent::$param['approval'] = isset($_REQUEST["p"]) ? "timesheetsapprove_index?period_id=" . parent::$param['period_id'] : "timesheetssummary_index";
		parent::$param['data_times'] = $this->managers->getManagerOf('Timesheets')->getTimes();
		parent::$param['data_lignes'] = $this->managers->getManagerOf('Timesheets')->getLignes(parent::$param['period_id'], parent::$param['user_id']);
	}

	public function executeFrmTimesheet (\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('op', 'add');

		$first_name = $last_name = "";
		if ($request->getData('id') != NULL) {
			$query = "SELECT t.period_id, u.user_id, t.task_id, u.first_name, u.last_name
			FROM ts_users AS u, ts_timesheets AS t
			WHERE t.user_id = u.user_id
				AND t.time_id='" . $request->getData('id') . "'";
			$result = parent::$dao->query($query);
			list($period_id, $user_id, $task_id, $first_name, $last_name) = $result->fetch();
		} elseif ($request->getData('user_id') != NULL) {
			$query = "SELECT u.first_name, u.last_name
			FROM ts_users AS u
			WHERE u.user_id = '" . $request->getData('user_id') . "'";
			$result = parent::$dao->query($query);
			list($first_name, $last_name) = $result->fetch();
		}
		$this->page->addVar('first_name', $first_name);
		$this->page->addVar('last_name', $last_name);

		parent::$param['period_id'] = (!empty($period_id)) ? $period_id : parent::$param['period_id'];
		parent::$param['user_id'] = (!empty($user_id)) ? $user_id : parent::$param['user_id'];

		if (isset($_REQUEST["p"]))
		{
			switch ($_REQUEST["p"]) {
				case 2:
					parent::$param['return'] = "timesheetsreports_index?report_id=5";
					break;
				case 1:
					parent::$param['return'] = "timesheets_edit?period_id=".parent::$param['period_id']."&user_id=".parent::$param['user_id']."&p=1";
					break;
				default:
					parent::$param['return'] = "timesheets_edit?period_id=".parent::$param['period_id'];
					break;
			}
		}

		$task_id = (!empty($task_id)) ? $task_id : $request->getData('task_id');
		$this->page->addVar('task_id', $task_id);

		$fct = new \Library\Functions();
		parent::$param['data_date_colonne'] = $this->DatesColonnes(parent::$param['data_period_id'], parent::$param['period_id']);
		parent::$param['data_task_id'] = $this->managers->getManagerOf('Timesheets')->getTasks(parent::$param['data_period_id']);

		$readonly = ($request->getData('id') == NULL && $request->getData('date') == NULL) ? "" : " readonly" ;
		$this->page->addVar('readonly', $readonly);

		if (!empty($readonly)) {
			$date = date("Y-m-d", $this->checkDate($request, parent::$param['period_id']));
		} else {
			$date = "";
		}
		$this->page->addVar('date', $date);

		// Visualisation uniquement
		$this->page->addVar('time_id', $request->getData('id'));
		if ($request->getData('id')) {
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
			$selfManager = $this->managers->getManagerOf('Timesheets');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('time_id'));
					//$json[4] = date("Ymd", strtotime($json[4]));
					$json[5] = date("H:i", strtotime($json[5]));
					break;
				case 'add' :
					if ($selfManager->timeIsAvailable($request) != true) {
						$json['reponse'] = "Project name already exists encoded hours and the desired date.";
					} else
						$json = $selfManager->add($request);
					break;
				case 'edit' :
					if ($selfManager->timeIsAvailable($request) != true) {
						$json['reponse'] = "You can not change the project name of the encoded hours. Project name already exists with hours encoded.";
					} else
						$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$json = $selfManager->deleteLine($request->postData('id'));
					if ($json['reponse'] === true) {
						$json['msg'] = 'All time project for this period have been removed.';
					} else {
						$json['msg'] = 'The update of the database is not successful!';
					}
					break;
				case 'delete_time' :
					$json = $selfManager->delete($request->postData('time_id'));
					break;
				case 'save_time' :
					$json = $selfManager->saveTime($request);
					break;
				case 'displayCodes' :
					$fct = new \Library\Functions();
					if ($request->postData('type') == 'last') {
						$array = $selfManager->displayCodes($request->postData('period_id'));
					} else {
						$array = $selfManager->getTasks(parent::$param['data_period_id']);
					}
					$output['reponse'] = true;
					$output['option'] = '<option value="">Select Project Code</option>';
					$output['option'] .= $fct->droplist("", $array);
					$json = $output;
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	private function checkStatusTimesheet() {
		$period_id = parent::$param['period_id'];
		$user_id = parent::$param['user_id'];
		$query = "SELECT status
		FROM ts_timesheets_users
		WHERE period_id='$period_id' AND user_id='$user_id'";
		$result = parent::$dao->query($query)->fetch();
		return $result;
	}

	private function checkDate(\Library\HTTPRequest $request, $period) {
		$startDate = strtotime(parent::$param['data_period_id']['start_date'][$period]);
		$endDate = strtotime(parent::$param['data_period_id']['end_date'][$period]);
		$time = time();
		if ($time > $startDate && $time < $endDate) {
			$date = $time;
		} else {
			$date = $startDate;
		}
		return (empty($request->getData('date'))) ? $date : $request->getData('date');
	}
}