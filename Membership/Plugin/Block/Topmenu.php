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

namespace Mageplaza\Membership\Plugin\Block;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\TreeFactory;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Topmenu
 * @package Mageplaza\Membership\Plugin\Block
 */
class Topmenu
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var TreeFactory
     */
    protected $treeFactory;

    /**
     * @var RequestInterface|Http
     */
    protected $request;

    /**
     * Topmenu constructor.
     *
     * @param Data $helper
     * @param TreeFactory $treeFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Data $helper,
        TreeFactory $treeFactory,
        RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->treeFactory = $treeFactory;
        $this->request = $request;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param array $args
     *
     * @return array
     */
    public function beforeGetHtml(\Magento\Theme\Block\Html\Topmenu $subject, ...$args)
    {
        if ($this->helper->isShowPageLinkOn()) {
            $subject->getMenu()->addChild(new Node($this->getMenuAsArray(), 'id', $this->treeFactory->create()));
        }

        return $args;
    }

    /**
     * @return array
     */
    private function getMenuAsArray()
    {
        $identifier = trim($this->request->getPathInfo(), '/');

        return [
            'name' => __('Membership'),
            'id' => 'mpmembership-node',
            'url' => $this->helper->getPageRouteUrl(),
            'has_active' => $identifier === $this->helper->getPageRoute()
        ];
    }
}
