<?php

namespace Loyalty\Point\Model\Order\Invoice\Total;

/**
 * Class Points
 * @package Loyalty\Point\Model\Order\Invoice\Total
 */
class Points extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $points = $invoice->getOrder()->getRewardPointsAmount();
        $invoice->setGrandTotal($invoice->getGrandTotal() - $points);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $points);

        return $this;
    }
}