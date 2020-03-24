<?php
	   
	class Fondy
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
		
		public static function isPaymentValid($fondySettings, $response)
		{
			
			if ($fondySettings['MERCHANT'] != $response['merchant_id']) {
				return 'An error has occurred during payment. Merchant data is incorrect.';
			}
			
			$responseSignature = $response['signature'];
			if (isset($response['response_signature_string'])){
				unset($response['response_signature_string']);
			}
			if (isset($response['signature'])){
				unset($response['signature']);
			}
			if (Fondy::getSignature($response, $fondySettings['SECURE_KEY']) != $responseSignature) {
				
				return 'An error has occurred during payment. Signature is not valid.';
			}
			
			return true;
		}
		public static function get_fondy_checkout($args)
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
		
	}
