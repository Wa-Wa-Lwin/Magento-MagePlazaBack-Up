<?php 


foreach ($collection as $item) {
    $value = json_decode($item['value'], true);
    $productId = $item['product_id'];
    $wishlistItemId = $item['wishlist_item_id'];
         
    $product = $this->customProduct->getProductDetailBySku($productId); 
    
    $product->setWishlistQty($item['qty']);
    $product->setAverageRating($this->customProduct->getRatingSummary($product));

    $product->setWishlistItemId($wishlistItemId);

    if (array_key_exists('super_attribute', $value)) {
        $attribute = $value['super_attribute'];

        $childProduct = $this->configurable->getProductByAttributes($attribute, $product);
        $childId = $childProduct->getId();

        $product->setSelectedProductId($childId);
        $childConfigProduct = $product->getConfigurableProductList();    

        $config1 = microtime(true);

        foreach ($childConfigProduct as $child) {
            if ($child->getId() == $childId) {
                $product->setConfigurableProductList([$child]);
            }
        }
        $setProduct[] = $product;      

        $config2 = microtime(true);
        $config_time = ($config2 - $config1)  ; // Time in milliseconds
        $logger->info($config_time . " Configuarable timer for else from if loop");

    } else {

        $product->setSelectedProductId($productId);
        $childConfigProduct = $product->getConfigurableProductList();

        $simple1 = microtime(true);

        ////////////////////////////////
        foreach ($childConfigProduct as $child) {
            $product->setConfigurableProductList([]);
        }
        $setProduct[] = $product;
        ////////////////////////////////


        $simple2 = microtime(true);
        $simple_time = ($simple2 - $simple1)  ; // Time in milliseconds
    
        $logger->info($simple_time . " Simple timer for else from if loop");
    }
}



$searchResult->setItems($setProduct);
$searchResult->setTotalCount($this->getWishlistTotalCount($customerId));
return $searchResult;