<?php

namespace Loyalty\Point\Model\ResourceModel;

use Magento\Rule\Model\ResourceModel\AbstractResource;

/**
 * Class Rule
 * @package Loyalty\Point\Model\ResourceModel
 */
class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var array
     */
    protected $_associatedEntitiesMap;

    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    protected $entityManager;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->_associatedEntitiesMap = $this->getAssociatedEntitiesMap();
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('loyalty_rewardrule', 'rule_id');
    }

    /**
     * @return array
     */
    private function getAssociatedEntitiesMap()
    {
        if (!$this->_associatedEntitiesMap) {
            $this->_associatedEntitiesMap = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Loyalty\Point\Model\ResourceModel\Rule\AssociatedEntityMap::class)
                ->getData();
        }
        return $this->_associatedEntitiesMap;
    }

    /**
     * @return \Magento\Framework\EntityManager\EntityManager
     */
    private function getEntityManager()
    {
        if ($this->entityManager === null) {
            $this->entityManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\EntityManager::class);
        }
        return $this->entityManager;
    }

//    /**
//     * @param \Magento\Framework\Model\AbstractModel $object
//     * @param int $value
//     * @param null $field
//     * @return $this
//     */
//    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
//    {
//        $this->getEntityManager()->load($object, $value);
//        return $this;
//    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->getEntityManager()->save($object);
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function delete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->getEntityManager()->delete($object);
        return $this;
    }

    /**
     * Bind specified rules to entities
     *
     * @param int[]|int|string $ruleIds
     * @param int[]|int|string $entityIds
     * @param string $entityType
     * @return $this
     * @throws \Exception
     */
    public function bindRuleToEntity($ruleIds, $entityIds, $entityType)
    {
        $this->getConnection()->beginTransaction();

        try {
            $this->_multiplyBunchInsert($ruleIds, $entityIds, $entityType);
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }

        $this->getConnection()->commit();

        return $this;
    }

    /**
     * Multiply rule ids by entity ids and insert
     *
     * @param int|[] $ruleIds
     * @param int|[] $entityIds
     * @param string $entityType
     * @return $this
     */
    protected function _multiplyBunchInsert($ruleIds, $entityIds, $entityType)
    {
        if (empty($ruleIds) || empty($entityIds)) {
            return $this;
        }
        if (!is_array($ruleIds)) {
            $ruleIds = [(int)$ruleIds];
        }
        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        $data = [];
        $count = 0;
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        foreach ($ruleIds as $ruleId) {
            foreach ($entityIds as $entityId) {
                $data[] = [
                    $entityInfo['entity_id_field'] => $entityId,
                    $entityInfo['rule_id_field'] => $ruleId,
                ];
                $count++;
                if ($count % 1000 == 0) {
                    $this->getConnection()->insertOnDuplicate(
                        $this->getTable($entityInfo['associations_table']),
                        $data,
                        [$entityInfo['rule_id_field']]
                    );
                    $data = [];
                }
            }
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable($entityInfo['associations_table']),
                $data,
                [$entityInfo['rule_id_field']]
            );
        }

        $this->getConnection()->delete(
            $this->getTable($entityInfo['associations_table']),
            $this->getConnection()->quoteInto(
                $entityInfo['rule_id_field'] . ' IN (?) AND ',
                $ruleIds
            ) . $this->getConnection()->quoteInto(
                $entityInfo['entity_id_field'] . ' NOT IN (?)',
                $entityIds
            )
        );
        return $this;
    }

    /**
     * Unbind specified rules from entities
     *
     * @param int[]|int|string $ruleIds
     * @param int[]|int|string $entityIds
     * @param string $entityType
     * @return $this
     */
    public function unbindRuleFromEntity($ruleIds, $entityIds, $entityType)
    {
        $connection = $this->getConnection();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        if (!is_array($ruleIds)) {
            $ruleIds = [(int)$ruleIds];
        }

        $where = [];
        if (!empty($ruleIds)) {
            $where[] = $connection->quoteInto($entityInfo['rule_id_field'] . ' IN (?)', $ruleIds);
        }
        if (!empty($entityIds)) {
            $where[] = $connection->quoteInto($entityInfo['entity_id_field'] . ' IN (?)', $entityIds);
        }

        $connection->delete($this->getTable($entityInfo['associations_table']), implode(' AND ', $where));

        return $this;
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of rule's associated entity by specified entity type
     *
     * @param string $entityType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('There is no information about associated entity type "%1".', $entityType)
        );
    }

    /**
     * Retrieve customer group ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getCustomerGroupIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'customer_group');
    }

    /**
     * Retrieve rule's associated entity Ids by entity type
     *
     * @param int $ruleId
     * @param string $entityType
     * @return array
     */
    public function getAssociatedEntityIds($ruleId, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table']),
            [$entityInfo['entity_id_field']]
        )->where(
            $entityInfo['rule_id_field'] . ' = ?',
            $ruleId
        );

        return $this->getConnection()->fetchCol($select);
    }
}