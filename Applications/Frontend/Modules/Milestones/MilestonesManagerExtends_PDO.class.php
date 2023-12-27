<?php

namespace Applications\Frontend\Modules\Milestones;

class MilestonesManagerExtends_PDO extends \Library\Models\MilestonesManager_PDO {

	public function getColonnes($milestone_type_id) {
		// Récupération des colonnes du Milestone Type
		$output = null;
		$query = "SELECT mf.name
		FROM ts_milestones m, ts_milestones_fields mf
		WHERE m.milestone_field_id=mf.milestone_field_id
			AND m.milestone_type_id='$milestone_type_id'
			AND mf.show_field='1'
		ORDER BY mf.order_field";
		$result = parent::$dao->query($query);
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$output['name'][$row['name']] = $row['name'];
		}
		return $output;
	}

	public function getLignes($milestone_type_id) {
		// Récupération des codes Projects avec un Milestone Type
		$output = null;
		$query = "SELECT task_id, code 
		FROM ts_tasks 
		WHERE milestone_type_id='$milestone_type_id'
		AND milestone_type_id>0
		AND status='1'";
		$result = parent::$dao->query($query);
		return $result;
	}

	public function getDate($milestone_type_id) {
		// Récupération des données des Milestones
		$output = null;
		$query = "SELECT m.milestone_id, m.task_id, m.date, mf.name, mf.show_field 
		FROM ts_milestones m, ts_tasks t, ts_milestones_fields mf
		WHERE m.task_id=t.task_id
			AND m.milestone_field_id=mf.milestone_field_id
			AND t.milestone_type_id='$milestone_type_id'
			AND mf.show_field='1'
			AND t.status='1'";
		$result = parent::$dao->query($query);
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$output['date'][$row['task_id']][$row['name']] = $row['date'];
			$output['milestone_id'][$row['task_id']][$row['name']] = $row['milestone_id'];
			$output['show_field'][$row['task_id']][$row['name']] = $row['show_field'];
		}
		return $output;
	}

	public function getMilestoneTypeList() {
		// Liste Milestones Types
		$output = null;
		$query = "SELECT milestone_type_id, code, name FROM ts_milestones_types ORDER BY code";
		$result = parent::$dao->query($query);
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$output['code'][] = $row['milestone_type_id'];
			$output['name'][] = $row['code'] . ' - ' . $row['name'];
		}
		if (empty($output)) {
			$output['code'][] = '';
			$output['name'][] = 'No list available';
		}
		return $output;
	}
}