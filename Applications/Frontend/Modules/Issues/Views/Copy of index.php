<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="col-sm-8">
	<h4>Issues Log</h4>
</div>
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
<div id="addIssue" class="col-sm-4">
	<button id="btnAddIssue" class="btn btn-primary">
		<span class="glyphicon glyphicon-plus"></span> New Issue
	</button>
</div>
<div id="panelAddIssue" class="row">
	<div class="col-sm-12">
		<br>
		<div class="panel panel-primary">
			<div class="panel-heading">
				Create New Issue
				<button type="button" id="btnClosefrmAddIssue" class="pull-right close">&times;</button>
			</div>
			<div class="panel-body">
				<form id="frmAddIssue" action="issues" method="post">
					<input type="hidden" name="token" value="" />
					<input type="hidden" id="op" name="op" value="add_issue" />
					<input type="hidden" id="status" name="status" value="1" />
					<input type="hidden" id="user_id" name="user_id" value="<?php echo parent::$param['user_id']; ?>" />
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group">
								<label for="title" class="sr-only">Summary</label>
								<input id="title" class="form-control" type="text" name="title" placeholder="Enter one-line summary" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="title" class="sr-only">Type:</label> <select class="form-control" name="type_id">
									<option value="">None</option>
									<option value="Defect">Defect</option>
									<option value="Enhancement">Enhancement</option>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="description" class="sr-only">Description</label>
						<textarea id="description" class="form-control" name="description" rows="4" placeholder="Description"></textarea>
					</div>
				</form>
				<form id="upload" action="issues/file" method="post" class="dropzone" enctype="multipart/form-data">
					<input type="hidden" name="token" value="" />
				</form>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-sm-6">
						<span class="glyphicon glyphicon-user"></span> <?php echo parent::$param['data_user_id'][parent::$param['user_id']]; ?>
					</div>
					<div class="col-sm-6 text-right">
						<button id="btn_cancel" class="btn btn-default btn-sm">Reset</button>
						<button id="btn_submit" class="btn btn-primary btn-sm">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
