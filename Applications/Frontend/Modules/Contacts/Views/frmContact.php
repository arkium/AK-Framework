<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="uk-modal-dialog">
	<button type="button" class="uk-modal-close uk-close"></button>
	<h3 id="title-frmClient"></h3>
	<form id="frmContact" class="uk-form uk-form-horizontal" action="contacts" method="post">
		<input type="hidden" id="token" name="token" value="">
		<input type="hidden" id="op" name="op" value="">
		<input type="hidden" id="contact_id" name="contact_id" value="">
		<div class="uk-form-row">
			<label for="entity_id" class="uk-form-label">Company:</label>
			<div class="uk-form-controls">
				<select id="entity_id" name="entity_id" class="uk-form-width-large">
					<?php echo $fct->droplist("", parent::$param['entity_id']); ?>
				</select>
			</div>
		</div>
		<div class="uk-form-row">
			<label for="contact_type_id" class="uk-form-label">Type contact:</label>
			<div class="uk-form-controls">
				<select id="contact_type_id" name="contact_type_id" class="uk-form-width-large">
					<?php echo $fct->droplist("", parent::$param['contact_type_id']); ?>
				</select>
			</div>
		</div>
		<br>
		<ul class="uk-tab" data-uk-tab="{connect:'#tab-content'}">
			<li class="uk-active"><a href="#">Contact</a></li>
			<li><a href="#">Note</a></li>
			<li><a href="#">Information</a></li>
		</ul>
		<ul id="tab-content" class="uk-switcher uk-margin">
			<li class="uk-active">
				<div class="uk-form-row">
					<label for="first_name" class="uk-form-label">First Name:</label>
					<div class="uk-form-controls">
						<input type="text" id="first_name" name="first_name" class="uk-form-width-large">
					</div>
				</div>
				<div class="uk-form-row">
					<label for="last_name" class="uk-form-label">Last Name:</label>
					<div class="uk-form-controls">
						<input type="text" id="last_name" name="last_name" class="uk-form-width-large">
					</div>
				</div>
				<div class="uk-form-row">
					<label for="email" class="uk-form-label">Email:</label>
					<div class="uk-form-controls">
						<input type="text" id="email" name="email" class="uk-form-width-large">
					</div>
				</div>
				<div class="uk-form-row">
					<label for="phone" class="uk-form-label">Phone Number:</label>
					<div class="uk-form-controls">
						<input type="text" id="phone" name="phone" class="uk-form-width-large">
					</div>
				</div>
				<div class="uk-form-row">
					<label for="fax" class="uk-form-label">Fax Number:</label>
					<div class="uk-form-controls">
						<input type="text" id="fax" name="fax" class="uk-form-width-large">
					</div>
				</div>
				<div class="uk-form-row">
					<label for="mobile" class="uk-form-label">Mobile Number:</label>
					<div class="uk-form-controls">
						<input type="text" id="mobile" name="mobile" class="uk-form-width-large">
					</div>
				</div>
			</li>
			<li>
				<div class="uk-form-row">
					<textarea id="note" name="note"></textarea>
				</div>
			</li>
			<li>
				<div class="uk-form-row">
					<label for="status" class="uk-form-label">Status:</label>
					<div class="uk-form-controls">
						<select id="status" name="status" class="uk-form-width-large">
							<?php echo $fct->droplist("1", parent::$param['status']); ?>
						</select>
					</div>
				</div>
				<div class="uk-form-row">
					<label for="created_time" class="uk-form-label">Creation Date:</label>
					<div class="uk-form-controls">
						<input type="text" id="created_time" name="created_time" class="uk-form-width-large" data-notremove="true" disabled="disabled">
					</div>
				</div>
				<div class="uk-form-row">
					<label for="update_time" class="uk-form-label">Update Date:</label>
					<div class="uk-form-controls">
						<input type="text" id="update_time" name="update_time" class="uk-form-width-large" data-notremove="true" disabled="disabled">
					</div>
				</div>
			</li>
		</ul>
	</form>
</div>