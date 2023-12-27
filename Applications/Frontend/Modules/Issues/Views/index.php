<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="col-sm-8">
	<h4>Issues Log</h4>
</div>
<div class="row" id="unseen">
	<div class="col-sm-12">
		<table id="exemple" class="table table-hover table-condensed table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Summary</th>
					<th><span class="glyphicon glyphicon-paperclip"></span></th>
					<th>Reporter</th>
					<th>Type</th>
					<th>Date</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div id="action_btn" class="btn-group">
	<button type="button" id="openView" class="btn btn-primaire">View comments</button>
<?php if ($user->permissions['add'] || $user->permissions['edit'] || $user->permissions['delete']) : ?>
	<button type="button" class="btn btn-primaire dropdown-toggle" data-toggle="dropdown" ovisible="true">
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu"><?php
	echo ($user->permissions['add']) ? "<li ovisible=\"true\"><a id=\"openAdd\">Add new issue</a></li>\n" : "";
	echo ($user->permissions['edit']) ? "<li><a id=\"openEdit\">Edit issue</a></li>\n" : "";
	echo ($user->permissions['delete']) ? "<li><a id=\"delete\">Delete issue</a></li>\n" : "";
	?></ul>
<?php endif; ?>
</div>