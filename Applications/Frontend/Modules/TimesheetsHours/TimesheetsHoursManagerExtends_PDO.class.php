<?php

namespace Applications\Frontend\Modules\TimesheetsHours;

class TimesheetsHoursManagerExtends_PDO extends \Library\Models\TimesheetsHoursManager_PDO {

	/**
	 * MAJ de la durée des pointages
	 * @param \Library\HTTPRequest $request
	 * @return array
	 */
	public function updateDuration($json, $time_id) {
		if (!empty($time_id) && $json['reponse']) {
			$query = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) AS time
FROM (
SELECT IF(end>start,TIMEDIFF(end, start),'00:00:00') AS duration
FROM ts_timesheets_hours
WHERE time_id='".$time_id."'
) AS VIEW";
			$result = parent::$dao->query($query)->fetch(\PDO::FETCH_ASSOC);
			$query = "UPDATE ts_timesheets
SET duration='" . $result['time'] . "'
WHERE time_id='$time_id'";
			$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
		}
		return $json;
	}

}