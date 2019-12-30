<?php


namespace Loyalty\Point\Controller\Adminhtml\Promo\Reward;


use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class Save extends \Loyalty\Point\Controller\Adminhtml\Promo\Reward implements HttpPostActionInterface
{
    protected $dataPersistor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Date $dateFilter,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context, $coreRegistry, $dateFilter);
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Loyalty\Point\Model\Rule $model */
                $model = $this->_objectManager->create(\Loyalty\Point\Model\Rule::class);

                /** @var \Loyalty\Point\Api\RewardRuleRepositoryInterface $ruleRepository */
                $ruleRepository = $this->_objectManager->get(
                    \Loyalty\Point\Api\RewardRuleRepositoryInterface::class
                );
                $data = $this->getRequest()->getPostValue();

                $inputFilter = new \Zend_Filter_Input(
                    ['from_date' => $this->_dateFilter, 'to_date' => $this->_dateFilter],
                    [],
                    $data,
                    ['allowEmpty' => true]
                );

                if (array_key_exists('from_date', $data)) {
                    $data['from_date'] = trim($data['from_date']) ? $inputFilter->getUnescaped('from_date') : '';
                }
                if (array_key_exists('to_date', $data)) {
                    $data['to_date'] = trim($data['to_date']) ? $inputFilter->getUnescaped('to_date') : '';
                }

                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model = $ruleRepository->get($id);
                }

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->dataPersistor->set('reward_rule', $data);
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }

                $model->loadPost($data);

                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('reward_rule', $data);

                $ruleRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                $this->dataPersistor->clear('reward_rule');

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('*/*/');
    }
}