<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Membership
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Membership\Block\Account\Dashboard;

use IntlDateFormatter;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order\ItemRepository;
use Magento\Theme\Block\Html\Pager;
use Mageplaza\Membership\Block\Account\Dashboard;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\History as HistoryModel;
use Mageplaza\Membership\Model\ResourceModel\History\Collection;
use Mageplaza\Membership\Model\ResourceModel\History\CollectionFactory;

/**
 * Class History
 * @method HistoryModel[]|Collection getHistory()
 * @method void setHistory($value)
 * @package Mageplaza\Membership\Block\Account\Dashboard
 */
class History extends Dashboard
{
    /**
     * @var ItemRepository
     */
    protected $itemRepository;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * History constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param Account $accountHelper
     * @param ItemRepository $itemRepository
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        Account $accountHelper,
        ItemRepository $itemRepository,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->itemRepository = $itemRepository;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $helper, $accountHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $history = $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', $this->accountHelper->getCustomerSession()->getCustomerId())
            ->setOrder('history_id', 'desc');

        $this->setHistory($history);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $historyData = $this->getHistory();
        if ($historyData) {
            /** @var Pager $pager */
            $pager = $this->getLayout()->createBlock(Pager::class, 'mpmembership.history.pager');
            $pager->setCollection($historyData);
            $this->setChild('pager', $pager);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param HistoryModel $history
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMembershipLabel($history)
    {
        $data = $history->getData();
        $name = Data::jsonDecode($data['name']);
        $storeId = $this->_storeManager->getStore()->getId();

        try {
            $item = $this->itemRepository->get($data['item_id']);

            $itemName = ' - ' . $item->getName();
        } catch (InputException $e) {
            $itemName = '';
        } catch (NoSuchEntityException $e) {
            $itemName = '';
        }

        $label = $data['customer_group_code'];

        if (!empty($name[0])) {
            $label = $name[0];
        }

        if (!empty($name[$storeId])) {
            $label = $name[$storeId];
        }

        return $label . $itemName;
    }

    /**
     * @param HistoryModel $history
     *
     * @return string
     */
    public function getDurationLabel($history)
    {
        return $this->helper->getDurationText($history->getMembershipData());
    }

    /**
     * @param HistoryModel $history
     *
     * @return string
     */
    public function getExpiredDate($history)
    {
        if (!$start = $history->getMembershipStart()) {
            return __('Waiting For Activation');
        }

        $duration = Data::jsonDecode($history->getMembershipData());

        if (empty($duration)) {
            return '';
        }

        if (isset($duration['permanent']) || (isset($duration['number']) && empty((float)$duration['number']))) {
            return __('Permanent');
        }

        $timestamp = strtotime($start . ' + ' . (float)$duration['number'] . $duration['unit']);

        return $this->formatDate(date('Y/m/d', $timestamp), IntlDateFormatter::MEDIUM);
    }
}
