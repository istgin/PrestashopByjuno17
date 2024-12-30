<form action="{$link->getModuleLink('byjuno', 'validation', [], true)|escape:'html':'UTF-8'}" method="post" id="byjuno-installment">
    <div class="form-fields">
        {if (count($selected_payments_installment) > 1)}
            <div class="form-group row">
                <label class="col-md-3 form-control-label">
                    {$l_select_payment_plan}
                </label>

                <div class="col-md-9 form-control-comment">
                    {foreach from=$selected_payments_installment item=s_payment}
                        <input type="radio" name="selected_plan"
                               value="{$s_payment.id}" {if $s_payment.selected == 1} checked="checked"{/if}>
                        &nbsp;{$s_payment.name}
                        <br/>
                    {/foreach}
                </div>
            </div>
        {/if}
        {if (count($selected_payments_installment) == 1)}
            <input type="hidden" name="selected_plan" value="{$selected_payments_installment[0].id}">
        {/if}
        {if ($byjuno_allowpostal == 1)}
            <div class="form-group row">
                <label class="col-md-3 form-control-label">
                    {$l_select_invoice_delivery_method}
                </label>

                <div class="col-md-9 form-control-comment">
                    <input type="radio" name="invoice_send" {if $invoice_send == "email"} checked="checked"{/if}
                           value="email"> &nbsp;{$l_by_email}: {$email}<br/>
                    <input type="radio" name="invoice_send" {if $invoice_send == "postal"} checked="checked"{/if}
                           value="postal"> &nbsp;{$l_by_post}: {$address}<br/>
                </div>
            </div>
        {/if}
        {if ($byjuno_gender_birthday_gender == 1)}
            <div class="form-group row">
                <label class="col-md-3 form-control-label">
                    {$l_gender}
                </label>

                <div class="col-md-9">
                    <select name="selected_gender" id="selected_gender" class="form-control">
                        <option value="1" {if $sl_gender == 1} selected="selected"{/if}>{$l_male}</option>
                        <option value="2" {if $sl_gender == 2} selected="selected"{/if}>{$l_female}</option>
                    </select>
                </div>
            </div>
        {/if}
        {if ($byjuno_gender_birthday == 1)}
            <div class="form-group row">
                <label class="col-md-3 form-control-label">
                    {$l_date_of_birth}
                </label>

                <div class="col-md-9">
                    <div class="row">
                        <div class="col-xs-4" style="max-width: 104px;">
                            <select id="days" name="days" class="form-control" style="max-width: 94px;">
                                {foreach from=$days item=day}
                                    <option value="{$day|escape:'html':'UTF-8'}" {if $sl_day == $day} selected="selected"{/if}>{$day|escape:'html':'UTF-8'}
                                        &nbsp;&nbsp;</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-xs-4" style="max-width: 154px;">
                            <select id="months" name="months" class="form-control" style="max-width: 144px;">
                                {foreach from=$months key=k item=month}
                                    <option value="{$k|escape:'html':'UTF-8'}" {if $sl_month == $k} selected="selected"{/if}>{l s=$month}
                                        &nbsp;</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-xs-4" style="max-width: 124px;">
                            <select id="years" name="years" class="form-control" style="max-width: 114px;">
                                {foreach from=$years item=year}
                                    <option value="{$year|escape:'html':'UTF-8'}" {if $sl_year == $year} selected="selected"{/if}>{$year|escape:'html':'UTF-8'}
                                        &nbsp;&nbsp;</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        {if ($cembra_direct_api == 1)}
        <div class="form-group byjuno_toc">
            <input type="checkbox" value="terms_conditions" name="terms_conditions" id="terms_conditions"
                   style="display: inline-block"/> &nbsp;
            {$l_i_agree_with_terms_and_conditions nofilter}
        </div>
        {else}
            <input type="hidden" value="terms_conditions" name="terms_conditions" id="terms_conditions"/> &nbsp;
        {/if}
        <input type="hidden" id="cembra_selected_installment" name="cembra_selected_installment" value="{$select_payment_option}">
    </div>
</form>
<style>
    div.byjuno_toc .checker,
    .checker + label {
        display: inline-block;
        vertical-align: middle;
    }
</style>
<script>
    document.addEventListener('change', function (event) {
        if (event.target.name === 'payment-option') {
            const hiddenTextbox = document.getElementById('cembra_selected_installment');
            hiddenTextbox.value = event.target.id;
        }
    });
</script>