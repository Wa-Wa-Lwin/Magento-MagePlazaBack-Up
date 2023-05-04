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

namespace Mageplaza\Membership\Controller\Index;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Router
 * @package Mageplaza\Membership\Controller
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    public $actionFactory;

    /**
     * @var Data
     */
    public $helper;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param Data $helper
     */
    public function __construct(
        ActionFactory $actionFactory,
        Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    /**
     * @param RequestInterface|Http $request
     *
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        if (!$this->helper->isEnabled() || $identifier !== $this->helper->getPageRoute()) {
            return null;
        }

        $request->setModuleName('membership')
            ->setControllerName('index')
            ->setActionName('index')
            ->setPathInfo('/membership/index/index')
            ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(Forward::class);
    }
}
