<?php

// Class: MilestonesTypesManager.class.php
// Table: ts_milestones_types
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Tue, 04 Feb 2014 19:14:36 +0000
namespace Library\Models;

abstract class MilestonesTypesManager extends \Library\ApplicationComponent {

	public $lastInsertId;

	abstract public function getDatabases(\Library\HTTPRequest $request, \Library\Datatable $ini);

	abstract public function getList($debut = -1, $limite = -1);

	abstract public function getUnique($id);

	abstract public function count();

	abstract public function add(\Library\HTTPRequest $request);

	abstract public function modify(\Library\HTTPRequest $request);

	abstract public function delete($id);
}

