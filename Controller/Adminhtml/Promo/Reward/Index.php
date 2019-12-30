<?php

namespace Loyalty\Point\Controller\Adminhtml\Promo\Reward;

use Loyalty\Point\Controller\Adminhtml\Promo\Reward;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Index
 * @package Loyalty\Point\Controller\Adminhtml\Promo\Reward
 */
class Index extends Reward implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Loyalty'), __('Loyalty'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Loyalty Reward Rule'));
        $this->_view->renderLayout();
    }
}