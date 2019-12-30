<?php


namespace Loyalty\Point\Api\Data;


/**
 * Interface RuleInterface
 * @package Loyalty\Point\Api\Data
 */
interface RuleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const RULE_ID = 'rule_id';
    const NAME = 'name';
    const STATUS = 'status';
    const MINIMUM_POINT = 'minimum_point';
    const TYPE = 'type';
    const POINT_TO_BE_EARNED = 'point_to_be_earned';
    const CONVERSION_RATE = 'conversion_rate';
    const PRICE_STEP = 'price_step';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';

    /**
     * @return int|null
     */
    public function getRuleId();

    /**
     * @param $value
     * @return $this
     */
    public function setName($value);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param string $value
     * @return $this
     */
    public function setMinimumPoint($value);

    /**
     * @return int|null
     */
    public function getMinimumPoint();

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @return int|null
     */
    public function getType();

    /**
     * @param string $value
     * @return $this
     */
    public function setPointToBeEarned($value);

    /**
     * @return int|null
     */
    public function getPointToBeEarned();

    /**
     * @param string $value
     * @return $this
     */
    public function setConversionRate($value);

    /**
     * @return int|null
     */
    public function getConversionRate();

    /**
     * @param string $value
     * @return $this
     */
    public function setPriceStep($value);

    /**
     * @return int|null
     */
    public function getPriceStep();

    /**
     * @param string $value
     * @return $this
     */
    public function setFromDate($value);

    /**
     * @return string|null
     */
    public function getFromDate();

    /**
     * @param string $value
     * @return $this
     */
    public function setToDate($value);

    /**
     * @return string|null
     */
    public function getToDate();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Loyalty\Point\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param RuleExtensionInterface $extensionAttributes
     * @return \Loyalty\Point\Api\Data\RuleExtensionInterface|null
     */
    public function setExtensionAttributes(\Loyalty\Point\Api\Data\RuleExtensionInterface $extensionAttributes);
}