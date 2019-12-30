<?php

namespace Loyalty\Point\Block\Adminhtml\Promo;

/**
 * Class Reward
 * @package Loyalty\Point\Block\Adminhtml\Promo
 */
class Reward extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Loyalty_Point';
        $this->_controller = 'adminhtml_promo_reward';
        $this->_headerText = __('Reward Rule');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}