<?php

// Class: IssuesFiles.class.php
// Table: ts_issues_files
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Sat, 19 Apr 2014 13:33:15 +0000
namespace Library\Entities;

class IssuesFiles extends \Library\Entity {

	protected $issue_id, $filename, $filesize, $update_time, $created_time;

	const ISSUE_ID_INVALIDE = 2;

	const FILENAME_INVALIDE = 3;

	const FILESIZE_INVALIDE = 4;

	const UPDATE_TIME_INVALIDE = 5;

	const CREATED_TIME_INVALIDE = 6;

	public function isValid() {
		return (isset($this->issue_id) && isset($this->filename) && isset($this->filesize) && isset($this->update_time) && isset($this->created_time));
	}
	
	// SETTERS //
	public function setFile_id($file_id) {
		$this->id = (int) $file_id;
	}

	public function setIssue_id($issue_id) {
		if (!isset($issue_id) || !is_int($issue_id)) {
			$this->erreurs[] = self::ISSUE_ID_INVALIDE;
		} else {
			$this->issue_id = (int) $issue_id;
		}
	}

	public function setFilename($filename) {
		$this->filename = (string) $filename;
	}

	public function setFilesize($filesize) {
		$this->filesize = (int) $filesize;
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
	public function file_id() {
		return $this->id;
	}

	public function issue_id() {
		return $this->issue_id;
	}

	public function filename() {
		return $this->filename;
	}

	public function filesize() {
		return $this->filesize;
	}

	public function update_time() {
		return $this->update_time;
	}

	public function created_time() {
		return $this->created_time;
	}
}