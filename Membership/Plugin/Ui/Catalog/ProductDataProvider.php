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

namespace Mageplaza\Membership\Plugin\Ui\Catalog;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class ProductDataProvider
 * @package Mageplaza\Membership\Plugin\Ui\Catalog
 */
class ProductDataProvider
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(\Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider $subject, $result)
    {
        if (isset($result['items'])) {
            foreach ($result['items'] as &$item) {
                if ($item['type_id'] === Membership::TYPE_MEMBERSHIP) {
                    try {
                        /** @var Product $product */
                        $product = $this->productRepository->getById($item['entity_id']);

                        $duration = $product->getData(FieldRenderer::DURATION);

                        $price = $duration !== DurationType::CUSTOM ? $product->getData(FieldRenderer::PRICE) : 0;
                    } catch (NoSuchEntityException $e) {
                        $price = 0;
                    }

                    if ($price) {
                        $item['price'] = $price;
                    }
                }
            }
        }

        return $result;
    }
}
