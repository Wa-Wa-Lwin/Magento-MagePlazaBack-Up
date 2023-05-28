<?php

public function getWishlistDetailForCustomer($customerId, SearchCriteriaInterface $searchCriteria)
{
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

    foreach ($collection as $item) {
        $value = json_decode($item['value'], true);
        $productId = $item['product_id'];
        $wishlistItemId = $item['wishlist_item_id'];
        $qty = $item['qty'];
        $productIdList[] = $productId;

        if (array_key_exists('super_attribute', $value)) {
            $attribute = $value['super_attribute'];
            $wishlistItemIdList[$productId] = [$wishlistItemId, $qty, $attribute];
        } else {
            $wishlistItemIdList[$productId] = [$wishlistItemId, $qty];
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

    $this->_searchCriteriaBuilder->setFilterGroups([$filterGroupList])->create();
    $searchCriteria = $this->_searchCriteriaBuilder
        ->setFilterGroups([$filterGroupList])
        ->create();

    $productList = $this->customProduct->getList($searchCriteria);
    $productIds = [];
    $setProduct = [];

    foreach ($productList->getItems() as $productItem) {
        $productIds[] = $productItem->getId();
    }

    $optionValues = $this->getProductOptionValues($productIds);

    foreach ($productList->getItems() as $productItem) {
        $productId = $productItem->getId();

        if (isset($wishlistItemIdList[$productId])) {
            $wishlistItemId = $wishlistItemIdList[$productId][0];
            $qty = $wishlistItemIdList[$productId][1];
            $attributes = isset($wishlistItemIdList[$productId][2]) ? $wishlistItemIdList[$productId][2] : null;

            if ($attributes) {
                $childProduct = $this->configurable->getProductByAttributes($attributes, $productItem);
                $childId = $childProduct->getId();
                
                if (isset($childId)) {
                    $attributeArray = $productItem->getExtensionAttributes()->getConfigurableProductOptions();
                    $setProductAttributes = [];

                    foreach ($attributeArray as $attributeList) {
                        $attributeListId = $attributeList->getId();
                        $attributeListAttributeId = $attributeList->getAttributeId();
                        $attributeLisLabel = $attributeList->getLabel();
                        
                        if (isset($optionValues[$childId][$attributeListAttributeId])) {
                            $optionValue = $optionValues[$childId][$attributeListAttributeId]['option_value'];
                            $optionCode = $optionValues[$childId][$attributeListAttributeId]['option_code'];
                            
                            $productAttributes = $this->simpleProductDataFactory->create();
                            $productAttributes->setOptionName($attributeLisLabel);
                            $productAttributes->setOptionValue($optionValue);
                            $productAttributes->setOptionCode($optionCode);
                            
                            $setProductAttributes[] = $productAttributes;
                        }
                    }
                    
                    $setProduct[] = $this->prepareProductItem($productItem, $childId, $qty, $setProductAttributes);
                } else {
                    $setProduct[] = $this->prepareProductItem($productItem, $childId, $qty);
                }
            } else {
                $setProduct[] = $this->prepareProductItem($productItem, $wishlistItemId, $qty);
            }
        }
    }
    
    $searchResult->setItems($setProduct);
    $searchResult->setTotalCount($this->getWishlistTotalCount($customerId));
    
    return $searchResult;

}

protected function getProductOptionValues($productIds)
{
    $connection = $this->resourceConnection->getConnection();
    $table1 = $connection->getTableName('catalog_product_entity_int');
    $table2 = $connection->getTableName('eav_attribute');
    $table3 = $connection->getTableName('eav_attribute_option_value');
    $table4 = $connection->getTableName('eav_attribute_option_swatch');

    $select = $connection->select()
        ->from(['cp_int' => $table1])
        ->join(['eav_attribute' => $table2], 'cp_int.attribute_id = eav_attribute.attribute_id')
        ->join(['eav_attribute_option_value' => $table3], 'cp_int.value = eav_attribute_option_value.option_id', ['option_value' => 'eav_attribute_option_value.value'])
        ->joinLeft(['eav_attribute_option_swatch' => $table4], 'eav_attribute_option_value.option_id = eav_attribute_option_swatch.option_id', ['option_code' => 'eav_attribute_option_swatch.value'])
        ->where('eav_attribute.attribute_id = ?', $attributeListAttributeId)
        ->where('cp_int.entity_id IN (?)', $productIds)
        ->limit(1);

    $rows = $connection->fetchAll($select);
    $optionValues = [];

    foreach ($rows as $row) {
        $productId = $row['entity_id'];
        $attributeListAttributeId = $row['attribute_id'];

        $optionValues[$productId][$attributeListAttributeId] = [
            'option_value' => $row['option_value'],
            'option_code' => $row['option_code']
        ];
    }

    return $optionValues;

}

protected function prepareProductItem($productItem, $wishlistItemId, $qty, $attributes = [])
{
    $productItem->setWishlistItemId($wishlistItemId);
    $productItem->setWishlistQty($qty);
    $productItem->setWishlistOptions($attributes);

    $imageAttributeCode = 'image';
    $imageFilePath = $this->getImageFilePath($wishlistItemId, $imageAttributeCode);

    if ($imageFilePath) {
        $productItem->setCustomImage($imageFilePath);
    }

    return $productItem;
}



protected function getImageFilePath($wishlistItemId, $imageAttributeCode)
{
    $connection = $this->resourceConnection->getConnection();
    $table = $connection->getTableName('catalog_product_entity_varchar');

    $select = $connection->select()
        ->from(['cpev' => $table])
        ->where('cpev.attribute_id = ?', $imageAttributeId)
        ->where('cpev.entity_id = ?', $wishlistItemId);


    $imageRow = $connection->fetchRow($select);

    if ($imageRow) {
        $imagePath = $this->mediaConfig->getMediaPath($imageRow['value']);
        $imageFilePath = $this->mediaDirectory->getAbsolutePath($imagePath);

        if ($this->fileDriver->isExists($imageFilePath)) {
            return $imageFilePath;
        }
    }

    return null;
}
    
        

protected function getWishlistTotalCount($customerId)
{
    $collection = $this->wishlistCollectionFactory->create();
    $collection->addFieldToFilter('customer_id', $customerId);
    $collection->setPageSize(1)->setCurPage(1);

    return $collection->getSize();
}