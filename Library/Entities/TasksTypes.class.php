<?php

// Class: TasksTypes.class.php
// Table: ts_tasks_types
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Sun, 22 Dec 2013 13:39:33 +0000
namespace Library\Entities;

class TasksTypes extends \Library\Entity {

	protected $code, $name, $task_family_id, $chargeable, $note, $color, $status, $update_time, $created_time;

	const CODE_INVALIDE = 2;

	const NAME_INVALIDE = 3;

	const TASK_FAMILY_ID_INVALIDE = 4;

	const CHARGEABLE_INVALIDE = 5;

	const NOTE_INVALIDE = 6;

	const COLOR_INVALIDE = 7;

	const STATUS_INVALIDE = 8;

	const UPDATE_TIME_INVALIDE = 9;

	const CREATED_TIME_INVALIDE = 10;

	public function isValid() {
		return (isset($this->code) && isset($this->name) && isset($this->task_family_id) && isset($this->chargeable) && isset($this->note) && isset($this->color) && isset($this->status) && isset($this->update_time) && isset($this->created_time));
	}
	
	// SETTERS //
	public function setTask_type_id($task_type_id) {
		$this->id = (int) $task_type_id;
	}

	public function setCode($code) {
		if (!isset($code) || !is_string($code)) {
			$this->erreurs[] = self::CODE_INVALIDE;
		} else {
			$this->code = (string) $code;
		}
	}

	public function setName($name) {
		if (!isset($name) || !is_string($name)) {
			$this->erreurs[] = self::NAME_INVALIDE;
		} else {
			$this->name = (string) $name;
		}
	}

	public function setTask_family_id($task_family_id) {
		if (!isset($task_family_id) || !is_int($task_family_id)) {
			$this->erreurs[] = self::TASK_FAMILY_ID_INVALIDE;
		} else {
			$this->task_family_id = (int) $task_family_id;
		}
	}

	public function setChargeable($chargeable) {
		if (!isset($chargeable) || !is_int($chargeable)) {
			$this->erreurs[] = self::CHARGEABLE_INVALIDE;
		} else {
			$this->chargeable = (int) $chargeable;
		}
	}

	public function setNote($note) {
		$this->note = (string) $note;
	}

	public function setColor($color) {
		$this->color = (string) $color;
	}

	public function setStatus($status) {
		if (!isset($status) || !is_int($status)) {
			$this->erreurs[] = self::STATUS_INVALIDE;
		} else {
			$this->status = (int) $status;
		}
	}

	public function setUpdate_time($update_time) {
		if (!isset($update_time) || !is_string($update_time)) {
			$this->erreurs[] = self::UPDATE_TIME_INVALIDE;
		} else {
			$this->update_time = (string) $update_time;
		}
	}

	public function setCreated_time($created_time) {
		$this->created_time = (string) $created_time;
	}
	
	// GETTERS //
	public function task_type_id() {
		return $this->id;
	}

	public function code() {
		return $this->code;
	}

	public function name() {
		return $this->name;
	}

	public function task_family_id() {
		return $this->task_family_id;
	}

	public function chargeable() {
		return $this->chargeable;
	}

	public function note() {
		return $this->note;
	}

	public function color() {
		return $this->color;
	}

	public function status() {
		return $this->status;
	}

	public function update_time() {
		return $this->update_time;
	}

	public function created_time() {
		return $this->created_time;
	}
}