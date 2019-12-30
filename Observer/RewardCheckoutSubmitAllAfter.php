<?php

namespace Loyalty\Point\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class RewardCheckoutSubmitAllAfter
 * @package Loyalty\Point\Observer
 */
class RewardCheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var \Loyalty\Point\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * @var \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var \Loyalty\Point\Helper\Data
     */
    private $helper;

    /**
     * @var Message\AfterPlaceOrder
     */
    private $messageManager;

    /**
     * RewardCheckoutSubmitAllAfter constructor.
     *
     * @param \Loyalty\Point\Model\HistoryFactory $historyFactory
     * @param \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Loyalty\Point\Helper\Data $helper
     * @param Message\AfterPlaceOrder $messageManager
     */
    public function __construct(
        \Loyalty\Point\Model\HistoryFactory $historyFactory,
        \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Loyalty\Point\Helper\Data $helper,
        \Loyalty\Point\Observer\Message\AfterPlaceOrder $messageManager
    ) {
        $this->historyFactory = $historyFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Loyalty\Point\Observer\RewardCheckoutSubmitAllAfter|void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getData('order');
        /** @var \Magento\Quote\Api\Data\CartInterface $quote */
        $quote = $observer->getData('quote');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $timezoneInterface = $objectManager->create(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);
        $quoteDate = $timezoneInterface->date($quote->getUpdatedAt())->format('Y-m-d');

        if ($quote->getCustomerIsGuest()) {
            return $this;
        }

        $rewardAmount = $quote->getRewardPointsAmount();
        if ($rewardAmount > 0) {
            /** @var \Loyalty\Point\Model\History $model */
            $model = $this->historyFactory->create();
            $model->setCustomerId($quote->getCustomer()->getId());
            $model->setPoints(-$rewardAmount);
            $model->setSource("Apply for order " . $order->getIncrementId());
            $model->setCreatedAt($quoteDate);

            $model->save();
        }

//        //apply rules
//        $groupId = $quote->getCustomer()->getGroupId();
//        $ruleCollection = $this->ruleCollectionFactory->create()->addCustomerGroupFilter($groupId);
//
//        $points = 0;
//        /** @var \Loyalty\Point\Model\Rule $rule */
//        foreach ($ruleCollection as $rule) {
//            if ($rule->getStatus() && $rule->getFromDate() && $rule->getToDate() &&
//                !($this->helper->getCustomerPoints($quote->getCustomer()->getId()) + $rewardAmount < $rule->getMinimumPoint())) {
//                if ($rule->getFromDate() <= $quoteDate && $rule->getToDate() >= $quoteDate)
//                switch ($rule->getType()) {
//                    case 1:
//                        $points += $rule->getPointToBeEarned();
//                        break;
//                    case 2:
//                        $points += floor($quote->getGrandTotal() / $rule->getConversionRate());
//                        break;
//                    case 3:
//                        $points += floor($quote->getGrandTotal() / $rule->getPriceStep())
//                            * $rule->getPointToBeEarned();
//                        break;
//                    default:
//                        $points = 0;
//                }
//            }
//        }
//
//        if ($points > 0) {
//            $this->messageManager->showMessage($points);
//
//            /** @var \Loyalty\Point\Model\History $model */
//            $model = $this->historyFactory->create();
//            $model->setCustomerId($quote->getCustomer()->getId());
//            $model->setPoints($points);
//            $model->setSource("Get by order " . $order->getIncrementId());
//            $model->setCreatedAt($quoteDate);
//
//            $model->save();
//        }

        return $this;
    }
}