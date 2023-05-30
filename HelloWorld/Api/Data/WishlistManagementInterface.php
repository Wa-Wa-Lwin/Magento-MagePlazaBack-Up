<?php

namespace Mageplaza\HelloWorld\Api\Data;

interface WishlistManagementInterface // extends \MIT\Product\Api\Data\StatusShowInterface
{
    const WISHLSIT_COUNT = 'wishlist_count';
    const WISHLIST_ITEM_ID = 'wishlist_item_id';

    /**
     * set wishlist count
     * @param int $count
     * @return $this
     */
    public function setCount(int $count);

    /**
     * get wishlist count
     * @return int
     */
    public function getCount();

    /**
     * set wishlist item id
     * @param int $itemId
     * @return $this
     */
    public function setWishlistItemId($itemId);

    /**
     * get wishlist item id
     * @return int
     */
    public function getWishlistItemId();

}
