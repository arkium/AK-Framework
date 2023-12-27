<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

class Datatables extends ApplicationComponent {

	private $iWhere;

	public $ini, $output;

	public function __construct(\Library\HTTPRequest $request, \Library\Datatable $ini) {
		parent::__construct(__CLASS__);
		$this->ini = $ini;

		if (isset($_GET['sSearch']) && $_GET['sSearch'] != '') {
			switch ($_GET['sSearch']) {
				default :
					$_GET['sSearch'] = '%' . $_GET['sSearch'] . '%';
			}
		}
	}

	/**
	 * Paging
	 * @return string
	 */
	private function fLimit() {
		$sLimit = "";
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
		}
		return $sLimit;
	}

	/**
	 * Ordering
	 * @return mixed
	 */
	private function fOrder() {
		$sOrder = "";
		if (isset($_GET['iSortCol_0'])) {
			$sOrder = "ORDER BY  ";
			for($i = 0; $i < intval($_GET['iSortingCols']); $i ++) {
				if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
					$sOrder .= "`" . $this->ini->aColumnsDisplay[intval($_GET['iSortCol_' . $i])] . "` " . $_GET['sSortDir_' . $i] . ", ";
				}
			}
			$sOrder = substr_replace($sOrder, "", -2);
			if ($sOrder == "ORDER BY") {
				$sOrder = "";
			}
		}
		return $sOrder;
	}

	/**
	 * Filtering
	 * @return string
	 */
	private function fWhere() {
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
			$this->ini->sWhere .= ($this->ini->sWhere == "") ? "WHERE (" : " AND (";
			for($i = 0; $i < count($this->ini->aColumnsDisplay); $i ++) {
				$this->ini->sWhere .= "`" . $this->ini->aColumnsDisplay[$i] . "` LIKE '" . $_GET['sSearch'] . "' OR ";
			}
			$this->ini->sWhere = substr_replace($this->ini->sWhere, "", -3);
			$this->ini->sWhere .= ')';
		}

		// Individual column filtering
		for($i = 0; $i < count($this->ini->aColumnsDisplay); $i ++) {
			if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
				if ($this->ini->sWhere == "") {
					$this->ini->sWhere = "WHERE ";
				} else {
					$this->ini->sWhere .= " AND ";
				}
				$this->ini->sWhere .= "`" . $this->ini->aColumnsDisplay[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
			}
		}

		$this->iWhere = empty($this->ini->sWhere) ? "" : $this->ini->sWhere;
		return $this->iWhere;
	}

	public function run() {
		// Get data to display
		if (empty($this->ini->sQuery)) {
			$this->ini->sQuery = "SELECT SQL_CALC_FOUND_ROWS `" . str_replace(" , ", " ", implode("`, `", $this->ini->aColumnsDisplay)) . "` FROM " . $this->ini->sTable;
		}
		$this->ini->sQuery .= " " . $this->fWhere() . " " . $this->fOrder() . " " . $this->fLimit();
		// Variable Output à renvoyer
		$this->output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => null,
				"iTotalDisplayRecords" => null,
				"aaData" => array()
		);
		try {
			$a = 0;
			$result = parent::$dao->query($this->ini->sQuery);
			while ($aRow = $result->fetch()) {
				if ($aRow[$this->ini->sIndexColumn] == '0') {
				//if (array_key_exists($this->ini->sIndexColumn, $aRow)) {
					// N'affiche pas la ligne 0
					$a = -1;
					continue;
				}
				$row = array();
				$column = $this->ini->aColumnsDisplay;
				for($i = 0; $i < count($column); $i ++) {
					$key = $column[$i];
					if (array_key_exists($key, $this->ini->sDisplay)) {
						$row[$i] = $this->ini->sDisplay[$key]($aRow, $key, parent::$param);
					} else {
						$row[$i] = $aRow[$key];
					}
				}
				$row['DT_RowId'] = $aRow[$this->ini->sIndexColumn];
				$this->output['aaData'][] = $row;
			}

			// Data set length after filtering
			$Query = "SELECT FOUND_ROWS()";
			$result = parent::$dao->query($Query)->fetch(\PDO::FETCH_NUM);
			$this->output['iTotalDisplayRecords'] = $result[0] + $a;
			$this->output['sql'] = $this->ini->sQuery;

			// Total data set length
			$Query = "SELECT COUNT(`" . $this->ini->sIndexColumn . "`) FROM " . $this->ini->sTable . " " . $this->iWhere;
			$result = parent::$dao->query($Query)->fetch(\PDO::FETCH_NUM);
			$this->output['iTotalRecords'] = $result[0] + $a;
		} catch (\PDOException $e) {
			$this->output['sql'] = $this->ini->sQuery;
			$this->output['where'] = $this->ini->sWhere;
			$this->output['reponse'] = "The database is not able to be read!<br/>Code Error: " . $e->getCode() . "<br/>Syntax Error: " . $e->getMessage();
			$this->output['type'] = 'error';
		}
		return $this->output;
	}

}