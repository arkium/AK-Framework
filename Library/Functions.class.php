<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2021, Arkium SCS
 */
namespace Library;

/**
 * Classe avec des méthodes complémentaires
 * @namespace Library
 * @package Functions.class.php
 */
class Functions
{

    /**
     * Renvoi une liste d'option pour le tag select selon un array(key, value)
     * Selon le param $selected : ajoute selected à l'option
     * @param string $selected
     * @param array $droplist
     * @return string retourne liste d'option
     */
    public function droplist($selected, $droplist, $directaffiche = false)
    {
        if (empty($droplist) || !is_array($droplist))
            return false;
        $content = "";
        $start = false;
        $last = null;
        foreach ($droplist['code'] as $key => $value) {
            if (isset($droplist['group'][$key])) {
                if ($start && $droplist['group'][$key] != $last) {
                    $content .= '</optgroup>';
                    $start = false;
                }
                if (!$start && $droplist['group'][$key] != $last) {
                    $start = true;
                    $last = $droplist['group'][$key];
                    $content .= '<optgroup label="' . $last . '">';
                }
            }
            if (array_key_exists('status', $droplist)) {
                $affiche = ($droplist['status'][$key] == false) ? false : true;
            } else
                $affiche = true;
            if ($affiche || $directaffiche) {
                $content .= '<option value="' . $value . '" ';
                if (isset($droplist['option'][$key]))
                    $content .= $droplist['option'][$key];
                if (is_array($selected)) {
                    $content .= (in_array($value, $selected)) ? 'selected' : '';
                } else {
                    $content .= ($selected == $value) ? 'selected' : '';
                }
                $content .= '>' . $droplist['name'][$key] . "</option>\n";
            }
        }
        return $content;
    }

    public function AddTime($time1, $time2)
    {
        $time1 = (empty($time1)) ? "00:00:00" : date('H:i:s', strtotime($time1));
        $time2 = (empty($time1)) ? "00:00:00" : date('H:i:s', strtotime($time2));
        list($hr1, $min1, $sec1) = mb_split(":", $time1);
        $UTime1 = ($hr1 * 3600) + ($min1 * 60) + $sec1;
        list($hr2, $min2, $sec2) = mb_split(":", $time2);
        $UTime2 = ($hr2 * 3600) + ($min2 * 60) + $sec2;
        $UTimeTotal = $UTime1 + $UTime2;
        $hr3 = intval($UTimeTotal / 3600);
        $reste = $UTimeTotal % 3600;
        $min3 = intval($reste / 60);
        $sec3 = $reste % 60;
        return sprintf('%1$02d:%2$02d', $hr3, $min3);
    }

    public function cSec_to_HrsMin($time)
    {
        $hr = intval($time / 3600);
        $min = intval(($time % 3600) / 60);
        $sec = intval((($time % 3600) % 60));
        return sprintf('%1$02d:%2$02d', $hr, $min);
    }

    public function c100eHrs_to_HrsMin($time)
    {
        $sec = $time * 3600;
        $hr = intval($sec / 3600);
        $min = intval(($sec % 3600) / 60);
        return sprintf('%1$02d:%2$02d', $hr, $min);
    }

    public function c100eHrs_to_days($time)
    {
        $sec = $time * 3600;
        $days = $sec / (3600 * 8);
        return sprintf('%01.2f', $days);
    }

    public function get_last_day($month, $year)
    {
        $days = date('t', strtotime("$year-$month-1"));
        return $days;
    }

    function week2day()
    {
        $numargs = func_num_args();
        if ($numargs >= 2) {
            $year = func_get_arg(1);
        } else {
            $year = date("Y");
        }

        $week = func_get_arg(0);

        $fdoty = date("w", mktime(0, 0, 0, 1, 1, $year));
        $days_to_second_week = 8 - $fdoty;

        $days_to_end_week = (($week - 1) * 7) + $days_to_second_week;
        $days_to_start_week = $days_to_end_week - 6;

        $daysofweek['start'] = date("Y-m-d", mktime(0, 0, 0, 1, $days_to_start_week, $year));
        $daysofweek['end'] = date("Y-m-d", mktime(0, 0, 0, 1, $days_to_end_week, $year));
        return $daysofweek;
    }

    /**
     * Retourne les dates de début et fin de semaine (Lundi et Dimanche)
     * @param numeric $annee : numéro de l'année
     * @param numeric $semaine : numéro de la semaine
     * @param string $format : format des dates pour le retour
     */
    function getWeekStartAndEnd($annee = "", $semaine = "", $format = 'Y-m-d')
    {
        $annee = (isset($annee) && !empty($annee)) ? $annee : date("Y");
        $semaine = (isset($semaine) && !empty($semaine)) ? $semaine : date("W");

        $dateObjet = new \DateTime();
        $dateObjet->setISOdate($annee, $semaine);
        $dateDebut = $dateObjet->format($format);
        date_modify($dateObjet, '+6 day');
        $dateFin = $dateObjet->format($format);
        $yearWeek = $dateObjet->format("Y-W");

        return array(
            "dateDebut" => $dateDebut,
            "dateFin" => $dateFin,
            "yearWeek" => $yearWeek
        );
    }

}