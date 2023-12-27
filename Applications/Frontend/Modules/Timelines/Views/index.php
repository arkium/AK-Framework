<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">

	<div id="title_table" class="col-sm-8 col-xs-10">
		<h1>Timelines</h1>
	</div>
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<?php echo parent::$param['data_task']; ?>
			</div>			<div class="col-sm-6">
				<?php echo parent::$param['data_timetotal']; ?>
			</div>
		</div>
	</div>
	<ul class="timeline">
		<?php
		$old_date = null;
		$old_month = null;
		$old_year = null;
		$position = '';
		$toggle = true;
		$result = parent::$param['data_timeline'];
		while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
			$datetime = new DateTime($row['date']);
			$date = date_format($datetime, 'M d Y');
			$daymonth = date_format($datetime, 'd');
			$month = date_format($datetime, 'F');
			$year = date_format($datetime, 'Y');
			if ($old_date != $row['date']) {
				$toggle = ($toggle == true) ? false : true;
				$position = ($toggle) ? ' class="timeline-inverted"' : '';
				$same = false;
			} else {
				$same = true;
			}
			$balloon = (!$same) ? "<div class=\"timeline-badge day\"><span>$daymonth</span></div>" : '';
			$body = ($row['comment'] != '') ? "<div class=\"timeline-body\"><p>{$row['comment']}</p></div>" : '';
			if ($old_year != $year) {
				echo <<<EOT
	<li>
		<div class="timeline-badge year primary">
			<span>$year</span>
		</div>
	</li>
EOT;
			}
			if ($old_month != $month) {
				echo <<<EOT
	<li>
		<div class="timeline-badge month info">
			<span>$month</span>
		</div>
	</li>
EOT;
			}
			echo <<<EOT
	<li$position>
		$balloon
		<div class="timeline-panel">
			<div class="timeline-heading">
				<h4 class="timeline-title">{$row['name']}</h4>
				<p class="text-muted">$date <i class="glyphicon glyphicon-time"></i> {$row['duration']}</p>
			</div>
			$body
		</div>
	</li>
EOT;
			$old_date = $row['date'];
			$old_month = $month;
			$old_year = $year;
		}
		?>
	</ul>
</div>
