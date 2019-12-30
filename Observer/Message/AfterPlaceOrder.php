<?php

namespace Loyalty\Point\Observer\Message;

/**
 * Class AfterPlaceOrder
 * @package Loyalty\Point\Observer\Message
 */
class AfterPlaceOrder extends AbstractMessage
{
    /**
     * @param $points
     */
    public function showMessage($points)
    {
        if ($this->getAfterPlaceOrderMessage($points)) {
            $this->messageManager->addNoticeMessage($this->getAfterPlaceOrderMessage($points));
        }
    }
}