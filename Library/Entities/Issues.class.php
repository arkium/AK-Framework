<?php

// Class: Issues.class.php
// Table: ts_issues
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Fri, 11 Apr 2014 09:24:46 +0000
namespace Library\Entities;

class Issues extends \Library\Entity {

	protected $user_id, $title, $description, $type_id, $status, $update_time, $created_time;

	const USER_ID_INVALIDE = 2;

	const TITLE_INVALIDE = 3;

	const DESCRIPTION_INVALIDE = 4;

	const TYPE_ID_INVALIDE = 5;

	const STATUS_INVALIDE = 6;

	const UPDATE_TIME_INVALIDE = 7;

	const CREATED_TIME_INVALIDE = 8;

	public function isValid() {
		return (isset($this->user_id) && isset($this->title) && isset($this->description) && isset($this->type_id) && isset($this->status) && isset($this->update_time) && isset($this->created_time));
	}
	
	// SETTERS //
	public function setIssue_id($issue_id) {
		$this->id = (int) $issue_id;
	}

	public function setUser_id($user_id) {
		if (!isset($user_id) || !is_int($user_id)) {
			$this->erreurs[] = self::USER_ID_INVALIDE;
		} else {
			$this->user_id = (int) $user_id;
		}
	}

	public function setTitle($title) {
		$this->title = (string) $title;
	}

	public function setDescription($description) {
		$this->description = (string) $description;
	}

	public function setType_id($type_id) {
		$this->type_id = (string) $type_id;
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
	public function issue_id() {
		return $this->id;
	}

	public function user_id() {
		return $this->user_id;
	}

	public function title() {
		return $this->title;
	}

	public function description() {
		return $this->description;
	}

	public function type_id() {
		return $this->type_id;
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