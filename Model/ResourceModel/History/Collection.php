<?php

namespace Loyalty\Point\Model\ResourceModel\History;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Loyalty\Point\Model\ResourceModel\History
 */
class Collection extends AbstractCollection
{
    /**
     * Set resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Loyalty\Point\Model\History',
            'Loyalty\Point\Model\ResourceModel\History');
    }
}