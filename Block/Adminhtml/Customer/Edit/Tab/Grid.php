<?php


namespace Loyalty\Point\Block\Adminhtml\Customer\Edit\Tab;


use Magento\Framework\DB\Select;

/**
 * Class Grid
 * @package Loyalty\Point\Block\Adminhtml\Customer\Edit\Tab
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Loyalty\Point\Model\ResourceModel\History\CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var \Loyalty\Point\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Loyalty\Point\Model\HistoryFactory $historyFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Loyalty\Point\Model\HistoryFactory $historyFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->customerRepository = $customerRepository;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->historyFactory = $historyFactory;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * Get customer's point balance
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPointBalance()
    {
        $id = $this->getCustomerId();
        /** @var \Loyalty\Point\Model\ResourceModel\History\Collection $collection */
        $collection = $this->historyCollectionFactory->create();
        $collection->getSelect()->reset(Select::COLUMNS)
            ->columns(['total' => new \Zend_Db_Expr('SUM(points)')])->group('customer_id');
        $collection->addFieldToFilter('customer_id', ['eq' => $id]);
        $collection->load();
        $item = $collection->fetchItem();
        return $item !== false ? $item->getData('total') : 0;
    }

    /**
     * Get customer's point history
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getPointHistory()
    {
        $id = $this->getCustomerId();
        $model = $this->historyFactory->create();

        return $model->getCollection()->addFieldToFilter('customer_id', ['eq' => "$id"]);
    }
}