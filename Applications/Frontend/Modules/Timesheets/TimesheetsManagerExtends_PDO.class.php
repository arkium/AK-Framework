<?php

namespace Applications\Frontend\Modules\Timesheets;

class TimesheetsManagerExtends_PDO extends \Library\Models\TimesheetsManager_PDO {

	/**
	 * Récupération des périodes
	 * @return array
	 */
	public function getPeriods() {
		parent::$param['data_period_id'] = null;
		$query = "SELECT period_id, start_date, end_date, status
		FROM ts_periods
		ORDER BY start_date";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($output as $row) {
			if ($row['status'] == "1") {
				$output['code'][] = $row['period_id'];
				$output['name'][] = 'Week: ' . $row['start_date'] . ' to ' . $row['end_date'];
			}
			$output['start_date'][$row['period_id']] = $row['start_date'];
			$output['end_date'][$row['period_id']] = $row['end_date'];
		}
		return $output;
	}

	/**
	 * Récupération des pointages pour une période et un utilisateur
	 * @return array
	 */
	public function getTimes() {
		$output = array();
		if (!empty(parent::$param['period_id'])) {
			//$query = "SELECT time_id, task_id, date, duration, comment
			//FROM ts_timesheets
			//WHERE period_id='" . parent::$param['period_id'] . "'
			//	AND user_id='" . parent::$param['user_id'] . "'";
			$query = "
			SELECT
				time_id AS time_id,
				task_id,
				date,
				SEC_TO_TIME( SUM( TIME_TO_SEC( duration ) ) ) AS duration,
				comment AS comment,
				COUNT(*) AS count
			FROM ts_timesheets
			WHERE period_id='" . parent::$param['period_id'] . "'
				AND user_id='" . parent::$param['user_id'] . "'
			GROUP BY date, task_id";
			$result = parent::$dao->query($query);
			while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
				$date = strtotime($row['date']);
				$code_id = 'c' . $row['task_id'];
				$output['id'][$date][$code_id] = empty($row['time_id']) ? '' : $row['time_id'];
				$output['duration'][$date][$code_id] = empty($row['duration']) ? '' : $row['duration'];
				$output['comment'][$date][$code_id] = empty($row['comment']) ? '' : $row['comment'];
			}
		}
		return $output;
	}

	/**
	 * Liste des groupes clients de l'utilisateur
	 * @param array $periods
	 * @return array
	 */
	public function getGroups($periods) {
		$output = array();
		$query = "SELECT cg.entity_group_id, cg.name
		FROM ts_tasks as t, ts_tasks_types as tt, ts_tasks_users as tu,
			ts_entities as c, ts_entities_groups as cg
		WHERE t.task_type_id=tt.task_type_id AND tt.chargeable='1'
			AND t.task_id=tu.task_id AND tu.user_id='" . parent::$user->data['user_id'] . "'
			AND t.customer_id=c.entity_id AND c.entity_group_id=cg.entity_group_id
			AND DATEDIFF(t.end_date, '" . $periods['start_date'][parent::$param['period_id']] . "') > 0
			AND DATEDIFF(t.start_date, '" . $periods['end_date'][parent::$param['period_id']] . "') < 0
		GROUP BY cg.name
		ORDER BY cg.name";
		$result = parent::$dao->query($query);
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$output['code'][] = $row['entity_group_id'];
			$output['name'][] = $row['name'];
		}
		return $output;
	}

	/**
	 * Liste des codes Tasks
	 * @param array $periods
	 * @param int $chargeable
	 * @param int $customer_group_id
	 * @return array
	 */
	public function getTasks($periods, $chargeable = null, $customer_group_id = null, $alluser = false) {
		$output = array();
		$customer_group_text = (empty($customer_group_id)) ? "" : "AND c.entity_group_id='$customer_group_id'";
		$chargeable_text = (empty($chargeable)) ? "" : "AND pt.chargeable='$chargeable'";
		$alluser_text = (!$alluser) ? "AND tu.user_id='" . parent::$param['user_id'] . "'" : "";
		$query = "SELECT t.task_id, t.code, t.name, t.status, eg.name AS namegroup
		FROM ts_tasks AS t, ts_tasks_types AS pt, ts_tasks_users AS tu, ts_entities AS c
		LEFT JOIN ts_entities_groups AS eg USING (entity_group_id)
		WHERE  t.customer_id=c.entity_id $customer_group_text
			AND t.task_type_id=pt.task_type_id $chargeable_text
			AND t.task_id=tu.task_id $alluser_text
			AND DATEDIFF(t.end_date, '" . $periods['start_date'][parent::$param['period_id']] . "') > 0
			AND DATEDIFF(t.start_date, '" . $periods['end_date'][parent::$param['period_id']] . "') < 0
		ORDER BY t.code ASC, eg.name ASC";
		$result = parent::$dao->query($query);
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$output['code'][$row['task_id']] = $row['task_id'];
			$output['name'][$row['task_id']] = $row['code'] . " - " . $row['name'];
			//$output['group'][$row['task_id']] = (!empty($row['namegroup'])) ? $row['namegroup'] : 'Companies';
			$output['status'][$row['task_id']] = ($row['status'] == '0') ? false : true;
			$status = ($row['status'] == '0') ? ' disabled="disabled" ' : '';
			$output['option'][$row['task_id']] = "data-description=\"" . $row['name'] . "\"$status";
		}
		return $output;
	}

	public function getLignes($period_id, $user_id) {
		$output = null;
		$query = "SELECT t.task_id, tt.chargeable, ts.code, ts.closing_date, tt.name, c.organisation
		FROM ts_timesheets as t, ts_tasks as ts, ts_tasks_types as tt, ts_entities as c
		WHERE t.task_id=ts.task_id AND ts.task_type_id=tt.task_type_id
			AND ts.customer_id=c.entity_id
			AND t.period_id ='$period_id'
			AND t.user_id='$user_id'
		GROUP BY t.task_id
		ORDER BY tt.chargeable DESC, ts.code";
		$result = parent::$dao->query($query);
		return $result;
	}

	public function deleteLine($task_id) {
		$user_id = parent::$param['user_id'];
		$period_id = parent::$param['period_id'];
		//Supprime les données de Timesheets y compris celle de Timesheets_hours si existantes
		$query = "DELETE a1, ts_timesheets_hours FROM ts_timesheets AS a1
		LEFT JOIN ts_timesheets_hours
		ON a1.time_id = ts_timesheets_hours.time_id
		WHERE a1.period_id='$period_id' AND a1.task_id='$task_id' AND a1.user_id='$user_id'";
		$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
		return $output;
	}

	public function timeIsAvailable(\Library\HTTPRequest $request) {
		$query = "SELECT time_id, duration
		FROM ts_timesheets
		WHERE period_id='" . parent::$param['period_id'] . "'
			AND user_id='" . parent::$param['user_id'] . "'
			AND date='" . $request->postData('date') . "'
			AND task_id='" . $request->postData('task_id') . "'
		LIMIT 1";
		$row = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		if ($row['time_id'] == $request->postData('time_id'))
			return true;
		return ($row == false) ? true : false;
	}

	private function timeIsFound(\Library\HTTPRequest $request) {
		$date = $request->postData('date');
		$date = date("Ymd", $date);
		$time_id = $request->postData('time_id');
		$op = $request->postData('op');
		if (empty($time_id)) {
			$query = "SELECT time_id, duration
			FROM ts_timesheets
			WHERE period_id='" . parent::$param['period_id'] . "'
				AND user_id='" . parent::$param['user_id'] . "'
				AND date='$date'
				AND task_id='" . $request->postData('task_id') . "'";
		} else {
			$query = "SELECT time_id, duration
			FROM ts_timesheets
			WHERE time_id='$time_id'";
		}
		$query .= " LIMIT 1";
		$row = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		return ($row === false) ? false : $row['time_id'];
	}

	public function saveTime(\Library\HTTPRequest $request) {
		$task_id = $request->postData('task_id');
		$date = $request->postData('date');
		$date = date("Ymd", $date);
		$period_id = parent::$param['period_id'];
		$user_id = parent::$param['user_id'];
		$time_id = $request->postData('time_id');
		$duration = $request->postData('duration');
		$direct = $request->postData('direct');
		$direct = isset($direct) ? $direct : false;
		$comment = $request->postData('comment');
		$task_type_id = 0;

		$time_id_found = $this->timeIsFound($request);
		if ($time_id_found === false) {
			if (!empty($task_id) && !empty($date)) {
				$query = "INSERT INTO ts_timesheets
				VALUES ('','$user_id','$period_id','0','$date','$duration','$task_id','$task_type_id','$comment',NOW(),'')";
			}
		} else {
			$modifycomment = (!$direct) ? ", comment='$comment'" : "";
			$query = "UPDATE ts_timesheets
			SET duration='$duration'$modifycomment
			WHERE time_id='$time_id_found'";
		}
		$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
		$lastID = parent::$dao->lastInsertId();
		$output['lastID'] = ($lastID == 0) ? $time_id_found : $lastID;
		return $output;
	}

	/**
	 * Récupération des périodes précédentes à une date
	 * @param int $period_id
	 * @return array
	 */
	private function lastPeriod($period_id) {
		// Récupération des dates de la période
		$query = "SELECT start_date, end_date FROM ts_periods WHERE period_id='$period_id' LIMIT 1";
		$date = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		// Récupération des périodes précédentes à une date
		$query = "SELECT period_id, start_date, end_date, status
		FROM ts_periods	WHERE end_date < '{$date['start_date']}'
		ORDER BY start_date DESC LIMIT 1";
		$lastDate = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		return $lastDate['period_id'];
	}

	public function displayCodes($period_id) {
		$period_id = $this->lastPeriod($period_id);
		$output = null;
		$query = "SELECT t.task_id, ts.code, ts.name, ts.status, eg.name AS namegroup
		FROM ts_timesheets t, ts_tasks ts, ts_entities AS c
		LEFT JOIN ts_entities_groups AS eg USING (entity_group_id)
		WHERE  t.task_id=ts.task_id
			AND ts.customer_id=c.entity_id
			AND t.user_id='" . parent::$user->data['user_id'] . "'
			AND t.period_id='$period_id'
		GROUP BY t.task_id
		ORDER BY eg.name ASC, ts.code ASC";
		$result = parent::$dao->query($query);
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$output['code'][$row['task_id']] = $row['task_id'];
			$output['name'][$row['task_id']] = $row['code']. " - " . $row['name'];
			//$output['group'][$row['task_id']] = (!empty($row['namegroup'])) ? $row['namegroup'] : 'Companies';
			$status = ($row['status'] == '0') ? ' disabled="disabled" ' : '';
			$output['option'][$row['task_id']] = "data-description=\"" . $row['name'] . "\"$status";
		}
		return $output;
	}

	// ************************************
	// Fonctions pour le pointage atelier *
	// ************************************
	/**
	 * Vérifier si un pointage Atelier est ouvert pour l'utilsateur
	 * @param string $id de l'utilisateur
	 * @return array (reponse -> true ou false, time_id -> Id du pointage)
	 */
	public function lastTimeDirect($id) {
        $query = "SELECT t.time_id, th.hour_id
FROM ts_timesheets AS t
LEFT JOIN ts_timesheets_hours AS th USING (time_id)
WHERE user_id='" .$id. "'
	AND th.end ='00:00:00'
LIMIT 1";
		$result = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		$row['reponse'] = ($result === false) ? false : true ;
		return $row;
	}

	/**
	 * Trouver le time_id avec l'utilisateur, la tâche, la période et la date
	 * @param \Library\HTTPRequest $request
	 * @return mixed false ou time_id
	 */
	private function time_idIsFound(\Library\HTTPRequest $request) {
		$date = $request->postData('date');
		$date = date("Ymd", $date);
		$query = "SELECT time_id
FROM ts_timesheets
WHERE period_id='" . parent::$param['period_id'] . "'
AND user_id='" . parent::$param['user_id'] . "'
AND date='$date'
AND task_id='" . $request->postData('task_id') . "'
LIMIT 1";
		$row = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		return ($row === false) ? false : $row['time_id'];
	}

	/**
	 * Récupération des données de pointage Atelier pour affichage
	 * @param mixed $id pointeur hour_id
	 * @return array Retour des données
	 */
	public function getTimeDirect($id) {
		$query = "SELECT
			t.time_id,
			t.user_id,
			t.period_id,
			t.user_rate_id,
			t.date,
			th.start,
			th.end,
			t.duration,
			t.task_id,
			t.task_type_id,
			t.comment,
			t.update_time,
			t.created_time,
			th.hour_id
		FROM ts_timesheets_hours AS th
		LEFT JOIN ts_timesheets AS t USING (time_id)
		WHERE th.hour_id = :id";
		try {
			$result = parent::$dao->prepare($query);
			$result->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			$result->execute();
			$output = (array) $result->fetch(\PDO::FETCH_NUM);
			$output['reponse'] = true;
		}
		catch (\PDOException $e) {
			$output['reponse'] = 'The database is not able to be read!<br/>';
			$output['reponse'] .= "Code Error: " . $e->getCode() . "<br/>";
			$output['reponse'] .= "Syntax Error: " . $e->getMessage();
		}
		return $output;
	}

	/**
	 * Sauvegarde des données de pointage Atelier
	 * @param \Library\HTTPRequest $request
	 * @return array
	 */
	public function saveTimeDirect(\Library\HTTPRequest $request) {
		$task_id = $request->postData('task_id');
		$date = $request->postData('date');
		$date = date("Ymd", $date);
		$period_id = parent::$param['period_id'];
		$user_id = parent::$param['user_id'];
		$time_id = $request->postData('time_id');
		$hour_id = $request->postData('hour_id');
		$start = $request->postData('start');
		$end = $request->postData('end');
		$duration = $request->postData('duration');
		$direct = $request->postData('direct');
		$direct = isset($direct) ? $direct : false;
		$comment = $request->postData('comment');
		$task_type_id = 0;

		$time_id_found = $this->time_idIsFound($request);

		// MAJ table timesheets
		if (!empty($time_id) && !empty($hour_id)) {
			$modifycomment = (!$direct) ? ", comment='$comment'" : "";
			$query = "UPDATE ts_timesheets
SET duration=ADDTIME('$duration', duration)$modifycomment
WHERE time_id='$time_id'";
		} elseif ($time_id_found === false) {
			$query = "INSERT INTO ts_timesheets
VALUES ('','$user_id','$period_id','0','$date','$duration','$task_id','$task_type_id','$comment','',NOW())";
		}
		if (!empty($query)) {
			$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
			$lastID = parent::$dao->lastInsertId();
		}
		$time_id2 = (empty($lastID)) ? $time_id_found : $lastID;
		$output['lastID'] = $time_id2;

		// MAJ table timesheets_hours
		if (!empty($time_id) && !empty($hour_id)) {
			$query = "UPDATE ts_timesheets_hours
SET end='$end' WHERE hour_id='$hour_id'";
		} else {
			$query = "INSERT INTO ts_timesheets_hours
VALUES ('','$time_id2','$start','','',NOW())";
		}
		$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';

		return $output;
	}

	/**
	 * Récupérer les commentaires de la tâche et celui du pointage
	 * @param \Library\HTTPRequest $request
	 * @return mixed Array
	 */
	public function noteTask(\Library\HTTPRequest $request) {
		$task_id = $request->postData('task_id');
		$query = "SELECT note
FROM ts_tasks
WHERE task_id='" .$task_id. "'
LIMIT 1";
		$row1 = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);

		$date = $request->postData('date');
		$date = date("Ymd", $date);
		$query = "SELECT comment
FROM ts_timesheets
WHERE period_id='" . parent::$param['period_id'] . "'
AND user_id='" . parent::$param['user_id'] . "'
AND date='$date'
AND task_id='$task_id'
LIMIT 1";
		$row2 = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);

		$result = array_merge((array)$row1, (array)$row2);
		$result['reponse'] = ($row1 === false && $row2 === false) ? false : true ;
		return $result;
	}


	/**
	 * Ajouter des temps non pointés - Disponible uniquement pour admin
	 * @param \Library\HTTPRequest $request
	 * @return array
	 */
	public function addTime(\Library\HTTPRequest $request) {
		$task_id = $request->postData('task_id');
		$user_id = $request->postData('user_id');
		$date = $request->postData('date');
		$start = $request->postData('start');
		$end = $request->postData('end');
		$duration = $request->postData('duration');
		$comment = $request->postData('comment');
		$task_type_id = 0;

		// Récupération de la période selon la date
		$query = "SELECT period_id
FROM ts_periods	WHERE (end_date > '$date' AND start_date < '$date') OR end_date='$date' OR start_date='$date'
ORDER BY start_date DESC
LIMIT 1";
		$lastDate = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		$period_id = $lastDate['period_id'];

		// Vérifier si la tâche n'a pas été pointé pour le même jour
		$query = "SELECT time_id
FROM ts_timesheets
WHERE period_id='$period_id'
AND user_id='$user_id'
AND date='$date'
AND task_id='$task_id'
LIMIT 1";
		$row = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
		$time_id = ($row === false) ? false : $row['time_id'];

		// MAJ table timesheets
		if ($time_id === false) {
			$query = "INSERT INTO ts_timesheets
VALUES ('','$user_id','$period_id','0','$date','$duration','$task_id','$task_type_id','$comment','',NOW())";
			$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
			$lastID = parent::$dao->lastInsertId();
		} else {
			$query = "UPDATE ts_timesheets
SET duration=ADDTIME('$duration', duration)
WHERE time_id='$time_id'";
			$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
			$lastID = $time_id;
		}
		$output['lastID'] = $lastID;

		// MAJ table timesheets_hours
		$query = "INSERT INTO ts_timesheets_hours
VALUES ('','$lastID','$start','$end','',NOW())";
		$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';

		return $output;
	}

}