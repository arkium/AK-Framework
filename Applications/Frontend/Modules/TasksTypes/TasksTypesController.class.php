<?php

namespace Applications\Frontend\Modules\TasksTypes;

class TasksTypesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des familles de tâches
		foreach ($this->managers->getManagerOf('TasksFamilies')->getList() as $row) {
			parent::$param['type_taskfamily']['code'][] = $row['task_family_id'];
			parent::$param['type_taskfamily']['name'][] = $row['name'];
		}
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

    public function executeFrmTaskType(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
        $this->page->addVar('return', 'taskstypes_index');
        $this->page->addVar('op', 'add');
        // Visualisation uniquement
        $this->page->addVar('task_type_id', $request->getData('id'));
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
				'family',
				'chargeable_',
				'status_' 
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT 
			tt.task_type_id, 
			tt.code, 
			tt.name, 
			tf.name AS family, 
			IF(tt.chargeable=0,'No Chargeable','Chargeable') AS chargeable_,
			IF(tt.status=0,'Closed','Open') AS status_
			FROM ts_tasks_types AS tt
			LEFT JOIN ts_tasks_families AS tf ON tt.task_family_id=tf.task_family_id
		) AS view";
		$ini->sIndexColumn = "task_type_id";
		$ini->sTable = "ts_tasks_types";
		$ini->sDisplay = array();
		$list = $this->managers->getManagerOf('EntitiesGroups')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('TasksTypes');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('task_type_id'));
					break;
				case 'add' :
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$query = "SELECT DISTINCT task_id FROM ts_tasks WHERE task_type_id='" . $request->postData('task_type_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a Activity Type used in the list of the Projects.";
					else {
						$json = $selfManager->delete($request->postData('task_type_id'));
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}