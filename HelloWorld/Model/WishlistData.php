<?php

namespace Mageplaza\HelloWorld\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mageplaza\HelloWorld\Api\Data\WishlistInterface; 
// app\code\Mageplaza\HelloWorld\Api\Data\WishlistInterface.php

class WishlistData extends AbstractExtensibleModel implements WishlistInterface
{

    /**
     * get OPTION_NAME
     * @return string
     */
    public function getOptionName()
    {
        return $this->getData(self::OPTION_NAME);
    }

    /**
     * set OPTION_NAME
     * @param string $optionName
     * @return $this
     */
    public function setOptionName($optionName)
    {
        return $this->setData(self::OPTION_NAME, $optionName);
    }

    /**
     * get OPTION_VALUE
     * @return string
     */
    public function getOptionValue()
    {
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * set OPTION_VALUE 
     * @param string $optionValue
     * @return $this
     */
    public function setOptionValue($optionValue)
    {
        return $this->setData(self::OPTION_VALUE, $optionValue);
    }

    /**
     * get OPTION_CODE
     * @return string
     */
    public function getOptionCode()
    {
        return $this->getData(self::OPTION_CODE);
    }

    /**
     * set OPTION_CODE 
     * @param string $optionCode
     * @return $this
     */
    public function setOptionCode($optionCode)
    {
        return $this->setData(self::OPTION_CODE, $optionCode);
    }
}
