<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="ten wide column">
    <h2 class="ui header">
        <i class="folder icon"></i>
        <div class="content">
            Projects Approval List
            <div class="sub header">Manage your projects approval</div>
        </div>
    </h2>
</div>

<div id="unseen" class="ui attached segment">
    <form id="approval_form" action="tasks" method="post">
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
        <input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
        <table class="ui celled padded table" id="exemple">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </form>
</div>

<div id="actionBtn" class="ui teal buttons">
    <div id="approval" class="ui button" ovisible="true">Approval</div>
</div>
