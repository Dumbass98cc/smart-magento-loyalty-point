<?php

namespace Loyalty\Point\Model;

use \Loyalty\Point\Api\PointsManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class PointsManagement implements PointsManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Loyalty\Point\Model\ResourceModel\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var \Loyalty\Point\Helper\Data
     */
    private $pointsHelper;

    /**
     * PointsManagement constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Loyalty\Point\Helper\Data $pointsHelper
     * @param ResourceModel\History\CollectionFactory $historyCollectionFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Loyalty\Point\Helper\Data $pointsHelper,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->pointsHelper = $pointsHelper;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * @param int $cartId
     * @param string $rewardPoints
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function set($cartId, $rewardPoints)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $amount = $this->_validate($rewardPoints, $quote);
            $quote->setRewardPointsAmount($amount);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply reward points'));
        }

        return true;
    }

    /**
     * @param $points
     * @param $quote
     * @return int
     * @throws \Exception
     */
    private function _validate($points, $quote)
    {
        if ($points > $this->getAvailablePoints($quote)) {
            throw new \Exception(__('Could not apply reward points'));
        }
        $subtotal = $quote->getSubtotal();

        if ($points > $subtotal) {
            throw new \Exception(__('Could not apply reward points'));
        }

        return $points;
    }

    /**
     * @param $quote
     * @return mixed
     */
    private function getAvailablePoints($quote)
    {
        return $this->pointsHelper->getCustomerPoints($quote->getCustomerId());
    }
}