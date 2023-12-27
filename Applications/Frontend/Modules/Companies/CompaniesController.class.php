<?php

namespace Applications\Frontend\Modules\Companies;

class CompaniesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des groupes de sociétés
		foreach ($this->managers->getManagerOf('EntitiesGroups')->getList() as $row) {
			parent::$param['entity_group_id']['code'][] = $row['entity_group_id'];
			parent::$param['entity_group_id']['name'][] = $row['name'];
		}
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

    public function executeFrmCompany(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
        $this->page->addVar('return', 'companies_index');
        $this->page->addVar('op', 'add');
        // Visualisation uniquement
        $this->page->addVar('entity_id', $request->getData('id'));
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
				'organisation',
				'country',
				'status_'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
				entity_id,
				code,
				organisation,
				country,
				IF(cp.status=0,'Closed','Open') AS status_
			FROM ts_entities AS cp
			WHERE entity_type_id='1'
		) AS view";
		$ini->sIndexColumn = "entity_id";
		$ini->sTable = "ts_entities";
		$ini->sDisplay = array();
		$list = $this->managers->getManagerOf('Entities')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Entities');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('entity_id'));
					break;
				case 'add' :
					$request->postSet('entity_type_id', '1');
					$request->postSet('entity_group_id', '1');
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$request->postSet('entity_type_id', '1');
					$request->postSet('entity_group_id', '1');
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$query = "SELECT DISTINCT task_id FROM ts_tasks WHERE invoicing_entity_id ='" . $request->postData('entity_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a company used in the Projects.";
					else {
						$json = $selfManager->delete($request->postData('entity_id'));
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}