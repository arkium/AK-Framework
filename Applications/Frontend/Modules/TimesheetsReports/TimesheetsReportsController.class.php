<?php

namespace Applications\Frontend\Modules\TimesheetsReports;

class TimesheetsReportsController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		// Liste des rapports
		parent::$param['timesheet_reports'][] = array('id' => '1', 'value' => 'Activités par clients');
		parent::$param['timesheet_reports'][] = array('id' => '2', 'value' => 'Activités facturables');
		parent::$param['timesheet_reports'][] = array('id' => '3', 'value' => 'Activités par utilisateurs');
		//parent::$param['timesheet_reports'][] = array('id' => '4', 'value' => 'Activités non facturable');
		parent::$param['timesheet_reports'][] = array('id' => '5', 'value' => 'Pointage en atelier');
		//parent::$param['timesheet_reports'][] = array('id' => '6', 'value' => 'Heures par activités et utilisateurs');

	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());

// Récupérer la page à afficher sinon page par défaut
		$repord_id = $request->getData('report_id');
		$repord_id = empty($repord_id) ? '5' : $repord_id;
		$this->page->addVar('repord_id', $repord_id);

		// Récupérer la période à afficher sinon période par défaut
		$from = $request->getData('date1');
		$to = $request->getData('date2');
		parent::$param['from'] = empty($from) ? date("Y-m-d") : $from;
		parent::$param['to'] = empty($to) ? date("Y-m-d") : $to;
		$this->page->addVar('date1', parent::$param['from']);
		$this->page->addVar('date2', parent::$param['to']);

		// Initialiser les scripts JS pour le formulaire
		$this->page->addArray('config_scriptJS', "var data = [{\"id\":\"1\",\"rapport\":\"" . $repord_id . "\",\"date1\":\"" . parent::$param['from'] . "\",\"date2\":\"" . parent::$param['to'] . "\"}];");
		$this->page->addArray('config_scriptJS', "var data2 = " . json_encode(parent::$param['timesheet_reports'], JSON_UNESCAPED_UNICODE) . ";");

		$date_filter = (!empty(parent::$param['from']) && !empty(parent::$param['to'])) ? " AND t.date<='".parent::$param['to']."' AND t.date>='".parent::$param['from']."' " : '';

		$text = null;
		if ($repord_id == '1') { // Activités par clients
			$text = $this->Rapport_1($date_filter);
		} elseif ($repord_id == '2') { // Activités facturables
			$text = $this->Rapport_2($date_filter);
		} elseif ($repord_id == '3') { // Activités par utilisateurs
			$text = $this->Rapport_3($date_filter);
		} elseif ($repord_id == '4') { // Activités non facturable
			$text = $this->Rapport_4($date_filter);
		} elseif ($repord_id == '5') { // Détails des pointages en atelier
			$text = $this->Rapport_details_pointage_atelier($date_filter);
		} elseif ($repord_id == '6') { // Heures par activités et utilisateurs
			$text = $this->Rapport_6($date_filter);
		}

		$this->page->addArray('config_scriptJS', "var colonnes = " . $text['colonnes'] .  ";");
		$this->page->addArray('config_scriptJS', "var database = " . json_encode($text['database'], JSON_UNESCAPED_UNICODE) .  ";");
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			switch ($request->postData('op')) {
				case 'submit' :
					$query = "INSERT INTO ts_timesheets_users
					VALUES ('', '" . $request->postData('period_id') . "', '" . parent::$user->data['user_id'] . "', NOW(), '', 'SUBMIT', '', NOW())";
					$json['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					if ($request->postData('email') === '1') {
						// timesheet_email_forapproval($request->postData('period_id').", parent::$user->data['user_id']);
					}
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	private function Rapport_1($date_filter) {
		// Activités par clients
		$query = "
		SELECT
			t.task_id AS task_id,
			e.code AS client,
			CONCAT(ts.code, ' - ', ts.name) AS code,
			ROUND(SUM(TIME_TO_SEC(t.duration)/3600),2) AS time,
			ts.note AS 'comment'
		FROM ts_timesheets AS t, ts_tasks AS ts, ts_tasks_types AS tt, ts_entities AS e, ts_users AS u
		WHERE
			t.task_id = ts.task_id
			AND ts.task_type_id=tt.task_type_id
			AND ts.customer_id=e.entity_id
			AND t.user_id=u.user_id
			$date_filter
		GROUP BY ts.code, client
		ORDER BY e.code, ts.code";

		$result = parent::$dao->query($query);
		$output['database'] = $result->fetchAll(\PDO::FETCH_ASSOC);

		$output['colonnes'] = '[
				{id:"task_id", header:"Id", hidden:true},
				{id:"client", header:"Client", sort:"string_strict", adjust:"data"},
				{id:"code", header:"Véhicule", sort:"string_strict", adjust:"data"},
				{id:"time", header:"Durée des pointages", width:200},
				{id:"comment", header:"Commentaire", width: 350}
			]';

		return $output;
	}

	private function Rapport_2($date_filter) {
		// Activités facturables
		$query = "
		SELECT
			t.task_id AS task_id,
			CONCAT(tt.code, ' - ', tt.name) AS type,
			CONCAT(ts.code, ' - ', ts.name) AS code,
			ROUND(SUM(TIME_TO_SEC(t.duration)/3600),2) AS time,
			ts.note AS comment
		FROM ts_timesheets AS t, ts_tasks AS ts, ts_tasks_types AS tt, ts_entities AS e
		WHERE
			t.task_id=ts.task_id
			AND ts.task_type_id=tt.task_type_id
			AND tt.chargeable='1'
			AND ts.customer_id=e.entity_id
			$date_filter
		GROUP BY ts.code
		ORDER BY type, e.code, ts.code";

		$result = parent::$dao->query($query);
		$output['database'] = $result->fetchAll(\PDO::FETCH_ASSOC);

		$output['colonnes'] = '[
				{id:"task_id", header:"Id", hidden:true},
				{id:"type", header:"Tâche", sort:"string_strict", adjust:"data"},
				{id:"code", header:"Véhicule", sort:"string_strict", adjust:"data"},
				{id:"time", header:"Durée des pointages", width:200},
				{id:"comment", header:"Commentaire", width: 350}
			]';

		return $output;
	}

	private function Rapport_3($date_filter) {
		// Activités par utilisateurs
		$query = "
		SELECT
			t.task_id AS task_id,
			CONCAT(u.code, ' - ', u.first_name) AS user,
			CONCAT(ts.code, ' - ', ts.name) AS code,
			t.date AS date,
			ROUND(TIME_TO_SEC(t.duration)/3600,2) AS time,
			t.comment AS comment
		FROM ts_timesheets AS t, ts_tasks AS ts, ts_users AS u
		WHERE
			t.task_id=ts.task_id
			AND t.user_id=u.user_id
			$date_filter
		ORDER BY t.date DESC, u.code ASC, ts.code ASC";

		$result = parent::$dao->query($query);
		$output['database'] = $result->fetchAll(\PDO::FETCH_ASSOC);

		$output['colonnes'] = '[
				{id:"task_id", header:"Id", hidden:true},
				{id:"user", header:"Utilisateur", sort:"string_strict", adjust:"data"},
				{id:"code", header:"Véhicule", sort:"string_strict", adjust:"data"},
				{id:"date", header:"Date du pointage", sort:"string_strict", width:200},
				{id:"time", header:"Durée des pointages", width:200},
				{id:"comment", header:"Commentaire", width: 350}
			]';

		return $output;
	}

	private function Rapport_4($date_filter) {
		// Non-chargeable Type
		$query = "
		SELECT
			ANY_VALUE(t.task_id) AS task_id,
			CONCAT(u.code, ' - ', u.first_name) AS user,
			CONCAT(tt.code, ' - ', tt.name) AS type,
			ROUND(SUM(TIME_TO_SEC(duration)/3600),2) AS time,
			ANY_VALUE(t.comment) AS comment
		FROM ts_timesheets AS t, ts_tasks AS ts, ts_tasks_types AS tt, ts_users AS u
		WHERE
			t.task_id=ts.task_id
			AND ts.task_type_id=tt.task_type_id
			AND tt.chargeable='0'
			AND t.user_id=u.user_id
			$date_filter
		GROUP BY type, u.code
		ORDER BY u.code, type";

		$result = parent::$dao->query($query);
		$output['database'] = $result->fetchAll(\PDO::FETCH_ASSOC);

		$output['colonnes'] = '[
				{id:"task_id", header:"Id", hidden:true},
				{id:"user", header:"Utilisateur", sort:"string_strict", adjust:"data"},
				{id:"type", header:"Activité", width:200},
				{id:"time", header:"Durée des pointages", width:200},
				{id:"comment", header:"Commentaire", width: 350}
			]';

		return $output;
	}

	private function Rapport_details_pointage_atelier($date_filter) {
		// Détails des pointages en atelier
		$query = "
		SELECT
			h.hour_id AS hour_id,
            t.time_id AS time_id,
			t.task_id AS task_id,
			CONCAT(u.code, ' - ', u.first_name) AS user,
			p.code AS vehicule,
			t.date AS 'date pointage',
			h.start AS 'début pointage',
			h.end AS 'fin pointage',
			TIMEDIFF(h.end, h.start) AS 'durée Hrs',
            ROUND(TIME_TO_SEC(TIMEDIFF(h.end, h.start))/3600,2) AS 'durée 100eHrs',
            SEC_TO_TIME(TIME_TO_SEC(duration)) as 'total task',
			d.total AS 'total day',
			t.comment AS 'comment'
		FROM
			ts_tasks AS p,
			ts_users AS u,
			(SELECT date, user_id, SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) AS total FROM ts_timesheets GROUP BY date, user_id ) AS d,
			ts_timesheets AS t
			LEFT JOIN ts_timesheets_hours as h
				ON t.time_id = h.time_id
		WHERE
			t.task_id = p.task_id
			AND t.user_id = u.user_id
			AND (t.date = d.date
			AND t.user_id = d.user_id)
			$date_filter
		ORDER BY t.date DESC, u.code ASC, h.start ASC;";

		$result = parent::$dao->query($query);
		$output['database'] = $result->fetchAll(\PDO::FETCH_ASSOC);

		$output['colonnes'] = '[
				{id:"hour_id", header:"hour_id", hidden:true},
				{id:"time_id", header:"time_id", hidden:true},
				{id:"task_id", header:"task_id", hidden:true},
				{id:"user", header:"Utilisateur", sort:"string_strict", adjust:"data"},
				{id:"vehicule", header:"Véhicule", sort:"string_strict"},
				{id:"date pointage", header:"Date", sort:"string_strict", adjust:"data"},
				{id:"début pointage", header:"Début", adjust:"data"},
				{id:"fin pointage", header:"Fin", adjust:"data"},
				{id:"durée Hrs", header:"Durée", adjust:"data"},
				{id:"durée 100eHrs", header:"100e", adjust:"data"},
				{id:"total task", header:"Tâche", adjust:"data"},
				{id:"total day", header:"Jour", adjust:"data"},
				{id:"comment", header:"Commentaire", adjust:"data"}
			]';

		return $output;
	}

	private function Rapport_6($date_filter) {
		// Heures par activités et utilisateurs
		$query = "SELECT user_id AS user, CONCAT(code, ' - ', first_name) AS name FROM ts_users WHERE status = 1 ORDER BY code";
		$result = parent::$dao->query($query);
		$text = "";
		$text2 = "";
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$text .= "ROUND(SUM(IF(user = '".$row['user']."', duree, 0)),2) AS '".$row['name']."',";
			$text2 .= "{id:\"" . $row['name'] . "\", header:\"" . $row['name'] . "\", width:120},";
		}
		$query = "
		SELECT
			ANY_VALUE(id) AS time_id,
			vehicule,
			$text
			ROUND(SUM(duree),2) AS time
		FROM (
			SELECT
				ANY_VALUE(ts.task_id) AS id,
				ts.code AS vehicule,
				u.user_id AS user,
				ROUND(SUM(TIME_TO_SEC(t.duration)/3600),2) AS duree
			FROM ts_timesheets AS t, ts_users AS u, ts_tasks AS ts
			WHERE
				u.user_id = t.user_id
				AND ts.task_id = t.task_id
				$date_filter
			GROUP BY vehicule, user
			ORDER BY vehicule, user
		)_
		GROUP BY vehicule WITH ROLLUP";

		$result = parent::$dao->query($query);
		$output['database'] = $result->fetchAll(\PDO::FETCH_ASSOC);

		$output['colonnes'] = '[
				{id:"time_id", header:"Id", hidden:true},
				{id:"vehicule", header:"Véhicule"},
				' . $text2 . '
				{id:"time", header:"Durée"},
				{id:"comment", header:"Commentaire", hidden:true}
			]';

		return $output;
	}

	public function executeCron(\Library\HTTPRequest $request) {
		//parent::$security->verifier_token();
		$json['reponse'] = "test";
		parent::$httpResponse->json($json);
	}

}