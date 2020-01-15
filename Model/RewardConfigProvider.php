<?php

namespace Loyalty\Point\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\DB\Select;

/**
 * Class RewardConfigProvider
 * @package Loyalty\Point\Model
 */
class RewardConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var \Loyalty\Point\Model\ResourceModel\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var \Loyalty\Point\Helper\Data
     */
    private $pointsHelper;

    /**
     * @var null
     */
    private $points = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $_date;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Loyalty\Point\Helper\Data $pointsHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Loyalty\Point\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Loyalty\Point\Helper\Data $pointsHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;

        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->pointsHelper = $pointsHelper;
        $this->_date = $date;
    }

    /**
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfig()
    {
        $selectedPoints = $this->checkoutSession->getQuote()->getRewardPointsAmount();
        $availablePoints = $this->getAvailablePoints();
        $maxPoints = $this->getMaxPoints();
        $earnPoints = $this->getEarnPoints();

        $maxPoints = $availablePoints < $maxPoints ? $availablePoints : $maxPoints;
        $selectedPoints = $selectedPoints ? $selectedPoints : 0;

        $output['reward'] = [
            'selected_points' => $selectedPoints,
            'available_points' => $availablePoints,
            'earn_points' => $earnPoints,
            'max_points' => $maxPoints,
            'is_visible' => $this->isVisible()
        ];

        return $output;
    }

    /**
     * @return int|mixed|null
     */
    private function getAvailablePoints()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->points = 0;
            return $this->points;
        }

        if ($this->points == null) {
            $this->points = $this->pointsHelper->getCustomerPoints($this->customerSession->getId());
        }
        return $this->points;
    }

    /**
     * @return float|int
     */
    private function getMaxPoints()
    {
        return $this->checkoutSession->getQuote()->getSubtotal();
    }

    /**
     * @return bool
     */
    private function isVisible()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getEarnPoints()
    {
        $groupId = $this->customerSession->getCustomerGroupId();
        $ruleCollection = $this->ruleCollectionFactory->create()->addCustomerGroupFilter($groupId);
        $quoteDate = $this->_date->formatDate();

//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $timezoneInterface = $objectManager->create(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);
//        $quoteDate = $timezoneInterface->date($this->checkoutSession->getQuote()->getUpdatedAt())->format('Y-m-d');

        $points = 0;
        /** @var \Loyalty\Point\Model\Rule $rule */
        foreach ($ruleCollection as $rule) {
            if ($rule->getStatus() && $rule->getFromDate() && $rule->getToDate()
                && $rule->getMinimumPoint() <= $this->getAvailablePoints()) {
                if ($rule->getFromDate() <= $quoteDate && $rule->getToDate() >= $quoteDate)
                switch ($rule->getType()) {
                    case 0:
                        $points += $rule->getPointToBeEarned();
                        break;
                    case 1:
                        $points += floor($this->checkoutSession->getQuote()->getGrandTotal() / $rule->getConversionRate());
                        break;
                    case 2:
                        $points += floor($this->checkoutSession->getQuote()->getGrandTotal() / $rule->getPriceStep())
                            * $rule->getPointToBeEarned();
                        break;
                    default:
                        $points = 0;
                }
            }
        }

        if ($points > 0) {
            $this->checkoutSession->getQuote()->setPointsEarn($points)->save();
        }

        return $points;
    }
}