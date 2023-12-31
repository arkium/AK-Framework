<?php

// Class: TimesheetsApprovalManager_PDO.class.php
// Table: ts_timesheets_approval
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Sun, 03 Nov 2013 14:09:02 +0000
namespace Library\Models;

class TimesheetsApprovalManager_PDO extends TimesheetsApprovalManager {

	private function getData(\Library\HTTPRequest $request) {
		return new \Library\Entities\TimesheetsApproval(array(
				'user_id' => (int) $request->postData('user_id'),
				'last_name' => (string) $request->postData('last_name'),
				'first_name' => (string) $request->postData('first_name'),
				'period_id' => (int) $request->postData('period_id'),
				'start_date' => (string) $request->postData('start_date'),
				'end_date' => (string) $request->postData('end_date'),
				'status' => (int) $request->postData('status'),
				'duration1' => (float) $request->postData('duration1'),
				'duration2' => (float) $request->postData('duration2'),
				'duration3' => (float) $request->postData('duration3'),
				'submit_date' => (string) $request->postData('submit_date'),
				'approval_date' => (string) $request->postData('approval_date'),
				'status_timesheet' => (int) $request->postData('status_timesheet') 
		));
	}

	public function getDatabases(\Library\HTTPRequest $request, \Library\Datatable $ini) {
		$table = new \Library\Datatables($request, $ini);
		return $table->run();
	}

	public function getList($debut = -1, $limite = -1) {
		$query = "SELECT * FROM ts_timesheets_approval";
		if ($debut != -1 || $limite != -1) {
			$query .= ' LIMIT ' . (int) $limite . ' OFFSET ' . (int) $debut;
		}
		try {
			$result = parent::$dao->query($query);
			$output = $result->fetchAll();
			$result->closeCursor();
		} catch (\PDOException $e) {
			$output['reponse'] = 'The database is not able to be read!<br/>';
			$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
			$output['reponse'] .= "Syntax Error: " . $e->getMessage();
		}
		return $output;
	}

	public function getUnique($id) {
		$query = "SELECT 
			,
			user_id,
			last_name,
			first_name,
			period_id,
			start_date,
			end_date,
			status,
			duration1,
			duration2,
			duration3,
			submit_date,
			approval_date,
			status_timesheet
		FROM ts_timesheets_approval
		WHERE  = :id";
		try {
			$result = parent::$dao->prepare($query);
			$result->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			$result->execute();
			// $result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\TimesheetsApproval');
			$output = (array) $result->fetch(\PDO::FETCH_NUM);
			
			$output['reponse'] = true;
		} catch (\PDOException $e) {
			$output['reponse'] = 'The database is not able to be read!<br/>';
			$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
			$output['reponse'] .= "Syntax Error: " . $e->getMessage();
		}
		return $output;
	}

	public function count() {
		$query = "SELECT COUNT(*) FROM ts_timesheets_approval";
		return parent::$dao->query($query)->fetchColumn();
	}

	public function add(\Library\HTTPRequest $request) {
		$timesheetsApproval = $this->getData($request);
		$query = "INSERT INTO ts_timesheets_approval SET 
			user_id = :user_id,
			last_name = :last_name,
			first_name = :first_name,
			period_id = :period_id,
			start_date = :start_date,
			end_date = :end_date,
			status = :status,
			duration1 = :duration1,
			duration2 = :duration2,
			duration3 = :duration3,
			submit_date = :submit_date,
			approval_date = :approval_date,
			status_timesheet = :status_timesheet";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);
			
			$result->bindValue(':user_id', $timesheetsApproval->user_id());
			$result->bindValue(':last_name', $timesheetsApproval->last_name());
			$result->bindValue(':first_name', $timesheetsApproval->first_name());
			$result->bindValue(':period_id', $timesheetsApproval->period_id());
			$result->bindValue(':start_date', $timesheetsApproval->start_date());
			$result->bindValue(':end_date', $timesheetsApproval->end_date());
			$result->bindValue(':status', $timesheetsApproval->status());
			$result->bindValue(':duration1', $timesheetsApproval->duration1());
			$result->bindValue(':duration2', $timesheetsApproval->duration2());
			$result->bindValue(':duration3', $timesheetsApproval->duration3());
			$result->bindValue(':submit_date', $timesheetsApproval->submit_date());
			$result->bindValue(':approval_date', $timesheetsApproval->approval_date());
			$result->bindValue(':status_timesheet', $timesheetsApproval->status_timesheet());
			
			$output['reponse'] = $result->execute();
			parent::$dao->commit();
		} catch (\PDOException $e) {
			parent::$dao->rollback();
			$error = $result->errorInfo();
			if ($error[1] == '1062') {
				$output['reponse'] = 'This code already exists in the database.';
			} else {
				$output['reponse'] = 'The update of the database is not successful!<br/>';
				$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
				$output['reponse'] .= "Syntax Error: " . $e->getMessage();
			}
		}
		return $output;
	}

	public function modify(\Library\HTTPRequest $request) {
		$timesheetsApproval = $this->getData($request);
		$query = "UPDATE ts_timesheets_approval SET 
			user_id = :user_id,
			last_name = :last_name,
			first_name = :first_name,
			period_id = :period_id,
			start_date = :start_date,
			end_date = :end_date,
			status = :status,
			duration1 = :duration1,
			duration2 = :duration2,
			duration3 = :duration3,
			submit_date = :submit_date,
			approval_date = :approval_date,
			status_timesheet = :status_timesheet
		WHERE  = :id";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);
			
			$result->bindValue(':id', $timesheetsApproval->id(), \PDO::PARAM_INT);
			$result->bindValue(':user_id', $timesheetsApproval->user_id());
			$result->bindValue(':last_name', $timesheetsApproval->last_name());
			$result->bindValue(':first_name', $timesheetsApproval->first_name());
			$result->bindValue(':period_id', $timesheetsApproval->period_id());
			$result->bindValue(':start_date', $timesheetsApproval->start_date());
			$result->bindValue(':end_date', $timesheetsApproval->end_date());
			$result->bindValue(':status', $timesheetsApproval->status());
			$result->bindValue(':duration1', $timesheetsApproval->duration1());
			$result->bindValue(':duration2', $timesheetsApproval->duration2());
			$result->bindValue(':duration3', $timesheetsApproval->duration3());
			$result->bindValue(':submit_date', $timesheetsApproval->submit_date());
			$result->bindValue(':approval_date', $timesheetsApproval->approval_date());
			$result->bindValue(':status_timesheet', $timesheetsApproval->status_timesheet());
			
			$output['reponse'] = $result->execute();
			parent::$dao->commit();
		} catch (\PDOException $e) {
			parent::$dao->rollback();
			$error = $result->errorInfo();
			if ($error[1] == '1062') {
				$output['reponse'] = 'This code already exists in the database.';
			} else {
				$output['reponse'] = 'The update of the database is not successful!<br/>';
				$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
				$output['reponse'] .= "Syntax Error: " . $e->getMessage();
			}
		}
		return $output;
	}

	public function delete($id) {
		$query = "DELETE FROM ts_timesheets_approval WHERE  = " . (int) $id;
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);
			$output['reponse'] = $result->execute();
			parent::$dao->commit();
		} catch (\PDOException $e) {
			parent::$dao->rollback();
			$output['reponse'] = 'The update of the database is not successful!<br/>';
			$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
			$output['reponse'] .= "Syntax Error: " . $e->getMessage();
		}
		return $output;
	}
}