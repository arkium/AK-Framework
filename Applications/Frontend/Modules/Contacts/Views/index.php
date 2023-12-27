<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="uk-width-8-10">
	<h4>View Contacts</h4>
</div>
<div id="div_contacttype_filter" class="uk-form-row">
	<label for="contacttype_filter">Filter by : <select id="contacttype_filter" name="contacttype_id">
			<option value="">All</option>
			<?php echo $fct->droplist("All", parent::$param['contact_type_id']); ?>
	</select>
	</label>
</div>
<div id="unseen">
	<table class="uk-table uk-table-hover uk-table-condensed uk-table-striped" id="exemple">
		<thead>
			<tr>
				<th>Company</th>
				<th>Type contact</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="actionBtn" class="uk-button-group">
	<button type="button" id="openView" class="uk-button" data-uk-dialog="{name:'frmContact', action:'view'}">View</button>
<?php if ($user->permissions['add'] || $user->permissions['edit'] || $user->permissions['delete']) : ?>
	<div data-uk-dropdown="{mode:'click'}">
		<button class="uk-button" ovisible="true">
			<i class="uk-icon-caret-down"></i>
		</button>
		<div class="uk-dropdown uk-dropdown-small">
			<ul class="uk-nav uk-nav-dropdown">
				<?php echo ($user->permissions['add']) ? "<li ovisible=\"true\"><a id=\"openAdd\" data-uk-dialog=\"{name:'frmContact', action:'add'}\">Add new contact</a></li>\n" : ""; ?>
				<?php echo ($user->permissions['edit']) ? "<li><a id=\"openEdit\" data-uk-dialog=\"{name:'frmContact', action:'edit'}\">Edit contact</a></li>\n" : ""; ?>
				<?php echo ($user->permissions['delete']) ? "<li><a id=\"delete\" data-uk-dialog=\"{name:'frmContact', action:'delete'}\">Delete contact</a></li>\n" : "";	?>
			</ul>
		</div>
	</div>
<?php endif; ?>
</div>