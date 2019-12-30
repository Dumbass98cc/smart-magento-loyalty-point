<?php


namespace Loyalty\Point\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class History
 * @package Loyalty\Point\Model\ResourceModel
 */
class History extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('loyalty_point_history', 'history_id');
    }
}