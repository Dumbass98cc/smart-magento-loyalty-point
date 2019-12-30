<?php

namespace Loyalty\Point\Block\Sales\Order;

/**
 * Class Points
 * @package Loyalty\Point\Block\Sales\Order
 */
class Points extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\DataObject
     */
    public $order;

    /**
     * @var \Magento\Framework\DataObject
     */
    public $source;

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $rewardPoints = new \Magento\Framework\DataObject(
            [
                'code' => 'reward_points',
                'strong' => true,
                'value' => -$this->order->getRewardPointsAmount(),
                'label' => __('Reward Points'),
            ]
        );

        $parent->addTotal($rewardPoints, 'reward_points');

        return $this;
    }
}
