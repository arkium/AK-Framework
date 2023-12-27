<?php

namespace Applications\Frontend\Modules\MilestonesTypes;

class MilestonesTypesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeList(\Library\HTTPRequest $request) {
		// parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'code',
				'name',
				'status_' 
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT 
			milestone_type_id, 
			code, 
			name, 
			IF(status=0,'Closed','Open') AS status_
			FROM ts_milestones_types
		) AS view";
		$ini->sIndexColumn = "milestone_type_id";
		$ini->sTable = "ts_milestones_types";
		$ini->sDisplay = array();
		$list = $this->managers->getManagerOf('MilestonesTypes')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeForm(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$fct = new \Library\Functions();
		include ('Applications/Frontend/Modules/MilestonesTypes/Views/frmMilestoneType.php');
		exit();
	}

	public function executeJson(\Library\HTTPRequest $request) {
		// parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('MilestonesTypes');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('milestone_type_id'));
					$json['field'] = $selfManager->getField($request);
					break;
				case 'add' :
					$json = $selfManager->add($request);
					$json['reponse'] = $selfManager->addFields($request, $selfManager->lastInsertId);
					break;
				case 'edit' :
					$json = $selfManager->modify($request);
					$json['reponse'] = $selfManager->editFields($request);
					break;
				case 'delete' :
					$query = "SELECT DISTINCT task_id FROM ts_tasks WHERE milestone_type_id='" . $request->postData('milestone_type_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a Milestone List for which it is used in a task. Please delete the task.";
					else {
						$json = $selfManager->delete($request->postData('milestone_type_id'));
						if ($json['reponse']) {
							$query = "DELETE FROM ts_milestones_fields WHERE milestone_type_id='" . $request->postData('milestone_type_id') . "'";
							$json['reponse'] = ($result = parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
						}
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}