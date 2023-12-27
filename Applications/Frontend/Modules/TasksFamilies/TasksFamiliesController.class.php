<?php

namespace Applications\Frontend\Modules\TasksFamilies;

class TasksFamiliesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeFrmTaskFamily(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
        $this->page->addVar('return', './');
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('TasksFamilies');
			switch ($request->postData('op')) {
				case 'list' :
                    $json = null;
					foreach ($selfManager->getList() as $row) {
						$json[] = array(
								'id' => $row['task_family_id'],
								'name' => $row['name'] 
						);
					}
					break;
				case 'add' :
					$_POST['name'] = $_POST['field_add'];
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$_POST['task_family_id'] = $_POST['id'];
					$_POST['name'] = $_POST['field_change'];
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$_POST['task_family_id'] = $_POST['id'];
					$query = "SELECT DISTINCT task_type_id FROM ts_tasks_types WHERE task_family_id='" . $request->postData('task_family_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a family used in the list of the Activity Types.";
					else {
						$json = $selfManager->delete($request->postData('task_family_id'));
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}