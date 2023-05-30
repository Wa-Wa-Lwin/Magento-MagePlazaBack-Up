<?php

namespace Mageplaza\HelloWorld\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mageplaza\HelloWorld\Api\Data\WishlistProductInterface;

//app\code\Mageplaza\HelloWorld\Api\Data\WishlistProductInterface.php

class WishlistProduct extends AbstractExtensibleModel implements WishlistProductInterface {

    /**
     * get id
     * @return int
     */
    public function getId() {
        return $this->getData(self::ID);
    }

    /**
     * set id
     * @param int $id
     * @return $this
     */
    public function setId($id) {
        return $this->setData(self::ID, $id);
    }

    /**
     * get name
     * @return string
     */
    public function getName() {
        return $this->getData(self::NAME);
    }

    /**
     * set name
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        return $this->setData(self::NAME, $name);
    }


    /**
     * get sku
     * @return string
     */
    public function getSku() {
        return $this->getData(self::SKU);
    }

    /**
     * set sku
     * @param string $sku
     * @return $this
     */
    public function setSku($sku) {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * get price
     * @return float
     */
    public function getPrice() {
        return $this->getData(self::PRICE);
    }

    /**
     * set price
     * @param float $price
     * @return $this
     */
    public function setPrice($price) {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * get discount price
     * @return float
     */
    public function getDiscountPrice() {
        return $this->getData(self::DISCOUNT_PRICE);
    }

    /**
     * set discount price
     * @param float $discountPrice
     * @return $this
     */
    public function setDiscountPrice($discountPrice) {
        return $this->setData(self::DISCOUNT_PRICE, $discountPrice);
    }

    /**
     * get image
     * @return string
     */
    public function getImage() {
        return $this->getData(self::IMAGE);
    }

    /**
     * set image
     * @param string $image
     * @return $this
     */
    public function setImage($image) {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * get discount label
     * @return string
     */
    public function getDiscountLabel() {
        return $this->getData(self::DISCOUNT_LABEL);
    }

    /**
     * set discount label
     * @param string $discountLabel
     * @return $this
     */
    public function setDiscountLabel($discountLabel) {
        return $this->setData(self::DISCOUNT_LABEL, $discountLabel);
    }

    /**
     * get mp reward points
     * @return int
     */
    public function getMpRewardPoints() {
        return $this->getData(self::MP_REWARD_POINTS);
    }

    /**
     * set mp reward points
     * @param int $mpRewardPoints
     * @return $this
     */
    public function setMpRewardPoints($mpRewardPoints) {
        return $this->setData(self::MP_REWARD_POINTS, $mpRewardPoints);
    }

    /**
     * get mp review points
     * @return int
     */
    public function getMpReviewPoints() {
        return $this->getData(self::MP_REVIEW_POINTS);
    }

    /**
     * set mp review points
     * @param int $mpReviewPoints
     * @return $this
     */
    public function setMpReviewPoints($mpReviewPoints) {
        return $this->setData(self::MP_REVIEW_POINTS, $mpReviewPoints);
    }

    /**
     * get is freeshipping
     * @return bool
     */
    public function getIsFreeShipping() {
        return $this->getData(self::IS_FREE_SHIPPING);
    }

    /**
     * set is freeshipping
     * @param bool $isFreeShipping
     * @return $this
     */
    public function setIsFreeShipping($isFreeShipping) {
        return $this->setData(self::IS_FREE_SHIPPING, $isFreeShipping);
    }

    /**
     * get freeshipping img
     * @return string
     */
    public function getFreeShippingImg() {
        return $this->getData(self::FREE_SHIPPING_IMG);
    }

    /**
     * set freeshipping img
     * @param string $freeShippingImg
     * @return $this
     */
    public function setFreeShippingImg($freeShippingImg) {
        return $this->setData(self::FREE_SHIPPING_IMG, $freeShippingImg);
    }
    
    /**
     * get options
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistInterface[]
     */
    public function getOptions(){
        return $this->getData(self::WISHLIST_OPTIONS);
    }

    /**
     * set freeshipping img
     * @param \Mageplaza\HelloWorld\Api\Data\WishlistInterface[] $items
     * @return $this
     */
    public function setOptions(array $items){
        return $this->setData(self::WISHLIST_OPTIONS, $items);
    }    


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

    /**
     * get WISHLIST_IMAGE_PATH
     * @return string
     */
    public function getWishlistImagePath() {
        return $this->getData(self::WISHLIST_IMAGE_PATH);
    }

    /**
     * set WISHLIST_IMAGE_PATH 
     * @param string $simpleSize
     * @return $this
     */
    public function setWishlistImagePath($simpleImagePath) {
        return $this->setData(self::WISHLIST_IMAGE_PATH, $simpleImagePath);
    }
}