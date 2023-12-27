<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
    <form id="frmTaskType" class="ui form" action="taskstypes" method="post">
        <h4 id="title-frmTaskType" class="ui dividing header">Activity Type Information</h4>
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>">
        <input type="hidden" id="op" name="op" value="<?php echo $op; ?>">
        <input type="hidden" id="task_type_id" name="task_type_id" value="<?php echo $task_type_id; ?>">
        <div class="field">
            <label for="code">Code:</label>
            <input type="text" id="code" name="code">
        </div>
        <div class="field">
            <label for="name">Name:</label>
            <textarea id="name" name="name" rows="2" cols="45"></textarea>
        </div>
        <div class="field">
            <div id="tab-frmTaskType">
                <div class="ui secondary pointing stackable menu">
                    <a class="active item" data-tab="a">Settings</a>
                    <a class="item" data-tab="b">Note</a>
                    <a class="item" data-tab="c">Information</a>
                </div>
                <div class="ui active tab" data-tab="a">
                    <div class="field">
                        <label for="task_family_id">Family:</label>
                        <select id="task_family_id" name="task_family_id">
                            <option value="">Select ...</option>
                            <?php echo $fct->droplist("", parent::$param['type_taskfamily']); ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="chargeable">Type:</label>
                        <select id="chargeable" name="chargeable">
                            <option value="">Select ...</option>
                            <?php echo $fct->droplist("", parent::$param['type_chargeable']); ?>
                        </select>
                    </div>
                    <!--<div class="field">
                        <label for="color">Color:</label>
                        <select id="color" name="color">
                            <option value="">No color</option>
                            <?php echo $fct->droplist("", parent::$param['type_color']); ?>
                        </select>
                    </div>-->
                </div>
                <div class="ui tab" data-tab="b">
                    <div class="field">
                        <textarea id="note" name="note"></textarea>
                    </div>
                </div>
                <div class="ui tab" data-tab="c">
                    <div class="three fields">
                        <div class="field">
                            <label for="status">Status:</label>
                            <select id="status" name="status">
                                <?php echo $fct->droplist("1", parent::$param['status']); ?>
                            </select>
                        </div>                        
                        <div class="field">
                            <label for="created_time">Creation Date:</label>
                            <input type="text" id="created_time" name="created_time" data-notremove="true" disabled="disabled">
                        </div>
                        <div class="field">
                            <label for="update_time">Update Date:</label>
                            <input type="text" id="update_time" name="update_time" data-notremove="true" disabled="disabled">
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