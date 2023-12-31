<?php

// Class: EntitiesManager_PDO.class.php
// Table: ts_entities
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : Fri, 10 Jan 2014 18:46:27 +0000
namespace Library\Models;

class EntitiesManager_PDO extends EntitiesManager {

	private function getData(\Library\HTTPRequest $request) {
		return new \Library\Entities\Entities(array(
				'entity_id' => (int) $request->postData('entity_id'),
				'entity_type_id' => (int) $request->postData('entity_type_id'),
				'code' => (string) $request->postData('code'),
				'organisation' => (string) $request->postData('organisation'),
				'entity_group_id' => (int) $request->postData('entity_group_id'),
				'address1' => (string) $request->postData('address1'),
				'address2' => (string) $request->postData('address2'),
				'postal_code' => (string) $request->postData('postal_code'),
				'city' => (string) $request->postData('city'),
				'state' => (string) $request->postData('state'),
				'country' => (string) $request->postData('country'),
				'http_url' => (string) $request->postData('http_url'),
				'inception_date' => (string) $request->postData('inception_date'),
				'legal_form' => (int) $request->postData('legal_form'),
				'juridiction' => (string) $request->postData('juridiction'),
				'opportunity' => (int) $request->postData('opportunity'),
				'dateLastRiskAssessment' => (string) $request->postData('dateLastRiskAssessment'),
				'direct' => (int) $request->postData('direct'),
				'indirect' => (int) $request->postData('indirect'),
				'note' => (string) $request->postData('note'),
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
		$query = "SELECT * FROM ts_entities";
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
			entity_id,
			entity_type_id,
			code,
			organisation,
			entity_group_id,
			address1,
			address2,
			postal_code,
			city,
			state,
			country,
			http_url,
			inception_date,
			legal_form,
			juridiction,
			opportunity,
			dateLastRiskAssessment,
			direct,
			indirect,
			note,
			status,
			update_time,
			created_time
		FROM ts_entities
		WHERE entity_id = :id";
		try {
			$result = parent::$dao->prepare($query);
			$result->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			$result->execute();
			// $result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Entities');
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
		$query = "SELECT COUNT(*) FROM ts_entities";
		return parent::$dao->query($query)->fetchColumn();
	}

	public function add(\Library\HTTPRequest $request) {
		$entities = $this->getData($request);
		$query = "INSERT INTO ts_entities SET 
			entity_type_id = :entity_type_id,
			code = :code,
			organisation = :organisation,
			entity_group_id = :entity_group_id,
			address1 = :address1,
			address2 = :address2,
			postal_code = :postal_code,
			city = :city,
			state = :state,
			country = :country,
			http_url = :http_url,
			inception_date = :inception_date,
			legal_form = :legal_form,
			juridiction = :juridiction,
			opportunity = :opportunity,
			dateLastRiskAssessment = :dateLastRiskAssessment,
			direct = :direct,
			indirect = :indirect,
			note = :note,
			status = :status,
			created_time = NOW()";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);
			
			$result->bindValue(':entity_type_id', $entities->entity_type_id());
			$result->bindValue(':code', $entities->code());
			$result->bindValue(':organisation', $entities->organisation());
			$result->bindValue(':entity_group_id', $entities->entity_group_id());
			$result->bindValue(':address1', $entities->address1());
			$result->bindValue(':address2', $entities->address2());
			$result->bindValue(':postal_code', $entities->postal_code());
			$result->bindValue(':city', $entities->city());
			$result->bindValue(':state', $entities->state());
			$result->bindValue(':country', $entities->country());
			$result->bindValue(':http_url', $entities->http_url());
			$result->bindValue(':inception_date', $entities->inception_date());
			$result->bindValue(':legal_form', $entities->legal_form());
			$result->bindValue(':juridiction', $entities->juridiction());
			$result->bindValue(':opportunity', $entities->opportunity());
			$result->bindValue(':dateLastRiskAssessment', $entities->dateLastRiskAssessment());
			$result->bindValue(':direct', $entities->direct());
			$result->bindValue(':indirect', $entities->indirect());
			$result->bindValue(':note', $entities->note());
			$result->bindValue(':status', $entities->status());
			
			$output['reponse'] = $result->execute();
			$this->lastInsertId = parent::$dao->lastInsertId();
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
		$entities = $this->getData($request);
		$query = "UPDATE ts_entities SET 
			entity_type_id = :entity_type_id,
			code = :code,
			organisation = :organisation,
			entity_group_id = :entity_group_id,
			address1 = :address1,
			address2 = :address2,
			postal_code = :postal_code,
			city = :city,
			state = :state,
			country = :country,
			http_url = :http_url,
			inception_date = :inception_date,
			legal_form = :legal_form,
			juridiction = :juridiction,
			opportunity = :opportunity,
			dateLastRiskAssessment = :dateLastRiskAssessment,
			direct = :direct,
			indirect = :indirect,
			note = :note,
			status = :status
		WHERE entity_id = :id";
		try {
			parent::$dao->beginTransaction();
			$result = parent::$dao->prepare($query);
			
			$result->bindValue(':id', $entities->id(), \PDO::PARAM_INT);
			$result->bindValue(':entity_type_id', $entities->entity_type_id());
			$result->bindValue(':code', $entities->code());
			$result->bindValue(':organisation', $entities->organisation());
			$result->bindValue(':entity_group_id', $entities->entity_group_id());
			$result->bindValue(':address1', $entities->address1());
			$result->bindValue(':address2', $entities->address2());
			$result->bindValue(':postal_code', $entities->postal_code());
			$result->bindValue(':city', $entities->city());
			$result->bindValue(':state', $entities->state());
			$result->bindValue(':country', $entities->country());
			$result->bindValue(':http_url', $entities->http_url());
			$result->bindValue(':inception_date', $entities->inception_date());
			$result->bindValue(':legal_form', $entities->legal_form());
			$result->bindValue(':juridiction', $entities->juridiction());
			$result->bindValue(':opportunity', $entities->opportunity());
			$result->bindValue(':dateLastRiskAssessment', $entities->dateLastRiskAssessment());
			$result->bindValue(':direct', $entities->direct());
			$result->bindValue(':indirect', $entities->indirect());
			$result->bindValue(':note', $entities->note());
			$result->bindValue(':status', $entities->status());
			
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
		$query = "DELETE FROM ts_entities WHERE entity_id = " . (int) $id;
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