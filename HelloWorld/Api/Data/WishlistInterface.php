<?php

namespace Mageplaza\HelloWorld\Api\Data; 
//app\code\Mageplaza\HelloWorld\Api\Data\WishlistInterface.php

interface WishlistInterface {

    const OPTION_NAME = 'option_name';
    const OPTION_VALUE = 'option_value';
    const OPTION_CODE = 'option_code';

    // /**
    //  * get Items
    //  * @return \Mageplaza\HelloWorld\Api\Data\WishlistProductInterface[]
    //  */
    // public function getItems();

    // /**
    //  * set Items
    //  *
    //  * @param \Mageplaza\HelloWorld\Api\Data\WishlistProductInterface[] $items
    //  * @return $this
    //  */
    // public function setItems(array $items);

    /**
     * get option name
     * @return string
     */
    public function getOptionName();

    /**
     * set option name 
     * @param string $optionName
     * @return $this
     */
    public function setOptionName($optionName);  

    /**
     * get option value
     * @return string
     */
    public function getOptionValue();

    /**
     * set option value 
     * @param string $optionValue
     * @return $this
     */
    public function setOptionValue($optionValue);

    /**
     * get option code
     * @return string
     */
    public function getOptionCode();

    /**
     * set option code 
     * @param string $optionCode
     * @return $this
     */
    public function setOptionCode($optionCode);
    
}