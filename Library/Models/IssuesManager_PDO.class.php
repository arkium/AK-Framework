<?php

// Class: IssuesManager_PDO.class.php
// Table: ts_issues
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Fri, 11 Apr 2014 09:24:46 +0000
namespace Library\Models;

class IssuesManager_PDO extends IssuesManager {

	private function getData(\Library\HTTPRequest $request) {
		return new \Library\Entities\Issues(array(
				'issue_id' => (int) $request->postData('issue_id'),
				'user_id' => (int) $request->postData('user_id'),
				'title' => (string) $request->postData('title'),
				'description' => (string) $request->postData('description'),
				'type_id' => (string) $request->postData('type_id'),
				'status' => (int) $request->postData('status'),
				'update_time' => (string) $request->postData('update_time'),
				'created_time' => (string) $request->postData('created_time') 
		));
	}

	public function getDatabases(\Library\HTTPRequest $request, \Library\Datatable $ini) {
		$table = new \Library\Datatables($request, $ini);
		return $table->run();
	}

	public function getList($debut = -1, $limite = -1) {
		$query = "SELECT * FROM ts_issues";
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
			issue_id,
			user_id,
			title,
			description,
			type_id,
			status,
			update_time,
			created_time
		FROM ts_issues
		WHERE issue_id = :id";
		try {
			$result = parent::$dao->prepare($query);
			$result->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			$result->execute();
			// $result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Issues');
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
		$query = "SELECT COUNT(*) FROM ts_issues";
		return parent::$dao->query($query)->fetchColumn();
	}

	public function add(\Library\HTTPRequest $request) {
		$issues = $this->getData($request);
		$query = "INSERT INTO ts_issues SET 
			user_id = :user_id,
			title = :title,
			description = :description,
			type_id = :type_id,
			status = :status,
			created_time = NOW()";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);
			
			$result->bindValue(':user_id', $issues->user_id());
			$result->bindValue(':title', $issues->title());
			$result->bindValue(':description', $issues->description());
			$result->bindValue(':type_id', $issues->type_id());
			$result->bindValue(':status', $issues->status());
			
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
		$issues = $this->getData($request);
		$query = "UPDATE ts_issues SET 
			user_id = :user_id,
			title = :title,
			description = :description,
			type_id = :type_id,
			status = :status
		WHERE issue_id = :id";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);
			
			$result->bindValue(':id', $issues->id(), \PDO::PARAM_INT);
			$result->bindValue(':user_id', $issues->user_id());
			$result->bindValue(':title', $issues->title());
			$result->bindValue(':description', $issues->description());
			$result->bindValue(':type_id', $issues->type_id());
			$result->bindValue(':status', $issues->status());
			
			$output['reponse'] = $result->execute();
			$output['lastID'] = $issues->id();
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
		$query = "DELETE FROM ts_issues WHERE issue_id = " . (int) $id;
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