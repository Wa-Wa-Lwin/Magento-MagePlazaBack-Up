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

namespace Mageplaza\Membership\Plugin\Model\Quote;

use Closure;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Api\Data\TotalsExtensionInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;

/**
 * Class CartTotalRepository
 * @package Mageplaza\Membership\Plugin\Model\Quote
 */
class CartTotalRepository
{
    /**
     * @var TotalsExtensionFactory
     */
    protected $totalsExtension;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CartTotalRepository constructor.
     *
     * @param TotalsExtensionFactory $totalsExtension
     * @param CartRepositoryInterface $cartRepository
     * @param Data $helper
     */
    public function __construct(
        TotalsExtensionFactory $totalsExtension,
        CartRepositoryInterface $cartRepository,
        Data $helper
    ) {
        $this->totalsExtension = $totalsExtension;
        $this->cartRepository = $cartRepository;
        $this->helper = $helper;
    }

    /**
     * @param CartTotalRepositoryInterface $subject
     * @param Closure $proceed
     * @param $cartId
     *
     * @return mixed
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGet(CartTotalRepositoryInterface $subject, Closure $proceed, $cartId)
    {
        /** @var TotalsInterface $quoteTotals */
        $quoteTotals = $proceed($cartId);

        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        $config = [];

        if ($this->helper->isEnabled($quote->getStoreId())) {
            $items = $quoteTotals->getItems();

            foreach ($items as $item) {
                $quoteItem = $quote->getItemById($item->getItemId());
                $option = $this->helper->getOptionValue(FieldRenderer::DURATION, $quoteItem);

                if (empty($option)) {
                    continue;
                }

                $config[] = [
                    'item_id' => $item->getItemId(),
                    'duration' => $this->helper->getDurationText($option)
                ];
            }
        }

        /** @var TotalsExtensionInterface $totalsExtension */
        $totalsExtension = $quoteTotals->getExtensionAttributes() ?: $this->totalsExtension->create();
        $totalsExtension->setMpMembership(Data::jsonEncode($config));

        $quoteTotals->setExtensionAttributes($totalsExtension);

        return $quoteTotals;
    }
}
