<?php

namespace Applications\Frontend\Modules\Periods;

class PeriodsController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

    public function executeFrmPeriod(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
        $this->page->addVar('return', 'periods_index');
        $this->page->addVar('op', 'add');
        // Visualisation uniquement
        $this->page->addVar('period_id', $request->getData('id'));
        if ($request->getData('id')) {
            $this->page->addVar('op', 'view');
        }
        // Edition uniquement
        if ($request->getData('op') == 'edit') {
            $this->page->addVar('op', 'edit');
        }
	}

	public function executeList(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'start_date',
				'end_date',
				'status_'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
			period_id,
			start_date,
			end_date,
			IF(status=0,'Closed','Open') AS status_
			FROM ts_periods
		) AS view";
		$ini->sIndexColumn = "period_id";
		$ini->sTable = "ts_periods";
		$ini->sDisplay = array();
		$list = $this->managers->getManagerOf('Periods')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Periods');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('period_id'));
					break;
				case 'add' :
					$json = $this->checkDates($request);
					if ($json['reponse'] === true) {
						$start_date = (string) $request->postData('start_date');
						$end_date = (string) $request->postData('end_date');
						$query = "
						SELECT DISTINCT status
						FROM ts_periods
						WHERE (end_date>='$start_date' AND end_date<='$end_date')
						OR (start_date>='$start_date' AND start_date<='$end_date')
						LIMIT 1";
						$result = parent::$dao->query($query)->fetch();
						if ($result !== false)
							$json['reponse'] = "This period already exists in the database.";
						else {
							$json = $selfManager->add($request);
						}
					}
					break;
				case 'edit' :
					$json = $this->checkDates($request);
					if ($json['reponse'] === true) {
						$json = $selfManager->modify($request);
					}
					break;
				case 'delete' :
					$query = "
					SELECT DISTINCT period_id
					FROM ts_timesheets
					WHERE period_id='" . $request->postData('period_id') . "'
					LIMIT 1";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a Period used in the Timesheet.";
					else {
						$json = $selfManager->delete($request->postData('period_id'));
					}
					break;
				case 'year_button' :
					$json = $this->yearButton($request);
					break;
				case 'add_periods' :
					$json = $this->addPeriods($request);
					break;
				case 'delete_periods' :
					$json = $this->deletePeriods($request);
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	private function checkDates(\Library\HTTPRequest $request) {
		$start_date = (string) $request->postData('start_date');
		$end_date = (string) $request->postData('end_date');
		try {
			$datetime1 = new \DateTime($start_date);
			$datetime2 = new \DateTime($end_date);
		} catch (Exception $e) {
			return $output['reponse'] = $e->getMessage();
		}

		$start_day = $datetime1->format('d');
		$start_month = $datetime1->format('m');
		$start_year = $datetime1->format('Y');
		$end_day = $datetime2->format('d');
		$end_month = $datetime2->format('m');
		$end_year = $datetime2->format('Y');

		$interval = $datetime1->diff($datetime2);
        $diff = $interval->format('%R%a');

		$output['reponse'] = true;
		// Vérification si la date de fin est valide
		if ((!checkdate($end_month, $end_day, $end_year))) {
			$output['reponse'] = "The end date is invalid in format YYYY-MM-DD.";
		}
		// Vérification si la date de début est valide
		if ((!checkdate($start_month, $start_day, $start_year))) {
			$output['reponse'] = "The stard date is invalid in format YYYY-MM-DD.";
		}
		// Vérification si start_date > end_date
		if ($diff < 0) {
			$output['reponse'] = "The end date is smaller than the start date.";
		}
		return $output;
	}

	private function addPeriods(\Library\HTTPRequest $request) {
		$year = (int) $request->postData('year');
		$query = "
		SELECT DATE_FORMAT(start_date,'%Y') AS year
		FROM ts_periods
		GROUP BY year";
		$year_period = parent::$dao->query($query)->fetchAll(\PDO::FETCH_COLUMN);
		if (in_array($year, $year_period)) {
			$output['reponse'] = "This period already exists in the database.";
		} else {
			$date_period['start'] = array();
			$date_period['end'] = array();
			for($i = 1; $i <= 12; $i ++) {
				$date_period['start'][] = date("Y-m-d", mktime(0, 0, 0, $i, 1, $year));
				$date_period['end'][] = date("Y-m-d", mktime(0, 0, 0, $i, 15, $year));
				$date_period['start'][] = date("Y-m-d", mktime(0, 0, 0, $i, 16, $year));
				$date_period['end'][] = date("Y-m-d", mktime(0, 0, 0, $i, date("t", mktime(0, 0, 0, $i, 16, $year)), $year));
			}
			$MAJ = true;
			foreach ($date_period['start'] as $key => $value) {
				$query = "
				INSERT INTO ts_periods SET
					start_date='$value',
					end_date='" . $date_period['end'][$key] . "',
					status=0,
					created_time=NOW()";
				$MAJ = (parent::$dao->exec($query) !== false) ? $MAJ : false;
			}
			$output['reponse'] = ($MAJ) ? true : 'The update of the database is not successful!';
			$output['msg'] = ($output['reponse']) ? 'The periods were correctly added to the database' : '';
		}
		return $output;
	}

	private function deletePeriods(\Library\HTTPRequest $request) {
		$year = (int) $request->postData('year');
		$query = "
		SELECT DATE_FORMAT(start_date,'%Y') AS year
		FROM ts_periods AS p, ts_timesheets AS t
		WHERE p.period_id=t.period_id
		GROUP BY year";
		$year_period = parent::$dao->query($query)->fetchAll(\PDO::FETCH_COLUMN);
		if (in_array($year, $year_period)) {
			$output['reponse'] = "You cannot delete a period for which there are timesheet.";
		} else {
			$query = "
			DELETE FROM ts_periods
			WHERE start_date>='$year-01-01'
				AND start_date<='$year-12-31'";
			$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
			$output['msg'] = ($output['reponse']) ? 'The periods have been deleted from the database' : '';
		}
		return $output;
	}

	private function yearButton(\Library\HTTPRequest $request) {
		$year = (int) $request->postData('year');
		$list = (int) $request->postData('list');
		$list = (empty($list)) ? false : true;
		if (empty($year))
			$year = date("Y");
		$query = "
		SELECT DATE_FORMAT(start_date,'%Y') AS year
		FROM ts_periods
		GROUP BY year";
		$year_period = parent::$dao->query($query)->fetchAll(\PDO::FETCH_COLUMN);
		$i = $year - 5;
		$content = "";
		if ($list) {
			$content .= (empty($year_period)) ? '<option value="0">None</option>\n' : '';
			foreach ($year_period as $key => $value) {
				switch ($value) {
					case $year :
						$content .= "<option value=\"$value\">$value</option>\n";
                        $output['selected'] = $value;
						break;
					default :
						$content .= "<option value=\"$value\">$value</option>\n";
				}
			}
		} else {
			while ($i <= $year + 5) {
				if (!in_array($i, $year_period)) {
					switch ($i) {
						case $year :
							$content .= "<option value=\"$i\">$i</option>\n";
                            $output['selected'] = $i;
							break;
						default :
							$content .= "<option value=\"$i\">$i</option>\n";
					}
				}
				$i ++;
			}
		}
		$output['reponse'] = $content;
		return $output;
	}
}