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
                         src="{link file="custom/plugins/TpayShopwarePayments/Resources/views/_public/img/blik.png"}"
                         alt="Blik">
                </div>
                <div class="blikmodal__text">
                    <p class="blikmodal__text-confirm">{s name="blikConfirmBank"}Potwierdź płatność w alikacji mobilnej swojego Banku{/s}</p>
                    <p class="blikmodal__text-wait is--hidden">{s name="blikWaiting"}Oczekiwanie na odpowiedź Banku{/s}</p>
                    <div class="blikmodal__text-error is--hidden">
                        <p>{s name="blikError"}Wystąpił błąd. Proszę spróbować ponownie lub wybrać inną metodę płatności.{/s}</p>
                        <button class="btn is--primary">{s name="blikErrorClouse"}Zamknij{/s}</button>
                    </div>
                    <p class="blikmodal__text-success is--hidden">{s name="blikSuccess"}Sukces! Poczekaj na przekierowanie.{/s}</p>
                </div>
            </div>
        </div>
        <div class="blik" data-blik="true" data-url="{url controller='TpayPaymentBlik' action='ajax'}" data-check="{url controller='TpayPaymentBlik' action='check'}">
            <div class="blik__text">
                <label for="blik" class="blik__text-label">{s name="typeBlikLabel"}Wprowadź kod Blik:{/s}</label>
                <img class="blik__text-img"
                     src="{link file="custom/plugins/TpayShopwarePayments/Resources/views/_public/img/blik.png"}"
                     alt="Blik">
            </div>
            <input id="blik" type="text" class="blik__input"
                   placeholder="{s name="typeBlikPlaceholder"}Wpisz 6-cyfrowy kod Blik{/s}">
            <button type="submit" class="btn is--primary is--large right is--icon-right blik__button"
                    form="confirm--form" data-preloader-button="true">
                {s name='ConfirmActionSubmit'}{/s}<i class="icon--arrow-right"></i>
            </button>
        </div>
    {elseif $sPayment.isTpayBankTransfer && $tpayBank.status === false}
        {s name="TpayBankInvalid" assign="snippet"}Wybrany przez Ciebie bank jest aktualnie niedostępny. Proszę wybrać inny bank lub inną metodę płatności.{/s}
        {include file="frontend/_includes/messages.tpl" type="error" content=$snippet icon="icon--cc-nc"}
        <a href="{url controller="checkout" action="shippingPayment" sTarget="checkout"}" class="btn is--primary is--small btn--change-payment">
            {s name="TpayBankInvalidButton"}Wybierz{/s}
        </a>
    {else}
        {$smarty.block.parent}
    {/if}

{/block}

{* Terms of service *}
{block name='frontend_checkout_confirm_agb'}
    {if $sPayment.isTpayBlik || $sPayment.isTpayBankTransfer}
        <li class="block-group row--tos">
            <span class="block column--checkbox">
                <input type="checkbox" required="required" aria-required="true"
                       id="tpayTOS" name="tpayTOS" value="1" data-invalid-tos-jump="true"/>
            </span>

            <span class="block column--label">
                <label for="tpayTOS" class="tpay__tos--label">{s name="ConfirmTpayTerms"}Akceptuję <a href="https://secure.tpay.com/regulamin.pdf" target="_blank">Regulamin</a> serwisu Tpay{/s}</label>
            </span>
        </li>
    {/if}
    {$smarty.block.parent}
{/block}
{* https://secure.tpay.com/regulamin.pdf *}
