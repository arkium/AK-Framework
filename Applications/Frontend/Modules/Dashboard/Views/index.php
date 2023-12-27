<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="unseen" class="ui attached segment">
	<div class="ui stackable grid">
		<!--  Panel My Projects -->
		<div class="sixteen wide column">
			<div class="ui top attached block header">
				<?php echo _("Véhicule en cours avec pointage") ?>
			</div>
			<div class="ui attached raised segment">
				<div id="scroll_myprojects">
					<table id="exemple" class="ui selectable celled padded table">
						<thead>
							<tr>
								<th>
									<?php echo _("Code véhicule") ?>
								</th>
								<th>
									<?php echo _("Client") ?>
								</th>
								<th>
									<?php echo _("Type d'activité") ?>
								</th>
								<th>
									<?php echo _("Finir pour le") ?>
								</th>
								<th>
									<?php echo _("Total heure (100eme)") ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$type_name = array(
									"Non-chargeable",
									"Chargeable"
							);
							$nbre_ligne = 0;
							//		AND tt.chargeable = '1'

							$query = "SELECT ts.task_id, ts.code, ts.closing_date, tt.name, c.organisation, ts.milestone_type_id, ts.project_proposal, tt.chargeable, ROUND(SUM(TIME_TO_SEC(t.duration)/3600),2) AS time
	FROM ts_timesheets as t, ts_tasks as ts, ts_tasks_types as tt, ts_entities as c
	WHERE t.task_id=ts.task_id
		AND ts.task_type_id=tt.task_type_id
		AND ts.customer_id=c.entity_id
		AND ts.status = '1'
	GROUP BY ts.code";
							$result = parent::$dao->query($query);
							$lastType = null;
							while (list($task_id, $code, $closing_date, $name, $organisation, $milestone_type_id, $project_proposal, $chargeable, $time) = $result->fetch()) {
								$nbre_ligne ++;
								print "<tr id=\"$task_id\" data-dashboard=\"myproject\">\n";
								$pop = 'data-content= "' . _('Cliquer sur la ligne pour afficher la timeline') . '"';
								$span = ($project_proposal == '1') ? ' <i class="icon red flag"></i>' : '';
								$span1 = ($chargeable != '0') ? ' <i class="icon blue euro sign"></i>' : '';
								//$span1 = ($milestone_type_id != '0') ? ' <i class="icon blue flag"></i>' : '';
								print "<td><span class=\"pop\" $pop>" . $code . $span . $span1 . "</span></td>\n";
								print "<td>" . $organisation . "</td>\n";
								print "<td>" . $name . "</td>\n";
								print "<td>" . $closing_date . "</td>\n";
								print "<td>$time</td>\n";
								print "</tr>\n";
							} ?>
						</tbody>
					</table>
				</div>
			</div>
			<div id="nbre_ligne_project" class="ui attached segment">
				<span>
					<?php echo ("Affichage de $nbre_ligne véhicules en cours de pointage."); ?>
				</span>
			</div>
			<div class="ui secondary attached segment">
				<i class="icon blue euro sign"></i>
				<small>
					<?php echo ("Véhicule à facturer") ?>
				</small>
				<i class="icon red flag"></i>
				<small>
					<?php echo ("Véhicule proposé à valider") ?>
				</small>
			</div>
		</div>
		<!-- End Panel My Projects -->
		<!-- Panel My Timesheets -->
		<div class="eight wide column" style="display:none;">
			<div class="ui top attached block header">My Timesheets</div>
			<div class="ui attached raised segment">
				<div id="scroll_mytimesheets">
					<table id="exemple1" class="ui selectable celled padded table">
						<thead>
							<tr>
								<th>Period</th>
								<th>Status</th>
								<th>Total Time</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$nbre_ligne = 0;
							$query = "SELECT
			period_id,
			CONCAT(start_date, ' - ', end_date) AS period,
			TIME_FORMAT(SEC_TO_TIME(IFNULL(duration1,0)+IFNULL(duration2,0)),'%H:%i') AS total,
			status_timesheet
		FROM ts_timesheets_approval
		WHERE user_id='" . parent::$user->data['user_id'] . "'
			AND status='1'
		ORDER BY period DESC";
							$result = parent::$dao->query($query);
							$lastType = null;
							while (list($period_id, $period, $total, $status_timesheet) = $result->fetch()) {
								$nbre_ligne ++;
								print "<tr id=\"$period_id\" data-dashboard=\"mytimesheet\">\n";
								// $pop = 'data-container="body" data-toggle="pmyproject" data-placement="right" data-content="Client : ' . $organisation . "<br />Description : " . $name . '" data-original-title=""';
								print "<td><span>$period</span></td>\n";
								$status = ($status_timesheet != '') ? parent::$param['status_timesheet']['name'][$status_timesheet] : "Open";
								print "<td>$status</td>\n";
								print "<td>$total</td>\n";
								print "</tr>\n";
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div id="nbre_ligne_timesheet" class="ui bottom attached header">
				<div class="pull-right">
					<?php echo "<span>Showing $nbre_ligne open timesheets</span>"; ?>
				</div>
			</div>
		</div>
		<!-- End Panel My Timesheets -->
	</div>
</div>
