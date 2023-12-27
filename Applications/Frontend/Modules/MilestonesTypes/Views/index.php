<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="col-sm-8 col-xs-10">
	<h4>Manage Milestone Lists</h4>
</div>
<div class="row" id="unseen">
	<div class="col-sm-12">
		<table class="table table-hover table-condensed table-striped" id="exemple">
			<thead>
				<tr>
					<th>Code</th>
					<th>Name</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div id="action_btn" class="btn-group">
	<button type="button" id="openView" class="btn btn-primaire">View</button>
<?php if ($user->permissions['add'] || $user->permissions['edit'] || $user->permissions['delete']) : ?>
	<button type="button" class="btn btn-primaire dropdown-toggle" data-toggle="dropdown" ovisible="true">
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu"><?php
	echo ($user->permissions['add']) ? "<li ovisible=\"true\"><a href=\"#\" id=\"openAdd\">Add New Milestone List</a></li>\n" : "";
	echo ($user->permissions['edit']) ? "<li><a href=\"#\" id=\"openEdit\">Edit Milestone List</a></li>\n" : "";
	echo ($user->permissions['delete']) ? "<li><a href=\"#\" id=\"delete\">Delete Milestone List</a></li>\n" : "";
	?></ul>
<?php endif; ?>
</div>