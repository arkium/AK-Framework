<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="div_status_filter" class="field">
    <label for="status_filter">Filter by :</label>
    <select id="status_filter" name="status">
        <option value="-1"><?php echo _("tout"); ?></option>
        <?php echo $fct->droplist("Open", parent::$param['status_filter']); ?>
    </select>
</div>

<div id="title_table" class="ten wide column">
    <h2 class="ui header">
        <i class="history icon"></i>
        <div class="content">
            <?php echo _("Mes feuilles de temps"); ?>
            <div class="sub header"><?php echo _("Gestion du temps"); ?></div>
        </div>
    </h2>
</div>

<div id="unseen" class="ui attached segment">
    <table class="ui celled padded table" id="exemple">
        <thead>
            <tr>
                <th><?php echo _("Période"); ?></th>
                <th><?php echo _("Statut"); ?></th>
                <th><?php echo _("Facturable"); ?></th>
                <th><?php echo _("Non facturable"); ?></th>
                <th><?php echo _("TOTAL"); ?></th>
                <th><?php echo _("Feuille de temps"); ?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="actionBtn" class="ui teal buttons">
    <div id="viewtimesheet" class="ui button"><?php echo _("Afficher"); ?></div>
    <?php if ($user->permissions['edit']) : ?>
    <div class="ui floating dropdown icon button" ovisible="true">
        <i class="dropdown icon"></i>
        <div class="menu">
            <?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"edittimesheet\">"._("Editer")."</div>\n" : ""; ?>
            <?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"submittimesheet\">"._("Soumettre la feuille")."</div>\n" : ""; ?>
        </div>
    </div>
    <?php endif; ?>
</div>