<?php

namespace Loyalty\Point\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class ChangePoint
 * @package Loyalty\Point\Controller\Adminhtml\Customer
 */
class ChangePoint extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Loyalty\Point\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * ChangePoint constructor.
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Loyalty\Point\Model\HistoryFactory $historyFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Loyalty\Point\Model\HistoryFactory $historyFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerRepository = $customerRepository;
        $this->historyFactory = $historyFactory;
        $this->date = $date;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $this->recordHistory($data['customer_id'], $data['point']);
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param int $customerId
     * @param int $point
     * @throws \Exception
     */
    private function recordHistory($customerId, $point)
    {
        $loyaltyPointHistory = $this->historyFactory->create();
        $data = [
            'customer_id' => $customerId,
            'points' => $point,
            'source' => "Point modified by admin",
            'created_at' => $this->date->gmtDate()
        ];

        $loyaltyPointHistory->setData($data);
        $loyaltyPointHistory->save();
    }
}