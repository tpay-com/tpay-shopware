{extends file="parent:frontend/checkout/finish.tpl"}

{block name='frontend_checkout_finish_dispatch_method'}
    {$smarty.block.parent}
    {if $orderStatus}
        <br><strong>{s name="OrderPaymentStatusValue" namespace="frontend/plugins/tpay"}{/s}</strong> {$orderStatus}
    {/if}
{/block}

{block name='frontend_checkout_finish_teaser_actions'}
    {$smarty.block.parent}
    {if $tpayError}
        {s name="TpayPaymentFailed" assign="snippetTpayPaymentFailed" namespace="frontend/plugins/tpay"}{/s}
        {include file="frontend/_includes/messages.tpl" type="error" content=$snippetTpayPaymentFailed}
    {/if}
{/block}
