<?php

namespace Loyalty\Point\Model\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

/**
 * Class Points
 * @package Loyalty\Point\Model\Quote\Total
 */
class Points extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @param Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return \Loyalty\Point\Model\Quote\Total\Points|bool
     */
    public function collect(
        Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();

        if (!$quote->isVirtual()) {
            if ($address->getAddressType() != 'shipping') {
                return $this;
            }
        }

        $discount = $quote->getRewardPointsAmount();

        $total->addTotalAmount('reward_points', -$discount);
        $total->addBaseTotalAmount('reward_points', -$discount);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        $amount = $quote->getRewardPointsAmount();

        return [
            'code' => 'reward_points',
            'title' => __('Reward Points'),
            'value' => -$amount
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Point applied');
    }
}