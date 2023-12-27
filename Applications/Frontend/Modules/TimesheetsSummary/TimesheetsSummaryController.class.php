<?php

namespace Applications\Frontend\Modules\TimesheetsSummary;

class TimesheetsSummaryController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des status_timesheet
		parent::$param['status_timesheet']['code'][0] = '0';
		parent::$param['status_timesheet']['name'][0] = 'Submitted';
		parent::$param['status_timesheet']['code'][1] = '1';
		parent::$param['status_timesheet']['name'][1] = 'Approved';
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeList(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'period',
				'status_',
				'd1',
				'd2',
				'total',
				'status_timesheet'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS * FROM (
			SELECT
				period_id,
				CONCAT(start_date, ' - ', end_date) AS period,
				IF(status=0,'Closed','Open') AS status_,
				TIME_FORMAT(SEC_TO_TIME(duration1),'%H:%i') AS d1,
				TIME_FORMAT(SEC_TO_TIME(duration2),'%H:%i') AS d2,
				TIME_FORMAT(SEC_TO_TIME(IFNULL(duration1,0)+IFNULL(duration2,0)),'%H:%i') AS total,
				status_timesheet,
				user_id
			FROM ts_timesheets_approval
		) AS view";
		$ini->sIndexColumn = "period_id";
		$ini->sTable = "ts_timesheets_approval";
		$ini->sWhere = "WHERE user_id='" . parent::$user->data['user_id'] . "'";
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
					$query = "INSERT INTO ts_timesheets_users
					VALUES ('', '" . $request->postData('period_id') . "', '" . parent::$user->data['user_id'] . "', NOW(), '', 'SUBMIT', '', NOW())";
					$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					if ($request->postData('email') === '1') {
						$json['reponse'] = $this->timesheet_email_forapproval($request->postData('period_id'), parent::$user->data['user_id']);
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	private function timesheet_email_forapproval($period_id, $user_id) {
		try {
			$query = "SELECT email_address, first_name, last_name FROM ts_users WHERE level='1' AND status='1' LIMIT 1";
			$data_approver = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
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
		$data_replace["first_name_approver"] = $data_approver['first_name'];
		$data_replace["first_name_submitter"] = $data_submitter['first_name'];
		$data_replace["name_submitter"] = $data_submitter['last_name'];

		$email = new \Library\Email();
		if ($email->checkServer())
			return 'You have no email server!';
		$email->setfrom(FROM_EMAIL);
		$email->data_replace = $data_replace;
		$email->subject = 'Your action required - Timesheet approval period #date_start# to #date_end#';
		$email->setFilePathMessage(getcwd() . DIR_TPL_EMAIL . 'for_approval.html');
		$email->destinationEmail = $data_approver['email_address'];
		$sendEmail = ($email->sendEmail()) ? true : 'The email could not be sent!';

		$email->subject = 'Information - Timesheet period #date_start# to #date_end# - Submitted';
		$email->setFilePathMessage(getcwd() . DIR_TPL_EMAIL . 'submitted.html');
		$email->destinationEmail = $data_submitter['email_address'];
		$sendEmail = ($email->sendEmail() && $sendEmail) ? true : 'The email could not be sent!';
		return $sendEmail;
	}
}