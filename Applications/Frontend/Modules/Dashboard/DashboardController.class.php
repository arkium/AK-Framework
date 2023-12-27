<?php

namespace Applications\Frontend\Modules\Dashboard;

class DashboardController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des status_timesheet
		parent::$param['status_timesheet']['code'][0] = '0';
		parent::$param['status_timesheet']['name'][0] = 'Submitted';
		parent::$param['status_timesheet']['code'][1] = '1';
		parent::$param['status_timesheet']['name'][1] = 'Approved';
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			$selfManager = $this->managers->getManagerOf('Periods');
			switch ($request->postData('op')) {
				case 'view' :
					$json = $selfManager->getUnique($request->postData('period_id'));
					break;
				case 'add' :
					$json = $this->checkDates($request);
					if ($json['reponse'] === true) {
						$json = $selfManager->add($request);
					}
					break;
				case 'edit' :
					$json = $this->checkDates($request);
					if ($json['reponse'] === true) {
						$json = $selfManager->modify($request);
					}
					break;
				case 'delete' :
					$json = $selfManager->delete($request->postData('period_id'));
					break;
			}
			parent::$httpResponse->json($json);
		}
	}
}