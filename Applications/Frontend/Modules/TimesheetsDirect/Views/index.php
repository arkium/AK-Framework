<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
    <form id="frmTimeDirect" class="ui form" action="timeclock/json" method="post">
        <h4 id="title-frmTimeDirect" class="ui dividing header"><?php echo _("Pointage atelier"); ?></h4>
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
        <input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
        <input type="hidden" id="time_id" name="time_id" value="<?php echo $time_id; ?>" />
		<input type="hidden" id="hour_id" name="hour_id" value="<?php echo $hour_id; ?>" />

        <input type="hidden" id="user_id" name="user_id" data-notremove="true" value="<?php echo parent::$param['user_id']; ?>" />
        <input type="hidden" id="period_id" name="period_id" data-notremove="true" value="<?php echo parent::$param['period_id']; ?>" />
        <input type="hidden" id="date" name="date" data-notremove="true" />
        <input type="hidden" id="start" name="start" data-notremove="true" />
        <input type="hidden" id="end" name="end" data-notremove="true" />
        <input type="hidden" id="duration" name="duration" data-notremove="true" />

        <div class="four fields">
            <div class="field">
                <div class="ui small statistic">
                    <div id="txtDate" class="value">
                        2016-02-23
                    </div>
                    <div class="label">
                        <?php echo _("Date"); ?>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="ui small statistic">
                    <div id="txtStart" class="value">
                        13:46
                    </div>
                    <div class="label">
                        <?php echo _("Heure début"); ?>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="ui small statistic">
                    <div id="txtEnd" class="value">
                        16:45
                    </div>
                    <div class="label">
                        <?php echo _("Heure fin"); ?>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="ui small statistic">
                    <div id="txtDuration" class="value">
                        02:00
                    </div>
                    <div class="label">
                        <?php echo _("Durée"); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="field">
            <label for="task_id"><?php echo _("Véhicule"); ?>:</label>
            <select id="task_id" name="task_id">
				<option value=""><?php echo _("Sélectionner"); ?> ...</option>
                <?php echo $fct->droplist("", parent::$param['data_task_id']); ?>
            </select>
        </div>
        <div class="two fields">
            <div class="field">
                <label for="note"><?php echo _("A faire"); ?>:</label>
                <textarea id="note" readonly=""></textarea>
            </div>
            <div class="field">
                <label for="comment"><?php echo _("Fait"); ?>:</label>
                <textarea id="comment" name="comment"></textarea>
            </div>
        </div>
        <div class="ui error message"></div>
        <button id="btnSave" class="ui primary button" type="button"><?php echo _("Enregistrer"); ?></button>
        <button id="btnCancel" class="ui button" type="button" data-return="<?php echo $return; ?>" ovisible="true"><?php echo _("Annuler"); ?></button>
    </form>
</div>
