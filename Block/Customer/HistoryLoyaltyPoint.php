<?php

namespace Loyalty\Point\Block\Customer;

use Magento\Framework\DB\Select;

/**
 * Class HistoryLoyaltyPoint
 * @package Loyalty\Point\Block\Customer
 */
class HistoryLoyaltyPoint extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Loyalty\Point\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Loyalty\Point\Model\ResourceModel\History\CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * HistoryLoyaltyPoint constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Loyalty\Point\Model\HistoryFactory $historyFactory
     * @param \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Loyalty\Point\Model\HistoryFactory $historyFactory,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->historyFactory = $historyFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getPointHistory()
    {
        $customerSession = $this->_objectManager->create(\Magento\Customer\Model\Session::class);
        $id = $customerSession->getId();
        $model = $this->historyFactory->create();

        $pointHistory = $model->getCollection();
        $pointHistory->addFieldToFilter('customer_id', ['eq' => "$id"]);
        return $pointHistory->getData();
    }

    /**
     * @return int
     */
    public function getPointBalance()
    {
        $customerSession = $this->_objectManager->create(\Magento\Customer\Model\Session::class);
        $id = $customerSession->getId();

        /** @var \Loyalty\Point\Model\ResourceModel\History\Collection $collection */
        $collection = $this->historyCollectionFactory->create();
        $collection->getSelect()->reset(Select::COLUMNS)
            ->columns(['total' => new \Zend_Db_Expr('SUM(points)')])->group('customer_id');
        $collection->addFieldToFilter('customer_id', ['eq' => $id]);
        $collection->load();
        $item = $collection->fetchItem();
        return $item !== false ? $item->getData('total') : 0;
    }
}