<?php

namespace Applications\Frontend\Modules\EntitiesGroups;

class EntitiesGroupsController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeFrmEntityGroup(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('return', './');
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('EntitiesGroups');
			switch ($request->postData('op')) {
				case 'list' :
					foreach ($selfManager->getList() as $row) {
						$json[] = array(
								'id' => $row['entity_group_id'],
								'name' => $row['name'] 
						);
					}
					break;
				case 'add' :
					$_POST['name'] = $_POST['field_add'];
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$_POST['entity_group_id'] = $_POST['id'];
					$_POST['name'] = $_POST['field_change'];
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$_POST['entity_group_id'] = $_POST['id'];
					$query = "SELECT DISTINCT entity_group_id FROM ts_entities WHERE entity_group_id='" . $request->postData('entity_group_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a field used in the list of the Clients/Opportunities.";
					else {
						$json = $selfManager->delete($request->postData('entity_group_id'));
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}