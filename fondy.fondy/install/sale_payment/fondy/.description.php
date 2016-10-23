<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
	include(GetLangFileName(dirname(__FILE__) . "/", "/.description.php"));
	
	
	$psTitle = "Fondy";
	$psDescription = "<a href=\"https://fondy.eu\" target=\"_blank\">https://fondy.eu</a>";
	
	$array = array(
	'fondy_onpage',
    'fondy_merchant',
    'fondy_secret_key',
    'fondy_price_currency',
    'fondy_server_callback_url',
    'fondy_response_url',
    'fondy_language'
	);
	
	
	$arPSCorrespondence = array(
	"ONPAGE" => array(
	"NAME" => GetMessage("FONDY_ONPAGE"),
	'SORT' => 900,
	"INPUT" => array(
	'TYPE' => 'Y/N'
	)
	),
    "MERCHANT" => array(
	"NAME" => GetMessage("FONDY_MERCHANT"),
	"DESCR" => GetMessage("FONDY_MERCHANT"),
	"VALUE" => "",
	"TYPE" => ""
    ),
    "SECURE_KEY" => array(
	"NAME" => GetMessage("FONDY_SECURE_KEY"),
	"DESCR" => GetMessage("FONDY_SECURE_KEY"),
	"VALUE" => "",
	"TYPE" => ""
    ),
	
    "SERVER_CALLBACK_URL" => array(
	"NAME" => GetMessage("FONDY_SERVER_CALLBACK_URL"),
	"DESCR" => GetMessage("FONDY_DESC_SERVER_CALLBACK_URL"),
	"VALUE" => "",
	"TYPE" => ""
    ),
    "RESPONSE_URL" => array(
	"NAME" => GetMessage("FONDY_RESPONSE_URL"),
	"DESCR" => GetMessage("FONDY_DESC_RESPONSE_URL"),
	"VALUE" => "",
	"TYPE" => ""
    ),
    "LANGUAGE" => array(
	"NAME" => GetMessage("FONDY_LANGUAGE"),
	"DESCR" => GetMessage("FONDY_DESC_LANGUAGE"),
	"VALUE" => "RU",
	"TYPE" => ""
    ),
	"PRICE_CURRENCY" => array(
	"NAME" => GetMessage("FONDY_PRICE_CURRENCY"),
	"DESCR" => GetMessage("FONDY_DESC_PRICE_CURRENCY"),
	"VALUE" => "CURRENCY",
	"TYPE" => "ORDER"
	),
	);
?>