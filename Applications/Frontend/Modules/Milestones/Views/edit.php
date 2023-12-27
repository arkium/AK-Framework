<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="title_table" class="col-sm-8">
	<h4>View Milestones</h4>
</div>
<div id="div_filter" class="col-sm-6 form-horizontal">
	<div class="form-group">
		<label for="milestone_type_id" class="control-label col-sm-3 ">Milestone Lists:</label>
		<div class="col-sm-9">
			<select class="form-control" id="milestone_type_id" name="milestone_type_id">
				<?php echo $fct->droplist(parent::$param['milestone_type_id'], parent::$param['milestone_type_list']); ?>
			</select>
		</div>
	</div>
</div>
<div class="row" id="unseen">
	<div class="table-responsive col-sm-12">
		<table class="table table-hover table-condensed table-striped table-header-rotated" id="exemple">
			<thead>
				<tr>
					<th>Code</th>
                    <?php
$data_colonnes = parent::$param['data_colonnes']['name'];
if (is_array($data_colonnes)) {
	reset($data_colonnes);
	foreach ($data_colonnes as $key => $val) {
		print "<th class=\"rotate-45\"><div><span>$val</span></div></th>\n";
	}
}
                    ?>
				</tr>
			</thead>
			<tbody>
                <?php
$nbre_ligne = 0;
$result = parent::$param['data_lignes'];
while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
	$nbre_ligne ++;
	print "<tr id=\"{$row['task_id']}\">\n";
	$pop = null;
	// $pop = 'data-container="body" data-toggle="popoverDetails" data-placement="right" data-content="Client : '.$organisation . "<br />Description : " . $name .'" data-original-title=""';
	print "<td class=\"row-header\" data-id=\"{$row['task_id']}\" data-dashboard=\"myproject\"><span class=\"pull-left\" $pop>{$row['code']}</span></td>\n";
	if (is_array($data_colonnes)) {
		reset($data_colonnes);
		$data = parent::$param['data_data'];
		foreach ($data_colonnes as $key => $val) {
			if (array_key_exists($key, $data['date'][$row['task_id']])) {
				$date = $data['date'][$row['task_id']][$key];
				$milestone_id = $data['milestone_id'][$row['task_id']][$key];
				if (!empty($data['date'][$row['task_id']][$key])) {
					print "<td>\n";
					print "<input data-date-format=\"YYYY-MM-DD\" data-milestone_id=\"$milestone_id\" size=\"8\" value=\"$date\" maxlength=\"10\" />";
					print "</td>\n";
				} else {
					print "<td>\n";
					print "<input data-date-format=\"YYYY-MM-DD\" data-milestone_id=\"$milestone_id\" size=\"8\" value=\"\" maxlength=\"10\" />";
					print "</td>\n";
				}
			} else {
				print "<td>\n";
				print "<input size=\"8\" value=\"\" disabled=\"disabled\"/>";
				print "</td>\n";
			}
		}
	}
	print "</tr>\n";
}
                ?>
			</tbody>
		</table>
	</div>
</div>