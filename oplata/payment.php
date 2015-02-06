<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include dirname(__FILE__) . "/oplata.cls.php";

if ( isset($arResult['ORDER_ID']) ) {
    $ORDER_ID = $arResult['ORDER_ID'];
}
  else {
    $ORDER_ID = $_GET['ORDER_ID'];
}


#--------------------------------------------
$ORDER_ID = filter_var($ORDER_ID, FILTER_SANITIZE_NUMBER_INT);
$arOrder = CSaleOrder::GetByID($ORDER_ID);

$orderID = "OplataOrder_".$ORDER_ID."_".CSaleBasket::GetBasketUserID()."_". md5( "oplataOrder_".time() );

$formFields = array('order_id' => $orderID,
    'merchant_id' => CSalePaySystemAction::GetParamValue("MERCHANT"),
    'order_desc' => $orderID,
    'amount' => Oplata::getAmount($arOrder),
    'currency' => CSalePaySystemAction::GetParamValue("PRICE_CURRENCY"),
    'server_callback_url' => CSalePaySystemAction::GetParamValue("SERVER_CALLBACK_URL"),
    'response_url' => CSalePaySystemAction::GetParamValue("RESPONSE_URL"),
    'lang' => CSalePaySystemAction::GetParamValue("LANGUAGE"),
    'sender_email' => $USER->GetEmail());

$formFields['signature'] = Oplata::getSignature($formFields, CSalePaySystemAction::GetParamValue("SECURE_KEY"));


$oplataArgsArray = array();
foreach ($formFields as $key => $value) {
    $oplataArgsArray[] = "<input type='hidden' name='$key' value='$value'/>";
}

echo '	<form action="' . Oplata::URL . '" method="post" id="oplata_payment_form">
  				' . implode('', $oplataArgsArray) .
    '</form>' .
    "<div><img src='https://oplata.com/img/loader.gif' width='50px' style='margin:20px 20px;'></div>".
    "<script> setTimeout(function() {
        document.getElementById('oplata_payment_form').submit();
     }, 100);
    </script>";
