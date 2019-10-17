{extends file="parent:frontend/checkout/finish.tpl"}

{block name='frontend_checkout_finish_dispatch_method'}
    {$smarty.block.parent}
    {if $orderStatus}
        <strong>{s name="OrderPaymentStatusValue"}Status płatności:{/s}</strong> {$orderStatus}
    {/if}
{/block}

{block name='frontend_checkout_finish_teaser_actions'}
    {$smarty.block.parent}
    {if $tpayError}
        {s name="TpayPaymentFailed" assign="snippetTpayPaymentFailed"}Wystąpił błąd podczas przetwarzania twojej płatności. Skontaktuj się z obsługą sklepu.{/s}
        {include file="frontend/_includes/messages.tpl" type="error" content=$snippetTpayPaymentFailed}
    {/if}
{/block}
