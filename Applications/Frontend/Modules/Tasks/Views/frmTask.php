<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
	<form id="frmTask" class="ui form" action="tasks" method="post">
		<h4 id="title-frmTask" class="ui dividing header"><?php echo("Véhicule:"); ?></h4>
		<input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>">
		<input type="hidden" id="op" name="op" value="<?php echo $op; ?>">
		<input type="hidden" id="task_id" name="task_id" value="<?php echo $task_id; ?>">
		<div class="two fields">
			<div class="field">
				<label for="code"><?php echo("Code du véhicule:"); ?></label>
				<div class="ui massive icon input">
					<input type="text" id="code" name="code">
				</div>
			</div>
				<div class="field">
					<label for="customer_id"><?php echo("Code du client:"); ?></label>
					<select id="customer_id" name="customer_id">
						<option value="">Select ...</option><?php echo $fct->droplist("", parent::$param['data_customer_id']); ?>
					</select>
				</div>
		</div>
		<div class="two fields">
			<div class="field">
				<label for="name"><?php echo("Nom du véhicule:"); ?></label>
				<textarea id="name" name="name" rows="2" cols="45"></textarea>
			</div>
			<div class="field">
				<label for="task_type_id"><?php echo("Type d'activité:"); ?></label>
				<select id="task_type_id" name="task_type_id">
					<option value="">Select ...</option><?php echo $fct->droplist("", parent::$param['data_task_type_id']); ?>
				</select>
			</div>
		</div>
		<div class="field">
			<label for="project_proposal"></label>
			<select id="project_proposal" name="project_proposal" <?php echo (parent::$user->permissions['approval']) ? '' : 'data-notremove="true" disabled="disabled"' ;?> >
				<?php echo $fct->droplist("", parent::$param['ProjectProposal']); ?>
			</select>
		</div>
		<div class="field">
			<div id="tab-frmTask">
				<div class="ui secondary pointing stackable menu">
					<a class="active item" data-tab="a">Details</a>
					<a class="item" data-tab="b">Periods</a>
					<a class="item" data-tab="c">Assignment</a>
					<a class="item" data-tab="d"><?php echo _("Travaux à faire"); ?></a>
					<a class="item" data-tab="e">Information</a>
				</div>

				<div class="ui active tab" data-tab="a">
					<div class="two fields">
						<div class="field">
							<label for="invoicing_entity_id">Office code:</label>
							<select id="invoicing_entity_id" name="invoicing_entity_id">
								<option value="">Select ...</option>
								<?php echo $fct->droplist("", parent::$param['data_invoicing_entity_id']); ?>
							</select>
						</div>
						<div class="field">
							<label for="closing_date">Closing date:</label>
							<div class="ui left icon input">
								<i class="calendar icon"></i>
								<input type="date" id="closing_date" name="closing_date" placeholder="YYYY/MM/DD">
							</div>
						</div>
					</div>
					<div class="two fields">
						<div class="field">
							<label for="intermediate_id">Direct/Indirect:</label>
                            <select id="intermediate_id" name="intermediate_id">
                                <option value="">Select ...</option>
                                <?php echo $fct->droplist("", parent::$param['data_intermediate_id']); ?>
                            </select>
						</div>
						<div class="field">
							<label for="num_proj">Number project:</label>
							<input type="text" id="num_proj" name="num_proj">
						</div>
					</div>
				</div>

				<div class="ui tab" data-tab="b">
					<div class="two fields">
						<div class="field">
							<label for="start_date">Start Date:</label>
							<div class="ui left icon input">
								<i class="calendar icon"></i>
								<input type="date" id="start_date" name="start_date" placeholder="YYYY/MM/DD">
							</div>
						</div>
						<div class="field">
							<label for="end_date">End Date:</label>
							<div class="ui left icon input">
								<i class="calendar icon"></i>
								<input type="date" id="end_date" name="end_date" placeholder="YYYY/MM/DD">
							</div>
						</div>
					</div>
				</div>

				<div class="ui tab" data-tab="c">
					<div class="field">
						<label for="staff">Staff:</label>
						<select id="staff" name="staff[]" multiple="multiple" size="10">
							<?php echo $fct->droplist("", parent::$param['data_user_id']); ?>
						</select>
					</div>
				</div>

				<div class="ui tab" data-tab="d">
					<div class="field">
						<textarea id="note" name="note"></textarea>
					</div>
				</div>

				<div class="ui tab" data-tab="e">
					<div class="three fields">
						<div class="field">
							<label for="status">Status:</label>
							<select id="status" name="status">
								<?php echo $fct->droplist("1", parent::$param['status']); ?>
							</select>
						</div>
						<div class="field">
							<label for="created_time">Creation Date:</label>
							<input type="text" id="created_time" name="created_time" data-notremove="true" disabled="disabled">
						</div>
						<div class="field">
							<label for="update_time">Update Date:</label>
							<input type="text" id="update_time" name="update_time" data-notremove="true" disabled="disabled">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="ui error message"></div>
		<button id="btnSave" class="ui primary button" type="button">Save</button>
		<button id="btnCancel" class="ui button" type="button" data-return="<?php echo $return; ?>" ovisible="true">Cancel</button>
	</form>
</div>