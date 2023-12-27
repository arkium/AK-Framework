<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
    <form id="frmUserRole" class="ui form" action="usersroles" method="post">
        <h4 id="title-frmUserRole" class="ui dividing header">User Role Information</h4>
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
        <input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
        <input type="hidden" id="role_id" name="role_id" value="<?php echo $role_id; ?>" />
        <div class="two fields">
            <div class="two wide field">
                <label for="code">Code:</label>
                <input type="text" id="code" name="code" />
            </div>
            <div class="fourteen wide field">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" />
            </div>
        </div>
        <div class="field">
            <div id="tab-frmUserRole">
                <div class="ui secondary pointing stackable menu">
                    <a class="active item" data-tab="a">Permissions</a>
                    <a class="item" data-tab="b">Information</a>
                </div>
                <div class="ui active tab" data-tab="a">
                    <div class="inline fields">
                        <label>Select the type of user:</label>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio" name="admin" id="admin" value="1" />
                                <label for="admin">Admin </label>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio" name="admin" id="noadmin" value="0" />
                                <label for="noadmin">User </label>
                            </div>
                        </div>
                    </div>
                    <div class="inline fields">
                        <label>Define their permissions:</label>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="checkbox" id="view" name="view" value="1" />
                                <label for="view">View</label>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="checkbox" id="add" name="add" value="1" />
                                <label for="add">Add</label>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="checkbox" id="edit" name="edit" value="1" />
                                <label for="edit">Edit</label>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="checkbox" id="delete" name="delete" value="1" />
                                <label for="delete">Delete</label>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="checkbox" id="approval" name="approval" value="1" />
                                <label for="approval">Approval</label>
                            </div>
                        </div>
                    </div>
                    <div class="inline fields">
                        <label>Select modules:</label>
                        <div class="field">
                            <select id="modules" name="modules[]" multiple class="search">
                                <?php echo $fct->droplist("", parent::$param['modules']); ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="ui tab" data-tab="b">
                    <div class="three fields">
                        <div class="field">
                            <label for="status">Status:</label>
                            <select id="status" name="status">
                                <?php echo $fct->droplist("1", parent::$param['status']); ?>
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
