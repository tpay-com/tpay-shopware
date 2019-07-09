{extends file="frontend/checkout/confirm.tpl"}
{namespace name="frontend/tpay/payment/banks"}

{block name='frontend_index_content'}
    <div class="tpay-confirm">
        {if !$basketEmpty}
            <div class="sw-tpay-form" data-url="{url controller=TpayPayment action=changePaymentMethod}">
                {$tpayForm nofilter}
            </div>
            <div class="tpay-buttons" id="tpay-payment-submit">
                <button data-url="{url controller=TpayPayment action={$paymentType}}" class="btn is--primary is--disabled tpay-select" disabled>{s name='TpayNextButton'}Dalej{/s}</button>
                <a href="{url controller=TpayPayment action={$paymentType}}" class="btn is--primary tpay-select is--hidden">{s name='TpayNextButton'}Dalej{/s}</a>
            </div>
        {else}
            <div class="basket--info-messages">
                {include file="frontend/_includes/messages.tpl" type="warning" content="{s name='CartInfoEmpty'}Sie haben keine Artikel im Warenkorb.{/s}"}
            </div>
        {/if}
    </div>
{/block}
