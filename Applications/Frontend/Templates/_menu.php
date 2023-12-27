<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<!-- logo -->
<img id="logo_menu" src="Ressources/images/gillet.jpg" alt="logo" class="ui tiny image" style="margin:5px" />
<!-- menu -->
<div id="menu" class="ui top attached stackable menu">
	<a href="." class="item">
		<i class="home icon"></i>
	</a>
	<?php if ($user->isAuthorized('Companies')): ?>
	<div class="ui dropdown item">
		<?php echo _("Clients"); ?>
		<i class="dropdown icon"></i>
		<div class="menu">
			<a href="clients_index" class="item">
				<?php echo _("Liste des clients"); ?>
			</a>
			<a href="frmclient" class="item">
				<?php echo _("Créer un client</a>"); ?>
			</a>
			<!--<a href="contacts_index" class="item">View Contacts</a>-->
			<!--<a href="#" class="item" id="mm_add_contact" data-uk-dialog="{name:'frmContact', action:'add'}">Create a Contact</a>-->
			<?php if ($user->permissions['admin'] || $user->permissions['approval']):
			?>
			<div class="divider"></div>
			<a href="clients_approval" class="item">
				<?php echo _("Valider les nouveaux clients"); ?>
			</a>
			<a href="frmentitygroup" class="item">
				<?php echo _("Gérer les groupes d'entités"); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if ($user->isAuthorized('Tasks')): ?>
	<div class="ui dropdown item">
		<?php echo _("Gestion des véhicules"); ?>
		<i class="dropdown icon"></i>
		<div class="menu">
			<!--<a href="milestones_edit" class="item">View Milestones</a>-->
			<a href="tasks_index" class="item">
				<?php echo _("Liste des véhicules"); ?>
			</a>
			<a href="frmtask" class="item">
				<?php echo _("Créer un véhicule"); ?>
			</a>
			<?php if ($user->permissions['admin']): ?>
			<div class="divider"></div>
			<a href="tasks_approval" class="item">
				<?php echo _("Approuver les nouveaux véhicules"); ?>
			</a>
			<a href="tasksusers_index" class="item">
				<?php echo _("Assigner les véhicules"); ?>
			</a>
			<a href="taskstypes_index" class="item">
				<?php echo _("Gérer les types d'activités"); ?>
			</a>
			<a href="frmtaskfamily" class="item">
				<?php echo _("Gérer les familles d'activités"); ?>
			</a>
			<!--<a href="milestonestypes_index" class="item">Manage Milestone Lists</a>-->
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if ($user->isAuthorized('Timesheets')): ?>
	<div class="ui dropdown item">
		<?php echo _("Gestion du temps"); ?>
		<i class="dropdown icon"></i>
		<div class="menu">
			<a href="timesheetssummary_index" class="item">
				<?php echo _("Mes feuilles de temps"); ?>
			</a>
			<div class="divider"></div>
			<?php if ($user->permissions['admin'] || $user->permissions['approval']): ?>
			<a href="timesheetsapprove_index" class="item">
				<?php echo _("Liste des feuilles de temps"); ?>
			</a>
			<a href="timeclock" class="item">
				<?php echo _("Pointage atelier"); ?>
			</a>
			<a href="timeclock_add" class="item">
				<?php echo _("Ajouter pointage atelier"); ?>
			</a>
			<div class="divider"></div>
			<a href="timesheetsreports_index" class="item">
				<?php echo _("Rapports"); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if ($user->permissions['admin']): ?>
	<div class="ui dropdown item">
		<?php echo _("Paramètres"); ?>
		<i class="dropdown icon"></i>
		<div class="menu">
			<a href="users_index" class="item">
				<?php echo _("Utilisateurs"); ?>
			</a>
			<a href="usersroles_index" class="item">
				<?php echo _("Permissions"); ?>
			</a>
			<a href="companies_index" class="item">
				<?php echo _("Mes sociétés"); ?>
			</a>
			<a href="periods_index" class="item">
				<?php echo _("Périodes"); ?>
			</a>
		</div>
	</div>
	<?php endif; ?>
	<div class="ui dropdown item">
		<?php echo _("Bienvenue"); ?>, <?php echo parent::$user->data['first_name']; ?>
		<i class="dropdown icon"></i>
		<div class="menu">
			<!--<a href="issues_index" class="item">Issues Log</a>-->
			<a href="about" class="item">
				<?php echo _("A propos"); ?>
			</a>
			<div class="divider"></div>
			<a href="logout" class="item">
				<i class="sign out icon"></i>
				<?php echo _("Quitter"); ?>
			</a>
		</div>
	</div>
</div>
<!-- end menu -->
