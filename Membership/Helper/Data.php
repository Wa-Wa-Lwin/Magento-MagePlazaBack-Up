<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\Membership\Helper;

use Magento\Backend\Model\Session\Quote;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\Membership\Model\Config\Source\DateUnit;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Config\Source\PageLinkArea;

/**
 * Class Data
 * @package Mageplaza\Membership\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpmembership';
    const TEMPLATE_MEDIA_PATH = 'mageplaza/membership';
    const PAGE_CONFIGURATION = '/page';

    /**
     * @var Quote|Session
     */
    protected $checkoutSession;

    /**
     * @var Account
     */
    protected $accountHelper;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var string
     */
    protected $placeHolderImage;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Account $accountHelper
     * @param Filesystem $filesystem
     * @param Repository $assetRepo
     * @param PriceCurrencyInterface $priceCurrency
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Account $accountHelper,
        Filesystem $filesystem,
        Repository $assetRepo,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->accountHelper = $accountHelper;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->assetRepo = $assetRepo;
        $this->priceCurrency = $priceCurrency;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Decode function for testing possibility
     *
     * @param string $encodedValue
     *
     * @return mixed
     */
    public function jsonDecodeData($encodedValue)
    {
        return self::jsonDecode($encodedValue);
    }

    /**
     * Encode function for testing possibility
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public function jsonEncodeData($valueToEncode)
    {
        return self::jsonEncode($valueToEncode);
    }

    /**
     * @return WriteInterface
     */
    public function getMediaDirectory()
    {
        return $this->mediaDirectory;
    }

    /**
     * @param string $withPath
     *
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getBaseMediaUrl($withPath)
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();

        if ($withPath) {
            return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::TEMPLATE_MEDIA_PATH;
        }

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param string $file
     * @param bool $withPath
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl($file, $withPath = true)
    {
        return $this->getBaseMediaUrl($withPath) . $this->_prepareFile($file);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFeaturedImageUrl()
    {
        return $this->getBaseMediaUrl(true) . '/default/featured.png';
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @param float $amount
     * @param bool $format
     * @param bool $includeContainer
     * @param null $scope
     *
     * @return float|string
     */
    public function convertPrice($amount, $format = true, $includeContainer = true, $scope = null)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $amount,
                $includeContainer,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $scope
            )
            : $this->priceCurrency->convert($amount, $scope);
    }

    /**
     * @param null $storeId
     *
     * @return int
     */
    public function getDefaultGroup($storeId = null)
    {
        return (int)$this->getConfigGeneral('default_group', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isAllowOverride($storeId = null)
    {
        return (bool)$this->getConfigGeneral('allow_override', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isAllowUpgrade($storeId = null)
    {
        return (bool)$this->getConfigGeneral('allow_upgrade', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return int
     */
    public function getUpgradingCost($storeId = null)
    {
        return (int)$this->getConfigGeneral('upgrading_cost', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return int
     */
    public function changeMembershipWhen($storeId = null)
    {
        return (int)$this->getConfigGeneral('change_membership_when', $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getConfigPage($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . self::PAGE_CONFIGURATION . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isPageEnabled($storeId = null)
    {
        return (bool)$this->getConfigPage('enabled', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getPageRoute($storeId = null)
    {
        return $this->getConfigPage('page_route', $storeId) ?: 'membership';
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getPageRouteUrl($storeId = null)
    {
        return $this->_urlBuilder->getUrl($this->getPageRoute($storeId));
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isShowOnToplink($storeId = null)
    {
        return $this->isEnabled($storeId)
            && $this->accountHelper->isCustomerLoggedIn()
            && $this->getConfigGeneral('show_on_toplink', $storeId);
    }

    /**
     * @param int $area
     * @param null $storeId
     *
     * @return bool
     */
    public function isShowPageLinkOn($area = PageLinkArea::MENU, $storeId = null)
    {
        $isEnabled = $this->isEnabled($storeId);
        $isPageEnabled = $this->getConfigPage('enabled', $storeId);
        $pageAreas = explode(',', $this->getConfigPage('page_link_area', $storeId));

        return $isEnabled && $isPageEnabled && in_array((string)$area, $pageAreas, true);
    }

    /**
     * @param Item|Item\AbstractItem|\Magento\Sales\Model\Order\Item $item
     * @param array $options
     *
     * @return array
     */
    public function getOptionList($item, $options = [])
    {
        $option = $this->getOptionValue(FieldRenderer::DURATION, $item);

        if (empty($option)) {
            return $options;
        }

        $optionList = [
            [
                'label' => __('Duration'),
                'value' => $this->getDurationText($option),
                'custom_view' => true
            ]
        ];

        return array_merge($optionList, $options);
    }

    /**
     * @param string $code
     * @param Item|Item\AbstractItem|\Magento\Sales\Model\Order\Item $item
     *
     * @return array|bool
     */
    public function getOptionValue($code, $item)
    {
        if ($item instanceof Item\AbstractItem && $option = $item->getOptionByCode($code)) {
            return $option->getValue();
        }

        if ($option = $item->getProductOptionByCode($code)) {
            return $option;
        }

        return false;
    }

    /**
     * @param string $option
     *
     * @return string
     */
    public function getDurationText($option)
    {
        $option = self::jsonDecode($option);

        if (isset($option['permanent']) || (isset($option['number']) && empty((float)$option['number']))) {
            return __('Permanent');
        }

        $value = '';

        if (isset($option['number'])) {
            $value .= (float)$option['number'] . ' ';
        }

        if (isset($option['unit'])) {
            $value .= DateUnit::getUnitStatic($option['unit']);
        }

        return $value;
    }

    /**
     * Check the current page is OSC
     *
     * @param null $storeId
     *
     * @return bool
     */
    public function isOscPage($storeId = null)
    {
        return $this->isEnabled($storeId) && $this->_request->getRouteName() === 'onestepcheckout';
    }

    /**
     * Get checkout session for admin and frontend
     *
     * @return Quote|Session
     */
    public function getCheckoutSession()
    {
        if ($this->checkoutSession === null) {
            $this->checkoutSession = $this->objectManager->get($this->isAdmin() ? Quote::class : Session::class);
        }

        return $this->checkoutSession;
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     * @throws LocalizedException
     */
    public function getScopeId($storeId = null)
    {
        if ($storeId) {
            return $storeId;
        }

        $scope = $this->_request->getParam(ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

        if ($websiteId = $this->_request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            /** @var Website $website */
            $website = $this->storeManager->getWebsite($websiteId);
            $scope = $website->getDefaultStore()->getId();
        }

        return $scope;
    }

    /**
     * @param string $hexColor
     *
     * @return string
     */
    public static function getContrastColor($hexColor)
    {
        $hexColor = $hexColor ?: '#abc';

        if (strlen($hexColor) === 4) {
            $hexColor = str_split($hexColor);

            $hexColor[1] .= $hexColor[1];
            $hexColor[2] .= $hexColor[2];
            $hexColor[3] .= $hexColor[3];

            $hexColor = implode('', $hexColor);
        }

        $valueR = hexdec(substr($hexColor, 1, 2));
        $valueG = hexdec(substr($hexColor, 3, 2));
        $valueB = hexdec(substr($hexColor, 5, 2));

        $yiq = (($valueR * 299) + ($valueG * 587) + ($valueB * 114)) / 1000;

        return ($yiq > 128) ? '#000' : '#fff';
    }
}
