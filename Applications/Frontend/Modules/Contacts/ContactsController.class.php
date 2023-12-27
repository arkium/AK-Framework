<?php

namespace Applications\Frontend\Modules\Contacts;

class ContactsController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des entreprises
		foreach ($this->managers->getManagerOf('Entities')->getList() as $row) {
			parent::$param['entity_id']['code'][] = $row['entity_id'];
			parent::$param['entity_id']['name'][] = $row['code'] . ' - ' . $row['organisation'];
			parent::$param['entity_id']['option'][] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeList(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'organisation',
				'contact_type_id',
				'contact_name',
				'email',
				'phone',
				'status_' 
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT 
				contact_id, 
				organisation, 
				contact_type_id, 
				CONCAT(first_name, ', ', last_name) AS contact_name, 
				email, 
				phone,
				IF(ct.status=0,'Closed','Open') AS status_
			FROM ts_contacts AS ct
			LEFT JOIN ts_entities AS cp USING (entity_id)
		) AS view";
		$ini->sIndexColumn = "contact_id";
		$ini->sTable = "ts_contacts";
		$ini->sDisplay = array(
				"contact_name" => function ($aRow, $key, $var = '') {
					return trim($aRow[$key], ', ');
				},
				"contact_type_id" => function ($aRow, $key, $var = '') {
					return isset($var['contact_type_id']['name'][$aRow[$key]]) ? $var['contact_type_id']['name'][$aRow[$key]] : null;
				} 
		);
		$list = $this->managers->getManagerOf('Contacts')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeForm(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$fct = new \Library\Functions();
		include ('Applications/Frontend/Modules/Contacts/Views/frmContact.php');
		exit();
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Contacts');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('contact_id'));
					break;
				case 'add' :
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$json = $selfManager->delete($request->postData('contact_id'));
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}