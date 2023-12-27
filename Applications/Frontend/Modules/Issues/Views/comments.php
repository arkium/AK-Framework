<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<?php $status = (parent::$param['issue'][5] == '1')? 'success' : 'warning'; ?>
<h3>
	<span id="status" class="label label-<?php echo $status; ?>">Issue <?php echo parent::$param['issue'][0]; ?></span>
	<div class="pull-right">
		<button id="btnAddComment" class="btn btn-primary btn-sm">
			<span class="glyphicon glyphicon-plus"></span> New Comment
		</button>
		<button id="modify" class="btn btn-default btn-sm">
			<span class="glyphicon glyphicon-cog"></span>
		</button>
		<button id="returnoverview" class="btn btn-default btn-sm">Back to list</button>
	</div>
</h3>
<div class="row">
	<div class="panel panel-default">
		<div class="panel-heading">
			<strong><?php echo parent::$param['issue'][2]; ?></strong> <span class="pull-right"><span class="glyphicon glyphicon-user"></span> <?php echo parent::$param['data_user_id'][parent::$param['issue'][1]]; ?> <span
				class="glyphicon glyphicon-time"></span> <?php echo parent::$param['issue'][7]; ?></span>
		</div>
		<div class="panel-body">
			<p>
				<span id="type" class="label label-info" data-type="<?php echo parent::$param['issue'][4]; ?>"><?php echo parent::$param['issue'][4]; ?></span>
				<?php echo parent::$param['issue'][3]; ?>
			</p>
			<div class="list-group">
			<?php
			foreach (parent::$param['data_file_id']['filename'] as $key => $value) {
				echo "<a href=\"uploads?file=$value\" class=\"list-group-item\" type=\"button\" class=\"btn btn-default btn-sm\"><span class=\"glyphicon glyphicon-paperclip\"></span> $value <span class=\"pull-right\">" . parent::$param['data_file_id']['filesize'][$key] . "</span></a>";
			}
			?>
		</div>
		</div>
		<div id="modifyPanel" class="panel-footer">
			<?php $radio1 = (parent::$param['issue'][5] == '1')? 'checked' : ''; ?>
			<?php $radio2 = ($radio1 == '')? 'checked' : ''; ?>
			<div class="row">
				<div id="radio" class="col-sm-4">
					<label for="open"> <input type="radio" name="status" id="open" value="1" <?php echo $radio1; ?>>Open
					</label> <label for="close"> <input type="radio" name="status" id="close" value="0" <?php echo $radio2; ?>>Close
					</label>
				</div>
				<div class="col-sm-4">
					<label for="title" class="sr-only">Type:</label> <select id="type_id" class="form-control" name="type_id">
						<option value="">None</option>
						<option value="Defect">Defect</option>
						<option value="Enhancement">Enhancement</option>
					</select>
				</div>
				<div class="col-sm-4">
					<button id="delete_issue" class="pull-right btn btn-danger">Delete Issue</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="frmAddComment" class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">
			Leave a comment
			<button type="button" id="btnClosefrmAddComment" class="pull-right close">&times;</button>
		</div>
		<div class="panel-body">
			<form id="frmaddcomment" action="issues" method="post">
				<input type="hidden" name="token" value="" />
				<input type="hidden" id="op" name="op" value="add_comment" />
				<input type="hidden" id="user_id" name="user_id" value="<?php echo parent::$param['user_id']; ?>" />
				<input type="hidden" id="issue_id" name="issue_id" value="<?php echo parent::$param['issue'][0]; ?>" />
				<label for="comment" class="sr-only">Comment</label>
				<textarea id="comment" class="form-control" name="comment" rows="4" placeholder="Comment"></textarea>
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
<div class="row">
	<div class="panel panel-default">
		<div id="count" class="panel-heading"></div>
		<div class="panel-body">
			<ul id="chat" class="chat"></ul>
		</div>
	</div>
</div>
<script id="commentTmpl" type="text/x-jsrender">
	<li>
		<small class="text-muted"><span class="glyphicon glyphicon-time"></span> {{:created_time}} <span class="glyphicon glyphicon-user"></span> {{:username}} say : </small>
		 <button class="btn btn-danger btn-xs remove" data-delete="true" data-id="{{:comment_id}}">Delete</button>
		<p>{{:comment}}</p>
	</li>
</script>
