<?php

namespace Applications\Frontend\Modules\Users;

class UsersController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des entreprises (de type 1)
		foreach ($this->managers->getManagerOf('Entities')->getList() as $row) {
			if ($row['entity_type_id'] == '1') {
				parent::$param['data_invoicing_entity_id']['code'][$row['entity_id']] = $row['entity_id'];
				parent::$param['data_invoicing_entity_id']['name'][$row['entity_id']] = $row['code'] . ' - ' . $row['organisation'];
				parent::$param['data_invoicing_entity_id']['option'][$row['entity_id']] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
			}
		}
		// Liste des permissions
		foreach ($this->managers->getManagerOf('UsersRoles')->getList() as $row) {
			parent::$param['role_id']['code'][$row['role_id']] = $row['role_id'];
			parent::$param['role_id']['name'][$row['role_id']] = $row['name'];
			parent::$param['role_id']['option'][$row['role_id']] = ($row['status'] == '0') ? 'disabled="disabled"' : '';
		}
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeFrmUser(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('return', 'users_index');
		$this->page->addVar('op', 'add');
		// Visualisation uniquement
		$this->page->addVar('user_id', $request->getData('id'));
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
				'company_id',
				'email_address',
				'level',
				'status_' 
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
			user_id,
			code,
			CONCAT(last_name, ', ', first_name) AS name,
			company_id,
			email_address,
			level,
			IF(status=0,'Closed','Open') AS status_
			FROM ts_users
		) AS view";
		$ini->sIndexColumn = "user_id";
		$ini->sTable = "ts_users";
		$ini->sDisplay = array(
				"name" => function ($aRow, $key, $var = '') {
					return trim($aRow[$key], ', ');
				},
				"level" => function ($aRow, $key, $var = '') {
					return isset($var['role_id']['code'][$aRow[$key]]) ? $var['role_id']['name'][$aRow[$key]] : null;
				},
				"company_id" => function ($aRow, $key, $var = '') {
					return isset($var['data_invoicing_entity_id']['name'][$aRow[$key]]) ? $var['data_invoicing_entity_id']['name'][$aRow[$key]] : null;
				} 
		);
		$list = $this->managers->getManagerOf('Users')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Users');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('user_id'));
					break;
				case 'add' :
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$query = "SELECT DISTINCT time_id FROM ts_timesheets WHERE user_id ='" . $request->postData('user_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a staff member used in the Timesheet.";
					else {
						$json = $selfManager->delete($request->postData('user_id'));
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}