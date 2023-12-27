<?php

namespace Applications\Frontend\Modules\Clients;

class ClientsController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des groupes de sociétés (super)
		foreach ($this->managers->getManagerOf('EntitiesGroups')->getList() as $row) {
			parent::$param['entity_group_id']['code'][] = $row['entity_group_id'];
			parent::$param['entity_group_id']['name'][] = $row['name'];
		}
		// Liste Opportunity, Client
		parent::$param['OpportunityClient']['code'][1] = '1';
		parent::$param['OpportunityClient']['name'][1] = 'Opportunity';
		parent::$param['OpportunityClient']['code'][0] = '0';
		parent::$param['OpportunityClient']['name'][0] = 'Client';
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeFrmClient(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('return', 'clients_index');
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
				'name',
				'opportunity',
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
				name,
				opportunity,
				IF(cp.status=0,'Closed','Open') AS status_
			FROM ts_entities AS cp
			LEFT JOIN ts_entities_groups AS ct USING (entity_group_id)
			WHERE entity_type_id='2'
		) AS view";
		$ini->sIndexColumn = "entity_id";
		$ini->sTable = "ts_entities";
		$ini->sDisplay = array(
				"opportunity" => function ($aRow, $key, $var = '') {
					return isset($var['OpportunityClient']['name'][$aRow[$key]]) ? $var['OpportunityClient']['name'][$aRow[$key]] : null;
				}
		);
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
					$request->postSet('entity_type_id', '2');
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$request->postSet('entity_type_id', '2');
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$query = "SELECT DISTINCT task_id FROM ts_tasks WHERE customer_id ='" . $request->postData('entity_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a client used in the Projects.";
					else {
						$json = $selfManager->delete($request->postData('entity_id'));
					}
					break;
				case 'approval' :
					$json['reponse'] = false;
					$select = $request->postData('select');
					if (!empty($select) && is_array($select)) {
						reset($select);
						foreach ($select as $key => $value) {
							$query = "UPDATE ts_entities SET
							opportunity='0'
							WHERE entity_id='$value'";
							$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
						}
					}
					$json['title'] = ($json['reponse']) ? 'Update' : 'Information';
					$json['status'] = ($json['reponse']) ? 'success' : 'warning';
					$json['msg'] = ($json['reponse']) ? 'Opportunities were converted into Clients' : 'Please check the opportunities and then click on approve';
					$json['reponse'] = true;
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
				'organisation',
				'country',
				'name',
				'status_'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
				entity_id,
				entity_id AS input,
				code,
				organisation,
				country,
				name,
				IF(cp.status=0,'Closed','Open') AS status_
			FROM ts_entities AS cp
			LEFT JOIN ts_entities_groups AS ct USING (entity_group_id)
			WHERE entity_type_id='2'
				AND opportunity='1'
		) AS view";
		$ini->sIndexColumn = "entity_id";
		$ini->sTable = "ts_entities";
		$ini->sDisplay = array(
				"input" => function ($aRow, $key, $var = '') {
					return '<input type="checkbox" name="select[]" value="' . $aRow[$key] . '">';
				}
		);
		$list = $this->managers->getManagerOf('Entities')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}
}