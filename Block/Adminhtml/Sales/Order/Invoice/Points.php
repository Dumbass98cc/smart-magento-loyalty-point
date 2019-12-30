<?php

namespace Loyalty\Point\Block\Adminhtml\Sales\Order\Invoice;

/**
 * Class Points
 * @package Loyalty\Point\Block\Adminhtml\Sales\Order\Invoice
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
     * @return \Loyalty\Point\Block\Adminhtml\Sales\Order\Invoice\Points
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $value = $this->order->getRewardPointsAmount();
        $rewardPoints = new \Magento\Framework\DataObject(
            [
                'code' => 'reward_points',
                'strong' => false,
                'value' => -$value,
                'base_value' => -$value,
                'label' => __('Reward Points'),
            ]
        );
        $parent->addTotal($rewardPoints, 'reward_points');
        return $this;
    }
}