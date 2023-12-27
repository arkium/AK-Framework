<?php

// Class: TasksTypesManager.class.php
// Table: ts_tasks_types
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Sun, 22 Dec 2013 13:39:33 +0000
namespace Library\Models;

abstract class TasksTypesManager extends \Library\ApplicationComponent {

	public $lastInsertId;

	abstract public function getDatabases(\Library\HTTPRequest $request, \Library\Datatable $ini);

	abstract public function getList($debut = -1, $limite = -1);

	abstract public function getUnique($id);

	abstract public function count();

	abstract public function add(\Library\HTTPRequest $request);

	abstract public function modify(\Library\HTTPRequest $request);

	abstract public function delete($id);
}
