<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="ten wide column">
	<h2 class="ui header">
		<i class="history icon"></i>
		<div class="content">
			<?php echo _("Rapports"); ?>
			<div class="sub header">
				<?php
				if (!empty($date1) || !empty($date2))
					echo _("Période du ") . $date1 . _(" au ") . $date2;
				?>
			</div>
		</div>
	</h2>
</div>

<div id="unseen" class="ui attached segment"></div>