<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;

Loc::loadMessages(__FILE__);

/**
 * Class FondyHandler
 * @package Sale\Handlers\PaySystem
 */
class FondyHandler extends PaySystem\BaseServiceHandler implements PaySystem\IRefundExtended
{
    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        global $USER;
        $busValues = $this->getParamsBusValue($payment);
        $shouldPay = (strlen($busValues['SHOULD_PAY']) > 0) ? $busValues['SHOULD_PAY'] : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
        $amount = round($shouldPay*100);
        $email = $busValues['BUYER_PERSON_EMAIL'];
        if (empty($email))
            $email = $USER->GetEmail();
        $orderID = "Order_" . $busValues['PAYMENT_ID'] . "_" . time();
        $params = array(
            'order_id' => $orderID,
            'merchant_id' => $busValues['MERCHANT'],
            'order_desc' => $busValues['PAYMENT_ID'],
            'amount' => $amount,
            'currency' => $busValues['PRICE_CURRENCY'],
            'server_callback_url' => $busValues['SERVER_CALLBACK_URL'],
            'response_url' => $busValues['RESPONSE_URL'],
            'lang' => $busValues['LANGUAGE'],
            'sender_email' => $email
        );
        if ($busValues["PREAUTH"] == 'Y') {
            $params['preauth'] = 'Y';
        } else {
            $params['preauth'] = 'N';
        }

        $params['signature'] = $this->getSignature($params, $busValues['SECURE_KEY']);

        $this->setExtraParams($params);

        return $this->showTemplate($payment, "template");
    }

    /**
     * @return array
     */
    public static function getIndicativeFields()
    {
        return array('BX_HANDLER' => 'FONDY');
    }
    /**
     * @param Payment $payment
     * @param int $refundableSum
     * @return PaySystem\ServiceResult
     */
    public function refund(Payment $payment, $refundableSum)
    {
        $busValues = $this->getParamsBusValue($payment);
        $url = 'https://api.fondy.eu/api/reverse/order_id';
        $error = '';
        $result = new PaySystem\ServiceResult();
        $requestDT = date('c');
		$in_id = $payment->getField('PS_INVOICE_ID');
        if (empty($in_id)){
            $error = "error invoice id is empty";
		}
        if ($busValues["PREAUTH"] != 'Y'){
            $error = Loc::getMessage('FONDY_REFUND_ERROR_HOLD_DSBLD');
		}

        $request = array(
            "order_id" => $in_id,
            "currency" => $payment->getField('PS_CURRENCY'),
            "amount" => round($payment->getField('SUM')*100),
            "merchant_id" => $busValues['MERCHANT']
        );
        $request['signature'] = $this->getSignature($request, $busValues['SECURE_KEY']);
        if ($error == '') {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json')
            );
            curl_setopt($ch, CURLOPT_USERAGENT, "1C-Bitrix");
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('request' => $request)));
            $content = json_decode(curl_exec($ch), TRUE);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
        }

        if ($content !== false ) {
            if ($content['response']['reverse_status'] == 'approved' and !$content['response']['error_message'])
                $result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);
            elseif($error == '')
                $error .= Loc::getMessage('FONDY_REFUND_ERROR') . ' ' . Loc::getMessage('FONDY_REFUND_ERROR_INFO', array('#STATUS#' => $content['response']['response_status'],'#REQ_ID#' => $content['response']['request_id'], '#ERROR#' => $content['response']['error_message']));
        } else {
            $error .= Loc::getMessage('FONDY_REFUND_CONNECTION_ERROR', array('#URL#' => $url, '#ERROR#' => $curlError ? $curlError : $content['response']['error_message'], '#CODE#' => $httpCode));
        }


        if ($error !== '') {
            $result->addError(new Error($error));
            PaySystem\ErrorLog::add(array(
                'ACTION' => 'returnPaymentRequest',
                'MESSAGE' => join("\n", $result->getErrorMessages())
            ));
        }

        return $result;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        return $request->get('orderNumber');
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        return array('RUB');
    }

    /**
     * @return bool
     */
    public function isRefundableExtended()
    {
        return true;
    }

    public function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function ($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $str = $password;
        foreach ($data as $k => $v) {
            $str .= '|' . $v;
        }

        if ($encoded) {
            return sha1($str);
        } else {
            return $str;
        }
    }

    /**
     * @param array $paySystemList
     * @return array
     */
    public static function findMyDataRefundablePage(array $paySystemList)
    {
        $result = array();
        $personTypeList = BusinessValue::getPersonTypes();
        $handler = PaySystem\Manager::getFolderFromClassName(get_called_class());
        $description = PaySystem\Manager::getHandlerDescription($handler);

        foreach ($paySystemList as $data) {
            foreach ($personTypeList as $personType) {
                $shopId = BusinessValue::get('FONDY_SHOP_ID', PaySystem\Service::PAY_SYSTEM_PREFIX . $data['ID'], $personType['ID']);
                if ($shopId && !isset($result[$shopId])) {

                    $result[$shopId] = array(
                        'EXTERNAL_ID' => $shopId,
                        'NAME' => $description['NAME'],
                        'HANDLER' => 'fondy',
                        'LINK_PARAMS' => 'shop_id=' . $shopId,
                        'CONFIGURED' => 'Y'
                    );
                }
            }
        }

        return $result;
    }
}