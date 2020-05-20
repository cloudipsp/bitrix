<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>

    <div class="sale-paysystem-wrapper">
	<span class="tablebodytext">
		<?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_FONDY_DESCRIPTION') . " " . SaleFormatCurrency($params['SHOULD_PAY'], $params['currency']); ?>
	</span>
        <form id="fondy_payment_form" name="form" action="https://api.fondy.eu/api/checkout/redirect/" method="post">

            <input name="order_id" value="<?= $params['order_id']; ?>" type="hidden">
            <input name="merchant_id" value="<?= $params['merchant_id']; ?>" type="hidden">
            <input name="order_desc" value="<?= htmlspecialcharsbx($params['order_desc']); ?>" type="hidden">
            <input name="amount" value="<?= $params['amount'] ?>" type="hidden">
            <input name="currency" value="<?= htmlspecialcharsbx($params['currency']); ?>" type="hidden">
            <input name="server_callback_url" value="<?= htmlspecialcharsbx($params['server_callback_url']) ?>"
                   type="hidden">
            <input name="preauth" value="<?= htmlspecialcharsbx($params['preauth']) ?>" type="hidden">
            <input name="signature" value="<?= htmlspecialcharsbx($params['signature']) ?>" type="hidden">
            <input name="response_url" value="<?= htmlspecialcharsbx($params['response_url']) ?>" type="hidden">
            <input name="lang" value="<?= htmlspecialcharsbx($params['lang']) ?>" type="hidden">
            <input name="sender_email" value="<?= htmlspecialcharsbx($params['sender_email']) ?>" type="hidden">
        </form>
        <div class="sale-paysystem-fondy-button-container">
			<span class="sale-paysystem-fondy-button">
				<button style="margin: 10px;padding: 15px 50px;border: 0;background-color: #62ba46;color: #fff;border-radius: 7px;font-size: 18px;" class="btn btn-default fondy" type="submit" form="fondy_payment_form"><?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_FONDY_BUTTON_PAID') ?></button>
			</span>
            <span
                class="sale-paysystem-fondy-button-descrition"><?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_FONDY_REDIRECT_MESS'); ?></span>
        </div>
        <p>
            <span
                class="tablebodytext sale-paysystem-description"><?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_FONDY_WARNING_RETURN'); ?></span>
        </p>

    </div>
<?php if(strpos($_SERVER['REQUEST_URI'], 'make') !== false) {
    echo "<script> setTimeout(function() {
            document.getElementById('fondy_payment_form').submit();
        }, 200);
    </script>";
}?>