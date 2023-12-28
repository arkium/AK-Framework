<?php

namespace Applications\Cli\Modules\PointageAtelier;
use \tecnickcom\tcpdf;

class PointageAtelierController extends \Library\BackController {

    /**
     * Requête SQL à traiter
     */
    protected $query;

	public function __construct() {
		parent::__construct();
	}

	public function executePDFweek() {
        if (array_key_exists('weekPointage', parent::$param))
            $week = parent::$param['weekPointage'];
        else
            $week = "";
        $nbweek = (isset($week) && !empty($week)) ? $week : date('W', time());
		//$nbweek = date('W', time());
		//$nameweek = date('o-W', time());

		$fct = new \Library\Functions();
		//$tbl = $fct->week2day($nbweek);
		//$dateto = $tbl['end'];
		//$datefrom = $tbl['start'];
		$tbl = $fct->getWeekStartAndEnd(date('Y'), $nbweek);
        $dateto = $tbl['dateFin'];
		$datefrom = $tbl['dateDebut'];
		$nameweek = $tbl['yearWeek'];

		echo "\nCréation du PDF avec le pointage atelier de la semaine : " . $nameweek;
		//if (parent::$cli->getLine("\nDo you want to continue? (y/n) : ", parent::$cli->YesNo, 'y') == 'n')
		//    exit();

		$date_filter = " AND t.date<='" . $dateto . "' AND t.date>='" . $datefrom . "' ";

        $this->query = "
		SELECT
			h.hour_id AS hour_id,
            t.time_id AS time_id,
			t.task_id AS task_id,
			CONCAT(u.code, ' - ', u.first_name) AS user,
			p.code AS vehicule,
			t.date AS 'date pointage',
			h.start AS 'début pointage',
			h.end AS 'fin pointage',
			TIMEDIFF(h.end, h.start) AS 'durée Hrs',
            ROUND(TIME_TO_SEC(TIMEDIFF(h.end, h.start))/3600,2) AS '100eHrs',
            SEC_TO_TIME(TIME_TO_SEC(duration)) as 'total task',
			d.total AS 'total day',
			t.comment AS 'comment'
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
		$tbl = <<<EOF
<style>
    table {
		width: 100%;
        font-size: 8pt;
    }
    th {
        width: 70px;
        color: #003300;
        background-color: #ccffcc;
    }
    th.Comment {
        width: 150px;
        color: #003300;
        background-color: #ccffcc;
    }
	td {
        width: 70px;
        background-color: #ffffee;
    }
    td.Comment {
        width: 150px;
        background-color: #ffffee;
    }
</style>
EOF;
		$tbl .= $tableau->generateReport();
		$PDF_HEADER_STRING = "Pointage du ". $datefrom . " au " . $dateto ." - Semaine : " . $nameweek;
		$file = __DIR__ . "/" . $nameweek . "_pointageatelier.pdf";
		$result = $this->createPDF($tbl, "Gillet - Pointage Atelier", $PDF_HEADER_STRING, $file);
		echo "\nProcess is complete PDF : ";
		echo (empty($result)) ? "OK - saved" : $result ;

		$email = new \Library\Email();
		$email->setfrom(FROM_EMAIL);
		$email->setfile($file);
		$email->subject = 'Pointage atelier du ' . $datefrom . ' au ' . $dateto ." - Semaine : " . $nameweek;
		$email->setFilePathMessage(DIRCLI_TPL_EMAIL . 'pointageatelier.html');
//		$email->destinationEmail = 't.gillet@gilletvertigo.com, info@gilletvertigo.com, technics@gilletvertigo.com, paulo.ferreira@arkium.eu';
		$email->destinationEmail = 'paulo.ferreira@arkium.eu';
		$result = $email->sendEmail();
		echo "\nProcess is complete Email : ";
		echo ($result) ? "OK sended" : "NOK - not sended" ;
	}

	public function executeHelp() {
		echo <<<EOT
DESCRIPTION
  Cette commande permet la création d'un rapport pointage atelier
  pour l'envoyer ensuite par email à son destinataire.

USAGE
  php ak-cli.php pointageatelier

PARAMETERS
  Aucun paramètre à utiliser
EOT;
		exit();
	}

	private function createPDF($tbl, $PDF_HEADER_TITLE, $PDF_HEADER_STRING, $FILE) {
		// create new PDF document
		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('AK Framework');
		$pdf->SetTitle('Rapport pointage atelier');
		$pdf->SetSubject('Pointage atelier');
		$pdf->SetKeywords('');

		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$pdf->SetHeaderData('', '', $PDF_HEADER_TITLE, $PDF_HEADER_STRING);

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

		$pdf->AddPage('L', 'A4');
        //$pdf->AddPage();
        //$pdf->SetFont('helvetica', 'B', 20);
		//$pdf->Write(0, 'Pointage atelier', '', 0, 'L', true, 0, false, false, 0);
		$pdf->SetFont('helvetica', '', 8);

		$pdf->writeHTML($tbl, true, false, false, false, '');

		return $pdf->Output($FILE, 'F');
	}
}