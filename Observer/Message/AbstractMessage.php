<?php

namespace Loyalty\Point\Observer\Message;

/**
 * Class AbstractMessage
 * @package Loyalty\Point\Observer\Message
 */
class AbstractMessage
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Loyalty\Point\Helper\Data
     */
    public $helper;

    /**
     * AbstractMessage constructor.
     *
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Loyalty\Point\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Loyalty\Point\Helper\Data $helper
    ) {
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * @param $points
     * @return bool|\Magento\Framework\Phrase
     */
    public function getAfterPlaceOrderMessage($points)
    {
        if (!empty($points)) {
            return __('%1 Reward Points have been added to your balance.', $points);
        }
        return false;
    }
}