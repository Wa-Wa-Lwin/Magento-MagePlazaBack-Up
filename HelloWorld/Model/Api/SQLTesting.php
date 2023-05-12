<?php 

// need to include resourceConnection in the constructor 
 /**
     * @inheritDoc
     */
    protected function getGiftQty(Quote\Item $item, Rule $rule, $qty): float
    {
        $connection = $this->resourceConnection->getConnection();

        $table = $this->resourceConnection->getTableName('salesrule');

        $fields = array('conditions_serialized');
        $sql = $connection->select()
                ->from($table, $fields)
                ->where('rule_id = ? ', $rule->getData('rule_id'));
        $result = $connection->fetchOne($sql);
        $rule['conditions_serialized'] = $result;
        $skus = $this->promoRetriever->getSaleRuleSkus($rule);
        $skusArray = explode(",", $skus);
        $giftQty = 0;
        if(in_array($item->getSku(), $skusArray)){
            if($qty <= $rule->getDiscountQty()){
                $giftQty = $qty;
            }elseif($qty >= $rule->getDiscountQty()){
                $giftQty = $rule->getDiscountQty();
            }
            else{
                return 0;
            }
            return (intval($giftQty / $rule->getDiscountStep())) * $rule->getDiscountAmount();
            
        }
        return 0;
    }

    SELECT 
  cp_int1.entity_id, eavo_value1.value AS color_value,
  eavo_value2.value AS size_value
FROM
  catalog_product_entity_int AS cp_int1
  JOIN eav_attribute AS eav1 ON cp_int1.attribute_id = eav1.attribute_id
  JOIN eav_attribute_option_value AS eavo_value1 ON cp_int1.value = eavo_value1.option_id
  JOIN catalog_product_entity_int AS cp_int2 ON cp_int1.entity_id = cp_int2.entity_id
  JOIN eav_attribute AS eav2 ON cp_int2.attribute_id = eav2.attribute_id
  JOIN eav_attribute_option_value AS eavo_value2 ON cp_int2.value = eavo_value2.option_id
WHERE
  eav1.attribute_code = 'color' AND cp_int1.entity_id = 111
  AND eav2.attribute_code = 'size' AND cp_int2.entity_id = 111;


    protected function getGiftQty(Color $color, Size $size): float
    {
        $connection = $this->resourceConnection->getConnection();

        $table_catalog_product_entity_int = $this->resourceConnection->getTableName('catalog_product_entity_int');
        $table_eav_attribute = $this->resourceConnection->getTableName('eav_attribute');
        $table_eav_attribute_option_value = $this->resourceConnection->getTableName('eav_attribute_option_value');
        
        $fields = array('conditions_serialized');

        $sql = $connection->select()
                ->from($table, $fields)
                ->where('rule_id = ? ', $rule->getData('rule_id'));
        $result = $connection->fetchOne($sql);

        $rule['conditions_serialized'] = $result;
        $skus = $this->promoRetriever->getSaleRuleSkus($rule);
        $skusArray = explode(",", $skus);
        $giftQty = 0;
        if(in_array($item->getSku(), $skusArray)){
            if($qty <= $rule->getDiscountQty()){
                $giftQty = $qty;
            }elseif($qty >= $rule->getDiscountQty()){
                $giftQty = $rule->getDiscountQty();
            }
            else{
                return 0;
            }
            return (intval($giftQty / $rule->getDiscountStep())) * $rule->getDiscountAmount();
            
        }
        return 0;
    }


    use Magento\Framework\App\ResourceConnection;

function getProductAttributes($productId)
{
    $result = array();
    
    $connection = $this->resourceConnection->getConnection();

    // $table_catalog_product_entity_int = $this->resourceConnection->getTableName('catalog_product_entity_int');
    
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
        ->where('cp_int1.entity_id = ?', $productId)
        ->where('eav2.attribute_code = ?', 'size')
        ->where('cp_int2.entity_id = ?', $productId);
    
    $rows = $connection->fetchAll($select);
    
    foreach ($rows as $row) {
        $result[] = array(
            'entity_id' => $row['entity_id'],
            'color_value' => $row['color_value'],
            'size_value' => $row['size_value']
        );
    }
    
    return $result;
}
