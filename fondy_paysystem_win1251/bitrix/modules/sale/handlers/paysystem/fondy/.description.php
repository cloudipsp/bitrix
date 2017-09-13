<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$data = array(
    'NAME' => "Fondy",
    'SORT' => 500,
    'DOMAIN' => 'BOX',
    'CODES' => array(
        "PREAUTH" => array(
            "NAME" => Loc::getMessage("FONDY_PREAUTH"),
            'SORT' => 900,
            "INPUT" => array(
                'TYPE' => 'Y/N'
            )
        ),
        "MERCHANT" => array(
            "NAME" => Loc::getMessage("FONDY_MERCHANT"),
            "DESCR" => Loc::getMessage("FONDY_MERCHANT"),
            "VALUE" => "",
            "TYPE" => ""
        ),
        "SECURE_KEY" => array(
            "NAME" => Loc::getMessage("FONDY_SECURE_KEY"),
            "DESCR" => Loc::getMessage("FONDY_SECURE_KEY"),
            "VALUE" => "",
            "TYPE" => ""
        ),
        "PAYMENT_ID" => array(
            "NAME" => Loc::getMessage("FONDY_ORDER_ID"),
            'SORT' => 500,
            'GROUP' => 'PAYMENT',
            "DEFAULT" => array(
                "PROVIDER_VALUE" => "ID",
                "PROVIDER_KEY" => "PAYMENT"
            )
        ),
        "BUYER_PERSON_EMAIL" => array(
            "NAME" => Loc::getMessage("FONDY_CLIENT_EMAIL"),
            'SORT' => 800,
            'GROUP' => 'BUYER_PERSON',
            "DEFAULT" => array(
                "PROVIDER_VALUE" => "PERSONAL_MAILBOX",
                "PROVIDER_KEY" => "USER"
            )
        ),
        'SHOULD_PAY' => array(
            'NAME' => Loc::getMessage('FONDY_AMOUNT'),
            'DESCR' => '',
            'VALUE' => 'SHOULD_PAY',
            'GROUP' => 'PAYMENT',
            "DEFAULT" => array(
                "PROVIDER_VALUE" => "SUM",
                "PROVIDER_KEY" => "PAYMENT"
            )
        ),
        "SERVER_CALLBACK_URL" => array(
            "NAME" => Loc::getMessage("FONDY_SERVER_CALLBACK_URL"),
            "DESCR" => Loc::getMessage("FONDY_DESC_SERVER_CALLBACK_URL"),
            "VALUE" => "",
            "TYPE" => "",
            "DEFAULT" => array(
                "PROVIDER_VALUE" => 'https://'.$_SERVER['HTTP_HOST'].'/bitrix/tools/fondy_result/fondy_result.php',
                "PROVIDER_KEY" => "VALUE"
            )
        ),
        "RESPONSE_URL" => array(
            "NAME" => Loc::getMessage("FONDY_RESPONSE_URL"),
            "DESCR" => Loc::getMessage("FONDY_DESC_RESPONSE_URL"),
            "VALUE" => "",
            "TYPE" => "",
            "DEFAULT" => array(
                "PROVIDER_VALUE" => 'https://'.$_SERVER['HTTP_HOST'].'/bitrix/tools/fondy_result/fondy_result.php',
                "PROVIDER_KEY" => "VALUE"
            )
        ),
        "LANGUAGE" => array(
            "NAME" => Loc::getMessage("FONDY_LANGUAGE"),
            "DESCR" => Loc::getMessage("FONDY_DESC_LANGUAGE"),
            "VALUE" => "RU",
            "TYPE" => ""
        ),
        "PRICE_CURRENCY" => array(
            "NAME" => Loc::getMessage("FONDY_PRICE_CURRENCY"),
            "DESCR" => Loc::getMessage("FONDY_DESC_PRICE_CURRENCY"),
            "VALUE" => "CURRENCY",
            'GROUP' => 'PAYMENT',
            "DEFAULT" => array(
                "PROVIDER_VALUE" => "CURRENCY",
                "PROVIDER_KEY" => "PAYMENT"
            )
        ),
    ));
?>