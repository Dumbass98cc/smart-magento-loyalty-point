<?php

namespace Loyalty\Point\Block\Cart;

class Points extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Loyalty\Point\Model\ResourceModel\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var null
     */
    private $points = null;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $pricingHelper;

    /**
     * @var \Loyalty\Point\Helper\Data
     */
    private $pointsHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Loyalty\Point\Helper\Data $pointsHelper
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Loyalty\Point\Helper\Data $pointsHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->pricingHelper = $pricingHelper;
        $this->pointsHelper = $pointsHelper;
    }

    /**
     * Return points from customer account
     *
     * @return int|null
     */
    public function getCustomerPoints()
    {
        if ($this->points == null) {
            $this->points = $this->pointsHelper->getCustomerPoints($this->_customerSession->getCustomerId());
        }
        return $this->points;
    }

    /**
     * @return float|int
     */
    public function getMaxPoints()
    {
        return $this->_checkoutSession->getQuote()->getSubtotal();
    }

    /**
     * @return int
     */
    public function getSelectedPoints()
    {
        $quote = $this->_checkoutSession->getQuote();
        $selectedPoints = $quote->getRewardPointsAmount();
        return $selectedPoints ? $selectedPoints : 0;
    }
}