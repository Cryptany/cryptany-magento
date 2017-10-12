<?php

class Cryptany_CAPayment_Block_Form_CAPayment extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
	Mage::log('In Block_Form constructor');
    parent::_construct();
    $this->setTemplate('capayment/form/payment.phtml');
  }
}