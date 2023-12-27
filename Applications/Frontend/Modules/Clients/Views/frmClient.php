<?php
defined('_KERNEL_FILE') or header('HTTP/1.0 404 Not Found');
defined('_KERNEL_FILE') or die();
?>
<div class="ui attached segment">
    <form id="frmClient" class="ui form" action="clients" method="post">
        <h4 id="title-frmCompany" class="ui dividing header">Client Information</h4>
        <input type="hidden" id="token" name="token" value="<?php echo $page_token; ?>" />
        <input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
        <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id; ?>" />
        <div class="fields">
            <div class="two wide field">
                <label for="code">Code:</label>
                <input type="text" id="code" name="code">
            </div>
            <div class="fourteen wide field">
                <label for="organisation">Organisation:</label>
                <input type="text" id="organisation" name="organisation">
            </div>
        </div>
		<div class="two fields">
			<div class="field">
				<label for="opportunity_client">Opportunity / Client:</label>
				<select id="opportunity_client" name="opportunity" <?php echo (parent::$user->permissions['approval']) ? '' : 'data-notremove="true" disabled="disabled"' ;?>>
					<?php echo $fct->droplist("", parent::$param['OpportunityClient']); ?>
				</select>
			</div>
			<div class="field">
				<label for="entity_group_id">Group:</label>
				<select id="entity_group_id" name="entity_group_id">
					<option value="">Select a group</option>
					<?php echo $fct->droplist("", parent::$param['entity_group_id']); ?>
				</select>
			</div>
		</div>

        <div class="field">
            <div id="tab-frmClient">
                <div class="ui secondary pointing stackable menu">
                    <a class="active item" data-tab="a">Address</a>
                    <!--<a class="item" data-tab="b">Details</a>-->
                    <!--<a class="item" data-tab="c">Legal data</a>-->
                    <a class="item" data-tab="d">Note</a>
                    <a class="item" data-tab="e">Information</a>
                </div>

                <div class="ui active tab" data-tab="a">
                    <div class="two fields">
                        <div class="field">
                            <label for="address1">Address 1:</label>
                            <input type="text" id="address1" name="address1">
                        </div>
                        <div class="field">
                            <label for="address2">Address 2:</label>
                            <input type="text" id="address2" name="address2">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label for="postal_code">Postal Code:</label>
                            <input type="text" id="postal_code" name="postal_code">
                        </div>
                        <div class="field">
                            <label for="city">City:</label>
                            <input type="text" id="city" name="city">
                        </div>
                    </div>
                    <div class="three fields">
                        <div class="field">
                            <label for="state">State:</label>
                            <input type="text" id="state" name="state">
                        </div>
                        <div class="field">
                            <label for="country">Country:</label>
                            <input type="text" id="country" name="country">
                        </div>
                        <div class="field">
                            <label for="http_url">Website:</label>
                            <input type="text" id="http_url" name="http_url">
                        </div>
                    </div>
                </div>

                <!--<div class="ui tab" data-tab="c">
                    <div class="field">
                        <label for="dateLastRiskAssessment">Last Risk Assessment:</label>
                        <div class="ui left icon input">
                            <i class="calendar icon"></i>
                            <input type="text" id="dateLastRiskAssessment" name="dateLastRiskAssessment">
                        </div>
                    </div>
                    <div class="field">
                        <label>Business relationship:</label>
                        <div class="ui checkbox">
                            <input type="radio" name="direct" id="direct" value="1">
                            <label for="direct">Direct</label>
                        </div>
                        <div class="ui checkbox">
                            <input type="radio" name="direct" id="indirect" value="0">
                            <label for="indirect">Indirect</label>
                        </div>
                    </div>
                    <div class="three fields">
                        <div class="field">
                            <label for="inception_date">Inception date:</label>
                            <div class="ui left icon input">
                                <i class="calendar icon"></i>
                                <input type="text" id="inception_date" name="inception_date" data-uk-datepicker>
                            </div>
                        </div>
                        <div class="field">
                            <label for="juridiction">Juridiction:</label>
                            <input type="text" id="juridiction" name="juridiction">
                        </div>
                        <div class="field">
                            <label for="legal_form">Legal form:</label>
                            <select id="legal_form" name="legal_form">
                                <?php echo $fct->droplist("", parent::$param['legal_form']); ?>
                            </select>
                        </div>
                    </div>
                </div>-->

                <div class="ui tab" data-tab="d">
                    <div class="field">
                        <textarea id="note" name="note"></textarea>
                    </div>
                </div>

                <div class="ui tab" data-tab="e">
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