<?php

namespace Cli;
use \tecnickcom\tcpdf;

class cli_pointageAtelier extends \Cli\Cli {

	private $query, $text;

	public function __construct() {
		parent::__construct();
	}

	function getOptions():bool {
		//if ($this->cmd != 'pointageatelier')
		//	return false;

		switch ($this->arg[2]) {
			case "help":
				$this->getHelp();
				break;
			case "version":
				$this->getVersion();
				break;
		}

//		if (count($this->arg) < 2) {
//			$this->getHelp();
//		}

		return true;
	}

	function execute() {
		echo "\nReceived parameters before creating the module:\n";
//		echo "\tclassName : " . $this->className . "\n";
//		echo "\ttableName : " . $this->tableName . "\n";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();

		$this->query = "
		SELECT
			ANY_VALUE(t.time_id) AS time_id,
			ANY_VALUE(t.task_id) AS task_id,
			CONCAT(ANY_VALUE(u.code), ' - ', ANY_VALUE(u.first_name)) AS user,
			ANY_VALUE(p.code) AS vehicule,
			ANY_VALUE(t.date) AS 'date pointage',
			ANY_VALUE(h.start) AS 'début pointage',
			ANY_VALUE(h.end) AS 'fin pointage',
			TIMEDIFF(ANY_VALUE(h.end), ANY_VALUE(h.start)) AS 'durée pointage',
			ANY_VALUE(d.total) AS 'total du jour',
			ANY_VALUE(t.comment) AS 'comment'
		FROM
			ts_tasks AS p,
			ts_users AS u,
			(SELECT date, user_id, SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) AS total FROM ts_timesheets GROUP BY date, user_id ) AS d,
			ts_timesheets AS t
			LEFT JOIN ts_timesheets_hours as h
				ON t.time_id = h.time_id
		WHERE
			t.task_id = p.task_id
			AND t.user_id = u.user_id
			AND (t.date = d.date
			AND t.user_id = d.user_id)
			$date_filter
		ORDER BY t.date DESC, u.code ASC, h.start ASC;";

		$tableau = new \Library\ReportGenerator();
		$tableau->mysql_resource = parent::$dao->query($this->query);
		echo $tableau->generateReport();
		//$this->createPDF();
		echo "\nProcess is complete\n";
	}

	function getHelp() {
		echo <<<EOT

DESCRIPTION
  Cette commande permet la création d'un rapport pointage atelier
  pour l'envoyer ensuite par email à son destinataire.

  Il est recommandé d'exécuter cette commande dans le répertoire "Cli".

USAGE
  php arkium.php pointageatelier

PARAMETERS
  Aucun paramètre à utiliser
EOT;
		exit();
	}

	function getversion() {
		echo <<<EOT

AK-Cli v1.0 (based on AK Framework)
Please type 'help' for help. Type 'exit' to quit.
EOT;
		exit();
	}

	function createPDF() {
		// create new PDF document
		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('AK Framework');
		$pdf->SetTitle('Rapport pointage atelier');
		$pdf->SetSubject('Pointage atelier');
		$pdf->SetKeywords('');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('helvetica', 'B', 20);

		// add a page
		$pdf->AddPage();

		$pdf->Write(0, 'Pointage atelier', '', 0, 'L', true, 0, false, false, 0);

		$pdf->SetFont('helvetica', '', 8);

		// -----------------------------------------------------------------------------

		$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
	<tr>
		<td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3</td>
		<td>COL 2 - ROW 1</td>
		<td>COL 3 - ROW 1</td>
	</tr>
	<tr>
		<td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
		<td>COL 3 - ROW 2</td>
	</tr>
	<tr>
		<td>COL 3 - ROW 3</td>
	</tr>

</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');

		// -----------------------------------------------------------------------------

		$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
	<tr>
		<td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3<br />text line<br />text line<br />text line<br />text line<br />text line<br />text line</td>
		<td>COL 2 - ROW 1</td>
		<td>COL 3 - ROW 1</td>
	</tr>
	<tr>
		<td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
			<td>COL 3 - ROW 2</td>
	</tr>
	<tr>
		<td>COL 3 - ROW 3</td>
	</tr>

</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');

		// -----------------------------------------------------------------------------

		$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
	<tr>
		<td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3<br />text line<br />text line<br />text line<br />text line<br />text line<br />text line</td>
		<td>COL 2 - ROW 1</td>
		<td>COL 3 - ROW 1</td>
	</tr>
	<tr>
		<td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
			<td>COL 3 - ROW 2<br />text line<br />text line</td>
	</tr>
	<tr>
		<td>COL 3 - ROW 3</td>
	</tr>

</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');

		// -----------------------------------------------------------------------------

		$tbl = <<<EOD
<table border="1">
<tr>
<th rowspan="3">Left column</th>
<th colspan="5">Heading Column Span 5</th>
<th colspan="9">Heading Column Span 9</th>
</tr>
<tr>
<th rowspan="2">Rowspan 2<br />This is some text that fills the table cell.</th>
<th colspan="2">span 2</th>
<th colspan="2">span 2</th>
<th rowspan="2">2 rows</th>
<th colspan="8">Colspan 8</th>
</tr>
<tr>
<th>1a</th>
<th>2a</th>
<th>1b</th>
<th>2b</th>
<th>1</th>
<th>2</th>
<th>3</th>
<th>4</th>
<th>5</th>
<th>6</th>
<th>7</th>
<th>8</th>
</tr>
</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');

		// -----------------------------------------------------------------------------

		// Table with rowspans and THEAD
		$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2">
<thead>
	<tr style="background-color:#FFFF00;color:#0000FF;">
	<td width="30" align="center"><b>A</b></td>
	<td width="140" align="center"><b>XXXX</b></td>
	<td width="140" align="center"><b>XXXX</b></td>
	<td width="80" align="center"> <b>XXXX</b></td>
	<td width="80" align="center"><b>XXXX</b></td>
	<td width="45" align="center"><b>XXXX</b></td>
	</tr>
	<tr style="background-color:#FF0000;color:#FFFF00;">
	<td width="30" align="center"><b>B</b></td>
	<td width="140" align="center"><b>XXXX</b></td>
	<td width="140" align="center"><b>XXXX</b></td>
	<td width="80" align="center"> <b>XXXX</b></td>
	<td width="80" align="center"><b>XXXX</b></td>
	<td width="45" align="center"><b>XXXX</b></td>
	</tr>
</thead>
	<tr>
	<td width="30" align="center">1.</td>
	<td width="140" rowspan="6">XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX</td>
	<td width="140">XXXX<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td width="80">XXXX</td>
	<td align="center" width="45">XXXX<br />XXXX</td>
	</tr>
	<tr>
	<td width="30" align="center" rowspan="3">2.</td>
	<td width="140" rowspan="3">XXXX<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td align="center" width="45">XXXX<br />XXXX</td>
	</tr>
	<tr>
	<td width="80">XXXX<br />XXXX<br />XXXX<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td align="center" width="45">XXXX<br />XXXX</td>
	</tr>
	<tr>
	<td width="80" rowspan="2" >RRRRRR<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td align="center" width="45">XXXX<br />XXXX</td>
	</tr>
	<tr>
	<td width="30" align="center">3.</td>
	<td width="140">XXXX1<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td align="center" width="45">XXXX<br />XXXX</td>
	</tr>
	<tr>
	<td width="30" align="center">4.</td>
	<td width="140">XXXX<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td width="80">XXXX<br />XXXX</td>
	<td align="center" width="45">XXXX<br />XXXX</td>
	</tr>
</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');

		$pdf->writeHTML($tbl, true, false, false, false, '');

		// -----------------------------------------------------------------------------

		// NON-BREAKING TABLE (nobr="true")

		$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2" nobr="true">
	<tr>
	<th colspan="3" align="center">NON-BREAKING TABLE</th>
	</tr>
	<tr>
	<td>1-1</td>
	<td>1-2</td>
	<td>1-3</td>
	</tr>
	<tr>
	<td>2-1</td>
	<td>3-2</td>
	<td>3-3</td>
	</tr>
	<tr>
	<td>3-1</td>
	<td>3-2</td>
	<td>3-3</td>
	</tr>
</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');

		// -----------------------------------------------------------------------------

		// NON-BREAKING ROWS (nobr="true")

		$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2" align="center">
	<tr nobr="true">
	<th colspan="3">NON-BREAKING ROWS</th>
	</tr>
	<tr nobr="true">
	<td>ROW 1<br />COLUMN 1</td>
	<td>ROW 1<br />COLUMN 2</td>
	<td>ROW 1<br />COLUMN 3</td>
	</tr>
	<tr nobr="true">
	<td>ROW 2<br />COLUMN 1</td>
	<td>ROW 2<br />COLUMN 2</td>
	<td>ROW 2<br />COLUMN 3</td>
	</tr>
	<tr nobr="true">
	<td>ROW 3<br />COLUMN 1</td>
	<td>ROW 3<br />COLUMN 2</td>
	<td>ROW 3<br />COLUMN 3</td>
	</tr>
</table>
EOD;

		$pdf->writeHTML($tbl, true, false, false, false, '');

		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$pdf->Output(__DIR__ . '/example_048.pdf', 'F');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

}
