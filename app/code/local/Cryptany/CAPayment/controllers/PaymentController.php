<?php
/**
 * Payment controller, performs actions handling for the gateway
 * PHP Version 7
 *
 * @category Controllers
 * @package  Cryptany\CAPayment\Controllers
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */

/**
 * Class represent Cryptany payment gateway in summary checkout form block
 *
 * @category Controllers
 * @package  Cryptany\CAPayment\Controllers
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */
class Cryptany_CAPayment_PaymentController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Processing webhook for 'payment received' gateway action
     *
     * @method gatewayAction
     * 
     * @return nothing
     */
    public function gatewayAction() 
    {
        if (null!==$this->getRequest()->get("orderId")
            && $this->getRequest()->get("status")=='confirmed'
        ) {
            $orderId = $this->getRequest()->get("orderId");
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $order->setState(
                Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW,
                true,
                'Payment Success.'
            );
            $order->save();
        }
    }

    /**
     * Processing webhook for 'order placed' gateway action
     *
     * @method redirectAction
     * 
     * @return nothing
     */
    public function redirectAction() 
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'payment',
            array('template' => 'capayment/redirect.phtml')
        );
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    /**
     * Processing webhook for 'payment completed' gateway action
     *
     * @method responseAction
     * 
     * @return nothing
     */
    public function responseAction() 
    {
        if ($this->getRequest()->get("flag") == "1" 
            && $this->getRequest()->get("orderId")
        ) {
            $orderId = $this->getRequest()->get("orderId");
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $order->setState(
                Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, 
                true, 
                'Payment Success.'
            );
            $order->save();

            Mage::getSingleton('checkout/session')->unsQuoteId();
            Mage_Core_Controller_Varien_Action::_redirect(
                'checkout/onepage/success',
                array('_secure'=> true)
            );
        } else {
            Mage_Core_Controller_Varien_Action::_redirect(
                'checkout/onepage/error',
                array('_secure'=> true)
            );
        }
    }
}