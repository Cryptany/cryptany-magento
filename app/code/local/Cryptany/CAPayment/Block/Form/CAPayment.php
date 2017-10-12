<?php
/**
 * Block for showing payment method in checkout form
 * PHP Version 7
 *
 * @category Blocks
 * @package  Cryptany\CAPayment\Block
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */

/**
 * Class represent Cryptany payment gateway checkout form block
 *
 * @category Blocks
 * @package  Cryptany\CAPayment\Block
 * @author   Eugene Rupakov <eugene.rupakov@gmail.com>
 * @license  Apache Common License 2.0
 * @link     http://cryptany.io
 */
class Cryptany_CAPayment_Block_Form_CAPayment extends Mage_Payment_Block_Form
{
    /**
     * Constructor for the class, performs simple template redirection
     *
     * @method _construct
     * @return nothing
     */
    protected function _construct()
    {
        parent::_construct();
        // simply order to use our template file
        $this->setTemplate('capayment/form/payment.phtml');
    }
}