<?php


namespace Loyalty\Point\Controller\Cart;


use Magento\Checkout\Controller\Cart;
use Magento\Framework\DB\Select;

class Apply extends Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Loyalty\Point\Helper\Data
     */
    private $pointsHelper;

    /**
     * @var
     */
    private $rule;

    /**
     * @var \Loyalty\Point\Model\ResourceModel\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * Apply constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Loyalty\Point\Helper\Data $pointsHelper
     * @param \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Loyalty\Point\Helper\Data $pointsHelper,
        \Loyalty\Point\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
    )
    {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->quoteRepository = $quoteRepository;
        $this->pointsHelper = $pointsHelper;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $rewardPoints = trim($this->getRequest()->getParam('reward_point_manually'));
        $cartQuote = $this->cart->getQuote();
        try {
            $amount = $this->_validate($rewardPoints, $cartQuote);

            if ($amount === false || $rewardPoints < 0) {
                $this->messageManager->addError(__('We cannot apply the reward points.'));
                return $this->_goBack();
            }

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setRewardPointsAmount($amount)
                    ->collectTotals();

                $this->quoteRepository->save($cartQuote);
                $this->messageManager->addSuccess(__('We applied "%1" points.', $rewardPoints));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We cannot apply the reward points.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return $this->_goBack();
    }

    /**
     * Validate reward points amount
     *
     * @param  $points
     * @param $cartQuote
     * @return bool|float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _validate($points, $cartQuote)
    {
        if (!is_numeric($points)) {
            return false;
        }

        if ($points > $this->getAvailablePoints($cartQuote)) {
            return false;
        }
        $subtotal = $this->_checkoutSession->getQuote()->getSubtotal();

        if ($points > $subtotal) {
            return false;
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