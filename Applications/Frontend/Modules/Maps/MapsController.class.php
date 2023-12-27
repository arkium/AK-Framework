<?php

namespace Applications\Frontend\Modules\Maps;

class MapsController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}
}