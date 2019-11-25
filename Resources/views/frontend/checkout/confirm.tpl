{extends file="parent:frontend/checkout/confirm.tpl"}

{block name='frontend_checkout_confirm_left_payment_method'}
    {if $sPayment.isTpayBankTransfer}
        <p class="payment--method-info">
            <strong class="payment--title">{s name="ConfirmInfoPaymentMethod" namespace="frontend/checkout/confirm"}{/s}</strong>
        </p>
        {if !$sUserData.additional.payment.esdactive && {config name="showEsd"}}
            <p class="payment--confirm-esd">{s name="ConfirmInfoInstantDownload" namespace="frontend/checkout/confirm"}{/s}</p>
        {/if}
        <div class="payment--method-tpay">
            <img class="payment--method-tpay--img" src="{$tpayBank.img}" alt="{$tpayBank.name}"/>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}


{block name='frontend_checkout_confirm_information_wrapper'}
    {$smarty.block.parent}
    {if $sPayment.isTpayBankTransfer && $tpayBank.status === true}
        <input type="hidden" name="bank" value="{$tpayBank.id}">
    {/if}
{/block}

{block name='frontend_checkout_confirm_submit'}
    {if $sPayment.isTpayBlik}
        <div id="blikModal" class="is--hidden">
            <div class="blikmodal">
                <div class="blikmodal__loader">
                    <div class="blikmodal__loader-animation"></div>
                    <img class="blikmodal__loader-img"
                         src="https://secure.tpay.com/_/g/150.png"
                         alt="Blik">
                </div>
                <div class="blikmodal__text">
                    <p class="blikmodal__text-confirm">{s name="BlikConfirmBank" namespace="frontend/plugins/tpay"}{/s}</p>
                    <p class="blikmodal__text-wait is--hidden">{s name="BlikWaiting" namespace="frontend/plugins/tpay"}{/s}</p>
                    <div class="blikmodal__text-error is--hidden">
                        <p>{s name="BlikError" namespace="frontend/plugins/tpay"}{/s}</p>
                        <button class="btn is--primary">{s name="BlikErrorClose" namespace="frontend/plugins/tpay"}{/s}</button>
                    </div>
                    <p class="blikmodal__text-success is--hidden">{s name="BlikSuccess" namespace="frontend/plugins/tpay"}{/s}</p>
                </div>
            </div>
        </div>
        <div class="blik" data-blik="true" data-url="{url controller='TpayPaymentBlik' action='ajax'}" data-check="{url controller='TpayPaymentBlik' action='check'}">
            <div class="blik__text">
                <label for="blik" class="blik__text-label">{s name="BlikLabel" namespace="frontend/plugins/tpay"}{/s}</label>
                <img class="blik__text-img"
                     src="https://secure.tpay.com/_/g/150.png"
                     alt="Blik">
            </div>
            <input id="blik" type="text" class="blik__input"
                   placeholder="{s name="BlikPlaceholder" namespace="frontend/plugins/tpay"}{/s}">
            <button type="submit" class="btn is--primary is--large right is--icon-right blik__button"
                    form="confirm--form" data-preloader-button="true">
                {s name='ConfirmActionSubmit'}{/s}<i class="icon--arrow-right"></i>
            </button>
        </div>
    {elseif $sPayment.isTpayBankTransfer && $tpayBank.status === false}
        {s name="TpayBankInvalid" namespace="frontend/plugins/tpay" assign="snippet"}{/s}
        {include file="frontend/_includes/messages.tpl" type="error" content=$snippet icon="icon--cc-nc"}
        <a href="{url controller="checkout" action="shippingPayment" sTarget="checkout"}" class="btn is--primary is--small btn--change-payment">
            {s name="TpayBankInvalidButton" namespace="frontend/plugins/tpay"}{/s}
        </a>
    {else}
        {$smarty.block.parent}
    {/if}

{/block}

{* Terms of service *}
{block name='frontend_checkout_confirm_agb'}
    {if $sPayment.isTpayBlik || $sPayment.isTpayCard || $sPayment.isTpayBankTransfer}
        {block name='frontend_checkout_confirm_agb_tpay'}
        <li class="block-group row--tos">
            <span class="block column--checkbox">
                <input type="checkbox" required="required" aria-required="true"
                       id="tpayTOS" name="tpayTOS" value="1" data-invalid-tos-jump="true" {if $sPayment.isTpayCard || $sPayment.isTpayBankTransfer}form="confirm--form"{/if}/>
            </span>

            <span class="block column--label">
                <label for="tpayTOS" class="tpay__tos--label">{s name="ConfirmTpayTerms" namespace="frontend/plugins/tpay"}{/s}</label>
            </span>
        </li>
        {/block}
    {/if}
    {$smarty.block.parent}
{/block}
