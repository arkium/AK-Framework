<?php

namespace Applications\Frontend\Modules\UsersRoles;

class UsersRolesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des modules
		$dir = scandir(_DIRMODULES);
		foreach ($dir as $key => $value) {
			if (!in_array($value, array(".",".."))) {
				if (is_dir(_DIRMODULES . DIRECTORY_SEPARATOR . $value)) {
					parent::$param['modules']['code'][] = $value;
					parent::$param['modules']['name'][] = $value;
				}
			}
		}
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeFrmUserRole(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		$this->page->addVar('return', 'usersroles_index');
		$this->page->addVar('op', 'add');
		// Visualisation uniquement
		$this->page->addVar('role_id', $request->getData('id'));
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
				'level',
				'modules',
				'status_'
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
			role_id,
			code,
			name,
			level,
			modules,
			IF(status=0,'Closed','Open') AS status_
			FROM ts_users_roles
		) AS view";
		$ini->sIndexColumn = "role_id";
		$ini->sTable = "ts_users_roles";
		$ini->sDisplay = array(
				"level" => function ($aRow, $key, $var = '') {
					$content = "";
					$i = 0;
					foreach ($var['permissions']['name'] as $row) {
						if (($aRow[$key] & pow(2, $i)) != 0) {
							$content .= $var['permissions']['name'][$i] . ', ';
						}
						$i ++;
					}
					$content = substr_replace($content, "", -2);
					return $content;
				}
		);
		$list = $this->managers->getManagerOf('UsersRoles')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$permissions = array(
					'view' => ($request->postExists('view')) ? $request->postData('view') : null,
					'add' => ($request->postExists('add')) ? $request->postData('add') : null,
					'edit' => ($request->postExists('edit')) ? $request->postData('edit') : null,
					'delete' => ($request->postExists('delete')) ? $request->postData('delete') : null,
					'approval' => ($request->postExists('approval')) ? $request->postData('approval') : null,
					'admin' => ($request->postExists('admin')) ? $request->postData('admin') : null
			);
			$bitmask = 0;
			$i = 0;
			foreach ($permissions as $key => $value) {
				if ($value == '1') {
					$bitmask += pow(2, $i);
				}
				$i ++;
			}
			$request->postSet('level', $bitmask);
			$selfManager = $this->managers->getManagerOf('UsersRoles');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('role_id'));
					$json['4'] = explode(',', $json['4']);
					break;
				case 'add' :
					$request->postSet('modules', implode(',', $request->postData('modules')));
					$json = $selfManager->add($request);
					break;
				case 'edit' :
					$request->postSet('modules', implode(',', $request->postData('modules')));
					$json = $selfManager->modify($request);
					break;
				case 'delete' :
					$query = "SELECT DISTINCT user_id FROM ts_users WHERE level ='" . $request->postData('role_id') . "'";
					$result = parent::$dao->query($query)->fetch();
					if ($result !== false)
						$json['reponse'] = "You can not delete a role used in the Staff.";
					else {
						$json = $selfManager->delete($request->postData('role_id'));
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

}
