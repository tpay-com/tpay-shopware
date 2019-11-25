<div class="tpay">
    <div class="tpay__title">
        {s name="BankListTitle" namespace="frontend/plugins/tpay"}{/s}
    </div>
    <div
            class="tpay__list {if $payment_mean.id == $form_data.payment}tpay__list__selected{/if}"
            data-tpay-bank="true"
            data-url="{url controller='TpayBankSelection' action='index'}"
            data-disable="{s name="BankMustBeSelected" namespace="frontend/plugins/tpay"}{/s}"
            data-error="{s name="TpayChangeError" namespace="frontend/plugins/tpay"}{/s}">
        {foreach from=$payment_mean.tPayBankList item=bank}
            <label class="tpay__list-item tpay__list-item-{$bank.id} {if $bank.id == $tpayBank.id}tpay__list-item--active{else}{if ($payment_mean.id == $form_data.payment) && $tpayBank.id}tpay__list-item--busy{/if}{/if}" data-bank-id="{$bank.id}" data-bank-name="{$bank.name}" data-bank-img="{$bank.img}" data-payment-selector="payment_mean{$payment_mean.id}">
                <input type="radio" name="tPayBank" value="{$bank.id}" form="shippingPaymentForm"/>
                <img class="tpay__list-img" src="{$bank.img}" alt="{$bank.name}"/>
            </label>
        {/foreach}
    </div>
</div>
