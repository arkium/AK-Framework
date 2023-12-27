<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="container">
	<form id="frmIssue" id="frmAddIssue" action="issues" method="post">
		<input type="hidden" id="token" name="token" value="" />
		<input type="hidden" id="op" name="op" value="" />
		<input type="hidden" id="status" name="status" value="1" />
		<input type="hidden" id="issue_id" name="issue_id" value="" />
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
	<form id="upload" class="dropzone"></form>
</div>