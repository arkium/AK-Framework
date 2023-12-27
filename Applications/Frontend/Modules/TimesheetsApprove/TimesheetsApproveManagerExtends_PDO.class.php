<?php

namespace Applications\Frontend\Modules\TimesheetsApprove;

class TimesheetsApproveManagerExtends_PDO extends \Library\Models\TimesheetsApprovalManager_PDO {

	/**
	 * Récupération de toutes les périodes
	 * @return array (code, name, start_end(period_id), end_date(period_id))
	 */
	public function getPeriods() {
		parent::$param['data_period_id'] = null;
		$query = "SELECT period_id, start_date, end_date, status
FROM ts_periods
ORDER BY start_date DESC";
		$output = parent::$dao->query($query)->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($output as $row) {
			$status_text = ($row['status'] == '1') ? 'Open' : 'Closed';
			$output['code'][] = $row['period_id'];
			$output['name'][] = $row['start_date'] . ' to ' . $row['end_date'] . ' - ' . $status_text;
			$output['start_date'][$row['period_id']] = $row['start_date'];
			$output['end_date'][$row['period_id']] = $row['end_date'];
		}
		return $output;
	}
}