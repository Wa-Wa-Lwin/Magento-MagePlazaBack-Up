<?php
namespace Mageplaza\HelloWorld\Api;

interface PostManagementInterface
{

    /**
     * get wishlist for customer
     * @param string $customerId
     * @return string
     */
    public function getWishlistForCustomer($customerId);

    // /**
    //  * get wishlist details list for customer
    //  * @param int $customerId
    //  * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    //  * @return \MIT\Product\Api\Data\CustomProductSearchResultsInterface
    //  */

    // C:\MIT\Product\Api\ProductApiInterface.php
    //  /**
    //  * get wishlist details list for customer
    //  * @param int $customerId
    //  * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    //  * @return \MIT\Product\Api\Data\ProductResultListInterface[]
    //  */

    /**
     * get wishlist details list for customer
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MIT\Product\Api\ProductApiInterface
     */
    public function getWishlistDetailForCustomer($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * get wishlist details for customer
     * @param string $customerId
     * @param string $wishlistItemId
     * @return \MIT\Product\Api\Data\CustomProductManagementInterface
     */
    public function getWishlistDetails($customerId, $wishlistItemId);

    /**
     * POST method for add wishlist data
     * @param string $productId
     * @param string $customerId
     * @param string $qty
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function addWishlistForCustomer($productId, $customerId, $qty);

    /**
     * delete wishlist product
     * @param string $wishlistItemId
     * @param string $customerId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function deleteWishlistForCustomer($wishlistItemId, $customerId);

    /**
     * add wishlist products to cart
     * @param string $customerId
     * @param string $wislistItemId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function addWishlistToCart($customerId, $wishlistItemId);

    /**
     * add all wishlist products to cart
     * @param string customerId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function addAllWishlistToCart($customerId);

    /**
     * update wishlist products
     * @param string customerId
     * @param string wishlistItemId
     * @param string productId
     * @param string qty
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function updateWishlistForCustomer($customerId, $wishlistItemId, $productId, $qty);

    /**
     * update wishlist products qty
     * @param string $customerId
     * @param \Mageplaza\HelloWorld\Api\Data\CustomWishlistInterface[] $items
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function updateWishlistQty($customerId, array $items);

    /**
     * move item from cart to wishlist
     * @param string $customerId
     * @param string $itemId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function moveToWishlistFromCart($customerId, $itemId);

}
