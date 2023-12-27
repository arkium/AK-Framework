<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div id="unseen" class="ui attached segment">
    <div class="ui stackable grid">
        <div id="title_table" class="ten wide column">
            <h2 class="ui header">
                <i class="history icon"></i>
                <div class="content">
                    <?php echo _("Feuille de temps"); ?>
                    <div class="sub header">
						<?php
                        echo _("Période du ") . parent::$param['data_period_id']['start_date'][parent::$param['period_id']] . _(" au ") . parent::$param['data_period_id']['end_date'][parent::$param['period_id']];
                        if (isset($_GET["p"])) {
                            $query = "SELECT first_name, last_name FROM ts_users WHERE user_id='" . parent::$param['user_id'] . "'";
                            $result = parent::$dao->query($query);
                            list($first_name, $last_name) = $result->fetch();
                            echo " - " . _("Feuille de temps de $last_name, $first_name");
                        }
						?>
                    </div>
                </div>
            </h2>
        </div>
    </div>

    <table class="ui celled padded table dataTable" id="exemple">
        <thead>
            <tr>
                <th>Code</th>
                <?php
                $data_date_colonne = parent::$param['data_date_colonne']['date'];
                if (is_array($data_date_colonne)) {
                    reset($data_date_colonne);
                    foreach ($data_date_colonne as $key => $val) {
                        $currentDayStr = strftime("%a", $val);
                        $jour = date("N", $val);
                        $text = $currentDayStr . "<br>" . date("d", $val);
                        $text = ($jour == '6' || $jour == '7') ? "<b>$text</b>" : $text;
                        $text_class = ($jour == '6' || $jour == '7') ? " class=\"weekend\"" : " class=\"week\"";
                        print "<th$text_class>$text</th>\n";
                    }
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $type_name = array(
                "Non-chargeable",
                "Chargeable"
            );
            $nbre_ligne = 0;
            $query = "SELECT t.task_id, tt.chargeable, ts.code, ts.closing_date, tt.name, c.organisation
FROM ts_timesheets as t, ts_tasks as ts, ts_tasks_types as tt, ts_entities as c
WHERE t.task_id=ts.task_id AND ts.task_type_id=tt.task_type_id
	AND ts.customer_id=c.entity_id
	AND t.period_id ='" . parent::$param['period_id'] . "'
	AND t.user_id='" . parent::$param['user_id'] . "'
GROUP BY t.task_id
ORDER BY tt.chargeable DESC, ts.code";
            $data_time = array();
            $result = parent::$dao->query($query);
            $lastType = null;
            while (list($task_id, $type, $code, $closing_date, $name, $organisation) = $result->fetch()) {
                if ($lastType != $type) {
                    print "<tr>\n<td colspan=\"16\" class=\"group\"><span class=\"ui blue horizontal label\">$type_name[$type]</span></td>\n</tr>\n";
                    $lastType = $type;
                }
                $nbre_ligne ++;
                $pop = 'class="pop" data-content="' . $organisation . ' - ' . $name . '"';
                print "<td><span $pop>" . $code . "</span></td>\n";
                if (is_array($data_date_colonne)) {
                    reset($data_date_colonne);
                    $data_times = parent::$param['data_times'];
                    foreach ($data_date_colonne as $key => $val) {
                        $jour = date("N", $val);
                        $text_class = ($jour == '6' || $jour == '7') ? " class=\"weekend\"" : " class=\"week\"";
                        if (!empty($data_times['duration'][$val]['c' . $task_id])) {
                            $a = empty($data_time[$val]) ? null : $data_time[$val];
                            $data_time[$val] = $fct->Addtime($a, $data_times['duration'][$val]['c' . $task_id]);
                            $time = strtotime($data_times['duration'][$val]['c' . $task_id]);
                            $log_message = $data_times['comment'][$val]['c' . $task_id];
                            $class_comment = (!empty($log_message)) ? "class=\"comment\"" : "";
                            print "<td$text_class>\n";
                            $pop = (!empty($log_message)) ? ' data-content="' . $log_message . '"' : '';
                            echo (!empty($log_message)) ? "<span $class_comment $pop>" : "";
                            print date("H:i", $time) . "\n";
                            echo (!empty($log_message)) ? "</span>\n" : "\n";
                            print "</td>\n";
                        } else {
                            print "<td$text_class></td>\n";
                        }
                    }
                }
                print "</tr>\n";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Total Time</th>
                <?php
                if (is_array($data_date_colonne)) {
                    reset($data_date_colonne);
                    foreach ($data_date_colonne as $key => $val) {
                        $jour = date("N", $val);
                        $text_class = ($jour == '6' || $jour == '7') ? " class=\"weekend\"" : " class=\"week\"";
                        $text = (!empty($data_time[$val]) && $data_time[$val] != '0:00') ? $data_time[$val] : "";
                        print "<th$text_class>$text</th>\n";
                    }
                }
                ?>
            </tr>
        </tfoot>
    </table>

    <div class="ui stackable grid">
        <div class="eight wide column">
            <div id="actionBtn" class="ui teal buttons">
                <div id="returnoverview" class="ui button" data-return="<?php echo parent::$param['return']; ?>"><?php echo _("Retour à la page précédente"); ?></div>
            </div>
        </div>
        <div class="right aligned eight wide column">
            <?php echo "Showing $nbre_ligne entries"; ?>
        </div>
    </div>
</div>
