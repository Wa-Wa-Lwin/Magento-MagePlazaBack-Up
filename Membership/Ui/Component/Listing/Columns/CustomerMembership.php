<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Membership
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Membership\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\Membership\Helper\Data;

/**
 * Class CustomerMembership
 * @package Mageplaza\Membership\Ui\Component\Listing\Columns
 */
class CustomerMembership extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getName()] = $this->_processData($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function _processData($item)
    {
        $name = Data::jsonDecode($item['name']);

        return empty($name[0]) ? '' : $name[0];
    }
}
