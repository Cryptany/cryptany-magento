<?php

class Cryptany_CAPayment_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_code = 'CAPayment';

	function getPaymentGatewayUrl()
	{
		Mage::log('In Helper getpaymentgatewayurl');
		return Mage::getUrl('capayment/payment/gateway', array('_secure' => true));
	}

	function processPayment()
	{
		// Get Eth exchange rate
        $contents = file_get_contents(
            "https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=USD"
        );
        $eth_data = json_decode($contents, true);

		$merchId = Mage::getStoreConfig('payment/CAPayment/merchant_id',
                   Mage::app()->getStore());
		$merchCode = Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/CAPayment/merchant_secret',
                   Mage::app()->getStore()));

		Mage::log($merchId.':'.$merchCode);

		$order = new Mage_Sales_Model_Order();
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order->loadByIncrementId($orderId);

		// check customer shipping address country
		$email = $order->getData()['customer_email'];
		Mage::log(print_r($email,true));
		$srcAmount = number_format(floatval($order->getGrandTotal())/floatval($eth_data[0]['price_usd']),6);

		$postdata = http_build_query(
	    array(
			'email' => $email,
	        'orderId' => $orderId,
	        'srcAmount' => $srcAmount,
			'dstAmount' => floatval($order->getGrandTotal()),
			'url' => Mage::getUrl('capayment/payment/gateway', array('_secure' => true))
	    	)
		);

		Mage::log($postdata);

        $authCode = base64_encode($merchId.":".$merchCode);

		Mage::log($authCode);

		// Generate wallet address
    	$context = stream_context_create(array(
        	'http' => array(
            	'method' => 'POST',
	            'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL .
					'Authorization: Basic '.$authCode. PHP_EOL,
	            'content' => $postdata,
    	    ),
	    ));

	    $result = file_get_contents(
	        $file = "https://cgw.cryptany.io/magento/addr",
	        $use_include_path = false,
	        $context
		);

		if ($result===false) {
			return Mage::getUrl('checkout/onepage/error', array('_secure' => true));
		} else {
			$rArray = json_decode($result, true);
			$order->setAdditionalInformation('capayment',$result);
			$order->save();
			$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
			// set data
			$session->setData("walletAddress", $rArray['address']);
			$session->setData("walletHash", $rArray['walletHash']);
			$session->setData("paymentSum", $srcAmount);
		}
	}

	function getPaymentAddress()
	{
		$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));

		// get data
		return $session->getData("walletAddress");
	}

	function getPaymentSum()
	{
		$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));

		return $session->getData("paymentSum").' ETH';
	}
}