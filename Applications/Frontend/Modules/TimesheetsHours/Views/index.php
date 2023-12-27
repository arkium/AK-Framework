<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>

<div id="title_table" class="ten wide column">
	<h2 class="ui header">
		<i class="history icon"></i>
		<div class="content">
			<?php echo _("Feuille de temps"); ?>
			<div class="sub header">
				<?php echo _("Pointage de") . " $last_name, $first_name " . _("du") . " $date"; ?>
			</div>
		</div>
	</h2>
</div>

<div id="unseen" class="ui attached segment">
	<table class="ui striped celled padded table" id="exemple" data-timeid="<?php echo $time_id; ?>">
		<thead>
			<tr>
				<th>
					<?php echo _("Heure début pointage"); ?>
				</th>
				<th>
					<?php echo _("Heure fin pointage"); ?>
				</th>
				<th>
					<?php echo _("Durée pointage"); ?>
				</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<div id="actionBtn" class="ui teal buttons">
	<div id="returnoverview" class="ui button" ovisible="true" data-return="<?php echo $return; ?>">
		<?php echo _("Retour à la page précédente"); ?>
	</div>
	<div class="ui floating dropdown icon button" ovisible="true">
		<i class="dropdown icon"></i>
		<div class="menu">
			<?php echo "<div class=\"item\" id=\"openView\">" . _("Voir le pointage") . "</div>\n"; ?>
			<?php echo ($user->permissions['add']) ? "<div class=\"item\" ovisible=\"true\" id=\"openAdd\">" . _("Ajouter un pointage") . "</div>\n" : ""; ?>
			<?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"openEdit\">" . _("Editer le pointage") . "</div>\n" : ""; ?>
			<?php echo ($user->permissions['delete']) ? "<div class=\"item\" id=\"delete\">" . _("Supprimer le pointage") . "</div>\n" : "";	?>
		</div>
	</div>
</div>