<?php

class Cryptany_CAPayment_Block_Info_CAPayment extends Mage_Payment_Block_Info
{
  protected function _prepareSpecificInformation($transport = null)
  {
	Mage::log('In Block_info prepare info');

    if (null !== $this->_paymentSpecificInformation) 
    {
      return $this->_paymentSpecificInformation;
    }
     
    $data = array();
    if ($this->getInfo()->getCustomFieldOne()) 
    {
      $data[Mage::helper('capayment')->__('Custom Field One')] = $this->getInfo()->getCustomFieldOne();
    }
     
    if ($this->getInfo()->getCustomFieldTwo()) 
    {
      $data[Mage::helper('capayment')->__('Custom Field Two')] = $this->getInfo()->getCustomFieldTwo();
    }
 
    $transport = parent::_prepareSpecificInformation($transport);
     
    return $transport->setData(array_merge($data, $transport->getData()));
  }
}