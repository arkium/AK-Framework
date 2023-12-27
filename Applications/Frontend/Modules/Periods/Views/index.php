<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="div_status_filter" class="field">
    <label for="status_filter">Filter by :</label>
    <select id="status_filter" name="status">
        <option value="-1">All</option>
        <?php echo $fct->droplist("Open", parent::$param['status_filter']); ?>
    </select>
</div>

<div id="title_table" class="ten wide column">
    <h2 class="ui header">
        <i class="settings icon"></i>
        <div class="content">
            Periods List
            <div class="sub header">Manage your periods</div>
        </div>
    </h2>
</div>

<div id="unseen" class="ui attached segment">
    <table class="ui celled padded table" id="exemple">
        <thead>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="actionBtn" class="ui teal buttons">
    <div id="openView" class="ui button">View</div>
    <?php if ($user->permissions['add'] || $user->permissions['edit'] || $user->permissions['delete']) : ?>
    <div class="ui floating dropdown icon button" ovisible="true">
        <i class="dropdown icon"></i>
        <div class="menu">
            <?php echo ($user->permissions['add']) ? "<div class=\"item\" ovisible=\"true\" id=\"openAdd\">Add new period</div>\n" : ""; ?>
            <?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"openEdit\">Edit period</div>\n" : ""; ?>
            <?php echo ($user->permissions['delete']) ? "<div class=\"item\" id=\"delete\">Delete period</div>\n" : ""; ?>
            <div class="divider" ovisible="true"></div>
            <?php echo ($user->permissions['add']) ? "<div class=\"item\" ovisible=\"true\" id=\"openAdds\">Add periods</div>\n" : ""; ?>
            <?php echo ($user->permissions['delete']) ? "<div class=\"item\" ovisible=\"true\" id=\"openDeletes\">Delete periods</div>\n" : ""; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div id="modalAddPeriods" class="ui small modal">
    <i class="close icon"></i>
    <div class="header">Add Periods</div>
    <div class="content">
        <form id="frmAddPeriods" class="ui form" action="periods" method="post">
            <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
            <input type="hidden" id="op" name="op" value="add_periods" />
            <div class="field">
                <label for="year">Year for the periods to be added:</label>
                <select id="yearAdd" name="year"></select>
            </div>
        </form>
    </div>
    <div class="actions">
        <div class="ui ok button">Ok</div>
        <div class="ui cancel button">Cancel</div>
    </div>
</div>

<div id="modalDeletePeriods" class="ui small modal">
    <i class="close icon"></i>
    <div class="header">Delete Periods</div>
    <div class="content">
        <form id="frmDeletePeriods" class="ui form" action="periods" method="post">
            <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
            <input type="hidden" id="op" name="op" value="delete_periods" />
            <div class="field">
                <label for="year">Year for the periods to be deleted:</label>
                <select id="yearDelete" name="year"></select>
            </div>
        </form>
    </div>
    <div class="actions">
        <div class="ui ok button">Ok</div>
        <div class="ui cancel button">Cancel</div>
    </div>
</div>