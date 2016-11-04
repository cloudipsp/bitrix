<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
	include dirname(__FILE__) . "/fondy.cls.php";
	 
	if ( isset($arResult['ORDER_ID']) ) {
		$ORDER_ID = $arResult['ORDER_ID'];
	}
	else {
		$ORDER_ID = $_GET['ORDER_ID'];
	}
	
	
	#--------------------------------------------
	$ORDER_ID = filter_var($ORDER_ID, FILTER_SANITIZE_NUMBER_INT);
	$arOrder = CSaleOrder::GetByID($ORDER_ID);
	$orderID = "Order_".$ORDER_ID."_".CSaleBasket::GetBasketUserID()."_". md5( "Order_".time() );
	$shouldPay = (strlen(CSalePaySystemAction::GetParamValue("SHOULD_PAY", '')) > 0) ? CSalePaySystemAction::GetParamValue("SHOULD_PAY", 0) : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
	if (CSalePaySystemAction::GetParamValue("ONPAGE")!='Y'){
		$amount = round($shouldPay*100);	
		}else{
		$amount = 	$shouldPay;
	}
	$formFields = array('order_id' => $orderID,
    'merchant_id' => CSalePaySystemAction::GetParamValue("MERCHANT"),
    'order_desc' => $orderID,
    'amount' => $amount,
    'currency' => CSalePaySystemAction::GetParamValue("PRICE_CURRENCY"),
    'server_callback_url' => CSalePaySystemAction::GetParamValue("SERVER_CALLBACK_URL"),
    'response_url' => CSalePaySystemAction::GetParamValue("RESPONSE_URL"),
    'lang' => CSalePaySystemAction::GetParamValue("LANGUAGE"),
    'sender_email' => $USER->GetEmail());
	$formFields['signature'] = Fondy::getSignature($formFields, CSalePaySystemAction::GetParamValue("SECURE_KEY"));
	$fondyArgsArray = array();
	foreach ($formFields as $key => $value) {
		$fondyArgsArray[] = "<input type='hidden' name='$key' value='$value'/>";
	}
	if (CSalePaySystemAction::GetParamValue("ONPAGE")!='Y'){
		$out =  '	<form action="' . Fondy::URL . '" method="post" id="fondy_payment_form">
		' . implode('', $fondyArgsArray) .
		'</form>' .
		"<div><img src='https://fondy.com/img/loader.gif' width='50px' style='margin:20px 20px;'></div>".
		"<script> setTimeout(function() {
        document.getElementById('fondy_payment_form').submit();
		}, 100);
		</script>";
	}
	else{
		$out =	"<script>
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
		$out .=  '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="https://api.fondy.eu/static_common/v1/checkout/ipsp.js"></script>
		<div id="checkout">
		<div id="checkout_wrapper" style="width:600px;"></div>
		</div>
		<script>
		function checkoutInit(url, val) {
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
		this.width(val);
		this.action("resize", function(data) {
		$("#checkout_wrapper").width(val).height(data.height);
		});
		this.action("resize", function(data) {
		$("#checkout_wrapper").height(data.height);
		});
		this.loadUrl(url);
		});
		};
		var button = $ipsp.get("button");
		button.setMerchantId('.$formFields['merchant_id'].');
		button.setAmount('.$formFields['amount'].', "'.$formFields['currency'].'", true);
		button.setHost("api.fondy.eu");
		button.addParam("order_desc","'.$formFields['order_desc'].'");
		button.addParam("order_id","'.$formFields['order_id'].'");
		button.addParam("lang","'.$formFields['lang'].'");//button.addParam("delayed","N");
		button.addParam("server_callback_url","'.$formFields['server_callback_url'].'");
		button.addParam("sender_email","'.$formFields['sender_email'].'");
		button.setResponseUrl("'.$formFields['response_url'].'");
		checkoutInit(button.getUrl());
		</script>';	
	}
	echo $out;	
