<?php

namespace Loyalty\Point\Api;

/**
 * Interface PointsManagementInterface
 * @package Loyalty\Point\Api
 */
interface PointsManagementInterface
{
    /**
     * Adds a reward points to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $rewardPoints The reward points data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function set($cartId, $rewardPoints);
}