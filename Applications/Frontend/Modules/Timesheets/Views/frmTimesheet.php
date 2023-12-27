<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
	<form id="frmTimesheet" class="ui form" action="timesheets" method="post">
		<h4 id="title-frmTimesheet" class="ui dividing header">
			<i class="history icon"></i>
			<div class="content">
				<?php echo _("Pointage du temps"); ?>
				<div class="sub header">
					<?php echo _("Pointage de") . " $last_name, $first_name " . _("du") . " $date"; ?>
				</div>
			</div>
		</h4>
		<input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>">
		<input type="hidden" id="op" name="op" value="<?php echo $op; ?>">
		<input type="hidden" id="time_id" name="time_id" value="<?php echo $time_id; ?>">

		<input type="hidden" id="user_id" name="user_id" data-notremove="true" value="<?php echo parent::$param['user_id']; ?>">
		<input type="hidden" id="period_id" name="period_id" data-notremove="true" value="<?php echo parent::$param['period_id']; ?>">

		<div class="three fields">
			<div class="field">
				<label for="task_id"><?php echo _("Code véhicule"); ?>:</label>
				<select id="task_id" name="task_id">
					<option value=""><?php echo _("Choisir le code véhicule"); ?></option><?php echo $fct->droplist($task_id, parent::$param['data_task_id']); ?>
				</select>
			</div>
			<div class="field">
				<label for="date"><?php echo _("Date"); ?>:</label>
				<div class="ui left icon input">
					<i class="calendar icon"></i>
					<input type="date" id="date" name="date" value="<?php echo $date; ?>" <?php echo $readonly; ?>>
				</div>
			</div>
			<div class="field">
				<label for="duration"><?php echo _("Durée"); ?>:</label>
				<div class="ui action input">
					<input type="text" id="duration" name="duration" maxlength="5" placeholder="00:00" readonly>
					<button id="btnHours" class="ui button" type="button" ovisible="true"><?php echo _("Ajouter ou modifier des pointages"); ?></button>
				</div>
			</div>
		</div>

		<div class="field">
			<label for="comment"><?php echo _("Fait"); ?>:</label>
			<textarea class="form-control" id="comment" name="comment" rows="8" cols="30"></textarea>
		</div>
		<div class="ui error message"></div>
		<button id="btnSave" class="ui primary button" type="button"><?php echo _("Enregistrer"); ?></button>
		<button id="btnCancel" class="ui button" type="button" data-return="<?php echo parent::$param['return']; ?>" ovisible="true"><?php echo _("Annuler"); ?></button>
	</form>
</div>
