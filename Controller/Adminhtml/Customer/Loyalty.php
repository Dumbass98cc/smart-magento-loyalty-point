<?php

namespace Loyalty\Point\Controller\Adminhtml\Customer;

/**
 * Class Loyalty
 * @package Loyalty\Point\Controller\Adminhtml\Customer
 */
class Loyalty extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        return $this->resultLayoutFactory->create();
    }
}