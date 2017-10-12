<?php

class Cryptany_CAPayment_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
 
	protected $_code = 'CAPayment';
 
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = false;
	protected $_canUseForMultishipping  = false;
	protected $_formBlockType = 'CAPayment/form_capayment';
	protected $_infoBlockType = 'CAPayment/info_capayment';
 
	public function assignData($data)
	{
		Mage::log('In Model_payment assignData');
	    $info = $this->getInfoInstance();

	    return $this;
	}

	public function validate()
	{
		Mage::log('In Model_payment validate');
		parent::validate();
		return $this;

		$info = $this->getInfoInstance();
     
    	if ($errorMsg) 
	    {
    		Mage::throwException($errorMsg);
		}
	    return $this;
	}

	/**
	 * Return Order place redirect url
	 *
	 * @return string
	 */
	public function getOrderPlaceRedirectUrl()
	{
		Mage::log('In Model_payment getorderplaceredirecturl');

		//when you click on place order you will be redirected on this url, if you don't want this action remove this method
		return Mage::getUrl('capayment/payment/redirect', array('_secure' => true));
	}
}
