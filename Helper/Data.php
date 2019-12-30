<?php

namespace Loyalty\Point\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DB\Select;

/**
 * Class Data
 * @package Loyalty\Point\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Loyalty\Point\Model\ResourceModel\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     */
    public function __construct(
        Context $context,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param $customerId
     * @return float
     */
    public function getCustomerPoints($customerId)
    {
        if (!$customerId) {
            return 0;
        }
        $collection = $this->historyCollectionFactory->create();
        $collection->getSelect()->reset(Select::COLUMNS)
            ->columns(['total' => new \Zend_Db_Expr('SUM(points)')])->group('customer_id');
        $collection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $collection->load();
        $item = $collection->fetchItem();
        if ($item) {
            return $item->getData('total');
        }
        return 0;
    }
}
