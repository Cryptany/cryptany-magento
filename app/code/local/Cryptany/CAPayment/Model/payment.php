<?php
/**
 * Payment model
 * PHP Version 7
 *
 * @category Model
 * @package  Cryptany\CAPayment\Model
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */

/**
 * Class represent Cryptany payment gateway model
 *
 * @category Model
 * @package  Cryptany\CAPayment\Model
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */
class Cryptany_CAPayment_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment module code
     *
     * @var _code
     */
    protected $_code = 'CAPayment';
 
    /**
     * Override variable to get initialized
     *
     * @var _isInitializeNeeded
     */
    protected $_isInitializeNeeded = true;

    /**
     * Override variable to not use interanal
     *
     * @var _isInitializeNeeded
     */
    protected $_canUseInternal = false;

    /**
     * Override variable to not allow multishipping
     *
     * @var _isInitializeNeeded
     */
    protected $_canUseForMultishipping = false;

    /**
     * Override variable for payment form block type
     *
     * @var _isInitializeNeeded
     */
    protected $_formBlockType = 'CAPayment/form_capayment';

    /**
     * Override variable for payment form summary page
     *
     * @var _isInitializeNeeded
     */
    protected $_infoBlockType = 'CAPayment/info_capayment';
 
    /**
     * Assign payment data to module
     *
     * @param array $data data to assign to module
     * 
     * @method assignData
     * 
     * @return Object closure
     */
    public function assignData($data)
    {
        $info = $this->getInfoInstance();

        return $this;
    }

    /**
     * Validate data entered by customer (if needed)
     *
     * @method validate
     * 
     * @return Object closure
     */
    public function validate()
    {
        parent::validate();
        return $this;

        $info = $this->getInfoInstance();
     
        if ($errorMsg) {
            Mage::throwException($errorMsg);
        }
        return $this;
    }

    /**
     * Return Order place redirect url
     *
     * @return string URL to pass customer to
     */
    public function getOrderPlaceRedirectUrl()
    {
        //when you click on place order you will be redirected on this url
        return Mage::getUrl('capayment/payment/redirect', array('_secure' => true));
    }
}
