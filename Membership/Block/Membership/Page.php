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

namespace Mageplaza\Membership\Block\Membership;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\MembershipStatus;
use Mageplaza\Membership\Model\Membership;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;
use Mageplaza\Membership\Model\ResourceModel\Membership\CollectionFactory;

/**
 * Class Page
 * @package Mageplaza\Membership\Block\Membership
 */
class Page extends \Mageplaza\Membership\Block\Account\Dashboard\Membership
{
    /**
     * @var bool
     */
    protected $_isUpgradePage = false;

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Membership::membership/page.phtml';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Page constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param Account $accountHelper
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param ProductRepository $productRepository
     * @param FormKey $formKey
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        Account $accountHelper,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        ProductRepository $productRepository,
        FormKey $formKey,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct(
            $context,
            $helper,
            $accountHelper,
            $membershipFactory,
            $membershipResource,
            $productRepository,
            $formKey,
            $data
        );
    }

    /**
     * @return bool
     */
    public function isPageEnabled()
    {
        return $this->helper->isPageEnabled();
    }

    /**
     * @return bool
     */
    public function isUpgradePage()
    {
        return $this->_isUpgradePage;
    }

    /**
     * @return DataObject[]|Membership[]
     */
    public function getMembershipCollection()
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('default_product', ['gt' => 0])
            ->addFieldToFilter('status', MembershipStatus::ACTIVE)
            ->setOrder('sort_order', 'asc');

        return $collection->getItems();
    }

    /**
     * @return float
     */
    public function getDeductAmount()
    {
        return 0;
    }
}
