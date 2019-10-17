<div class="tpay">
    <div class="tpay__title">
        {s name="bankListTitle"}Wybierz swój bank:{/s}
    </div>
    <div class="tpay__list" data-tpay-bank="true" data-url="{url controller='TpayBankSelection' action='index'}" data-disable="{s name="bankMustBeSelected"}Proszę wybrać bank{/s}" data-error="{s name="changeError"}Wystąpił błąd. Proszę spróbować ponownie lub wybrać inną metodę płatności.{/s}">
        {foreach from=$payment_mean.tPayBankList item=bank}
            <div class="tpay__list-item tpay__list-item-{$bank.id} {if $bank.id == $tpayBank.id}tpay__list-item--active{/if}" data-bank-id="{$bank.id}" data-bank-name="{$bank.name}" data-bank-img="{$bank.img}">
                <img class="tpay__list-img" src="{$bank.img}" alt="{$bank.name}"/>
            </div>
        {/foreach}
    </div>
</div>



