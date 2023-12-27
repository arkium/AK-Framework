<?php

namespace Applications\Frontend\Modules\TimesheetsApprove;

class TimesheetsApproveController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des status_filter
		parent::$param['status_filter']['code'][0] = 'Closed';
		parent::$param['status_filter']['name'][0] = 'Closed';
		parent::$param['status_filter']['code'][1] = 'Open';
		parent::$param['status_filter']['name'][1] = 'Open';
		// Liste des status_timesheet
		parent::$param['status_timesheet']['code'][0] = '0';
		parent::$param['status_timesheet']['name'][0] = 'Submitted';
		parent::$param['status_timesheet']['code'][1] = '1';
		parent::$param['status_timesheet']['name'][1] = 'Approved';

		parent::$param['data_period_id'] = $this->managers->getManagerOf('TimesheetsApprove')->getPeriods();
		// Vérifier si tableau avec des périodes existe
		$period = (array_key_exists('code',parent::$param['data_period_id'])) ? parent::$param['data_period_id']['code'][0] : null;
		parent::$param['period_id'] = isset($_REQUEST["period_id"]) ? $_REQUEST["period_id"] : $this->checkPeriod($period);
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeList(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'code',
				'name',
				'status_',
				'd1',
				'd2',
				'total',
				'status_timesheet'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS * FROM (
			SELECT
				a.user_id,
				e.code,
				a.name,
				IF(a.status=0,'Closed','Open') AS status_,
				TIME_FORMAT(SEC_TO_TIME(a.duration1),'%H:%i') AS d1,
				TIME_FORMAT(SEC_TO_TIME(a.duration2),'%H:%i') AS d2,
				TIME_FORMAT(SEC_TO_TIME(IFNULL(a.duration1,0)+IFNULL(a.duration2,0)),'%H:%i') AS total,
				a.status_timesheet,
				a.period_id,
				a.submit_date,
				a.approval_date
			FROM ts_timesheets_approval AS a, ts_users AS u, ts_entities AS e
			WHERE
				a.user_id=u.user_id
				AND u.company_id=e.entity_id
			ORDER BY total DESC, e.entity_id, a.user_id ASC
		) AS view";

		$ini->sIndexColumn = "user_id";
		$ini->sTable = "ts_timesheets_approval";
		$ini->sWhere = "WHERE period_id='" . parent::$param['period_id'] . "'";
		$ini->sDisplay = array(
				"status_timesheet" => function ($aRow, $key, $var = '') {
					return isset($var['status_timesheet']['name'][$aRow[$key]]) ? $var['status_timesheet']['name'][$aRow[$key]] : null;
				}
		);
		$list = $this->managers->getManagerOf('TimesheetsApproval')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			switch ($request->postData('op')) {
				case 'submit' :
				// $query = "INSERT INTO ts_timesheets_users
				// VALUES ('', '" . $request->postData('period_id') . "', '" . $request->postData('user_id') . "', NOW(), '', 'SUBMIT', '', NOW())";
				// $json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				// if ($request->postData('email') === '1') {
				// $json['reponse'] = $this->timesheet_email_forapproval($request->postData('period_id'), $request->postData('user_id'));
				// }
				// break;
				case 'approval' :
					$query = "UPDATE ts_timesheets_users
					SET status='1', approval_date=NOW()
					WHERE period_id='" . $request->postData('period_id') . "' AND user_id='" . $request->postData('user_id') . "'";
					$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					if ($json['reponse'] === true)
						$json['message'] = 'The timesheet has been approved.';
					if ($request->postData('email') === '1') {
						$json['reponse'] = $this->timesheet_email_approved($request->postData('period_id'), $request->postData('user_id'));
					}
					break;
				case 'open' :
					$query = "DELETE FROM ts_timesheets_users
					WHERE period_id='" . $request->postData('period_id') . "' AND user_id='" . $request->postData('user_id') . "'";
					$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					if ($json['reponse'] === true)
						$json['message'] = 'The timesheet was opened.';
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	private function timesheet_email_approved($period_id, $user_id) {
		try {
			$query = "SELECT email_address, first_name, last_name FROM ts_users WHERE user_id='$user_id'";
			$data_submitter = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
			$query = "SELECT start_date, end_date FROM ts_periods WHERE period_id='$period_id'";
			$data_period = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		} catch (\Library\ArkiumException $e) {
			$output['reponse'] = 'The database is not able to be read!<br/>';
			$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
			$output['reponse'] .= "Syntax Error: " . $e->getMessage();
		}
		$data_replace["date_start"] = $data_period['start_date'];
		$data_replace["date_end"] = $data_period['end_date'];
		$data_replace["first_name_submitter"] = $data_submitter['first_name'];
		$data_replace["name_submitter"] = $data_submitter['last_name'];

		$email = new \Library\Email();
		if ($email->checkServer())
			return 'You have no email server!';
		$email->setfrom(FROM_EMAIL);
		$email->data_replace = $data_replace;
		$email->subject = 'Information - Timesheet period #date_start# to #date_end# - Approved';
		$email->setFilePathMessage(getcwd() . DIR_TPL_EMAIL . 'approved.html');
		$email->destinationEmail = $data_submitter['email_address'];
		return ($email->sendEmail()) ? true : 'The email could not be sent!';
	}

	/**
	 * Récupération de la période selon la date d'aujourd'hui
	 * @param mixed $return
	 * @return string period_id
	 */
	private function checkPeriod($return = null) {
		$date = date("Y-m-d", time());
		$query = "SELECT period_id
FROM ts_periods
WHERE (end_date > '$date' AND start_date < '$date') OR end_date='$date' OR start_date='$date'
ORDER BY start_date DESC
LIMIT 1";
		$lastDate = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		$lastDate = ($lastDate === false) ? $return : $lastDate ;
		return $lastDate['period_id'];
	}
}