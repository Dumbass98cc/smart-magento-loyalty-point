<?php

namespace Loyalty\Point\Model\ResourceModel;

/**
 * Class ReadHandler
 * @package Loyalty\Point\Model\ResourceModel
 */
class ReadHandler implements \Magento\Framework\EntityManager\Operation\AttributeInterface
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
     * ReadHandler constructor.
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
        $entityId = $entityData[$linkField];

        $entityData['customer_group_ids'] = $this->ruleResource->getCustomerGroupIds($entityId);

        return $entityData;
    }
}