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


 /**
     * get wishlist details list for customer
     * @param string $customerId
     * @return \MIT\Product\Api\Data\CustomProductSearchResultsInterface
     */
    public function getWishlistDetailForCustomer($customerId, SearchCriteriaInterface $searchCriteria)
    {
        $wishlist = new \Zend_Log_Writer_Stream(BP . '/var/log/wishlist.log');
        $logger = new \Zend_Log();
        $logger->addWriter($wishlist);
        $logger->info("Start");

        $searchResult = $this->customProductSearchResultsInterface->create();
        $searchResult->setSearchCriteria($searchCriteria);

        $storeId = $this->storeManager->getStore()->getId();

        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToSelect('qty');
        $collection->getSelect()->joinInner('wishlist', 'wishlist.wishlist_id = main_table.wishlist_id', 'customer_id');
        $collection->getSelect()->joinInner('wishlist_item_option', 'wishlist_item_option.wishlist_item_id = main_table.wishlist_item_id', ['value', 'code', 'product_id', 'wishlist_item_id']);
        $collection->getSelect()->where('wishlist.customer_id = ? ', $customerId)
            ->where('wishlist_item_option.code = ? ', 'info_buyRequest')
            ->where('main_table.store_id = ? ', $storeId);
        $collection->setPageSize($searchCriteria->getPageSize())
            ->setCurPage($searchCriteria->getCurrentPage());

        $productIdList = [];
        $wishlistItemIdList = [];
        $attribute = []; 

        foreach ($collection as $item) {
            $value = json_decode($item['value'], true);
            $productId = $item['product_id'];
            
            $wishlistItemId = $item['wishlist_item_id'];
            $qty = $item['qty'];
            $productIdList[] = $item['product_id'];

            if (array_key_exists('super_attribute', $value)) {
                $attribute = $value['super_attribute'];
                $wishlistItemIdList[$productId] = array($wishlistItemId, $qty, $attribute);
            }             
            else {
                $wishlistItemIdList[$productId] = array($wishlistItemId, $qty);
            }            
        }

        $filteredSku = $this->_filterBuilder
            ->setConditionType('in')
            ->setField('entity_id')
            ->setValue($productIdList)
            ->create();

        $filteredVisibility = $this->_filterBuilder
            ->setConditionType('eq')
            ->setField('visibility')
            ->setValue(4)
            ->create();

        $filterGroupList = $this->_filterGroupBuilder
            ->addFilter($filteredSku)
            ->addFilter($filteredVisibility)
            ->create();

        $filterGroupList = [];

        $filterGroupList[] = $this->_filterGroupBuilder->addFilter($filteredSku)->create();
        $filterGroupList[] = $this->_filterGroupBuilder->addFilter($filteredVisibility)->create();
        $this->_searchCriteriaBuilder->setFilterGroups($filterGroupList)->create();
        $searchCriteria = $this->_searchCriteriaBuilder
            ->setFilterGroups($filterGroupList)
            ->create();

        $productList = $this->customProduct->getList($searchCriteria);
        $product = $productList->getItems(); 
         
        foreach ($product as $productItem) {

            $productItemId = $productItem->getId();

            if (count($wishlistItemIdList[$productItemId]) == 3) {

                $setWishlistItemId = $wishlistItemIdList[$productItemId][0];
                $setQty = $wishlistItemIdList[$productItemId][1];
                $attribute = $wishlistItemIdList[$productItemId][2];

                $childProduct = $this->configurable->getProductByAttributes($attribute, $productItem);
                $childId = $childProduct->getId();

                $logger->info("Start 1");
                
                if (isset($childId)){

                    $logger->info("Start 2");

                    
                        $logger->info("Start 3");
                        $result = array();
                        
                        $connection = $this->resourceConnection->getConnection();
                        
                        $table1 = $connection->getTableName('catalog_product_entity_int');
                        $table2 = $connection->getTableName('eav_attribute');
                        $table3 = $connection->getTableName('eav_attribute_option_value');
                        
                        $select = $connection->select()
                            ->from(array('cp_int1' => $table1), array('entity_id'))
                            ->join(array('eav1' => $table2), 'cp_int1.attribute_id = eav1.attribute_id', array())
                            ->join(array('eavo_value1' => $table3), 'cp_int1.value = eavo_value1.option_id', array('color_value' => 'value'))
                            ->join(array('cp_int2' => $table1), 'cp_int1.entity_id = cp_int2.entity_id', array())
                            ->join(array('eav2' => $table2), 'cp_int2.attribute_id = eav2.attribute_id', array())
                            ->join(array('eavo_value2' => $table3), 'cp_int2.value = eavo_value2.option_id', array('size_value' => 'value'))
                            ->where('eav1.attribute_code = ?', 'color')
                            ->where('cp_int1.entity_id = ?', $childId)
                            ->where('eav2.attribute_code = ?', 'size')
                            ->where('cp_int2.entity_id = ?', $childId);

                        $testing = $select->__toString(); 
                        // Model -> custom product mhar implement 
                        // Custom Product Mngnt Interface mhar define loke Key Set -> definition ll yay 
                        

                        $logger->info($testing);

                        $logger->info("Start 4");
                        
                        $rows = $connection->fetchAll($select);
                        
                        foreach ($rows as $row) {
                            $result[] = array(
                                'entity_id' => $row['entity_id'],
                                'color_value' => $row['color_value'],
                                'size_value' => $row['size_value']
                            );
                        }  

                        // return $result;

                        // $productItem->setWishlistItemId($result);
                        // // $productItem->setWishlistItemId($childId);
                        // $productItem->setWishlistQty($setQty);
                        // // $productItem->setColorAndSize($result);
    
                        $setProduct[] = $productItem; 
                    
                }    
                else 
                {
                    $productItem->setWishlistItemId($childId);
                    //$productItem->setWishlistItemId(8888);
                    $productItem->setWishlistQty($setQty);
                    $productItem->setSimpleProductColor($setSimpleProductColor);
                    $productItem->setSimpleProductSize($setSimpleProductSize);
                 //   $productItem->setColorAndSize($result);

                    $setProduct[] = $productItem; 
                }         

            }
            else {
                $setWishlistItemId = $wishlistItemIdList[$productItemId][0];
                $setQty = $wishlistItemIdList[$productItemId][1];

                $productItem->setWishlistItemId(999);
                $productItem->setWishlistQty($setQty);

                $setProduct[] = $productItem;   
            }   
        }

        // if (count($wishlistItemIdList) == 3) {

        //     $product = $productList->getItems(); 
            
        //     foreach ($product as $productItem) {
                
        //         $productItemId = $productItem->getId();

        //         $setWishlistItemId = $wishlistItemIdList[$productItemId][0];
        //         $setQty = $wishlistItemIdList[$productItemId][1];
        //         $attribute = $wishlistItemIdList[$productItemId][2];
               
        //         $childProduct = $this->configurable->getProductByAttributes($attribute, $productItem);
        //         $childId = $childProduct->getId();

        //         if ($productItem->getId() == $childId) {
        //             $product->setConfigurableProductList([$productItem]); 
        //         }

        //         $setProduct[] = $product;
        //     }            

        // } 
        // else {

        //     $product = $productList->getItems();
        //     foreach ($product as $productItem) {

        //         $productItemId = $productItem->getId();

        //         $setWishlistItemId = $wishlistItemIdList[$productItemId][0];
        //         $setQty = $wishlistItemIdList[$productItemId][1];

        //         $productItem->setWishlistItemId($setWishlistItemId);
        //         $productItem->setWishlistQty($setQty);

        //         $setProduct[] = $productItem; 
               
        //     }

        // }
        
            
        $searchResult->setItems($setProduct);
        $searchResult->setTotalCount($this->getWishlistTotalCount($customerId));
        return $searchResult;
     
    }