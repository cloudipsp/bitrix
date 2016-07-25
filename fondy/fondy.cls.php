<?php

class Oplata
{
    const ORDER_APPROVED = 'approved';
    const ORDER_DECLINED = 'declined';

    const ORDER_SEPARATOR = '#';

    const SIGNATURE_SEPARATOR = '|';

    const URL = "https://api.fondy.eu/api/checkout/redirect/";


    public static function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $str = $password;
        foreach ($data as $k => $v) {
            $str .= self::SIGNATURE_SEPARATOR . $v;
        }

        if ($encoded) {
            return sha1($str);
        } else {
            return $str;
        }
    }

    public static function isPaymentValid($oplataSettings, $response)
    {
		
        if ($oplataSettings['MERCHANT'] != $response['merchant_id']) {
            return 'An error has occurred during payment. Merchant data is incorrect.';
        }
		//print_r ($oplataSettings); die;
		$responseSignature = $response['signature'];
		if (isset($response['response_signature_string'])){
        unset($response['response_signature_string']);
		}
		if (isset($response['signature'])){
		unset($response['signature']);
		}
		if (Oplata::getSignature($response, $oplataSettings['SECURE_KEY']) != $responseSignature) {
			
            return 'An error has occurred during payment. Signature is not valid.';
        }

        return true;
    }


}
