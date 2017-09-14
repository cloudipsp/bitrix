<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include dirname(__FILE__) . "/fondy.cls.php";


global $APPLICATION;
$APPLICATION->AddHeadScript('https://api.fondy.eu/static_common/v1/checkout/ipsp.js');
CJSCore::Init(array("jquery"));


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
    'lang' => $f_lang,
    'sender_email' => $sender_email);


if ($preauth == 'Y') {
    $formFields['preauth'] = 'Y';
}
$formFields['signature'] = Fondy::getSignature($formFields, $secret_key);
$fondyArgsArray = array();
foreach ($formFields as $key => $value) {
    $fondyArgsArray[] = "<input type='hidden' name='$key' value='$value'/>";
}
$on_page = CSalePaySystemAction::GetParamValue("ONPAGE");
if ($on_page != 'Y') {
    $out = '<form action="' . Fondy::URL . '" method="post" id="fondy_payment_form">
		' . implode('', $fondyArgsArray) .
        '</form><button style="margin: 10px" class="btn btn-default fondy" type="submit" form="fondy_payment_form">' . GetMessage('SALE_HANDLERS_PAY_SYSTEM_FONDY_BUTTON_PAID') . '</button>' .
        "";
    if (strpos($_SERVER['REQUEST_URI'], 'make') !== false) {
        $out .= "<script> setTimeout(function() {
			document.getElementById('fondy_payment_form').submit();
			}, 100);
			</script>";
    }
} else {
    $url = get_checkout($formFields);
    $out = "<script>
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
		<div style="min-width:400px;min-height:350px" id="checkout_wrapper"></div>
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
		};
		checkoutInit("' . $url . '");
		</script>';
}
echo $out;
function get_checkout($args)
{
    if (is_callable('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.fondy.eu/api/checkout/url/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('request' => $args)));

        $result = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode != 200) {
            echo "Return code is {$httpCode} \n"
                . curl_error($ch);
            exit;
        }
        if ($result->response->response_status == 'failure') {
            echo $result->response->error_message;
            exit;
        }
        $url = $result->response->checkout_url;
        return $url;
    } else {
        echo "Curl not found!";
        die;
    }
}
