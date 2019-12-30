<?php

namespace Loyalty\Point\Model\ResourceModel;

/**
 * Class SaveHandler
 * @package Loyalty\Point\Model\ResourceModel
 */
class SaveHandler implements \Magento\Framework\EntityManager\Operation\AttributeInterface
{
    /**
     * @var Rule
     */
    protected $ruleResource;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * SaveHandler constructor.
     * @param Rule $ruleResource
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        Rule $ruleResource,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    ) {
        $this->ruleResource = $ruleResource;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param string $entityType
     * @param array $entityData
     * @param array $arguments
     * @return array
     * @throws \Exception
     */
    public function execute($entityType, $entityData, $arguments = [])
    {
        $linkField = $this->metadataPool->getMetadata($entityType)->getLinkField();

        if (isset($entityData['customer_group_ids'])) {
            $customerGroupIds = $entityData['customer_group_ids'];
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->ruleResource->bindRuleToEntity($entityData[$linkField], $customerGroupIds, 'customer_group');
        }

        return $entityData;
    }
}