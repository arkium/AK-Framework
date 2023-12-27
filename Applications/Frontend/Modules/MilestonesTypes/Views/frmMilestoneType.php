<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<form id="frmMilestoneType" class="form-horizontal" action="milestonestypes" method="post">
	<input type="hidden" id="token" name="token" value="">
	<input type="hidden" id="op" name="op" value="">
	<input type="hidden" id="milestone_type_id" name="milestone_type_id" value="">
	<input type="hidden" id="count" name="count" value="1">
	<div class="col-sm-12">
		<div class="form-group">
			<label for="code" class="col-sm-3 control-label">Code:</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" id="code" name="code">
			</div>
		</div>
		<div class="form-group">
			<label for="name" class="col-sm-3 control-label">Name:</label>
			<div class="col-sm-9">
				<textarea class="form-control" id="name" name="name" rows="2" cols="45"></textarea>
			</div>
		</div>
	</div>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">Milestones</a></li>
			<li><a href="#tabs-2">Note</a></li>
			<li><a href="#tabs-3">Information</a></li>
		</ul>
		<div id="tabs-1" class="col-sm-12">
			<br>
			<table class="table table-condensed table-striped">
				<thead>
					<tr>
						<td width="5%"><strong>#</strong></td>
						<td width="80%"><strong>Milestone:</strong></td>
						<td width="20%"><strong>In List:</strong></td>
					</tr>
				</thead>
				<tbody>
					<tr id="f1">
						<td><input type="hidden" name="field_id[1]" value="">1</td>
						<td>
							<div class="input-group">
								<input autocomplete="off" class="form-control" id="field1" name="field[1]" type="text" placeholder="Description" value="">
								<span id="s1" class="input-group-btn">
									<button id="add-more" class="btn btn-default" type="button">+</button>
								</span>
							</div>
						</td>
						<td><input type="checkbox" name="show[1]" value="1" checked> Show</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="tabs-2" class="col-sm-12">
			<br>
			<div class="form-group">
				<div class="col-sm-12">
					<textarea class="form-control" id="note" name="note" rows="4" cols="30"></textarea>
				</div>
			</div>
		</div>
		<div id="tabs-3" class="col-sm-12">
			<br>
			<div class="form-group">
				<label for="status" class="col-sm-3 control-label">Status:</label>
				<div class="col-sm-9">
					<select class="form-control" id="status" name="status">
							<?php echo $fct->droplist("", parent::$param['status']); ?>
						</select>
				</div>
			</div>
			<div class="form-group">
				<label for="created_time" class="col-sm-3 control-label">Creation Date:</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="created_time" name="created_time" data-notremove="true" disabled="disabled">
				</div>
			</div>
			<div class="form-group">
				<label for="update_time" class="col-sm-3 control-label">Update Date:</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="update_time" name="update_time" data-notremove="true" disabled="disabled">
				</div>
			</div>
		</div>
	</div>
</form>
