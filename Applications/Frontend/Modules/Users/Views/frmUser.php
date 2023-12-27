<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
    <form id="frmUser" class="ui form" action="users" method="post">
        <h4 id="title-frmUser" class="ui dividing header"><?php echo _("Utilsateur"); ?></h4>
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>">
        <input type="hidden" id="op" name="op" value="<?php echo $op; ?>">
        <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">
        <div class="fields">
            <div class="two wide field">
                <label for="code">Code:</label>
                <input type="text" id="code" name="code">
            </div>
            <div class="seven wide field">
                <label for="first_name"><?php echo _("Prénom"); ?>:</label>
                <input type="text" id="first_name" name="first_name">
            </div>
            <div class="seven wide field">
                <label for="last_name"><?php echo _("Nom"); ?>:</label>
                <input type="text" id="last_name" name="last_name">
            </div>
        </div>
        <div class="field">
            <div id="tab-frmUser">
                <div class="ui secondary pointing stackable menu">
                    <a class="active item" data-tab="a"><?php echo _("Login"); ?></a>
                    <a class="item" data-tab="b"><?php echo _("Détails"); ?></a>
                    <a class="item" data-tab="c"><?php echo _("Information"); ?></a>
                </div>
                <div class="ui active tab" data-tab="a">
                    <div class="two fields">
                        <div class="field ">
                            <label for="username"><?php echo _("Login"); ?>:</label>
                            <div class="ui left icon input">
                                <i class="user icon"></i>
                                <input type="text" id="username" name="username">
                            </div>
                        </div>
                        <div class="field">
                            <label for="password"><?php echo _("Mot de passe"); ?>:</label>
                            <div class="ui left icon input">
                                <i class="lock icon"></i>
                                <input type="text" id="password" name="password">
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label for="level"><?php echo _("Droits d'accès"); ?>:</label>
                        <select id="level" name="level">
                            <?php echo $fct->droplist("", parent::$param['role_id']); ?>
                        </select>
                    </div>
                </div>
                <div class="ui tab" data-tab="b">
					<div class="two fields">
						<div class="field">
							<label for="email_address"><?php echo _("Email"); ?>:</label>
							<div class="ui left icon input">
								<i class="mail icon"></i>
								<input type="text" id="email_address" name="email_address">
							</div>
						</div>
						<div class="field">
							<label for="typetimesheet"><?php echo _("Feuille de temps"); ?>:</label>
							<select id="typetimesheet" name="typetimesheet">
								<option value=""><?php echo _("Sélectionner un type"); ?></option><?php echo $fct->droplist("", parent::$param['typetimesheet']); ?>
							</select>
						</div>
					</div>
					<div class="two fields">
						<div class="field">
							<label for="invoicing_entity_id"><?php echo _("Employeur"); ?>:</label>
							<select id="invoicing_entity_id" name="company_id">
								<option value=""><?php echo _("Sélectionner un employeur"); ?></option><?php echo $fct->droplist("", parent::$param['data_invoicing_entity_id']); ?>
							</select>
						</div>
						<div class="field">
							<label for="contract"><?php echo _("Type de contrat"); ?>:</label>
							<input type="text" id="contract" name="contract">
						</div>
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