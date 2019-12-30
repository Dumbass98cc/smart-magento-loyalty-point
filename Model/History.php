<?php

namespace Loyalty\Point\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class History
 * @package Loyalty\Point\Model
 */
class History extends AbstractModel
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Loyalty\Point\Model\ResourceModel\History');
    }
}