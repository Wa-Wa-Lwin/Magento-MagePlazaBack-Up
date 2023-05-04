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

namespace Mageplaza\Membership\Model\Api;

use Mageplaza\Membership\Api\Data\MembershipPageInterface;
use Mageplaza\Membership\Model\Membership;

/**
 * Class MembershipPage
 * @package Mageplaza\Membership\Model\Api
 */
class MembershipPage extends Membership implements MembershipPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getDuration()
    {
        return $this->getData(self::DURATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDuration($value)
    {
        return $this->setData(self::DURATION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($value)
    {
        return $this->setData(self::OPTIONS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsOutOfStock()
    {
        return $this->getData(self::IS_OUT_OF_STOCK);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsOutOfStock($value)
    {
        return $this->setData(self::IS_OUT_OF_STOCK, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBackgroundColor()
    {
        return parent::getBackgroundColor() ?: '#1979c3';
    }

    /**
     * {@inheritdoc}
     */
    public function getBenefit()
    {
        return $this->getData(self::BENEFIT);
    }
}
