<?php

namespace Applications\Frontend\Modules\Tasks;

class TasksController extends \Library\BackController {

	public function __construct() {
		parent::__construct();

		// Liste des Companies
		$query = "SELECT entity_id, code, organisation, status
		FROM ts_entities
		WHERE entity_type_id=1
		ORDER BY code";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_invoicing_entity_id']['code'][$row['entity_id']] = $row['entity_id'];
			parent::$param['data_invoicing_entity_id']['name'][$row['entity_id']] = $row['code'] . ' - ' . $row['organisation'];
			parent::$param['data_invoicing_entity_id']['option'][$row['entity_id']] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}

		// Liste des clients
		/*
		$query = "SELECT e.entity_id, e.code, e.organisation, e.status, eg.name
		FROM ts_entities AS e
		LEFT JOIN ts_entities_groups AS eg USING (entity_group_id)
		WHERE e.entity_type_id=2 OR e.entity_type_id=1
		ORDER BY eg.name ASC, e.code ASC";
		*/
		$query = "SELECT e.entity_id, e.code, e.organisation, e.status, eg.name
		FROM ts_entities AS e
		LEFT JOIN ts_entities_groups AS eg USING (entity_group_id)
		WHERE e.entity_type_id=2 OR e.entity_type_id=1
		ORDER BY e.code ASC";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_customer_id']['code'][$row['entity_id']] = $row['entity_id'];
			parent::$param['data_customer_id']['name'][$row['entity_id']] = $row['code'] . ' - ' . $row['organisation'] . ' - '. $row['name'];
			parent::$param['data_customer_id']['group'][$row['entity_id']] = (!empty($row['name'])) ? $row['name'] : 'Companies';
			parent::$param['data_customer_id']['option'][$row['entity_id']] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}

		// Liste Tasks Types
		$query = "SELECT tt.task_type_id, tt.code, tt.name, tt.status, tf.name AS namegroup
		FROM ts_tasks_types AS tt
		LEFT JOIN ts_tasks_families AS tf USING (task_family_id)
		ORDER BY tf.name";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_task_type_id']['code'][$row['task_type_id']] = $row['task_type_id'];
			parent::$param['data_task_type_id']['name'][$row['task_type_id']] = $row['code'] . ' - ' . $row['name'];
			parent::$param['data_task_type_id']['group'][$row['task_type_id']] = (!empty($row['namegroup'])) ? $row['namegroup'] : 'Others';
			parent::$param['data_task_type_id']['option'][$row['task_type_id']] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}

		// Liste des indirects/direct
		parent::$param['data_intermediate_id']['code'][0] = 0;
		parent::$param['data_intermediate_id']['name'][0] = "DIRECT";
		$query = "SELECT e.entity_id, e.code, e.organisation, e.status, eg.name
		FROM ts_entities AS e
		LEFT JOIN ts_entities_groups AS eg USING (entity_group_id)
		ORDER BY eg.name ASC, e.code ASC";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_intermediate_id']['code'][$row['entity_id']] = $row['entity_id'];
			parent::$param['data_intermediate_id']['name'][$row['entity_id']] = $row['code'] . ' - ' . $row['organisation'];
			parent::$param['data_intermediate_id']['group'][$row['entity_id']] = (!empty($row['name'])) ? $row['name'] : 'Companies';
			parent::$param['data_intermediate_id']['option'][$row['entity_id']] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}

		// Liste Reviews Types
		$query = "SELECT milestone_type_id, code, name, status FROM ts_milestones_types ORDER BY code";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_milestone_type_id']['code'][] = $row['milestone_type_id'];
			parent::$param['data_milestone_type_id']['name'][] = $row['code'] . ' - ' . $row['name'];
			parent::$param['data_milestone_type_id']['option'][] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}

		// Liste des utilisateurs
		$query = "SELECT user_id, code, first_name, last_name, status FROM ts_users WHERE user_id>0 ORDER BY code";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_user_id']['code'][] = $row['user_id'];
			parent::$param['data_user_id']['name'][] = $row['code'] . ' - ' . $row['last_name'] . ', ' . $row['first_name'];
			parent::$param['data_user_id']['option'][] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}

		// Liste des utilisateurs
		$query = "SELECT user_id, code, first_name, last_name, status FROM ts_users	WHERE user_id>0 ORDER BY code";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			parent::$param['data_proj_leader_id']['code'][$row['user_id']] = $row['user_id'];
			parent::$param['data_proj_leader_id']['name'][$row['user_id']] = $row['code'] . ' - ' . $row['last_name'] . ', ' . $row['first_name'];
			parent::$param['data_proj_leader_id']['list'][$row['user_id']] = $row['code'];
			parent::$param['data_proj_leader_id']['option'][$row['user_id']] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}

		// Liste Project Proposal / Project
		parent::$param['ProjectProposal']['code'][1] = '1';
		parent::$param['ProjectProposal']['name'][1] = 'Project Proposal';
		parent::$param['ProjectProposal']['code'][0] = '0';
		parent::$param['ProjectProposal']['name'][0] = 'Project approved';
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

    public function executeFrmTask(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
        $this->page->addVar('return', 'tasks_index');
        $this->page->addVar('op', 'add');
        // Visualisation uniquement
        $this->page->addVar('task_id', $request->getData('id'));
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
				'code',
				'name',
				'organisation',
				'start_date',
				'end_date',
				'status_'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
			t.task_id,
			t.code,
			t.name,
			e.organisation,
			t.start_date,
			t.end_date,
			t.project_proposal,
			t.milestone_type_id,
			IF(t.status=0,'Closed','Open') AS status_
			FROM ts_tasks AS t INNER JOIN
				ts_entities AS e ON t.customer_id = e.entity_id
		) AS view";
		$ini->sIndexColumn = "task_id";
		$ini->sTable = "ts_tasks";
		$ini->sDisplay = array(
				"code" => function ($aRow, $key, $var = '') {
					$span = ($aRow['project_proposal'] == '1') ? ' <span class="ui blue label">Proposal</span></span>' : '';
					$span1 = ($aRow['milestone_type_id'] != '0') ? ' <span class="icon red flag"></span>' : '';
					return $aRow[$key] . $span1 . $span;
				}
		);
		$list = $this->managers->getManagerOf('Tasks')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Tasks');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('task_id'));
					$json[8] = (!empty($json[8])) ? sprintf("%04d", $json[8]) : '';
					$json['user_id'] = $selfManager->viewStaff($request->postData('task_id'));
					$json['field'] = $selfManager->editField($request->postData('task_id'));
					break;
				case 'add' :
					$json = $selfManager->checkDates($request);
					if ($json['reponse'] === true) {
						//$newCode = $selfManager->newCode($request);
						//$_POST["code"] = $newCode[0];
						$json = $selfManager->add($request);
						$task_id = $selfManager->lastInsertId;
						$json = $selfManager->addStaff($json, $request->postData('staff'), $task_id);
						//$json = $selfManager->add_milestones($json, $request->postData('datemilestone'), $request->postData('milestone_type_id'), $task_id);
					}
					break;
				case 'edit' :
					$json = $selfManager->checkDates($request);
					if ($json['reponse'] === true) {
						//$newCode = $selfManager->newCode($request);
						//$_POST["code"] = $newCode[0];
						$task_id = $request->postData('task_id');
						//$json = $selfManager->edit_milestones($json, $request->postData('datemilestone'), $request->postData('milestone_type_id'), $task_id);
						$json = $selfManager->modify($request);
						$json = $selfManager->editStaff($json, $request->postData('staff'), $task_id);
					}
					break;
				case 'delete' :
					$query = "SELECT DISTINCT task_id FROM ts_timesheets WHERE task_id='" . $request->postData('task_id') . "' LIMIT 1";
					$check_timesheet = parent::$dao->query($query)->fetch();
					$query = "SELECT DISTINCT milestone_id FROM ts_milestones WHERE task_id='" . $request->postData('task_id') . "' LIMIT 1";
					$check_milestones = parent::$dao->query($query)->fetch();
					if ($check_timesheet !== false)
						$json['reponse'] = "You can not delete a Project used in the Timesheet.";
					else if ($check_milestones !== false)
						$json['reponse'] = "You can not delete a Project used in the Milestones.";
					else {
						$json = $selfManager->delete($request->postData('task_id'));
						if ($json['reponse']) {
							$query = "DELETE FROM ts_tasks_users WHERE task_id='" . $request->postData('task_id') . "'";
							$json['reponse'] = ($result = parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
						}
					}
					break;
				case 'newcode' :
					$json = $selfManager->newCode($request);
					break;
				case 'approval' :
					$json = $selfManager->approvalTask($request->postData('select'));
					break;
				case 'milestones_type' :
					$json['reponse'] = true;
					$json['field'] = $selfManager->getField($request->postData('milestone_type_id'));
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	public function executeApproval(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('op', 'approval');
	}

	public function executeList_approval(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'input',
				'code',
				'name',
				'start_date',
				'end_date',
				'status_'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
			task_id AS input,
			task_id,
			code,
			name,
			start_date,
			end_date,
			IF(status=0,'Closed','Open') AS status_
			FROM ts_tasks
			WHERE project_proposal='1'
		) AS view";
		$ini->sIndexColumn = "task_id";
		$ini->sTable = "ts_tasks";
		$ini->sDisplay = array(
				"input" => function ($aRow, $key, $var = '') {
					return '<input type="checkbox" name="select[]" value="' . $aRow[$key] . '">';
				}
		);
		$list = $this->managers->getManagerOf('Tasks')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}
}