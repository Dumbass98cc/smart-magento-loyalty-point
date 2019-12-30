<?php


namespace Loyalty\Point\Controller\Adminhtml\Promo\Reward;


use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Loyalty\Point\Controller\Adminhtml\Promo\Reward implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Loyalty\Point\Api\RewardRuleRepositoryInterface $ruleRepository */
        $ruleRepository = $this->_objectManager->get(
            \Loyalty\Point\Api\RewardRuleRepositoryInterface::class
        );

        if ($id) {
            try {
                /** @var \Loyalty\Point\Model\Rule $model */
                $model = $ruleRepository->get($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            /** @var \Loyalty\Point\Model\Rule $model */
            $model = $this->_objectManager->create(
                \Loyalty\Point\Model\Rule::class
            );
        }

        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_promo_reward_rule', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Loyalty Reward Rule'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Loyalty Reward Rule')
        );

        $breadcrumb = $id ? __('Edit Rule') : __('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}