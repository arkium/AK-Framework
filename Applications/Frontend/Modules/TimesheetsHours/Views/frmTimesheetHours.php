<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
	<form id="frmTimesheethours" class="ui form" action="timesheetshours" method="post">
		<h4 id="title-frmTimesheethours" class="ui dividing header">
			<i class="history icon"></i>
			<div class="content">
				<?php echo _("Pointage du temps"); ?>
				<div class="sub header">
					<?php echo _("Pointage de") . " $last_name, $first_name " . _("du") . " $date"; ?>
				</div>
			</div>
		</h4>

		<input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
		<input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
		<input type="hidden" id="hour_id" name="hour_id" value="<?php echo $hour_id; ?>" />
		<input type="hidden" id="time_id" name="time_id" value="<?php echo $time_id; ?>" />
		<input type="hidden" id="period_id" name="period_id" value="<?php echo $period_id; ?>" />
		<input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>" />
		<input type="hidden" id="date" name="date" value="<?php echo $date; ?>" />
		<input type="hidden" id="task_id" name="task_id" value="<?php echo $task_id; ?>" />

		<div class="three fields">
			<div class="field">
				<label for="start">
					<?php echo _("Heure de début"); ?>:
				</label>
				<input type="text" id="start" name="start" maxlength="5" placeholder="<?php echo _("00:00"); ?>" />
			</div>
			<div class="field">
				<label for="end">
					<?php echo _("Heure de fin"); ?>:
				</label>
				<input type="text" id="end" name="end" maxlength="5" placeholder="<?php echo _("00:00"); ?>" />
			</div>
			<div class="field">
				<label for="duration">
					<?php echo _("Durée"); ?>:
				</label>
				<input type="text" id="duration" name="duration" maxlength="5" placeholder="00:00" readonly />
			</div>
		</div>

		<div class="ui error message"></div>
		<button id="btnSave" class="ui primary button" type="button"><?php echo _("Enregistrer"); ?></button>
		<button id="btnCancel" class="ui button" type="button" data-return="<?php echo $return; ?>" ovisible="true">
			<?php echo _("Annuler"); ?>
		</button>
	</form>
</div>
