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
     * @var \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $_date;

    /**
     * PointsManagement constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Loyalty\Point\Helper\Data $pointsHelper
     * @param ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Loyalty\Point\Helper\Data $pointsHelper,
        \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->pointsHelper = $pointsHelper;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->_date = $date;
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

            $quote->setPointsEarn($this->getEarnPoints($quote));
            $this->quoteRepository->save($quote);
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

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getEarnPoints(\Magento\Quote\Model\Quote $quote)
    {
        $groupId = $quote->getCustomerGroupId();
        $ruleCollection = $this->ruleCollectionFactory->create()->addCustomerGroupFilter($groupId);
        $quoteDate = $this->_date->formatDate('');

//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $timezoneInterface = $objectManager->create(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);
//        $quoteDate = $timezoneInterface->date($quote->getUpdatedAt())->format('Y-m-d');

        $points = 0;
        /** @var \Loyalty\Point\Model\Rule $rule */
        foreach ($ruleCollection as $rule) {
            if ($rule->getStatus() && $rule->getFromDate() && $rule->getToDate()
                && $rule->getMinimumPoint() <= $this->getAvailablePoints($quote)) {
                if ($rule->getFromDate() <= $quoteDate && $rule->getToDate() >= $quoteDate)
                    switch ($rule->getType()) {
                        case 0:
                            $points += $rule->getPointToBeEarned();
                            break;
                        case 1:
                            $points += floor($quote->getGrandTotal() / $rule->getConversionRate());
                            break;
                        case 2:
                            $points += floor($quote->getGrandTotal() / $rule->getPriceStep())
                                * $rule->getPointToBeEarned();
                            break;
                        default:
                            $points = 0;
                    }
            }
        }

        return $points;
    }
}