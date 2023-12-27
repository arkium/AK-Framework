<?php

namespace Applications\Frontend\Modules\MilestonesTypes;

class MilestonesTypesManagerExtends_PDO extends \Library\Models\MilestonesTypesManager_PDO {

	public function getField(\Library\HTTPRequest $request) {
		$query = "SELECT milestone_field_id, name, show_field
		FROM ts_milestones_fields
		WHERE milestone_type_id='" . $request->postData('milestone_type_id') . "'
		ORDER BY order_field ASC";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		$i = 0;
		$content = $value = $valueShow = '';
		$max = count($output);
		foreach ($output as $row) {
			$i ++;
			$valueShow = ($row['show_field']) ? ' checked' : '';
			if ($i == $max) {
				$value = $row['name'];
				break;
			}
			$content .= '
				<tr id="f' . $i . '">
					<td>
						<input type="hidden" name="field_id[' . $i . ']" value="' . $row['milestone_field_id'] . '" />' . $i . '
					</td>
					<td>
						<div class="input-group">
							<input autocomplete="off" class="form-control" id="field' . $i . '" name="field[' . $i . ']" type="text"
								placeholder="Description" value="' . $row['name'] . '"/>
							<span id="s' . $i . '" class="input-group-btn">
								<button id="b' . $i . '" class="btn btn-danger remove-me" type="button">-</button>
							</span>
						</div>
					</td>
					<td>
						<input type="checkbox" name="show[' . $i . ']" value="1"' . $valueShow . ' /> Show
					</td>
				</tr>';
		}
		if ($i == 0)
			$i ++;
		$content .= '
			<tr id="f' . $i . '">
				<td>
					<input type="hidden" name="field_id[' . $i . ']" value="' . $row['milestone_field_id'] . '" />' . $i . '
				</td>
				<td>
					<div class="input-group">
						<input autocomplete="off" class="form-control" id="field' . $i . '" name="field[' . $i . ']" type="text"
						placeholder="Description" value="' . $value . '" />
						<span id="s' . $i . '" class="input-group-btn">
							<button id="add-more" class="btn btn-default" type="button">+</button>
						</span>
					</div>
				</td>
				<td>
					<input type="checkbox" name="show[' . $i . ']" value="1"' . $valueShow . ' /> Show
				</td>
			</tr>
			<input type="hidden" id="count" name="count" value="' . $i . '" />';
		return $content;
	}

	public function addFields($request, $milestone_type_id) {
		$milestone_field_id = $request->postData('field');
		$show = $request->postData('show');
		if (!empty($milestone_field_id) && is_array($milestone_field_id)) {
			reset($milestone_field_id);
			$i = 0;
			foreach ($milestone_field_id as $key => $value) {
				if (empty($value))
					continue;
				$i ++;
				$showValue = (empty($show[$key])) ? '0' : $show[$key];
				$query = "INSERT INTO ts_milestones_fields SET
				milestone_type_id='$milestone_type_id',
				order_field='$i',
				name='$value',
				show_field='$showValue',
				created_time=NOW()";
				$output = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				if ($output !== true)
					break;
			}
			return $output;
		}
	}

	public function editFields($request) {
		$milestone_field_id = $request->postData('field');
		$milestone_type_id = $request->postData('milestone_type_id');
		$show = $request->postData('show');
		$field_id = $request->postData('field_id');
		
		// On récupère les input initiaux
		$query = "SELECT milestone_field_id FROM ts_milestones_fields WHERE milestone_type_id='$milestone_type_id'";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll();
		foreach ($output as $row) {
			$arrayOld[] = $row['milestone_field_id'];
		}
		
		if (!empty($milestone_field_id) && is_array($milestone_field_id)) {
			reset($milestone_field_id);
			$i = 0;
			$arrayNew = array();
			foreach ($milestone_field_id as $key => $value) {
				if (empty($value)) {
					// si input vide, on le supprime de la DB
					$query = "DELETE FROM ts_milestones_fields WHERE milestone_field_id='" . $field_id[$key] . "'";
				} else {
					$i ++;
					$showValue = (empty($show[$key])) ? '0' : $show[$key];
					if (empty($field_id[$key])) {
						// si nouveau input, on l'ajoute à la DB
						$query = "INSERT INTO ts_milestones_fields SET
						milestone_type_id='$milestone_type_id',
						order_field='$i',
						name='$value',
						show_field='$showValue',
						created_time=NOW()";
					} else {
						// sinon on met à jour l'input
						$query = "UPDATE ts_milestones_fields SET
						milestone_type_id='$milestone_type_id',
						order_field='$i',
						name='$value',
						show_field='$showValue'
						WHERE milestone_field_id='" . $field_id[$key] . "'";
						$arrayNew[] = $field_id[$key];
					}
				}
				$output = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				if ($output !== true)
					break;
			}
			$result = array_diff($arrayOld, $arrayNew);
			foreach ($result as $value) {
				// on supprime tous les anciens input qui n'ont pas été mis à jour
				$query = "DELETE FROM ts_milestones_fields WHERE milestone_field_id='" . $value . "'";
				$output = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				if ($output !== true)
					break;
					// on supprime toutes les données des anciens input qui n'ont pas été mis à jour
				$query = "DELETE FROM ts_milestones WHERE milestone_field_id='" . $value . "'";
				$output = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
				if ($output !== true)
					break;
			}
			return $output;
		}
	}
}