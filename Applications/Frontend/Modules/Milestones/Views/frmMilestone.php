<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<form id="frmMilestone" class="form-horizontal" action="milestones" method="post">
	<input type="hidden" id="token" name="token" value="">
	<input type="hidden" id="op" name="op" value="">
	<input type="hidden" id="task_id" name="task_id" value="">
	<div class="well well-sm">
		<strong>Code :</strong> <span id="code"></span> <small class="pull-right"><strong>Closing Date :</strong> <span id="closing"></span></small>
	</div>
	<div id="lst_milestones">
		<table class="table table-condensed table-striped">
			<thead>
				<tr>
					<td width="60%"><strong>Milestone:</strong></td>
					<td width="40%"><strong>Date:</strong></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</form>
