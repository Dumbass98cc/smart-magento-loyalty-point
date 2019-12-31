<?php

namespace Loyalty\Point\Model;

use Loyalty\Point\Api\Data;
use Loyalty\Point\Api\RewardRuleRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class RewardRuleRepository
 * @package Loyalty\Point\Model
 */
class RewardRuleRepository implements RewardRuleRepositoryInterface
{
    /**
     * @var ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var array
     */
    private $rules = [];

    /**
     * @param ResourceModel\Rule $ruleResource
     * @param RuleFactory $ruleFactory
     */
    public function __construct(
        \Loyalty\Point\Model\ResourceModel\Rule $ruleResource,
        \Loyalty\Point\Model\RuleFactory $ruleFactory
    ) {
        $this->ruleResource = $ruleResource;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     */
    public function save(Data\RuleInterface $rule)
    {
        if ($rule->getRuleId()) {
            /** @var \Loyalty\Point\Model\Rule $rule */
            $rule = $this->get($rule->getRuleId())->addData($rule->getData());
        }

        try {
            $this->ruleResource->save($rule);
            unset($this->rules[$rule->getId()]);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId)
    {
        if (!isset($this->rules[$ruleId])) {
            /** @var \Loyalty\Point\Model\Rule $rule */
            $rule = $this->ruleFactory->create();
            $this->ruleResource->load($rule, $ruleId);

            if (!$rule->getRuleId()) {
                throw new NoSuchEntityException(
                    __('The rule with the "%1" ID wasn\'t found. Verify the ID and try again.', $ruleId)
                );
            }
            $this->rules[$ruleId] = $rule;
        }
        return $this->rules[$ruleId];
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotSaveException
     */
    public function delete(Data\RuleInterface $rule)
    {
        try {
            $this->ruleResource->delete($rule);
            unset($this->rules[$rule->getId()]);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('The "%1" rule couldn\'t be removed.', $rule->getRuleId()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function deleteById($ruleId)
    {
        $model = $this->get($ruleId);
        $this->delete($model);
        return true;
    }
}