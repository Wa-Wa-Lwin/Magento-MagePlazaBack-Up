<?php

namespace Mageplaza\HelloWorld\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mageplaza\HelloWorld\Api\Data\CustomWishlistInterface;

class CustomWishlist extends AbstractExtensibleModel implements CustomWishlistInterface
{

    /**
     * @inheritdoc
     */
    public function setWishlistItemId($wishlistItemId)
    {
        return $this->setData(self::WISHLIST_ITEM_ID, $wishlistItemId);
    }

    /**
     * @inheritdoc
     */
    public function getWishlistItemId()
    {
        return $this->getData(self::WISHLIST_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setWishlistQty($qty)
    {
        return $this->setData(self::WISHLIST_QTY, $qty);
    }

    /**
     * @inheritdoc
     */
    public function getWishlistQty()
    {
        return $this->getData(self::WISHLIST_QTY);
    }

}
