<?php

namespace Loyalty\Point\Api;

use Loyalty\Point\Api\Data\RuleInterface;

/**
 * Interface RewardRuleRepositoryInterface
 * @package Loyalty\Point\Api
 */
interface RewardRuleRepositoryInterface
{
    /**
     * @param RuleInterface $rule
     * @return RuleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(RuleInterface $rule);

    /**
     * @param int $ruleId
     * @return RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($ruleId);

    /**
     * @param RuleInterface $rule
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(RuleInterface $rule);

    /**
     * @param int $ruleId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($ruleId);
}