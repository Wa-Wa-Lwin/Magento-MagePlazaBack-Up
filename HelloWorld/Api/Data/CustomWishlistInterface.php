<?php

namespace Mageplaza\HelloWorld\Api\Data;

interface CustomWishlistInterface
{
    const WISHLIST_ITEM_ID = 'wishlist_item_id';
    const WISHLIST_QTY = 'wishlist_qty';

    /**
     * set wishlist item id
     * @param string $wishlistItemId
     * @return $this
     */
    public function setWishlistItemId($wishlistItemId);

    /**
     * get wishlist item id
     * @return string
     */
    public function getWishlistItemId();

    /**
     * set wishlist qty
     * @param int $qty
     * @return $this
     */
    public function setWishlistQty(int $qty);

    /**
     * get wishlist qty
     * @return int
     */
    public function getWishlistQty();

}
