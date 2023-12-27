<?php

namespace Applications\Frontend\Modules\TasksUsers;

class TasksUsersController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des utilisateurs
		$query = "SELECT user_id, code, CONCAT(last_name, ', ', first_name) AS name
		FROM ts_users WHERE user_id > '0' AND status = '1' ORDER BY code";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_user_code'][$row['user_id']] = $row['code'];
			parent::$param['data_user_name'][$row['user_id']] = $row['name'];
		}
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeList(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'code'
		);

		$fct = function ($aRow, $key, $var = '') {
			if ($aRow[$key] != '0') {
				return "<input type=\"checkbox\" data-task_user_id=\"" . $aRow['task_user_id'] . "\" data-task_id=\"" . $aRow['task_id'] . "\" data-user_id=\"" . $key . "\" value=\"1\" checked=\"checked\" />";
			} else {
				return "<input type=\"checkbox\" data-task_user_id=\"" . $aRow['task_user_id'] . "\" data-task_id=\"" . $aRow['task_id'] . "\" data-user_id=\"" . $key . "\" value=\"1\" />";
			}
		};

		// Liste des utilisateurs
		$query = "SELECT user_id, code, CONCAT(last_name, ', ', first_name) AS name FROM ts_users WHERE user_id > '0' AND status = '1' ORDER BY code";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			$data_user_code[$row['user_id']] = $row['user_id'];
			$keys[] = $row['user_id'];
		}
		ksort($data_user_code);
		$ini->aColumnsDisplay = array_merge($ini->aColumnsDisplay, $keys);

		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
			v.task_id,
			ANY_VALUE(v.code) AS code,
			ANY_VALUE(v.name) AS name,
			ANY_VALUE(v.user_id) AS user_id,
			ANY_VALUE(v.task_user_id) AS task_user_id";
		if (is_array($data_user_code)) {
			reset($data_user_code);
			foreach ($data_user_code as $key => $val) {
				$ini->sQuery .= ", SUM(IF(ANY_VALUE(v.user_id) = '$key', ANY_VALUE(v.nbre), 0)) AS '$val' ";
			}
		}
		$ini->sQuery .= "
			FROM (
				SELECT t.task_id, t.code, t.name, tu.user_id, tu.task_user_id, '1' AS nbre
				FROM ts_tasks AS t
					LEFT JOIN ts_tasks_users AS tu USING (task_id)
				WHERE t.status = '1'
			) AS v
			GROUP BY v.task_id WITH ROLLUP
		) AS view";
		$ini->sIndexColumn = "task_id";
		$ini->sTable = "ts_tasks";
		$ini->sDisplay = array_fill_keys($keys, $fct);
		$ini->sDisplay['code'] = function ($aRow, $key, $var = '') {
			$pop = 'class="pop" data-content="' . $aRow[$key] . ' = ' . $aRow['name'] . '"';
			return "<span $pop>$aRow[$key] - " . $aRow['name'] . "</span>\n";
		};
		$list = $this->managers->getManagerOf('TasksUsers')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('TasksUsers');
			$task = $this->managers->getManagerOf('Tasks')->getUnique($request->postData('task_id'));
			switch ($request->postData('op')) {
				case 'taskassign' :
					if ($request->postData('val') == "true") {
						$json = $selfManager->add($request);
						$json['title'] = ($json['reponse']) ? 'Add' : 'Information!';
						$json['msg'] = ($json['reponse']) ? 'The user has been added to ' . $task[2] : 'The user was not updated correctly in ' . $task[2];
					} else {
						$json = $selfManager->delete($request->postData('task_user_id'));
						$json['title'] = ($json['reponse']) ? 'Update' : 'Information!';
						$json['msg'] = ($json['reponse']) ? 'The user has been removed from ' . $task[2] : 'The user was not updated correctly in ' . $task[2];
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}