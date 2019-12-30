<?php

namespace Loyalty\Point\Controller\Adminhtml\Promo\Reward;

use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class NewAction
 * @package Loyalty\Point\Controller\Adminhtml\Promo\Reward
 */
class NewAction extends \Loyalty\Point\Controller\Adminhtml\Promo\Reward implements HttpGetActionInterface
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}