<?php

namespace Loyalty\Point\Model\ResourceModel\Rule;

/**
 * Class Collection
 * @package Loyalty\Point\Model\ResourceModel\Rule
 */
class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
    /**
     * Init Model and Resource Model
     */
    protected function _construct()
    {
        $this->_init(\Loyalty\Point\Model\Rule::class, \Loyalty\Point\Model\ResourceModel\Rule::class);
    }

    /**
     * @param $customerGroupId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        if (!$this->getFlag('is_customer_group_joined')) {
            $this->setFlag('is_customer_group_joined', true);
            $this->getSelect()->join(
                ['customer_group' => $this->getTable('loyalty_rewardrule_customer_group')],
                $this->getConnection()
                    ->quoteInto('customer_group.customer_group_id = ?', $customerGroupId)
                . ' AND main_table.rule_id = customer_group.rule_id',
                []
            );
        }
        return $this;
    }
//    /**
//     * Store associated with rule entities information map
//     *
//     * @var array
//     */
//    protected $_associatedEntitiesMap;
//
//    /**
//     * Collection constructor.
//     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
//     * @param \Psr\Log\LoggerInterface $logger
//     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
//     * @param \Magento\Framework\Event\ManagerInterface $eventManager
//     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
//     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
//     */
//    public function __construct(
//        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
//        \Psr\Log\LoggerInterface $logger,
//        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
//        \Magento\Framework\Event\ManagerInterface $eventManager,
//        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
//        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
//    ) {
//        parent::__construct(
//            $entityFactory,
//            $logger,
//            $fetchStrategy,
//            $eventManager,
//            $connection,
//            $resource
//        );
//        $this->_associatedEntitiesMap = $this->getAssociatedEntitiesMap();
//    }
//
//    /**
//     * @param $entityType
//     * @param $objectField
//     * @throws \Magento\Framework\Exception\LocalizedException
//     */
//    protected function mapAssociatedEntities($entityType, $objectField)
//    {
//        if (!$this->_items) {
//            return;
//        }
//
//        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
//        $ruleIdField = $entityInfo['rule_id_field'];
//        $entityIds = $this->getColumnValues($ruleIdField);
//
//        $select = $this->getConnection()->select()->from(
//            $this->getTable($entityInfo['associations_table'])
//        )->where(
//            $ruleIdField . ' IN (?)',
//            $entityIds
//        );
//
//        $associatedEntities = $this->getConnection()->fetchAll($select);
//
//        array_map(function ($associatedEntity) use ($entityInfo, $ruleIdField, $objectField) {
//            $item = $this->getItemByColumnValue($ruleIdField, $associatedEntity[$ruleIdField]);
//            $itemAssociatedValue = $item->getData($objectField) === null ? [] : $item->getData($objectField);
//            $itemAssociatedValue[] = $associatedEntity[$entityInfo['entity_id_field']];
//            $item->setData($objectField, $itemAssociatedValue);
//        }, $associatedEntities);
//    }
//
//    /**
//     * @return \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
//     * @throws \Magento\Framework\Exception\LocalizedException
//     */
//    protected function _afterLoad()
//    {
//        $this->mapAssociatedEntities('customer_group', 'customer_group_ids');
//
//        return parent::_afterLoad();
//    }
//
//    /**
//     * @param $customerGroupId
//     * @return $this
//     * @throws \Magento\Framework\Exception\LocalizedException
//     */
//    public function addCustomerGroupFilter($customerGroupId)
//    {
//        $entityInfo = $this->_getAssociatedEntityInfo('customer_group');
//        if (!$this->getFlag('is_customer_group_joined')) {
//            $this->setFlag('is_customer_group_joined', true);
//            $this->getSelect()->join(
//                ['customer_group' => $this->getTable($entityInfo['associations_table'])],
//                $this->getConnection()
//                ->quoteInto('customer_group.' . $entityInfo['entity_id_field'] . ' = ?', $customerGroupId)
//                . ' AND main_table.' . $entityInfo['rule_id_field'] . ' = customer_group.'
//                . $entityInfo['rule_id_field'],
//                []
//            );
//        }
//        return $this;
//    }
//
//    /**
//     * @return mixed
//     */
//    private function getAssociatedEntitiesMap()
//    {
//        if (!$this->_associatedEntitiesMap) {
//            $this->_associatedEntitiesMap = \Magento\Framework\App\ObjectManager::getInstance()
//                ->get(\Loyalty\Point\Model\ResourceModel\Rule\AssociatedEntityMap::class)
//                ->getData();
//        }
//        return $this->_associatedEntitiesMap;
//    }
}