<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

include dirname(__FILE__) . "/fondy.cls.php";

$ORDER_ID = (strlen(CSalePaySystemAction::GetParamValue('ORDER_ID')) > 0)
    ? CSalePaySystemAction::GetParamValue('ORDER_ID')
    : $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['ID'];
$orderID = "Order_" . $ORDER_ID . "_" . time();
$shouldPay = (strlen(CSalePaySystemAction::GetParamValue("SHOULD_PAY", '')) > 0)
    ? CSalePaySystemAction::GetParamValue("SHOULD_PAY", 0)
    : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
$amount = round($shouldPay * 100);
$merchant_id = CSalePaySystemAction::GetParamValue("MERCHANT");
$currency = (strlen(CSalePaySystemAction::GetParamValue('PRICE_CURRENCY')) > 0)
    ? CSalePaySystemAction::GetParamValue('PRICE_CURRENCY')
    : $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['CURRENCY'];
$server_callback_url = CSalePaySystemAction::GetParamValue("SERVER_CALLBACK_URL");
$response_url = CSalePaySystemAction::GetParamValue("RESPONSE_URL");
if(empty($response_url))
    $response_url = $server_callback_url;
$sender_email = $USER->GetEmail();
$f_lang = CSalePaySystemAction::GetParamValue("LANGUAGE");
$preauth = CSalePaySystemAction::GetParamValue("PREAUTH");
$secret_key = CSalePaySystemAction::GetParamValue("SECURE_KEY");
$formFields = array(
    'order_id' => $orderID,
    'merchant_id' => $merchant_id,
    'order_desc' => $ORDER_ID,
    'amount' => $amount,
    'currency' => $currency,
    'server_callback_url' => $server_callback_url,
    'response_url' => $response_url,
    'lang' => $f_lang
);

if (!empty($sender_email)) {
  	$formFields['sender_email'] = $sender_email;
}

if ($preauth == 'Y') {
    $formFields['preauth'] = 'Y';
}
$formFields['signature'] = Fondy::getSignature($formFields, $secret_key);
$url = Fondy::get_fondy_checkout($formFields);
$on_page = CSalePaySystemAction::GetParamValue("ONPAGE");
if ($on_page != 'Y') {
    $out = '<a href="'.$url.'" id="fondy_payment_form" style="margin: 10px;padding: 15px 50px;border: 0;background-color: #62ba46;color: #fff;border-radius: 7px;font-size: 18px;text-decoration: none;">' . Loc::getMessage("SALE_HANDLERS_PAY_SYSTEM_FONDY_BUTTON_PAID") . '</a>';
    if (strpos($_SERVER['REQUEST_URI'], 'make') !== false) {
        $out .= "<script> setTimeout(function() {
			document.getElementById('fondy_payment_form').click();
			}, 200);
			</script>";
    }
} else {
    $out = "
	<script type='text/javascript' src='https://api.fondy.eu/static_common/v1/checkout/ipsp.js'></script>
	<script>
		var checkoutStyles = {
		    'html , body' : {
		        'overflow' : 'hidden'
		    },'.col.col-shoplogo' : {
		        'display' : 'none'
		    },
		    '.col.col-language' : {
		        'display' : 'none'
		    },
		    '.pages-checkout' : {
		        'background' : 'transparent'
		    },
		    '.col.col-login' : {
		        'display' : 'none'
		    },
		    '.pages-checkout .page-section-overview' : {
		        'background' : '#fff',
		        'color' : '#252525',
		        'border-bottom' : '1px solid #dfdfdf'
		    },
		    '.col.col-value.order-content' : {
		        'color' : '#252525'
		    },
		    '.page-section-footer' : {
		        'display' : 'none'
		    },
		    '.page-section-tabs' : {
		        'display' : 'none'
		    },
		    
		    '.page-section-shopinfo' : {
		        'display': 'none'
		    },
		    
		    '.page-section-overview' : {
		        'display': 'none'
		    },
		}
		</script>";
    $out .= '
		<div style="min-height:350px" id="checkout">
		<div style="min-width:400px;min-height:450px" id="checkout_wrapper"></div>
		</div>
		<script>
		function checkoutInit(url) {
		    $ipsp("checkout").scope(function() {
		    this.setCheckoutWrapper("#checkout_wrapper");
		    this.addCallback(__DEFAULTCALLBACK__);
		    this.setCssStyle(checkoutStyles);
		    this.action("show", function(data) {
		    $("#checkout_loader").remove();
		    $("#checkout").show();
		    });
		    this.action("hide", function(data) {
		        $("#checkout").hide();
		    });
		    this.action("resize", function(data) {
		        $("#checkout_wrapper").height(data.height);
		    });
		        this.loadUrl(url);
		    });
		}
		checkoutInit("' . $url . '");
		</script>';
}
echo $out;