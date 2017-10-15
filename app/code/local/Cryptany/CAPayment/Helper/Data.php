<?php
/**
 * Payment helper
 * PHP Version 7
 *
 * @category Helper
 * @package  Cryptany\CAPayment\Helper
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */

/**
 * Class represent Cryptany payment gateway helper
 *
 * @category Helper
 * @package  Cryptany\CAPayment\Helper
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */
class Cryptany_CAPayment_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Payment system code
     *
     * @var _code
     */    
    protected $_code = 'CAPayment';

    /**
     * Processing webhook for 'payment received' gateway action
     *
     * @method getPaymentGatewayUrl
     * 
     * @return nothing
     */
    function getPaymentGatewayUrl()
    {
        return Mage::getUrl('capayment/payment/gateway', array('_secure' => true));
    }

    /**
     * Processing actual payment request and register it at gateway
     *
     * @method processPayment
     * 
     * @return nothing
     */    
    function processPayment()
    {
        // Get Eth exchange rate
        $contents = file_get_contents(
            "https://cgw.cryptany.io/data/rate"
        );
        $eth_data = json_decode($contents, true);

        $merchId = Mage::getStoreConfig(
            'payment/CAPayment/merchant_id',
            Mage::app()->getStore()
        );

        $merchCode = Mage::helper('core')->decrypt(
            Mage::getStoreConfig(
                'payment/CAPayment/merchant_secret',
                Mage::app()->getStore()
            )
        );

        $order = new Mage_Sales_Model_Order();
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order->loadByIncrementId($orderId);

        // check customer shipping address country
        $email = $order->getData()['customer_email'];
        $srcAmount = number_format(
            floatval($order->getGrandTotal())/floatval($eth_data['rate']), 6
        );

        // create parameters to deliver to gateway
        $postdata = http_build_query(
            array(
                'email' => $email,
                'orderId' => $orderId,
                'srcAmount' => $srcAmount,
                'dstAmount' => floatval($order->getGrandTotal()),
                'url' => Mage::getUrl(
                    'capayment/payment/gateway', array('_secure' => true)
                )
            )
        );

        $authCode = base64_encode($merchId.":".$merchCode);

        // Generate wallet address:
        // create POST context to send data request
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . 
                        PHP_EOL . 'Authorization: Basic '.$authCode. PHP_EOL,
                    'content' => $postdata,
                ),
            )
        );

        // send actual request to register transaction
        $result = file_get_contents(
            $file = "https://cgw.cryptany.io/magento/addr",
            $use_include_path = false,
            $context
        );

        if ($result===false) { // process gateway call error
            Mage::log('Cryptany payment: Error requesting transaction details');
            return Mage::getUrl('checkout/onepage/error', array('_secure' => true));
        } else { // process success result
            $rArray = json_decode($result, true);
            $order->setAdditionalInformation('capayment', $result);
            $order->save();
            $session = Mage::getSingleton(
                "core/session",  
                array("name"=>"frontend")
            );
            // set data
            $session->setData("walletAddress", $rArray['address']);
            $session->setData("walletHash", $rArray['walletHash']);
            $session->setData("paymentSum", $srcAmount);
        }
    }

    /**
     * Processing webhook for 'payment received' gateway action
     *
     * @method getPaymentAddress
     * 
     * @return string walletAddress to pay to
     */
    function getPaymentAddress()
    {
        $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));

        return $session->getData("walletAddress");
    }

    /**
     * Processing webhook for 'payment received' gateway action
     *
     * @method getPaymentSum
     * 
     * @return string sum to pay via Ethereum blockchain
     */
    function getPaymentSum()
    {
        $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));

        return $session->getData("paymentSum").' ETH';
    }
}