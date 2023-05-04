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

namespace Mageplaza\Membership\Ui\Component\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\Store;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Mageplaza\Membership\Model\Config\Source\DateUnitFactory;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;

/**
 * Class Membership
 * @package Mageplaza\Membership\Ui\Component\DataProvider\Product\Modifier
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Membership extends AbstractModifier
{
    /**
     * @type array
     */
    protected $_meta;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var DateUnitFactory
     */
    protected $dateUnitFactory;

    /**
     * Membership constructor.
     *
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param DateUnitFactory $dateUnitFactory
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        DateUnitFactory $dateUnitFactory
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->dateUnitFactory = $dateUnitFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->_meta = $meta;

        $this->customizeDurationField();
        $this->customizePriceField();
        $this->customizeOptionField();

        return $this->_meta;
    }

    /**
     * @return $this|array
     */
    protected function customizeDurationField()
    {
        $containerIndex = 'container_' . FieldRenderer::DURATION;
        $containerPath = $this->arrayManager->findPath($containerIndex, $this->_meta, null, 'children');

        if (!$containerPath) {
            return $this;
        }

        $this->_meta = $this->arrayManager->merge($containerPath, $this->_meta, [
            'children' => [
                FieldRenderer::DURATION => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'component' => 'Mageplaza_Membership/js/form/element/duration',
                                'exports' => [
                                    'value' => '${$.parentName}.' . FieldRenderer::DURATION . ':duration',
                                    '__disableTmpl' => ['value' => false],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return $this;
    }

    /**
     * @return $this|array
     */
    protected function customizePriceField()
    {
        $containerIndex = 'container_' . FieldRenderer::PRICE;
        $containerPath = $this->arrayManager->findPath($containerIndex, $this->_meta, null, 'children');

        if (!$containerPath) {
            return $this;
        }

        $this->_meta = $this->arrayManager->merge($containerPath, $this->_meta, [
            'children' => [
                FieldRenderer::PRICE => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'validation' => ['required-entry' => true, 'validate-zero-or-greater' => true],
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return $this;
    }

    /**
     * @return $this|array
     */
    protected function customizeOptionField()
    {
        $fieldPath = $this->arrayManager->findPath(FieldRenderer::OPTIONS, $this->_meta, null, 'children');

        if (!$fieldPath) {
            return $this;
        }

        $this->_meta = $this->arrayManager->merge($fieldPath, $this->_meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'label' => __('Duration Options'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ]
                    ]
                ]
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                                'disabled' => false,
                            ]
                        ]
                    ],
                    'children' => [
                        'unit' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'dataScope' => 'unit',
                                        'label' => __('Duration'),
                                        'options' => $this->dateUnitFactory->create()->toOptionArray(),
                                    ]
                                ]
                            ]
                        ],
                        'number' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Price::NAME,
                                        'dataScope' => 'number',
                                        'label' => __('No.'),
                                    ]
                                ]
                            ]
                        ],
                        'price' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement' => Input::NAME,
                                        'dataType' => Price::NAME,
                                        'dataScope' => 'price',
                                        'label' => __('Price'),
                                        'addbefore' => $this->getStore()->getBaseCurrency()->getCurrencySymbol()
                                    ]
                                ]
                            ]
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return $this;
    }

    /**
     * @return Store
     */
    protected function getStore()
    {
        /** @var Store $store */
        $store = $this->locator->getStore();

        return $store;
    }
}
