<?php

namespace Applications\Frontend\Modules\Tasks;

class TasksManagerExtends_PDO extends \Library\Models\TasksManager_PDO {

	public function approvalTask($select) {
		$json['reponse'] = false;
		if (!empty($select) && is_array($select)) {
			reset($select);
			foreach ($select as $key => $value) {
				$query = "UPDATE ts_tasks SET
				project_proposal='0'
				WHERE task_id='$value'";
				$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				if (!is_bool($json['reponse']))
					break;
			}
		}
		if (is_bool($json['reponse'])) {
			$json['title'] = ($json['reponse']) ? 'Update' : 'Information';
			$json['msg'] = ($json['reponse']) ? 'Proposal Projects were converted into Projects' : 'Please check the Proposal Projects and then click on approve';
			$json['reponse'] = true;
		}
		return $json;
	}

	public function viewStaff($task_id) {
		$query = "SELECT user_id FROM ts_tasks_users WHERE task_id='$task_id'";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			$output[] = $row['user_id'];
		}
		return $output;
	}

	public function addStaff($json, $staff, $task_id) {
		if (!empty($staff) && is_array($staff) && $json['reponse']) {
			reset($staff);
			foreach ($staff as $key => $value) {
				$query = "INSERT INTO ts_tasks_users SET
				task_id='$task_id',
				user_id='$value',
				created_time=NOW()";
				$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				if ($json['reponse'] !== true)
					break;
			}
		}
		return $json;
	}

	public function editStaff($json, $staff, $task_id) {
		if ($json['reponse']) {
			$query = "DELETE FROM ts_tasks_users WHERE task_id='$task_id'";
			$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
			if (!empty($staff) && is_array($staff) && $json['reponse']) {
				reset($staff);
				foreach ($staff as $key => $value) {
					$query = "INSERT INTO ts_tasks_users SET
					task_id='$task_id',
					user_id='$value',
					created_time=NOW()";
					$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					if ($json['reponse'] !== true)
						break;
				}
			}
		}
		return $json;
	}

	public function newCode(\Library\HTTPRequest $request) {
		$query = "SELECT code FROM ts_entities WHERE entity_id='" . $request->postData('invoicing_entity_id') . "'";
		$result = parent::$dao->query($query);
		list($codecompany) = $result->fetch();
		$codecompany = (empty($codecompany)) ? '????' : $codecompany;
		
		$query = "SELECT code FROM ts_entities WHERE entity_id='" . $request->postData('customer_id') . "'";
		$result = parent::$dao->query($query);
		list($codecustomer) = $result->fetch();
		$codecustomer = (empty($codecustomer)) ? '????' : $codecustomer;
		
		$query = "SELECT code, name, chargeable FROM ts_tasks_types WHERE task_type_id='" . $request->postData('task_type_id') . "'";
		$result = parent::$dao->query($query);
		list($codetask, $nametask, $chargeable) = $result->fetch();
		$codetask = (empty($codetask)) ? '????' : $codetask;
		
		$date = $request->postData('closing_date');
		$datetime3 = new \DateTime($date);
		$closing_date_text = (empty($date)) ? '????' : $datetime3->format('Y/m');
		
		$query = "SELECT code FROM ts_entities WHERE entity_id='" . $request->postData('intermediate_id') . "'";
		$result = parent::$dao->query($query);
		list($codeintermediate) = $result->fetch();
		$codeintermediate = (empty($codeintermediate)) ? 'DIRECT' : $codeintermediate;
		
		$num_proj = $request->postData('num_proj');
		$num_proj = (empty($num_proj)) ? '????' : sprintf("%04d", $num_proj);
		
		// Création du code projet
		if ($codecompany === $codecustomer && $chargeable == 0) {
			$output[0] = $codecompany . '-' . $codetask . '-' . $nametask;
		} else {
			$output[0] = $codecompany . '-' . $codecustomer . '-' . $codetask . '-' . $closing_date_text . '-' . $codeintermediate . '-' . $num_proj;
		}
		$output[1] = $nametask;
		$output['reponse'] = true;
		return $output;
	}

	public function checkDates(\Library\HTTPRequest $request) {
		$start_date = (string) $request->postData('start_date');
		$end_date = (string) $request->postData('end_date');
		try {
			$datetime1 = new \DateTime($start_date);
			$datetime2 = new \DateTime($end_date);
		} catch (Exception $e) {
			return $output['reponse'] = $e->getMessage();
		}
		
		$start_day = $datetime1->format('d');
		$start_month = $datetime1->format('m');
		$start_year = $datetime1->format('Y');
		$end_day = $datetime2->format('d');
		$end_month = $datetime2->format('m');
		$end_year = $datetime2->format('Y');
		
		$interval = $datetime1->diff($datetime2);
		$diff = $interval->format('%R%a');
		
		$output['reponse'] = true;
		// Vérification si la date de fin est valide
		if ((!checkdate($end_month, $end_day, $end_year))) {
			$output['reponse'] = "The end date is invalid in format YYYY-MM-DD.";
		}
		// Vérification si la date de début est valide
		if ((!checkdate($start_month, $start_day, $start_year))) {
			$output['reponse'] = "The stard date is invalid in format YYYY-MM-DD.";
		}
		// Vérification si start_date > end_date
		if ($diff < 0) {
			$output['reponse'] = "The end date is smaller than the start date.";
		}
		return $output;
	}

	public function getField($milestone_type_id) {
		$query = "SELECT name
		FROM ts_milestones_fields
		WHERE milestone_type_id='$milestone_type_id'
		ORDER BY order_field ASC";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		$content = "";
		foreach ($output as $row) {
			$content .= '
				<tr>
					<td><span>' . $row['name'] . '</span></td>
					<td><input type="text" class="form-control date-milestone" name="datemilestone[]" value=""></td>
				</tr>';
		}
		return $content;
	}

	public function add_milestones($json, $datemilestone, $milestone_type_id, $task_id) {
		if (is_array($datemilestone) && $json['reponse']) {
			$query = "SELECT milestone_field_id, name
			FROM ts_milestones_fields
			WHERE milestone_type_id='$milestone_type_id'
			ORDER BY milestone_field_id";
			$result = parent::$dao->query($query);
			$output = $result->fetchAll();
			$i = 0;
			foreach ($output as $row) {
				$date = (!empty($datemilestone[$i])) ? "'$datemilestone[$i]'" : "NULL";
				$query = "INSERT INTO ts_milestones SET
				task_id='$task_id',
				milestone_type_id='$milestone_type_id',
				milestone_field_id='" . $row['milestone_field_id'] . "',
				date=" . $date . ",
				created_time=NOW()";
				$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				$i ++;
			}
		}
		return $json;
	}

	public function editField($task_id) {
		$query = "SELECT mf.name, m.date
		FROM ts_milestones m, ts_milestones_fields mf
		WHERE m.milestone_field_id=mf.milestone_field_id
			AND m.task_id='$task_id'
		ORDER BY mf.order_field ASC";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		$content = "";
		foreach ($output as $row) {
			$content .= '
				<tr>
					<td><span>' . $row['name'] . '</span></td>
					<td><input type="text" class="form-control date-milestone" name="datemilestone[]" value="' . $row['date'] . '"></td>
				</tr>';
		}
		return $content;
	}

	public function edit_milestones($json, $datemilestone, $milestone_type_id, $task_id) {
		if ($json['reponse']) {
			$query = "SELECT DISTINCT milestone_type_id FROM ts_tasks WHERE task_id='$task_id'";
			$result = parent::$dao->query($query)->fetch();
			$old_milestone_type_id = $result['milestone_type_id'];
			if ($old_milestone_type_id != $milestone_type_id) {
				$query = "SELECT DISTINCT milestone_id FROM ts_milestones WHERE task_id='$task_id' LIMIT 1";
				$check_milestone = parent::$dao->query($query)->fetch();
				if ($check_milestone !== false) {
					$query = "DELETE FROM ts_milestones WHERE task_id='$task_id'";
					$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				}
				$json = $this->add_milestones($json, $datemilestone, $milestone_type_id, $task_id);
			} else {
				$query = "SELECT m.milestone_id
				FROM ts_milestones m, ts_milestones_fields mf
				WHERE m.milestone_field_id=mf.milestone_field_id
					AND m.task_id='$task_id'
				ORDER BY mf.order_field";
				$result = parent::$dao->query($query);
				$output = $result->fetchAll();
				$i = 0;
				foreach ($output as $row) {
					$date = (!empty($datemilestone[$i])) ? "'$datemilestone[$i]'" : "NULL";
					$query = "UPDATE ts_milestones SET
					date=" . $date . "
					WHERE milestone_id='" . $row['milestone_id'] . "'";
					$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					$i ++;
				}
			}
		}
		return $json;
	}
}