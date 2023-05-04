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

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class AddToCart
 * @package Mageplaza\Membership\Observer
 */
class AddToCart implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * AddToCart constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param UrlInterface $url
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        UrlInterface $url
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_url = $url;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();

        /** @var Http $request */
        $request = $observer->getEvent()->getRequest();

        $params = $request->getParams();

        if (isset($params['mpmembership_action']) && Membership::TYPE_MEMBERSHIP === $product->getTypeId()) {
            $observer->getEvent()->getResponse()->setRedirect(
                $this->_url->getUrl('checkout/cart', ['_secure' => true])
            );
            $this->checkoutSession->setNoCartRedirect(true);
        }
    }
}
