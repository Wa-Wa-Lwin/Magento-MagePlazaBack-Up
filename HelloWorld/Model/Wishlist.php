<?php

namespace Mageplaza\HelloWorld\Model;

use Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface;

class Wishlist extends \MIT\Product\Model\StatusShow implements WishlistManagementInterface
{

    /**
     * @inheritdoc
     */
    public function setCount($count)
    {
        return $this->setData(self::WISHLSIT_COUNT, $count);
    }

    /**
     * @inheritdoc
     */
    public function getCount()
    {
        return $this->getData(self::WISHLSIT_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setWishlistItemId($itemId)
    {
        return $this->setData(self::WISHLIST_ITEM_ID, $itemId);
    }

    /**
     * @inheritdoc
     */
    public function getWishlistItemId()
    {
        return $this->getData(self::WISHLIST_ITEM_ID);
    }

}
