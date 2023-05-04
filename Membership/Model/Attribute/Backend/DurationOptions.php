<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\Membership\Model\Attribute\Backend;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;

/**
 * Class DurationOptions
 * @package Mageplaza\Membership\Model\Attribute\Backend
 */
class DurationOptions extends AbstractBackend
{
    /**
     * @inheritdoc
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getName();
        $rows = $object->getData($attrCode);

        if ($rows !== null) {
            $amounts = [];

            foreach (array_filter((array)$rows) as $data) {
                if (!isset($data['delete']) || (isset($data['delete']) && !$data['delete'])) {
                    $amounts[] = $data;
                }
            }

            $object->setData($attrCode, Data::jsonEncode($amounts));
        }

        return parent::beforeSave($object);
    }

    /**
     * Assign duration options to product data
     *
     * @param Product $object
     *
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $data = $object->getData($attributeCode);

        if (is_string($data)) {
            try {
                $data = Data::jsonDecode($data);
                $object->setData($attributeCode, $data);
            } catch (Exception $e) {
                $object->setData($attributeCode, []);
            }
        }

        return $this;
    }

    /**
     * @param Product $object
     *
     * @return bool
     * @throws LocalizedException
     */
    public function validate($object)
    {
        if ($object->getData(FieldRenderer::DURATION) !== DurationType::CUSTOM) {
            return parent::validate($object);
        }

        $rows = $object->getData($this->getAttribute()->getName());

        if (!$rows) {
            throw new LocalizedException(__('Please setup duration options.'));
        }

        $cleanRows = [];
        foreach ($rows as $row) {
            if (empty($row['delete'])) {
                $cleanRows[] = $row;
            }
        }

        foreach ($cleanRows as $row) {
            if (!$this->isPositiveOrZero($row['number']) || !$this->isPositiveOrZero($row['price'])) {
                throw new LocalizedException(__('Invalid duration value.'));
            }
        }

        if (empty($cleanRows)) {
            throw new LocalizedException(__('Invalid duration value.'));
        }

        return parent::validate($object);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isPositiveOrZero($value)
    {
        if (!is_numeric($value)) {
            return false;
        }

        return $value >= 0;
    }
}
