<style>
    table.intrum-css {
        border-collapse: collapse;
    }

    table.intrum-css td {
        padding: 2px;
        border: 1px solid #DDDDDD;
    }

    tr.intrum-css-tr label {
        padding: 0 0 0 2px;
        width: auto;
    }

    tr.intrum-css-tr td {
        padding: 5px 2px 5px 2px;
        font-weight: bold;
    }

    .alert {
        padding: 8px 35px 8px 14px;
        margin-bottom: 20px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        background-color: #fcf8e3;
        border: 1px solid #fbeed5;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        width: 300px;
    }

    .alert-success {
        color: #468847;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .cdp_plugin {
        margin: 0 0 5px 0;
        padding: 0;
    }

    #tabs1 {
        font: bold 11px/1.5em Verdana;
        float: left;
        width: 100%;
        background: #FFFFFF;
        font-size: 93%;
        line-height: normal;
    }

    #tabs1 ul {
        margin: 0;
        padding: 10px 10px 0 0px;
        list-style: none;
    }

    #tabs1 li {
        display: inline;
        margin: 0;
        padding: 0;
    }

    #tabs1 a {
        float: left;
        background: url("{$this_path}images/tableft1.gif") no-repeat left top;
        margin: 0;
        padding: 0 0 0 4px;
        text-decoration: none;
    }

    #tabs1 a span {
        float: left;
        display: block;
        background: url("{$this_path}images/tabright1.gif") no-repeat right top;
        padding: 5px 15px 4px 6px;
        color: #627EB7;
    }

    /* Commented Backslash Hack hides rule from IE5-Mac \*/
    #tabs1 a span {
        float: none;
    }

    /* End IE5-Mac hack */
    #tabs a:hover span {
        color: #627EB7;
    }

    #tabs1 a:hover {
        background-position: 0% -42px;
    }

    #tabs1 a:hover span {
        background-position: 100% -42px;
    }

    #tab-settings, #tab-logs {
        padding: 5px;
        border: 1px solid #DDDDDD;
        clear: both;
        display: block;
    }
    table.table-logs {
        width: 100%;
        border-collapse: collapse;
    }

    table.table-logs td {
        padding: 3px;
        border: 1px solid #DDDDDD;

    }
</style>
<h1 style="padding: 0; margin: 0">CembraPay payment gateway configuration</h1>


<ul class="tab nav nav-tabs">
    <li><a href="{$url}" id="href-settings" title="Settings"><span>Settings</span></a></li>
    <li><a href="{$urllogs}" id="href-logs" title="Logs"><span>Logs</span></a></li>
</ul>
{if ($cembra_view_json)}
    <a href="javascript:history.go(-1)">Back to log</a>
    <h1>Input & output JSON</h1>
    <table width="100%">
        <tr>
            <td>Input</td>
            <td>Response</td>
        </tr>
        <tr>
            <td width="50%" style="border: 1px solid #CCCCCC; background-color: #FFFFFF; padding: 5px;" valign="top"><code style="width: 100%; word-wrap: break-word; white-space: pre-wrap;">{$cembra_single_log["input"]}</code></td>
            <td width="50%" style="border: 1px solid #CCCCCC; background-color: #FFFFFF; padding: 5px;" valign="top"><code style="width: 100%; word-wrap: break-word; white-space: pre-wrap;">{$cembra_single_log["output"]}</code></td>
        </tr>
    </table>
{elseif ($showlogs)}
    <div id="tab-logs">
        <div>
            Searh in log
            <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
                <input value="{$search_in_log|escape}" name="searchInLog"> <input type="submit" value="search">
                <input type="hidden" value="ok" name="submitLogSearch">
            </form>
        </div>
        <br/>
        {if !$search_in_log}Last 20 results
        {else}
            Search result for string "{$search_in_log|escape}"
        {/if}
        <table class="table-logs">
            <tr>
                <td>Firstname</td>
                <td>Lastname</td>
                <td>IP</td>
                <td>Status</td>
                <td>Date</td>
                <td>Request ID</td>
                <td>Type</td>
            </tr>
            {foreach from=$cembra_logs item=log}
                <tr>
                    <td>{$log.firstname|escape}</td>
                    <td>{$log.lastname|escape}</td>
                    <td>{$log.ip|escape}</td>
                    <td>{$log.cembra_status|escape}</td>
                    <td>{$log.creation_date|escape}</td>
                    <td>{$log.request_id|escape}</td>
                    <td><a href="{$url}&viewjson={$log.cembra_id}">{$log.request_type|escape}</a>
                    </td>
                </tr>
            {/foreach}
            {if !$cembra_logs}
                <tr>
                    <td colspan="5" style="padding: 10px">
                        No results found
                    </td>
                </tr>
            {/if}
        </table>
    </div>
{else}
    <div id="tab-settings">
        {if ($intrum_submit_main == 'OK')}
            <div class="alert alert-success" style="width: 100%">
                Configuration saved
            </div>
        {/if}
        <form method="post" class="defaultForm form-horizontal"
              action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}"
              id="intrum_main_configuration">

            <div class="panel" id="fieldset_0">
                <div class="panel-heading">
                    <i class="icon-cogs"></i> General settings
                </div>
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Mode:
                        </label>

                        <div class="col-lg-9">
                            <select name="INTRUM_MODE" id="INTRUM_MODE">
                                <option value="test"{if ($intrum_mode == 'test')} selected{/if}>Test mode</option>
                                <option value="live"{if ($intrum_mode == 'live')} selected{/if}>Production mode</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Payment mode:
                        </label>

                        <div class="col-lg-9">
                            <select name="CEMBRAPAY_PAYMENT_MODE" id="CEMBRAPAY_PAYMENT_MODE">
                                <option value="api"{if ($CEMBRAPAY_PAYMENT_MODE == 'api')} selected{/if}>API with no redirect</option>
                                <option value="checkout"{if ($CEMBRAPAY_PAYMENT_MODE == 'checkout')} selected{/if}>Checkout with redirect</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Live client ID:
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="CEMBRAPAY_LIVE_CLIENT_ID" id="CEMBRAPAY_LIVE_CLIENT_ID"
                                   value="{$cembra_live_client_id|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Live password:
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="CEMBRAPAY_LIVE_PASSWORD" id="CEMBRAPAY_LIVE_PASSWORD"
                                   value="{$cembra_live_password|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Test client ID:
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="CEMBRAPAY_TEST_CLIENT_ID" id="CEMBRAPAY_TEST_CLIENT_ID"
                                   value="{$cembra_test_client_id|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Test password:
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="CEMBRAPAY_TEST_PASSWORD" id="CEMBRAPAY_TEST_PASSWORD"
                                   value="{$cembra_test_password|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Connection timeout to CembraPay server in seconds:
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="BYJUNO_CONN_TIMEOUT" id="BYJUNO_CONN_TIMEOUT"
                                   value="{$BYJUNO_CONN_TIMEOUT|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Enable ThreatMetrix:
                        </label>

                        <div class="col-lg-9">
                            <select name="INTRUM_ENABLETMX" id="INTRUM_ENABLETMX">
                                <option value="false"{if ($INTRUM_ENABLETMX == 'false')} selected{/if}>Disabled</option>
                                <option value="true"{if ($INTRUM_ENABLETMX == 'true')} selected{/if}>Enabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            ThreatMetrix orgid:
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="INTRUM_TMXORGID" id="INTRUM_TMXORGID"
                                   value="{$INTRUM_TMXORGID|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Allow postal delivery:
                        </label>

                        <div class="col-lg-9">
                            <select name="BYJUNO_ALLOW_POSTAL" id="BYJUNO_ALLOW_POSTAL">
                                <option value="false"{if ($BYJUNO_ALLOW_POSTAL == 'false')} selected{/if}>Disabled
                                </option>
                                <option value="true"{if ($BYJUNO_ALLOW_POSTAL == 'true')} selected{/if}>Enabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Enable gender &amp; birthday selection:
                        </label>

                        <div class="col-lg-9">
                            <select name="BYJUNO_GENDER_BIRTHDAY" id="BYJUNO_GENDER_BIRTHDAY">
                                <option value="false"{if ($BYJUNO_GENDER_BIRTHDAY == 'false')} selected{/if}>Disabled
                                </option>
                                <option value="true"{if ($BYJUNO_GENDER_BIRTHDAY == 'true')} selected{/if}>Enabled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="hidden" name="submitIntrumMain" value="intrum_main_configuration"/>
                    <button type="submit" value="1" id="module_form_submit_btn" name="btnSubmit"
                            class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> Save
                    </button>
                </div>
            </div>

            <div class="panel" id="fieldset_0">
                <div class="panel-heading">
                    <i class="icon-cogs"></i> Risk managment
                </div>

                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Mininmal amount for checkout (default 10):
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="BYJUNO_MIN_AMOUNT" id="BYJUNO_MIN_AMOUNT"
                                   value="{$BYJUNO_MIN_AMOUNT|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Maximal amount for checkout (default 1000):
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="BYJUNO_MAX_AMOUNT" id="BYJUNO_MAX_AMOUNT"
                                   value="{$BYJUNO_MAX_AMOUNT|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                        </label>

                        <div class="col-lg-9">
                            <div>
                                if "Screening before show payment" is enabled by the merchant, the merchant explicitly agrees and warrants the following:
                                <br><br>- The merchant (as data controller) assigns CembraPay (as data processor) to perform a credit-check (screening) on its behalf. In this case the end-customers data (name, address, date of birth, contact information (email, telephone number) as well as IP-Address, Used Proxy Servers, etc.) is automatically being sent from the merchant to CembraPay and further to Intrum as subprocessor. CembraPay confirms or rejects to the merchant the availability of CembraPay payment solution for the specific customer.
                                <br><br>- The merchant must clearly and transparently inform the end-customers about the data processing by CembraPay and Intrum and obtain (if legally required) their consent. It is in the sole responsibility of the merchant to assess the legality of this data processing and the merchant must ensure at all times full compliance with applicable data protection regulations.
                                <br><br>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Screening before show payment:
                        </label>

                        <div class="col-lg-9">
                            <select name="BYJUNO_CREDIT_CHECK" id="BYJUNO_CREDIT_CHECK">
                                <option value="enable"{if ($BYJUNO_CREDIT_CHECK == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($BYJUNO_CREDIT_CHECK == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Enable B2B check:
                        </label>

                        <div class="col-lg-9">
                            <select name="BYJUNO_B2B" id="BYJUNO_B2B">
                                <option value="enable"{if ($BYJUNO_B2B == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($BYJUNO_B2B == 'disable')} selected{/if}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                           Order state after success payment:
                        </label>
                        <div class="col-lg-9">
                            <select name="BYJUNO_SUCCESS_TRIGGER" id="BYJUNO_SUCCESS_TRIGGER">
                                {foreach from=$order_success_status_list item=ostatus}
                                <option value="{$ostatus['id_order_state']}"{if ($ostatus['id_order_state'] == $BYJUNO_SUCCESS_TRIGGER)} selected{/if}>{$ostatus['name']}</option>
                                {/foreach}>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Do not change order status after success payment:
                        </label>
                        <div class="col-lg-9">
                            <select name="BYJUNO_SUCCESS_TRIGGER_NOT_MODIFY[]" id="BYJUNO_SUCCESS_TRIGGER_NOT_MODIFY" multiple="multiple" style="height: 340px">
                                {foreach from=$order_status_list item=ostatus}
                                <option value="{$ostatus['id_order_state']}"{if (in_array($ostatus['id_order_state'], $BYJUNO_SUCCESS_TRIGGER_NOT_MODIFY))} selected{/if}>{$ostatus['name']}</option>
                                {/foreach}>
                            </select><br />
                            Order will not change the status if it will have the following statuses<br />
                            Ctrl + click select multiple
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Settle transactions:
                        </label>

                        <div class="col-lg-9">
                            <select name="BYJUNO_S4_ALLOWED" id="BYJUNO_S4_ALLOWED">
                                <option value="enable"{if ($BYJUNO_S4_ALLOWED == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($BYJUNO_S4_ALLOWED == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Settle trigger order state:
                        </label>
                        <div class="col-lg-9">
                            <select name="BYJUNO_S4_TRIGGER[]" id="BYJUNO_S4_TRIGGER" multiple="multiple" style="height: 340px">
                                {foreach from=$order_status_list item=ostatus}
                                    <option value="{$ostatus['id_order_state']}"{if (in_array($ostatus['id_order_state'], $BYJUNO_S4_TRIGGER))} selected{/if}>{$ostatus['name']}</option>
                                {/foreach}>
                            </select><br />
                            Ctrl + click select multiple
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Cancel Credit transactions:
                        </label>

                        <div class="col-lg-9">
                            <select name="BYJUNO_CANCEL_S5_ALLOWED" id="BYJUNO_CANCEL_S5_ALLOWED">
                                <option value="enable"{if ($BYJUNO_CANCEL_S5_ALLOWED == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($BYJUNO_CANCEL_S5_ALLOWED == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Refund Credit transactions:
                        </label>

                        <div class="col-lg-9">
                            <select name="BYJUNO_REFUND_S5_ALLOWED" id="BYJUNO_REFUND_S5_ALLOWED">
                                <option value="enable"{if ($BYJUNO_REFUND_S5_ALLOWED == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($BYJUNO_REFUND_S5_ALLOWED == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <!--
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Accepted CDP risks if credit check enabled (comma separated):
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="BYJUNO_CDP_ACCEPT" id="BYJUNO_CDP_ACCEPT"
                                   value="{$BYJUNO_CDP_ACCEPT|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Accepted CembraPay Risk for S2 (comma separated):
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="BYJUNO_S2_IJ_ACCEPT" id="BYJUNO_S2_IJ_ACCEPT"
                                   value="{$BYJUNO_S2_IJ_ACCEPT|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Accepted Merchant Risk for S2 (comma separated):
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="BYJUNO_S2_MERCHANT_ACCEPT" id="BYJUNO_S2_MERCHANT_ACCEPT"
                                   value="{$BYJUNO_S2_MERCHANT_ACCEPT|escape}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Accepted statuses for S3 response (comma separated):
                        </label>

                        <div class="col-lg-9">
                            <input type="text" name="BYJUNO_S3_ACCEPT" id="BYJUNO_S3_ACCEPT"
                                   value="{$BYJUNO_S3_ACCEPT|escape}"/>
                        </div>
                    </div>
                    -->
                </div>
                <div class="panel-footer">
                    <input type="hidden" name="submitIntrumMain" value="intrum_main_configuration"/>
                    <button type="submit" value="1" id="module_form_submit_btn" name="btnSubmit"
                            class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> Save
                    </button>
                </div>
            </div>

            <div class="panel" id="fieldset_0">
                <div class="panel-heading">
                    <i class="icon-cogs"></i> CembraPay Invoice payment settings
                </div>
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            CembraPay Invoice (with partial payment option):
                        </label>

                        <div class="col-lg-9">
                            <select name="byjuno_invoice" id="byjuno_invoice">
                                <option value="enable"{if ($byjuno_invoice == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($byjuno_invoice == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            Single invoice:
                        </label>

                        <div class="col-lg-9">
                            <select name="single_invoice" id="single_invoice">
                                <option value="enable"{if ($single_invoice == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($single_invoice == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="hidden" name="submitIntrumMain" value="intrum_main_configuration"/>
                    <button type="submit" value="1" id="module_form_submit_btn" name="btnSubmit"
                            class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> Save
                    </button>
                </div>
            </div>

            <div class="panel" id="fieldset_0">
                <div class="panel-heading">
                    <i class="icon-cogs"></i> CembraPay Installment payment settings
                </div>
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            3 installments:
                        </label>

                        <div class="col-lg-9">
                            <select name="installment_3" id="installment_3">
                                <option value="enable"{if ($installment_3 == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($installment_3 == 'disable')} selected{/if}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            4 installments:
                        </label>

                        <div class="col-lg-9">
                            <select name="installment_4" id="installment_4">
                                <option value="enable"{if ($installment_4 == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($installment_4 == 'disable')} selected{/if}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            6 installments:
                        </label>

                        <div class="col-lg-9">
                            <select name="installment_6" id="installment_6">
                                <option value="enable"{if ($installment_6 == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($installment_6 == 'disable')} selected{/if}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            12 installments:
                        </label>

                        <div class="col-lg-9">
                            <select name="installment_12" id="installment_12">
                                <option value="enable"{if ($installment_12 == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($installment_12 == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            24 installments:
                        </label>

                        <div class="col-lg-9">
                            <select name="installment_24" id="installment_24">
                                <option value="enable"{if ($installment_24 == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($installment_24 == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            36 installments:
                        </label>

                        <div class="col-lg-9">
                            <select name="installment_36" id="installment_36">
                                <option value="enable"{if ($installment_36 == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($installment_36 == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">
                            48 installments:
                        </label>

                        <div class="col-lg-9">
                            <select name="installment_48" id="installment_48">
                                <option value="enable"{if ($installment_48 == 'enable')} selected{/if}>Enable</option>
                                <option value="disable"{if ($installment_48 == 'disable')} selected{/if}>Disable
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="hidden" name="submitIntrumMain" value="intrum_main_configuration"/>
                    <button type="submit" value="1" id="module_form_submit_btn" name="btnSubmit"
                            class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
{/if}