{extends file="parent:frontend/checkout/change_payment.tpl"}
{* Method Name *}
{block name='frontend_checkout_payment_fieldset_input_label'}
    {if $payment_mean.isTpay}
        <div class="method--label method--label--tpay is--first">
            <label class="method--name is--strong" for="payment_mean{$payment_mean.id}">
                {include file="string:{$payment_mean.additionaldescription}"}
            </label>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{* Method Description *}
{block name='frontend_checkout_payment_fieldset_description'}
    {if $payment_mean.isTpay}
        <div class="method--description method--description--tpay is--last">
        </div>
    {else}
        {$smarty.block.parent}
    {/if}

{/block}


{* Method Logo *}
{block name='frontend_checkout_payment_fieldset_template'}
    <div class="payment--method-logo payment_logo_{$payment_mean.name}"></div>
    {if "frontend/plugins/payment/`$payment_mean.template`"|template_exists}
        <div class="method--bankdata{if ($payment_mean.id != $form_data.payment) && !$payment_mean.tPayDetail.bankTransfer } is--hidden{/if}">
            {include file="frontend/plugins/payment/`$payment_mean.template`" form_data=$sFormData error_flags=$sErrorFlag payment_means=$sPayments}
        </div>
    {/if}
{/block}