<?php

// Class: TasksUsers.class.php
// Table: ts_tasks_users
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Fri, 01 Nov 2013 22:12:09 +0000
namespace Library\Entities;

class TasksUsers extends \Library\Entity {

	protected $task_id, $user_id, $update_time, $created_time;

	const TASK_ID_INVALIDE = 2;

	const USER_ID_INVALIDE = 3;

	const UPDATE_TIME_INVALIDE = 4;

	const CREATED_TIME_INVALIDE = 5;

	public function isValid() {
		return (isset($this->task_id) && isset($this->user_id) && isset($this->update_time) && isset($this->created_time));
	}
	
	// SETTERS //
	public function setTask_user_id($task_user_id) {
		$this->id = (int) $task_user_id;
	}

	public function setTask_id($task_id) {
		if (!isset($task_id) || !is_int($task_id)) {
			$this->erreurs[] = self::TASK_ID_INVALIDE;
		} else {
			$this->task_id = (int) $task_id;
		}
	}

	public function setUser_id($user_id) {
		if (!isset($user_id) || !is_int($user_id)) {
			$this->erreurs[] = self::USER_ID_INVALIDE;
		} else {
			$this->user_id = (int) $user_id;
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
	public function task_user_id() {
		return $this->id;
	}

	public function task_id() {
		return $this->task_id;
	}

	public function user_id() {
		return $this->user_id;
	}

	public function update_time() {
		return $this->update_time;
	}

	public function created_time() {
		return $this->created_time;
	}
}