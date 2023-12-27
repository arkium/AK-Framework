<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="div_status_filter" class="field">
    <label for="status_filter">Filter by :</label>
    <select id="status_filter" name="status">
        <option value="-1">All</option>
        <?php echo $fct->droplist("All", parent::$param['status_filter']); ?>
    </select>
</div>

<div id="title_table" class="ten wide column">
    <h2 class="ui header">
        <i class="settings icon"></i>
        <div class="content">
            Companies List
            <div class="sub header">Manage your Companies</div>
        </div>
    </h2>
</div>

<div id="unseen" class="ui attached segment">
    <table class="ui celled padded table" id="exemple">
        <thead>
            <tr>
                <th>Code</th>
                <th>Organisation</th>
                <th>Country</th>
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
            <?php echo ($user->permissions['add']) ? "<div class=\"item\" ovisible=\"true\" id=\"openAdd\">Add new company</div>\n" : ""; ?>
            <?php echo ($user->permissions['edit']) ? "<div class=\"item\" id=\"openEdit\">Edit company</div>\n" : ""; ?>
            <?php echo ($user->permissions['delete']) ? "<div class=\"item\" id=\"delete\">Delete company</div>\n" : "";	?>
        </div>
    </div>
    <?php endif; ?>
</div>
