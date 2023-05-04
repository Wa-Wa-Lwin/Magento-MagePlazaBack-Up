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

namespace Mageplaza\Membership\Plugin\Api\Osc;

use Closure;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Mageplaza\Osc\Api\CheckoutManagementInterface;

/**
 * Class CheckoutManagement
 * @package Mageplaza\Membership\Plugin\Api\Osc
 */
class CheckoutManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * CheckoutManagement constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param CheckoutManagementInterface $subject
     * @param Closure $proceed
     * @param mixed ...$args
     *
     * @return mixed
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function aroundUpdateItemQty(CheckoutManagementInterface $subject, Closure $proceed, ...$args)
    {
        [$cartId, $itemId, $itemQty] = $args;

        if ($itemQty <= 1) {
            return $proceed(...$args);
        }

        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $item = $quote->getItemById($itemId);

        if ($item->getProductType() === Membership::TYPE_MEMBERSHIP) {
            throw new CouldNotSaveException(__('Please buy one membership product only.'));
        }

        return $proceed(...$args);
    }
}
