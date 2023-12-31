<?php

// Class: TimesheetsHoursManager_PDO.class.php
// Table: ts_timesheets_hours
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Sun, 21 May 2017 15:07:10 +0200

namespace Library\Models;

class TimesheetsHoursManager_PDO extends TimesheetsHoursManager {

	private function getData(\Library\HTTPRequest $request) {
		return new \Library\Entities\TimesheetsHours(array(
				'hour_id' => (int) $request->postData('hour_id'),
				'time_id' => (int) $request->postData('time_id'),
				'start' => (string) $request->postData('start'),
				'end' => (string) $request->postData('end'),
				'update_time' => (string) $request->postData('update_time'),
				'created_time' => (string) $request->postData('created_time')
		));
	}

	public function getDatabases(\Library\HTTPRequest $request, \Library\Datatable $ini) {
		$table = new \Library\Datatables($request, $ini);
		return $table->run();
	}

	public function getList($debut = -1, $limite = -1) {
		$query = "SELECT * FROM ts_timesheets_hours";
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
			hour_id,
			time_id,
			start,
			end,
			update_time,
			created_time
		FROM ts_timesheets_hours
		WHERE hour_id = :id";
		try {
			$result = parent::$dao->prepare($query);
			$result->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			$result->execute();
			// $result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\TimesheetsHours');
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
		$query = "SELECT COUNT(*) FROM ts_timesheets_hours";
		return parent::$dao->query($query)->fetchColumn();
	}

	public function add(\Library\HTTPRequest $request) {
		$TimesheetsHours = $this->getData($request);
		$query = "INSERT INTO ts_timesheets_hours SET 
			time_id = :time_id,
			start = :start,
			end = :end,
			created_time = NOW()";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);

			$result->bindValue(':time_id', $TimesheetsHours->time_id());
			$result->bindValue(':start', $TimesheetsHours->start());
			$result->bindValue(':end', $TimesheetsHours->end());

			$output['reponse'] = $result->execute();
			$this->lastInsertId = parent::$dao->lastInsertId();
			$output['lastID'] = $this->lastInsertId;
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
		$TimesheetsHours = $this->getData($request);
		$query = "UPDATE ts_timesheets_hours SET 
			time_id = :time_id,
			start = :start,
			end = :end
		WHERE hour_id = :id";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);

			$result->bindValue(':id', $TimesheetsHours->id(), \PDO::PARAM_INT);
			$result->bindValue(':time_id', $TimesheetsHours->time_id());
			$result->bindValue(':start', $TimesheetsHours->start());
			$result->bindValue(':end', $TimesheetsHours->end());

			$output['reponse'] = $result->execute();
			$output['lastID'] = $TimesheetsHours->id();
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
		$query = "DELETE FROM ts_timesheets_hours WHERE hour_id = " . (int) $id;
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