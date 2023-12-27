<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="div_period_filter" class="field">
	<label for="period_id">Access to the period : </label>
	<button id="next" class="ui icon button" type="button" title="Next period">
		<i class="arrow left icon"></i>
	</button>
	<select id="period_id" name="period_id">
		<?php echo $fct->droplist(parent::$param['period_id'], parent::$param['data_period_id']); ?>
	</select>
	<button id="previous" class="ui icon button" type="button" title="Previous period">
		<i class="arrow right icon"></i>
	</button>
</div>

<div id="title_table" class="ten wide column">
	<h2 class="ui header">
		<i class="history icon"></i>
		<div class="content">
			<?php echo _("Liste des feuilles de temps"); ?>
			<div class="sub header"><?php echo _("Gestion du temps"); ?></div>
		</div>
	</h2>
</div>

<div id="unseen" class="ui attached segment">
	<table class="ui striped celled padded table" id="exemple">
		<thead>
			<tr>
				<th><?php echo _("Société"); ?></th>
				<th><?php echo _("Nom"); ?></th>
				<th><?php echo _("Etat de la période"); ?></th>
				<th><?php echo _("Facturable"); ?></th>
				<th><?php echo _("Non facturable"); ?></th>
				<th><?php echo _("TOTAL"); ?></th>
				<th><?php echo _("Etat"); ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

</div>

<div id="actionBtn" class="ui teal buttons">
	<div id="viewtimesheet" class="ui button">View</div>
	<?php if ($user->permissions['edit'] || $user->permissions['approval']) : ?>
	<div class="ui floating dropdown icon button" ovisible="true">
		<i class="dropdown icon"></i>
		<div class="menu">
			<?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"edittimesheet\">Edit timesheet</div>\n" : ""; ?>
			<?php echo ($user->permissions['approval']) ? "<div class=\"item\" id=\"approvaltimesheet\">Approval timesheet</div>\n" : ""; ?>
			<?php echo ($user->permissions['approval']) ? "<div class=\"item\" id=\"opentimesheet\">Open timesheet</div>\n" : ""; ?>
		</div>
	</div>
	<?php endif; ?>
</div>
