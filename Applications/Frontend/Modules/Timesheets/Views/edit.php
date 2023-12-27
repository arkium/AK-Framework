<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="unseen" class="ui attached segment">
	<div class="ui stackable grid">
		<div id="title_table" class="ten wide column">
			<h2 class="ui header">
				<i class="history icon"></i>
				<div class="content">
					<?php echo _("Feuille de temps"); ?>
					<div class="sub header">
						<?php
						echo _("Période du ") . parent::$param['data_period_id']['start_date'][parent::$param['period_id']] . _(" au ") . parent::$param['data_period_id']['end_date'][parent::$param['period_id']];
						$query = "SELECT first_name, last_name, typetimesheet FROM ts_users WHERE user_id='" . parent::$param['user_id'] . "'";
						$result = parent::$dao->query($query);
						list($first_name, $last_name, $typetimesheet) = $result->fetch();
						$readonly = ($typetimesheet == 1) ? "readonly" : "" ;
						if (isset($_GET["p"])) {
							echo " - ". "Feuille de temps de $last_name, $first_name";
						}
						?>
					</div>
				</div>
			</h2>
		</div>
	</div>

	<table class="ui small striped celled table" id="exemple">
		<thead>
			<tr>
				<th><?php echo _("Code véhicule"); ?></th>
                <?php
				$data_date_colonne = parent::$param['data_date_colonne']['date'];
				if (is_array($data_date_colonne)) {
					reset($data_date_colonne);
					foreach ($data_date_colonne as $key => $val) {
						$currentDayStr = strftime("%a", $val);
						$jour = date("N", $val);
						$text = $currentDayStr . "<br>" . date("d", $val);
						$text = ($jour == '6' || $jour == '7') ? "<b>$text</b>" : $text;
						$text_class = ($jour == '6' || $jour == '7') ? " class=\"weekend\"" : " class=\"week\"";
						print "<th$text_class>$text</th>\n";
					}
				}
                ?>
			</tr>
		</thead>
		<form id="timesheet" name="timesheet" action="index.php?act=timesheet_edit" method="post">
			<input type="hidden" id="user_id" name="user_id" value="<?php echo parent::$param['user_id'] ?>" />
			<input type="hidden" id="period_id" name="period_id" value="<?php echo parent::$param['period_id'] ?>" />
			<input type="hidden" name="op" value="save_timesheet" />
			<tbody>
                <?php
				$type_name = array(
					  _("Non facturable"),
					  _("Facturable")
				  );
				$nbre_ligne = 0;
				$data_time = array();
				$result = parent::$param['data_lignes'];
				$lastType = null;
				while (list($task_id, $type, $code, $closing_date, $name, $organisation) = $result->fetch()) {
					if ($lastType != $type) {
						print "<tr>\n<td colspan=\"16\" class=\"group\"><div class=\"ui blue horizontal label\">$type_name[$type]</div></td>\n</tr>\n";
						$lastType = $type;
					}
					$nbre_ligne ++;
					print "<tr id=\"$task_id\">\n";
					$pop = 'class="pop" data-content="' . $organisation . ' - ' . $name . '"';
					print "<td class='codeName selectable center aligned'><span $pop>" . $code . "</span></td>\n";
					if (is_array($data_date_colonne)) {
						reset($data_date_colonne);
						$data_times = parent::$param['data_times'];
						foreach ($data_date_colonne as $key => $val) {
							$jour = date("N", $val);
							$text_class = ($jour == '6' || $jour == '7') ? " class=\"weekend\"" : " class=\"week\"";
							$p = (isset($_GET["p"])) ? "1" : "";
							if (!empty($data_times['duration'][$val]['c' . $task_id])) {
								$a = empty($data_time[$val]) ? null : $data_time[$val];
								$data_time[$val] = $fct->Addtime($a, $data_times['duration'][$val]['c' . $task_id]);
								$times_id = $data_times['id'][$val]['c' . $task_id];
								$time = strtotime($data_times['duration'][$val]['c' . $task_id]);
								$log_message = $data_times['comment'][$val]['c' . $task_id];
								$class_comment = (!empty($log_message)) ? "class=\"comment\"" : "";
								print "<td$text_class>\n";
								$pop = (!empty($log_message)) ? 'data-content="' . $log_message . '"' : '';
								print "<input $class_comment data-datetext=\"" . date("d/m/Y", $val) . "\" data-date=\"$val\"  data-task_id=\"$task_id\" data-time_id=\"$times_id\" data-p=\"$p\" $pop size=\"2\" value=\"" . date("H:i", $time) . "\" maxlength=\"5\" $readonly/>";
								print "</td>\n";
							} else {
								print "<td$text_class>\n";
								print "<input data-datetext=\"" . date("d/m/Y", $val) . "\" data-date=\"$val\" data-task_id=\"$task_id\" data-p=\"$p\" size=\"2\" value=\"\" maxlength=\"5\" $readonly/>";
								print "</td>\n";
							}
						}
					}
					print "</tr>\n";
				}
                ?>
			</tbody>
		</form>
		<tfoot>
			<tr>
				<th><?php echo _("Temps Total"); ?></th>
                <?php
				if (is_array($data_date_colonne)) {
					reset($data_date_colonne);
					foreach ($data_date_colonne as $key => $val) {
						$jour = date("N", $val);
						$text_class = ($jour == '6' || $jour == '7') ? " class=\"weekend\"" : " class=\"week\"";
						$text = (!empty($data_time[$val]) && $data_time[$val] != '0:00') ? $data_time[$val] : "";
						print "<th$text_class id=\"$val\">$text</th>\n";
					}
				}
                ?>
			</tr>
		</tfoot>
	</table>

	<div class="ui stackable grid">
		<div class="eight wide column">
			<div id="actionBtn" class="ui teal buttons">
				<div id="returnoverview" class="ui button" ovisible="true" data-return="<?php echo parent::$param['return']; ?>">
					<?php echo _("Retour à la page précédente"); ?>
				</div>
				<div class="ui floating dropdown icon button" ovisible="true">
					<i class="dropdown icon"></i>
					<div class="menu">
						<?php echo ($user->permissions['add']) ? "<div class=\"item\" ovisible=\"true\" id=\"addcode\">" . _("Ajouter une nouvelle ligne") . "</div>\n" : ""; ?>
						<?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"deletecode\">" . _("Supprimer la ligne") . "</div>\n" : ""; ?>
						<?php echo ($user->permissions['add']) ? "<div class=\"item\" id=\"viewcode\">" . _("Voir le code véhicule") . "</div>\n" : ""; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="right aligned eight wide column">
			<?php echo "Showing $nbre_ligne entries"; ?>
		</div>
	</div>
</div>
