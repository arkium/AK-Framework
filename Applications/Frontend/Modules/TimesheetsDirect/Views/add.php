<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
	<form id="frmAddTime" class="ui form" action="timeclock/json" method="post">
		<h4 id="title-frmUser" class="ui dividing header">
			<?php echo _("Ajouter des pointages atelier"); ?>
		</h4>
		<input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
		<input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
		<div class="fields">
			<div class="five wide required field">
				<label for="task_id">
					<?php echo _("Véhicule:"); ?>
				</label>
                <select id="task_id" name="task_id">
                    <?php echo $fct->droplist("", parent::$param['data_task_id']); ?>
                </select>
			</div>
			<div class="five wide required field">
				<label for="user_id">
					<?php echo _("Code utilisateur:"); ?>
				</label>
				<select id="user_id" name="user_id">
					<?php echo $fct->droplist("", parent::$param['data_user_id']); ?>
				</select>
			</div>
			<div class="six wide required field">
				<label for="date">
					<?php echo _("Date:"); ?>
				</label>
				<input type="date" id="date" name="date" />
			</div>
		</div>
        <div class="fields">
            <div class="five wide field">
                <label for="start">
                    <?php echo _("Heure début:"); ?>
                </label>
                <input type="text" id="start" name="start" />
            </div>
            <div class="five wide field">
                <label for="end">
                    <?php echo _("Heure fin:"); ?>
                </label>
                <input type="text" id="end" name="end" />
            </div>
            <div class="six wide field">
                <label for="duration">
                    <?php echo _("Total heure:"); ?>
                </label>
                <input type="text" id="duration" name="duration" readonly="" />
            </div>
        </div>
		<div class="fields">
            <div class="sixteen wide field">
                <label for="comment">
                    <?php echo _("Fait"); ?>:
                </label>
                <textarea id="comment" name="comment"></textarea>
            </div>
		</div>
		<div class="ui error message"></div>
		<button id="btnSave" class="ui primary button" type="button">
			<?php echo _("Enregistrer"); ?>
		</button>
		<button id="btnCancel" class="ui button" type="button" data-return="<?php echo $return; ?>" ovisible="true">
			<?php echo _("Annuler"); ?>
		</button>
	</form>
</div>
