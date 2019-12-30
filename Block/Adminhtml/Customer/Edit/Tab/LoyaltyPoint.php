<?php

namespace Loyalty\Point\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class LoyaltyPoint
 * @package Loyalty\Point\Block\Adminhtml\Customer\Edit\Tab
 */
class LoyaltyPoint extends \Magento\Framework\View\Element\Template implements TabInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Loyalty constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('Loyalty Point');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('Loyalty Point');
    }

    /**
     * @inheritDoc
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTabUrl()
    {
        return $this->getUrl('loyalty/customer/loyalty', ['_current' => true]);
    }

    /**
     * @inheritDoc
     */
    public function isAjaxLoaded()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canShowTab()
    {
        return $this->getCustomerId() ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function isHidden()
    {
        return $this->getCustomerId() ? false : true;
    }
}