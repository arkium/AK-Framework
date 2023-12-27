<?php

namespace Applications\Frontend\Modules\Help;

class HelpController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeForm(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		include ('Applications/Frontend/Modules/Help/Views/frmHelp.php');
		exit();
	}
}