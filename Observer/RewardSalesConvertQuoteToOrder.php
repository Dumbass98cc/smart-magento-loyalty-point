<?php

namespace Loyalty\Point\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RewardSalesConvertQuoteToOrder
 * @package Loyalty\Point\Observer
 */
class RewardSalesConvertQuoteToOrder implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getData('order');
        $quote = $observer->getData('quote');

        $order->setRewardPointsAmount($quote->getRewardPointsAmount());
        $order->setPointsEarn($quote->getPointsEarn());
    }
}