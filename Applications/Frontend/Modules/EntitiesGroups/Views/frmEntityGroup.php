<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
    <form id="frmEntityGroup" class="ui form" action="entitiesgroups" method="post">
        <h4 id="title-frmEntityGroup" class="ui dividing header">Entities Groups Information</h4>
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
        <div class="two fields">
            <div class="field">
                <label for="tf_name_add">Add:</label>
                <div class="ui action input">
                    <input type="text" id="tf_name_add" name="tf_name_add" />
                    <button type="button" id="tf_btn_add" class="ui button">Add</button>
                </div>
            </div>
        </div>
        <div class="field">
            <label for="tf_entity_group_id">Entities Groups:</label>
            <select id="tf_entity_group_id" name="tf_task_family_id" size="6"></select>
        </div>
        <div class="field">
            <label for="tf_name_edit">Edit:</label>
            <div class="ui action input">
                <input type="text" id="tf_name_edit" name="tf_name_edit" />
                <div class="ui buttons">
                    <button type="button" id="tf_btn_edit" class="ui button">Ok</button>
                    <div class="or"></div>
                    <button type="button" id="tf_btn_remove" class="ui red button">Remove</button>
                </div>
            </div>
        </div>
        <button type="button" id="tf_return" class="ui primary button" data-return="<?php echo $return; ?>">Return</button>
    </form>
</div>