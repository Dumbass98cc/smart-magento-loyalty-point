<?php

namespace Loyalty\Point\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RewardOrderSaveAfter
 * @package Loyalty\Point\Observer
 */
class RewardOrderSaveAfter implements ObserverInterface
{
    /**
     * @var \Loyalty\Point\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * RewardCheckoutSubmitAllAfter constructor.
     *
     * @param \Loyalty\Point\Model\HistoryFactory $historyFactory
     */
    public function __construct(
        \Loyalty\Point\Model\HistoryFactory $historyFactory
    ) {
        $this->historyFactory = $historyFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getData('order');

        if ($order->getState() == "complete") {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $timezoneInterface = $objectManager->create(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);

            $orderDate = $timezoneInterface->date($order->getCreatedAt())->format('Y-m-d');
            $pointsEarn = $order->getPointsEarn();

            if ($pointsEarn > 0) {
                /** @var \Loyalty\Point\Model\History $model */
                $model = $this->historyFactory->create();
                $model->setCustomerId($order->getCustomerId());
                $model->setPoints($pointsEarn);
                $model->setSource("Get by order " . $order->getIncrementId());
                $model->setCreatedAt($orderDate);

                $model->save();
            }
        }
    }
}