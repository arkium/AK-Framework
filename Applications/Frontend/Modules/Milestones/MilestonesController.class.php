<?php

namespace Applications\Frontend\Modules\Milestones;

class MilestonesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeEdit(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		parent::$param['milestone_type_list'] = $this->managers->getManagerOf('Milestones')->getMilestoneTypeList();
		parent::$param['milestone_type_id'] = isset($_REQUEST["milestone_type_id"]) ? $_REQUEST["milestone_type_id"] : parent::$param['milestone_type_list']['code'][0];
		parent::$param['data_colonnes'] = $this->managers->getManagerOf('Milestones')->getColonnes(parent::$param['milestone_type_id']);
		parent::$param['data_lignes'] = $this->managers->getManagerOf('Milestones')->getLignes(parent::$param['milestone_type_id']);
		parent::$param['data_data'] = $this->managers->getManagerOf('Milestones')->getDate(parent::$param['milestone_type_id']);
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
				'closing_date',
				'period',
				'status_' 
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT 
			t.task_id, 
			t.code, 
			CONCAT(c.code, ' - ', c.organisation) AS name,
			t.closing_date, 
			CONCAT(t.start_date, ' to ', t.end_date) AS period, 
			IF(t.status=0,'Closed','Open') AS status_
			FROM ts_tasks AS t, ts_entities AS c
			WHERE t.customer_id = c.entity_id  
			AND t.milestone_type_id > 0 AND t.status='1'
		) AS view";
		$ini->sIndexColumn = "task_id";
		$ini->sTable = "ts_tasks";
		$ini->sDisplay = array();
		$list = $this->managers->getManagerOf('Tasks')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeForm(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$fct = new \Library\Functions();
		include ('Applications/Frontend/Modules/Milestones/Views/frmMilestone.php');
		exit();
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Tasks');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('task_id'));
					// $json['company'] = $this->managers->getManagerOf('Entities')->getUnique($json['5']);
					$json['list_fields'][] = $this->listFields($request);
					break;
				case 'edit' :
					$query = "SELECT milestone_id 
					FROM ts_milestones 
					WHERE task_id='" . $request->postData('task_id') . "'";
					$result = parent::$dao->query($query);
					$output = $result->fetchAll();
					foreach ($output as $row) {
						$value = ($request->postData('f_' . $row['milestone_id']) == '') ? 'NULL' : "'" . $request->postData('f_' . $row['milestone_id']) . "'";
						$query = "UPDATE ts_milestones SET
						date = $value
						WHERE milestone_id = '" . $row['milestone_id'] . "'";
						$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					}
					break;
				case 'save_date' :
					$json = $this->saveDate($request);
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	private function listFields(\Library\HTTPRequest $request) {
		$query = "SELECT m.milestone_id, m.date, mf.name 
		FROM ts_milestones m, ts_milestones_fields mf
		WHERE m.milestone_field_id=mf.milestone_field_id				
			AND m.task_id='" . $request->postData('task_id') . "'";
		$result = parent::$dao->query($query)->fetchAll(\PDO::FETCH_ASSOC);
		$fields = "";
		foreach ($result as $row) {
			$id = $row['milestone_id'];
			$fields .= '
					<tr>
					<td><span>' . $row['name'] . '</span></td>
					<td><input type="text" class="form-control jdate" id="f_' . $id . '" name="f_' . $id . '" value="' . $row['date'] . '"></td>
					</tr>';
		}
		return $fields;
	}

	private function saveDate(\Library\HTTPRequest $request) {
		$task_id = $request->postData('task_id');
		$milestone_field_id = $request->postData('milestone_field_id');
		$date = $request->postData('date');
		$milestone_id = $request->postData('milestone_id');
		$direct = $request->postData('direct');
		$direct = isset($direct) ? $direct : false;
		// $comment = $request->postData('comment');
		
		// $date = date("Ymd", $request->postData('date'));
		$date = (empty($date)) ? " date=null" : " date='$date'";
		
		$query = "SELECT milestone_id FROM ts_milestones";
		if (!empty($milestone_id)) {
			$query .= " WHERE milestone_id='$milestone_id'";
		} else {
			$query .= " WHERE task_id='$task_id' AND milestone_field_id='$milestone_field_id'";
		}
		$query .= " LIMIT 1";
		$result = parent::$dao->query($query)->fetch();
		if ($result === false) {
			$output['reponse'] = 'The update of the database is not successful!';
		} else {
			$modifycomment = '';
			// $modifycomment = (!$direct) ? ", comment='$comment'" : "";
			if (!empty($milestone_id)) {
				$query = "UPDATE ts_milestones 
				SET $date$modifycomment 
				WHERE milestone_id='$milestone_id'";
			} else {
				$query = "UPDATE ts_milestones 
				SET $date$modifycomment 
				WHERE task_id='$task_id' AND milestone_field_id='$milestone_field_id'";
			}
		}
		// $output['debug'] = $query;
		$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
		$output['msg'] = "The date was updated successfully!";
		$output['title'] = "Update";
		return $output;
	}
}