<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

/**
 * Generateur de rapport
 */
class ReportGenerator {

	public $mysql_resource;

	public $group_active = false;

	public $group_fields = array();

	public $group_sum_field = "";

	private $fields = array();

	private $field_count;

	private $LastGroup = 0;

	private $LastGroupName = "";

	private $start_group = false;

	private $group_sum = 0;

	private $grandtotal = 0;

	private $fct;

	public function __construct() {
		$this->fct = new \Library\Functions();
	}

	/**
	 * Chargement des données dans le tableau $fields
	 */
	private function LoadData() {
		$this->field_count = $this->mysql_resource->columnCount();
		$i = 0;
		while ($i < $this->field_count) {
			$meta = $this->mysql_resource->getColumnMeta($i);
			$this->fields[$i] = $meta['name']; // Récupére le nom de la colonne
			$this->fields[$i][0] = strtoupper($this->fields[$i][0]);
			$i ++;
		}
	}

	/**
	 * Affichage des groupes
	 * @param mixed $Group
	 * @return string
	 */
	private function GroupDisplay($Group = "") {
		$content = "";
		if ($this->group_active && $this->start_group && $Group != $this->LastGroupName) {
			$col = $this->field_count - 1;
			$content .= "<tr class=\"parent\" data-parent=\"" . $this->LastGroup . "\">\n";
			for($i = 1; $i < $this->field_count; $i ++) {
				if (in_array($this->fields[$i], $this->group_fields)) {
					$content .= "<td class=\"group\">" . $this->LastGroupName . "</td>\n";
					//$content .= "<td class=\"group\"><div class=\"ui horizontal label\">" . $this->LastGroup . "</div></td>\n";
					// } elseif ($this->fields[$i] == $this->group_sum_field) {
					// $content .= "<td class=\"group\">$this->group_sum</td>\n";
				} elseif ($this->fields[$i] == 'Time') {
					$content .= "<td class=\"group_sum\">" . $this->fct->c100eHrs_to_HrsMin($this->group_sum) . "</td>\n";
				} elseif ($this->fields[$i] == 'Mandays') {
					$content .= "<td class=\"group_sum\">" . $this->fct->c100eHrs_to_days($this->group_sum) . "</td>\n";
				} else {
					$content .= "<td class=\"group\"></td>\n";
				}
			}
			$content .= "</tr>\n";

			$this->start_group = false;
			$this->grandtotal += $this->group_sum;
			$this->group_sum = 0;
		}
		if ($this->group_active && $Group != $this->LastGroupName && $Group != "") {
			$this->LastGroup = $this->LastGroup + 1 ;
			$this->LastGroupName = $Group;
			$this->start_group = true;
		}
		return $content;
	}

	/**
	 * Affichage du grand total
	 * @return string
	 */
	private function GrandTotalDisplay() {
		$content = "";
		if ($this->group_active){
			$col = $this->field_count - 1;
			$content .= "<tfoot>\n<tr >\n";
			for($i = 1; $i < $this->field_count; $i ++) {
				if (in_array($this->fields[$i], $this->group_fields)) {
					$content .= "<td class=\"group\">TOTAL :</td>\n";
				} elseif ($this->fields[$i] == 'Time') {
					$content .= "<td class=\"group_sum\">" . $this->fct->c100eHrs_to_HrsMin($this->grandtotal) . "</td>\n";
				} elseif ($this->fields[$i] == 'Mandays') {
					$content .= "<td class=\"group_sum\">" . $this->fct->c100eHrs_to_days($this->grandtotal) . "</td>\n";
				} else {
					$content .= "<td class=\"group\"></td>\n";
				}
			}
			$content .= "</tr>\n</tfoot>\n";
		}
		return $content;
	}

	/**
	 * Affichage du tableau HTML
	 */
	public function generateReport() {
		if (get_class($this->mysql_resource) != 'PDOStatement')
			die('The result set returned by the query is not an object PDOStatement parent class PDO.');

		$this->LoadData();

		// En-tête
		//$output = "<div id=\"unseen\" class=\"ui attached segment\">\n";

		$output = "<table border=\"1\" cellpadding=\"4\" cellspacing=\"0\" >\n" . "<thead>\n" . "<tr>\n";
		for($i = 1; $i < $this->field_count; $i ++) {
			// Now Draw Headers
			$output .= "<th class=\"" . $this->fields[$i] . "\">" . $this->fields[$i] . "</th>\n";
		}
		$output .= "</tr>\n" . "</thead>\n" . "<tbody>\n";

		// Données
		while ($row = $this->mysql_resource->fetch()) {
			$output .= $this->GroupDisplay($row[1]);
			$id = ($this->fields[0] == 'Id') ? " id=\"$row[0]\"" : '';
			$group = ($this->group_active) ? " class=\"child-" . $this->LastGroup . "\"" : '';
			$output .=  "<tr$id$group>\n";
			for($i = 1; $i < $this->field_count; $i ++) {
				if ($this->fields[$i] == 'Time') {
					$output .=  "<td class=\"Sum\">" . $this->fct->c100eHrs_to_HrsMin($row[$i]) . "</td>\n";
				} else {
					$output .=  "<td class=\"" . $this->fields[$i] . "\">" . $row[$i] . "</td>\n";
				}
				if ($this->fields[$i] == $this->group_sum_field) {
					$this->group_sum = $this->group_sum + $row[$i];
				}
		}
			$output .=  "</tr>\n";
		}
		$output .=  $this->GroupDisplay();
		$output .=  "</tbody>\n";
		$output .=  $this->GrandTotalDisplay();
		$output .=  "</table>\n";
		//$output .= "\t</div>\n";
		return $output;
	}
}