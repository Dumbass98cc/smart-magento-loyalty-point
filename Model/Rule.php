<?php

namespace Loyalty\Point\Model;

use Loyalty\Point\Api\Data\RuleExtensionInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\ObjectManager;
use Loyalty\Point\Model\ResourceModel\Rule as RuleResourceModel;

/**
 * Class Rule
 * @package Loyalty\Point\Model
 */
class Rule extends \Magento\Framework\Model\AbstractExtensibleModel implements \Loyalty\Point\Api\Data\RuleInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'loyaltypoint_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * @var \Loyalty\Point\Model\ResourceModel\Rule
     */
    private $ruleResourceModel;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param RuleResourceModel $ruleResourceModel
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        RuleResourceModel $ruleResourceModel,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->ruleResourceModel = $ruleResourceModel ? : ObjectManager::getInstance()->get(RuleResourceModel::class);
    }

    /**
     * Init resource model and id field
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Loyalty\Point\Model\ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }

    /**
     * @inheritDoc
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->ruleResourceModel->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * @inheritDoc
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setMinimumPoint($value)
    {
        return $this->setData(self::MINIMUM_POINT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMinimumPoint()
    {
        return $this->getData(self::MINIMUM_POINT);
    }

    /**
     * @inheritDoc
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setPointToBeEarned($value)
    {
        return $this->setData(self::POINT_TO_BE_EARNED, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPointToBeEarned()
    {
        return $this->getData(self::POINT_TO_BE_EARNED);
    }

    /**
     * @inheritDoc
     */
    public function setConversionRate($value)
    {
        return $this->setData(self::CONVERSION_RATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getConversionRate()
    {
        return $this->getData(self::CONVERSION_RATE);
    }

    /**
     * @inheritDoc
     */
    public function setPriceStep($value)
    {
        return $this->setData(self::PRICE_STEP, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPriceStep()
    {
        return $this->getData(self::PRICE_STEP);
    }

    /**
     * @inheritDoc
     */
    public function setFromDate($value)
    {
        return $this->setData(self::FROM_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setToDate($value)
    {
        return $this->setData(self::TO_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(RuleExtensionInterface $extensionAttributes)
    {
        return $this->setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param \Magento\Framework\DataObject $dataObject
     * @return array|bool
     */
    public function validateData(\Magento\Framework\DataObject $dataObject)
    {
        $result = [];
        $fromDate = $toDate = null;

        if ($dataObject->hasFromDate() && $dataObject->hasToDate()) {
            $fromDate = $dataObject->getFromDate();
            $toDate = $dataObject->getToDate();
        }

        if ($fromDate && $toDate) {
            if ($fromDate > $toDate) {
                $result[] = __('End Date must follow Start Date');
            }
        }

        return !empty($result) ? $result : true;
    }
}