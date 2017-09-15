<?php
ini_set("display_errors", true);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] !== "POST") die();
if (!require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php")) die('prolog_before.php not found!');

if (CModule::IncludeModule('sale')) {
    if (empty($_POST)) {
        $callback = json_decode(file_get_contents("php://input"));
        $_POST = array();
        foreach ($callback as $key => $val) {
            $_POST[$key] = $val;
        }
    }
    $ordArray = explode("_", $_POST['order_id']);

    $ORDER_ID = $ordArray['1'];

    $User_ID = $ordArray['2'];

    $arOrder = CSaleOrder::GetByID($ORDER_ID);

    $payID = $arOrder['PAY_SYSTEM_ID'];

    $temp = CSalePaySystemAction::GetList(array(), array("PAY_SYSTEM_ID" => $payID));
    $payData = $temp->Fetch();

    include $_SERVER['DOCUMENT_ROOT'] . $payData['ACTION_FILE'] . "/fondy.cls.php";

    $fondyOpt = array();
    $b = unserialize($payData['PARAMS']);
    foreach ($b as $k => $v) $fondyOpt[$k] = $v['VALUE'];

    $fondy = new Fondy();
    $fondyResult = $fondy->isPaymentValid($fondyOpt, $_POST);

    if ($_POST['order_status'] != Fondy::ORDER_APPROVED) {
        $answer = 'declined';
    } elseif ($fondyResult == true) {
        $answer = 'OK';
		CSaleOrder::PayOrder($arOrder['ID'], 'Y');
    } else {
        $answer = $fondyResult;
    }
    
    if ($arOrder) {
        $arFields = array(
            "STATUS_ID" => $answer == 'OK' ? "P" : "N",
            "PAYED" => $answer == 'OK' ? "Y" : "N",
            "PS_STATUS" => $answer == 'OK' ? "Y" : "N",
            "PS_STATUS_CODE" => $_POST['order_status'],
            "PS_STATUS_DESCRIPTION" => $_POST['order_status'] . " " . $payID . " " .
                ($answer != 'OK' ? $_POST['response_description'] : ''),
            "PS_STATUS_MESSAGE" => " - ",
            "PS_SUM" => $_POST['amount'],
            "PS_CURRENCY" => $_POST['currency'],
            "PS_RESPONSE_DATE" => date("d.m.Y H:i:s"),
        );
    }
    CSaleOrder::Update($ORDER_ID, $arFields);
    echo $answer . "<script>window.location.replace('/personal/order/');</script>";
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");