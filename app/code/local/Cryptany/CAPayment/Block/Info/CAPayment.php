<?php
/**
 * Block for showing payment method in summary form
 * PHP Version 7
 *
 * @category Blocks
 * @package  Cryptany\CAPayment\Block\Info
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */

/**
 * Class represent Cryptany payment gateway in summary checkout form block
 *
 * @category Blocks
 * @package  Cryptany\CAPayment\Block\Info
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */
class Cryptany_CAPayment_Block_Info_CAPayment extends Mage_Payment_Block_Info
{
    /**
     * Constructor for the class, performs simple template redirection
     *
     * @param array $transport transport data to use
     * 
     * @method _prepareSpecificInformation
     * 
     * @return array transport data
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }

        $data = array();

        $transport = parent::_prepareSpecificInformation($transport);

        return $transport->setData(array_merge($data, $transport->getData()));
    }
}