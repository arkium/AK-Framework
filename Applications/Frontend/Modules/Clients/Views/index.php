<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="div_opportunity_filter" class="field">
	<label for="opportunity_filter">Filtrer par :</label>
	<select id="opportunity_filter" name="status">
		<option value="-1">Tous</option>
		<?php echo $fct->droplist("Open", parent::$param['OpportunityClient']); ?>
	</select>
</div>

<div id="title_table" class="ten wide column">
	<h2 class="ui header">
		<i class="users icon"></i>
		<div class="content">
			<?php echo _("Liste des clients"); ?>
			<div class="sub header"><?php echo _("Gestion de vos clients"); ?></div>
		</div>
	</h2>
</div>

<div id="unseen" class="ui attached segment">
	<table class="ui celled padded table" id="exemple">
		<thead>
			<tr>
				<th><?php echo _("Code"); ?></th>
				<th><?php echo _("Nom"); ?></th>
				<th><?php echo _("Pays"); ?></th>
				<th><?php echo _("Groupe"); ?></th>
				<th><?php echo _("Type"); ?></th>
				<th><?php echo _("Statut"); ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<div id="actionBtn" class="ui teal buttons">
	<div id="openView" class="ui button"><?php echo _("Voir le client"); ?></div>
	<?php if ($user->permissions['add'] || $user->permissions['edit'] || $user->permissions['delete']) : ?>
	<div class="ui floating dropdown icon button" ovisible="true">
		<i class="dropdown icon"></i>
		<div class="menu">
			<?php echo ($user->permissions['add']) ? "<div class=\"item\" ovisible=\"true\" id=\"openAdd\">"._("Ajouter un nouveau client")."</div>\n" : ""; ?>
			<?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"openEdit\">"._("Editer le client")."</div>\n" : ""; ?>
			<?php echo ($user->permissions['delete']) ? "<div class=\"item\" id=\"delete\">"._("Supprimer le client")."</div>\n" : "";	?>
		</div>
	</div>
	<?php endif; ?>
</div>