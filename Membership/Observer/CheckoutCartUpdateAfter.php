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
use Magento\Checkout\Model\Cart;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class CheckoutCartUpdateAfter
 * @package Mageplaza\Membership\Observer
 */
class CheckoutCartUpdateAfter implements ObserverInterface
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
     * CheckoutCartUpdateAfter constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ManagerInterface $messageManager
    ) {
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getCart();

        /** @var DataObject $info */
        $info = $observer->getEvent()->getInfo();

        if (!$info || empty($info->getData())) {
            return $this;
        }

        $isAlert = false;
        foreach ($info->getData() as $objId => $config) {
            $item = $cart->getQuote()->getItemById($objId);

            if (!$item
                || empty($config['qty'])
                || $config['qty'] <= 1
                || $item->getProductType() !== Membership::TYPE_MEMBERSHIP) {
                continue;
            }

            $item->setQty(1);
            $isAlert = true;
        }

        if ($isAlert) {
            $this->messageManager->addWarningMessage(__('Please buy one membership product only.'));
        }

        return $this;
    }
}
