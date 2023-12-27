<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="panel" class="ui stackable three column page grid">
	<div class="column"></div>
	<div class="seven wide column aligned ">
		<div class="ui basic segment center">
			<!-- logo -->
			<img id="logo_menu" src="Ressources/images/gillet.jpg" alt="logo" class="ui small image" style="margin:20px" />
		</div>
		<div class="ui attached message">
			<div class="header">Welcome to Administration Desk!</div>
			<p>Fill out the form below to enter</p>
		</div>
		<div id="signup">
			<form id="login" action="." method="post" class="ui form attached fluid segment">
				<input type="hidden" name="token" value="<?php echo $page_token; ?>" />
				<input type="hidden" name="action" value="login" />
				<h5>Please enter your sign in details.</h5>
				<div class="field">
					<div class="ui left icon input">
						<input type="text" id="username" name="username" placeholder="Username" autofocus />
						<i class="user icon"></i>
					</div>
				</div>
				<div class="field">
					<div class="ui left icon input">
						<input type="password" id="password" name="password" placeholder="Password" />
						<i class="lock icon"></i>
					</div>
				</div>
				<div class="inline field">
					<div class="ui checkbox">
						<input type="checkbox" id="remember" name="remember" value="1" />
						<label for="remember">Stay signed in</label>
					</div>
				</div>
				<div class="ui error message"></div>
				<div class="field">
					<div class="ui stackable two column grid">
						<div class="column">
							<input type="submit" id="submit" class="ui primary button" value="Sign in" />
						</div>
						<div class="column right aligned">
							<button type="button" id="forgot_btn" class="ui basic button">Forgot your password ?</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div id="forgot" style="display: none;">
			<form id="forgot_form" action="." method="post" class="ui form attached fluid segment">
				<input type="hidden" name="token" value="<?php echo $page_token; ?>" />
				<input type="hidden" name="action" value="forgot" />
				<h5>Forgot your password ?</h5>
				<div class="field">
					<div class="ui left icon input">
						<input type="text" id="username" name="username" placeholder="Enter your username" autofocus />
						<i class="user icon"></i>
					</div>
					<span>You will receive an email with your password.</span>
				</div>
				<div class="ui error message"></div>
				<div class="field">
					<div class="ui stackable two column grid">
						<div class="column">
							<input type="submit" id="submit_forgot" class="ui primary button" value="Submit" />
						</div>
						<div class="column right aligned">
							<button type="button" id="return_btn" class="ui basic button">Back to login page</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div id="newpassword" style="display: none;">
			<form id="newpassword_form" action="." method="post" class="ui form attached fluid segment">
				<input type="hidden" id="id" name="id" value="" />
				<input type="hidden" id="token_change" name="token_change" value="" />
				<input type="hidden" name="token" value="<?php echo $page_token; ?>" />
				<input type="hidden" name="action" value="change" />
				<h5>Select your new password and enter it below.</h5>
				<div class="field">
					<div class="ui left icon input">
						<input type="password" id="password" name="password" placeholder="New Password" autofocus />
						<i class="lock icon"></i>
					</div>
				</div>
				<div class="field">
					<div class="ui left icon input">
						<input type="password" id="password2" name="password2" placeholder="Confirm New Password" />
						<i class="lock icon"></i>
					</div>
					<span>Your new password will expire after 60 days.</span>
				</div>
				<div class="ui error message"></div>
				<div class="field">
					<input type="submit" id="submit_newpassword" class="ui primary button" value="Change Password" />
				</div>
			</form>
		</div>
		<div class="ui bottom attached message basic center">Arkium SCS (c) 2012-<?php echo date('Y'); ?></div>
	</div>
</div>
