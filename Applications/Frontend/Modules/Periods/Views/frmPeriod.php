<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
    <form id="frmPeriod" class="ui form" action="periods" method="post">
        <h4 id="title-frmPeriod" class="ui dividing header">Period Information</h4>
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
        <input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
        <input type="hidden" id="period_id" name="period_id" value="<?php echo $period_id; ?>" />
        <div class="field">
            <div id="tab-frmPeriod">
                <div class="ui secondary pointing stackable menu">
                    <a class="active item" data-tab="a">Periods</a>
                    <a class="item" data-tab="b">Information</a>
                </div>
                <div class="ui active tab" data-tab="a">
                    <div class="two fields">
                        <div class="field">
                            <label for="start_date">Start Date:</label>
                            <div class="uk-form-controls">
                                <div class="uk-form-icon">
                                    <i class="uk-icon-calendar"></i>
                                    <input type="text" id="start_date" name="start_date" />
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label for="end_date">End Date:</label>
                            <div class="uk-form-controls">
                                <div class="uk-form-icon">
                                    <i class="uk-icon-calendar"></i>
                                    <input type="text" id="end_date" name="end_date" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ui tab" data-tab="b">
                    <div class="three fields">
                        <div class="field">
                            <label for="status">Status:</label>
                            <select id="status" name="status">
                                <?php echo $fct->droplist("", parent::$param['status']); ?>
                            </select>
                        </div>
                        <div class="field">
                            <label for="created_time">Creation Date:</label>
                            <input type="text" id="created_time" name="created_time" data-notremove="true" disabled="disabled" />
                        </div>
                        <div class="field">
                            <label for="update_time">Update Date:</label>
                            <input type="text" id="update_time" name="update_time" data-notremove="true" disabled="disabled" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ui error message"></div>
        <button id="btnSave" class="ui primary button" type="button">Save</button>
        <button id="btnCancel" class="ui button" type="button" data-return="<?php echo $return; ?>" ovisible="true">Cancel</button>
    </form>
</div>
