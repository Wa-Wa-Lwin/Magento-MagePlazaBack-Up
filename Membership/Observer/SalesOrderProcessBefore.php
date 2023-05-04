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

namespace Mageplaza\Membership\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\AdminOrder\Create;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class SalesOrderProcessBefore
 * @package Mageplaza\Membership\Observer
 */
class SalesOrderProcessBefore implements ObserverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Account
     */
    protected $account;

    /**
     * SalesOrderProcessBefore constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param ManagerInterface $messageManager
     * @param Data $helper
     * @param Account $account
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ManagerInterface $messageManager,
        Data $helper,
        Account $account
    ) {
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->account = $account;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Http $request */
        $request = $observer->getEvent()->getRequestModel();

        if (!$request || !$request->has('item')) {
            return $this;
        }

        /** @var Create $order */
        $order = $observer->getEvent()->getOrderCreateModel();
        $quote = $order->getQuote();
        $items = $request->getPost('item');
        $customer = $this->account->getCustomerById($order->getQuote()->getCustomerId());

        if (!$customer) {
            return $this;
        }

        $isAlertQty = false;
        $isAlertOverride = false;

        foreach ($items as $objId => &$config) {
            if (empty($config['qty'])) {
                continue;
            }

            if ($request->getPost('update_items')) {
                $item = $quote->getItemById($objId);
                $productType = $item->getProductType();
            } else {
                try {
                    $product = $this->productRepository->getById($objId);
                    $productType = $product->getTypeId();
                } catch (NoSuchEntityException $e) {
                    continue;
                }
            }

            if ($productType !== Membership::TYPE_MEMBERSHIP) {
                continue;
            }

            if ((empty($config['action']) || $config['action'] !== 'remove')
                && (int)$customer->getStatus() === CustomerStatus::ACTIVE
                && !$this->helper->isAllowOverride($quote->getStoreId())) {
                unset($items[$objId]);
                $isAlertOverride = true;
            } elseif ($config['qty'] > 1) {
                $config['qty'] = 1;
                $isAlertQty = true;
            }
        }

        unset($config);

        if ($isAlertQty) {
            $this->messageManager->addWarningMessage(__('Please buy one membership product only.'));
        }

        if ($isAlertOverride) {
            $this->messageManager->addWarningMessage(
                __('Cannot buy membership products. Customer already has a membership.')
            );
        }

        $request->setPostValue('item', $items);

        return $this;
    }
}
