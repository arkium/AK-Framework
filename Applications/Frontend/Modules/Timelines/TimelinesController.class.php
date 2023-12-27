<?php

namespace Applications\Frontend\Modules\Timelines;

class TimelinesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		parent::$param['data_timeline'] = $this->getTimeline($request->getData('task_id'));
		parent::$param['data_task'] = $this->getTask($request->getData('task_id'));
		parent::$param['data_timetotal'] = $this->getTimeTotal($request->getData('task_id'));
	}

	private function getTimeline($task_id) {
		// Récupération des données Timesheets du projet
		$output = null;
		$query = "SELECT t.date, t.duration, t.comment, CONCAT(u.first_name, ' ', u.last_name) as name
		FROM
			ts_timesheets t, ts_users u
		WHERE t.user_id=u.user_id
			AND task_id='$task_id'
		ORDER BY t.date DESC";
		$result = parent::$dao->query($query);
		return $result;
	}

	private function getTask($task_id) {
		// Récupération des données du projet
		$output = null;
		$query = "SELECT t.code, t.name, t.status, e.code AS codeclient, e.organisation AS client
		FROM ts_tasks AS t, ts_entities AS e
		WHERE
			t.customer_id = e.entity_id
			AND t.task_id='$task_id'";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		$content = "";
		foreach ($output as $row) {
			$status = ($row['status'] == '1') ? '<span class="label label-success">Open</span>' : '<span class="label label-danger">Close</span>';
			$content .= '
				<div>
					<h1>
						<span>' . $row['code'] . ' - ' . $row['codeclient'] . ' - ' . $row['client'] . ' / ' . $status . '</span><br>
						<small>' . $row['name'] . '</small>
					</h1>
				</div>';
		}
		return $content;
	}

	private function getTimeTotal($task_id) {
		$query = "SELECT ROUND(SUM(TIME_TO_SEC(duration)/3600),2) AS time
		FROM ts_timesheets AS t
		WHERE t.task_id='$task_id'";
		$result = parent::$dao->query($query);
		$row = $result->fetch();
		$time = (empty($row['time'])) ? '0' : $row['time'];
		$content = '
			<div>
				<h4 class="pull-right">Total : ' . $time . ' hours</h4>
			</div>';
		return $content;
	}
}