<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="ten wide column">
	<h2 class="ui header">
		<i class="folder icon"></i>
		<div class="content">
			<?php echo _("Assigner les véhicules"); ?>
			<div class="sub header">
				<?php echo _("Gestion de vos équipes par véhicule"); ?>
			</div>
		</div>
	</h2>
</div>

<div id="unseen" class="ui attached segment">
	<table class="ui celled padded table" id="exemple">
		<thead>
			<tr>
				<th><?php echo _("Véhicule"); ?></th>
                <?php if (is_array(parent::$param['data_user_code'])) {
						  reset(parent::$param['data_user_code']);
						  foreach (parent::$param['data_user_code'] as $key => $val) {
							  $pop = 'class="pop" data-content="' . $val . ' = ' . parent::$param['data_user_name'][$key] . '"';
							  print "<th><span $pop>$val</span></th>\n";
						  }
					  } ?>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<?php if ($user->permissions['view']) : ?>
<div id="actionBtn" class="ui teal buttons">
	<div id="openView" class="ui button">View</div>
</div>
<?php endif; ?>
