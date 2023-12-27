<?php

// Class: UsersRolesManager_PDO.class.php
// Table: ts_users_roles
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Sun, 13 Mar 2016 14:34:21 +0100

namespace Library\Models;

class UsersRolesManager_PDO extends UsersRolesManager {

	private function getData(\Library\HTTPRequest $request) {
		return new \Library\Entities\UsersRoles(array(
				'role_id' => (int) $request->postData('role_id'),
				'code' => (string) $request->postData('code'),
				'name' => (string) $request->postData('name'),
				'level' => (int) $request->postData('level'),
				'modules' => (string) $request->postData('modules'),
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
		$query = "SELECT * FROM ts_users_roles";
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
			role_id,
			code,
			name,
			level,
			modules,
			status,
			update_time,
			created_time
		FROM ts_users_roles
		WHERE role_id = :id";
		try {
			$result = parent::$dao->prepare($query);
			$result->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			$result->execute();
			// $result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\UsersRoles');
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
		$query = "SELECT COUNT(*) FROM ts_users_roles";
		return parent::$dao->query($query)->fetchColumn();
	}

	public function add(\Library\HTTPRequest $request) {
		$UsersRoles = $this->getData($request);
		$query = "INSERT INTO ts_users_roles SET 
			code = :code,
			name = :name,
			level = :level,
			modules = :modules,
			status = :status,
			created_time = NOW()";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);

			$result->bindValue(':code', $UsersRoles->code());
			$result->bindValue(':name', $UsersRoles->name());
			$result->bindValue(':level', $UsersRoles->level());
			$result->bindValue(':modules', $UsersRoles->modules());
			$result->bindValue(':status', $UsersRoles->status());

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
		$UsersRoles = $this->getData($request);
		$query = "UPDATE ts_users_roles SET 
			code = :code,
			name = :name,
			level = :level,
			modules = :modules,
			status = :status
		WHERE role_id = :id";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);

			$result->bindValue(':id', $UsersRoles->id(), \PDO::PARAM_INT);
			$result->bindValue(':code', $UsersRoles->code());
			$result->bindValue(':name', $UsersRoles->name());
			$result->bindValue(':level', $UsersRoles->level());
			$result->bindValue(':modules', $UsersRoles->modules());
			$result->bindValue(':status', $UsersRoles->status());

			$output['reponse'] = $result->execute();
			$output['lastID'] = $UsersRoles->id();
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
		$query = "DELETE FROM ts_users_roles WHERE role_id = " . (int) $id;
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