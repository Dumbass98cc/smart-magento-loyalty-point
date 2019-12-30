<?php

namespace Loyalty\Point\Controller\Adminhtml\Promo\Reward;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Loyalty\Point\Controller\Adminhtml\Promo\Reward
 */
class Delete extends \Loyalty\Point\Controller\Adminhtml\Promo\Reward implements HttpPostActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Loyalty\Point\Model\RewardRuleRepository $ruleRepository */
                $ruleRepository = $this->_objectManager->create(\Loyalty\Point\Model\RewardRuleRepository::class);
                $ruleRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the rule.'));
                $this->_redirect('*/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $id]);
                return;
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a rule to delete.'));
        $this->_redirect('*/*/');
    }
}