<?php

namespace Mageplaza\HelloWorld\Model\Api;

use Magento\Framework\App\ResourceConnection;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;

use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Quote\Item\ConfigurableItemOptionValueFactory;
// 
use Magento\Framework\Api\SearchCriteriaInterface;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\Quote\ItemFactory as QuoteItemFactory;
use Magento\Quote\Model\Quote\ProductOptionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
//
use Mageplaza\HelloWorld\Api\Data\WishlistManagementInterfaceFactory;
use Mageplaza\HelloWorld\Api\PostManagementInterface;
use MIT\Product\Api\Data\CustomProductSearchResultsInterfaceFactory;
use MIT\Product\Model\Api\CustomProductFactory as CustomProductApiFactory;
use MIT\Product\Model\CustomProductFactory;


use MIT\Product\Api\ProductApiInterface;
use MIT\Product\Api\Data\ProductResultListInterface;

use MIT\Product\Model\SimpleProductDataFactory as SimpleProductDataFactory;

use Psr\Log\LoggerInterface;

use Magento\Framework\DB\Adapter\AdapterInterface;

class Wishlist implements PostManagementInterface
{
    protected $_wishlistRepository;
    protected $_productRepository;
    private $wishlist;
    protected $cart;
    protected $messageManager;
    protected $storeManager; 

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistRepository;

    /**
     * @var \MIT\Product\Api\CustomProductInterface
     */
    protected $customProduct;

    /**
     * @var SearchCriteriaBuilder
     */
    private $_searchCriteriaBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $_filterGroupBuilder;

    /**
     * @var FilterBuilder
     */
    private $_filterBuilder;

    /**
     * @var CustomProductFactory
     */
    protected $customProductFactory;


     /**
     * @var ProductApiInterface
     */
    private $productApiInterface;

    /**
     * @var ProductResultListInterface
     */
    private $productResultListInterface;

       

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    private $resourceConfigurable;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $resourceProduct;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var CustomProductSearchResultsInterfaceFactory
     */
    protected $customProductSearchResultsInterface;

    /**
     * @var CustomProductApiFactory
     */
    protected $customProductApiFactory;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var DataObjectFactory
     */
    private $dataObject;

    /**
     * @var WishlistManagementInterfaceFactory
     */
    protected $wishlistManagementInterface;

    /**
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var CollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var QuoteItemFactory
     */
    protected $quoteItemFactory;

    /**
     * @var ConfigurableItemOptionValueFactory
     */
    private $configurableItemOptionValueFactory;

    /**
     * @var ProductOptionFactory
     */
    private $productOptionFactory;


    /**
     * @var StockRegistryInterface
     */
    private $stockRegistryInterface;

    /**
     * @var SimpleProductDataFactory
     */
    protected $simpleProductDataFactory;

    public function __construct(
        // SimpleProductDataTotalInterfaceFactory // $simpleProductDataTotalInterfaceFactory, 
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        CartRepositoryInterface $quoteRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Quote\Model\Quote $quoteModel,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        FilterBuilder $filterBuilder,
        SortOrder $sortOrder,
        Request $request,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \MIT\Product\Api\CustomProductInterface $customProduct,

        ProductApiInterface $productApiInterface,

        ProductResultListInterface $productResultListInterface,

        CustomProductFactory $customProductFactory,
        ProductAttributeRepositoryInterface $attributeRepository,
        LoggerInterface $logger,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $resourceConfigurable,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        ItemCollectionFactory $itemCollectionFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        CustomProductSearchResultsInterfaceFactory $customProductSearchResultsInterface,
        CustomProductApiFactory $customProductApiFactory,
        ProductRepository $_productRepository,
        DataObjectFactory $dataObject,
        WishlistManagementInterfaceFactory $wishlistManagementInterface,
        ItemFactory $itemFactory,
        CollectionFactory $quoteCollectionFactory,
        QuoteItemFactory $quoteItemFactory,
        ConfigurableItemOptionValueFactory $configurableItemOptionValueFactory,
        ProductOptionFactory $productOptionFactory,
        StockRegistryInterface $stockRegistryInterface,
        JsonFactory $resultJsonFactory,
        ResourceConnection $resourceConnection,
        SimpleProductDataFactory $simpleProductDataFactory
    ) {
        $this->_productFactory = $productFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteRepository = $quoteRepository;
        $this->wishlist = $wishlist;
        $this->_wishlistRepository = $wishlistRepository;
        $this->_productRepository = $productRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterGroupBuilder = $filterGroupBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->_sortOrder = $sortOrder;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->wishlistRepository = $wishlistRepository;
        $this->storeManager = $storeManager;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->quoteModel = $quoteModel;
        $this->product = $product;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->customProduct = $customProduct;

        $this->productApiInterface = $productApiInterface;
        $this->productResultListInterface = $productResultListInterface;
  

        $this->customProductFactory = $customProductFactory;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $logger;
        $this->resourceConfigurable = $resourceConfigurable;
        $this->resourceProduct = $resourceProduct;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->configurable = $configurable;
        $this->customProductSearchResultsInterface = $customProductSearchResultsInterface;
        $this->customProductApiFactory = $customProductApiFactory;
        $this->productRepository = $_productRepository;
        $this->dataObject = $dataObject;
        $this->wishlistManagementInterface = $wishlistManagementInterface;
        $this->itemFactory = $itemFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->configurableItemOptionValueFactory = $configurableItemOptionValueFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->resourceConnection = $resourceConnection;
        $this->simpleProductDataFactory = $simpleProductDataFactory;
    }

    /**
     * get wishlist list
     * @param string $customerId
     * @return array
     */
    public function getWishlistForCustomer($customerId)
    {
        #$customer_id = 1;
        $wishlist_collection = $this->wishlist->loadByCustomerId($customerId, true)->getItemCollection();
        $wishlistData = [];
        foreach ($wishlist_collection as $item) {
            $data = [
                "wishlist_item_id" => $item->getWishlistItemId(),
                "wishlist_id" => $item->getWishlistId(),
                "product_id" => $item->getProductId(),
                "store_id" => $item->getStoreId(),
                "added_at" => $item->getAddedAt(),
                "description" => $item->getDescription(),
                "qty" => round($item->getQty()),
                "product_name" => $item->getProductName(),
            ];
            $wishlistData[] = $data;
        }
        return $wishlistData;
    }

   
    /**
     * get wishlist details for customer
     * @param string $customerId
     * @param string $wishlistItemId
     * @return \MIT\Product\Api\Data\CustomProductManagementInterface
     */
    public function getWishlistDetails($customerId, $wishlistItemId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToSelect('qty');
        $collection->getSelect()->joinInner('wishlist', 'wishlist.wishlist_id = main_table.wishlist_id', 'customer_id');
        $collection->getSelect()->joinInner('wishlist_item_option', 'wishlist_item_option.wishlist_item_id = main_table.wishlist_item_id', ['value', 'code', 'product_id', 'wishlist_item_id']);
        $collection->getSelect()->joinInner('wishlist_item', 'wishlist_item.wishlist_item_id = main_table.wishlist_item_id', ['product_id', 'qty']);
        $collection->getSelect()->where('wishlist.customer_id = ? ', $customerId)
            ->where('wishlist_item.wishlist_item_id = ? ', $wishlistItemId)
            ->where('wishlist_item_option.code = ? ', 'info_buyRequest')
            ->where('main_table.store_id = ? ', $storeId);
        if ($collection->getSize() == 0) {
            throw new NoSuchEntityException(__('The wishlist item that was requested doesn\'t exist. Verify the wishlist item and try again.', $wishlistItemId));
        }
        foreach ($collection as $item) {
            $value = json_decode($item['value'], true);
            $productId = $item['product_id'];

            if (array_key_exists('super_attribute', $value)) {
                $attribute = $value['super_attribute'];

                $_configProduct = $this->_productRepository->getById($productId);
                $childProduct = $this->configurable->getProductByAttributes($attribute, $_configProduct);
                $childId = $childProduct->getId();

                $product = $this->customProduct->getProductDetailBySku($productId);
                $product->setWishlistQty($item['qty']);
                $customProduct = $this->customProductApiFactory->create();
                $product->setAverageRating($customProduct->getRatingSummary($product));
                $product->setWishlistItemId($wishlistItemId);
                $product->setSelectedProductId($childId);
                return $product;
            } else {
                $product = $this->customProduct->getProductDetailBySku($productId);
                $product->setWishlistQty($item['qty']);
                $customFactory = $this->customProductApiFactory->create();
                $product->setAverageRating($customFactory->getRatingSummary($product));
                $product->setWishlistItemId($wishlistItemId);
                $product->setSelectedProductId($productId);
                return $product;
            }
        }
    }

    /**
     * Add products to wishlist by product id
     * @param string $productId
     * @param string $customerId
     * @param string $qty
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function addWishlistForCustomer($productId, $customerId, $qty)
    {
        $itemId = 0;
        $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);
        $parentIds = $this->resourceConfigurable->getParentIdsByChild($productId);
        $errorMessage = [];
        $successMessage = [];
        if (!empty($parentIds)) {
            $skus = $this->resourceProduct->getProductsSku($parentIds);
            $product = $this->_productRepository->getById($skus[0]['entity_id']);
            $productName = $product->getName();

            $superCustomAttribute = $this->getChildSuperAttribute($productId);
            $buyRequest = ['qty' => $qty, 'super_attribute' => $superCustomAttribute];

            $item = $wishlist->addNewItem($product, $buyRequest);
            $itemId = $item->getId();
            $wishlist->save();
            $status = true;
            $successMessage[] = "" . $productName . " has been added to your Wish List. Click here to continue shopping.";
        } else {
            $product = $this->_productRepository->getById($productId);
            $productName = $product->getName();
            $buyRequest = ['qty' => $qty];

            $item = $wishlist->addNewItem($product, $buyRequest);
            $itemId = $item->getId();
            $wishlist->save();
            $status = true;
            $successMessage[] = "" . $productName . " has been added to your Wish List. Click here to continue shopping.";
        }
        return $this->showStatus($customerId, $status, $errorMessage, $successMessage, $itemId);
    }

    /**
     * delete wishlist product
     * @param string $wishlistItemId
     * @param string $customerId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function deleteWishlistForCustomer($wishlistItemId, $customerId)
    {
        $wish = $this->wishlist->loadByCustomerId($customerId);
        $items = $wish->getItemCollection();
        $errorMessage = [];
        $successMessage = [];
        $status = false;
        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($items as $item) {
            if ($item->getWishlistItemId() == $wishlistItemId) {
                $productName = $item->getProduct()->getName();
                $item->delete();
                $wish->save();

                $status = true;
                $successMessage[] = "" . $productName . " has been removed from your Wish List.";
            }
        }
        return $this->showStatus($customerId, $status, $errorMessage, $successMessage);
    }

    /**
     * add wishlist products to cart
     * @param string $customerId
     * @param string $wislistItemId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function addWishlistToCart($customerId, $wishlistItemId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToSelect('qty');
        $collection->getSelect()->joinInner('wishlist', 'wishlist.wishlist_id = main_table.wishlist_id', 'customer_id');
        $collection->getSelect()->joinInner('wishlist_item_option', 'wishlist_item_option.wishlist_item_id = main_table.wishlist_item_id', ['value', 'code', 'product_id', 'wishlist_item_id']);
        $collection->getSelect()->joinInner('wishlist_item', 'wishlist_item.wishlist_item_id = main_table.wishlist_item_id', 'qty');
        $collection->getSelect()->where('wishlist.customer_id = ? ', $customerId)
            ->where('wishlist_item_option.code = ? ', 'info_buyRequest')
            ->where('main_table.store_id = ? ', $storeId);

        if ($collection->getSize() == 0) {
            throw new NoSuchEntityException(__('There is no item in wish list.'));
        }
        $errorMessage = [];
        $successMessage = [];
        $status = false;
        foreach ($collection as $item) {
            $value = json_decode($item['value'], true);
            $productId = $item['product_id'];
            $qty = $item['qty'];

            if ($item['wishlist_item_id'] == $wishlistItemId) {
                $product = $this->_productRepository->getById($productId);
                $productType = $product->getTypeID();
                $productName = $product->getName();

                if ($productType == 'configurable') {
                    if (array_key_exists('super_attribute', $value)) {
                        $attribute = $value['super_attribute'];

                        $_configProduct = $this->_productRepository->getById($productId);
                        $childProduct = $this->configurable->getProductByAttributes($attribute, $_configProduct);
                        $childId = $childProduct->getId();

                        $stock = $this->stockRegistryInterface->getStockItem($childId);
                        if ($stock->getQty() > 0 && $stock->getIsInStock() && $stock->getQty() >= $qty) {
                            $this->addWishlistProductToCart($customerId, $productId, $qty, $attribute, true);
                            $this->deleteWishlistForCustomer($wishlistItemId, $customerId);
                            $status = true;
                            $successMessage[] = "You added " . $productName . " to your shopping cart.";
                        } else {
                            $errorMessage[] = "You need to choose options for your item for \"" . $productName . "\".";
                        }
                    } else {
                        $errorMessage[] = "You need to choose options for your item for \"" . $productName . "\".";
                    }
                } else {
                    $stock = $this->stockRegistryInterface->getStockItem($productId);
                    if ($stock->getQty() > 0 && $stock->getIsInStock() && $stock->getQty() >= $qty) {
                        $this->addWishlistProductToCart($customerId, $productId, $qty, [], false);
                        $this->deleteWishlistForCustomer($wishlistItemId, $customerId);
                        $status = true;
                        $successMessage[] = "You added " . $productName . " to your shopping cart.";
                    } else {
                        $errorMessage[] = "The requested qty is not available for \"" . $productName . "\".";
                    }
                }
            }
        }
        return $this->showStatus($customerId, $status, $errorMessage, $successMessage);
    }

    /**
     * add all wishlist products to cart
     * @param string customerId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function addAllWishlistToCart($customerId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToSelect('qty');
        $collection->getSelect()->joinInner('wishlist', 'wishlist.wishlist_id = main_table.wishlist_id', 'customer_id');
        $collection->getSelect()->joinInner('wishlist_item_option', 'wishlist_item_option.wishlist_item_id = main_table.wishlist_item_id', ['value', 'code', 'product_id', 'wishlist_item_id']);
        $collection->getSelect()->joinInner('wishlist_item', 'wishlist_item.wishlist_item_id = main_table.wishlist_item_id', 'qty');
        $collection->getSelect()->where('wishlist.customer_id = ? ', $customerId)
            ->where('wishlist_item_option.code = ? ', 'info_buyRequest')
            ->where('main_table.store_id = ? ', $storeId);

        if ($collection->getSize() == 0) {
            throw new NoSuchEntityException(__('There is no item in wish list.'));
        }
        $errorMessage = [];
        $successMessageArr = [];
        $status = false;
        foreach ($collection as $item) {
            $value = json_decode($item['value'], true);
            $productId = $item['product_id'];
            $qty = $item['qty'];
            $wishlistItemId = $item['wishlist_item_id'];

            $product = $this->_productRepository->getById($productId);
            $productType = $product->getTypeID();
            $productName = $product->getName();

            if ($productType == 'configurable') {
                if (array_key_exists('super_attribute', $value)) {
                    $attribute = $value['super_attribute'];

                    $_configProduct = $this->_productRepository->getById($productId);
                    $childProduct = $this->configurable->getProductByAttributes($attribute, $_configProduct);
                    $childId = $childProduct->getId();

                    $stock = $this->stockRegistryInterface->getStockItem($childId);
                    if ($stock->getQty() > 0 && $stock->getIsInStock() && $stock->getQty() >= $qty) {
                        $this->addWishlistProductToCart($customerId, $productId, $qty, $attribute, true);
                        $this->deleteWishlistForCustomer($wishlistItemId, $customerId);
                        $status = true;
                        $successMessageArr[] = $productName;
                    } else {
                        $errorMessage[] = "You need to choose options for your item for \"" . $productName . "\".";
                    }
                } else {
                    $errorMessage[] = "You need to choose options for your item for \"" . $productName . "\".";
                }
            } else {
                $stock = $this->stockRegistryInterface->getStockItem($productId);
                if ($stock->getQty() > 0 && $stock->getIsInStock() && $stock->getQty() >= $qty) {
                    $this->addWishlistProductToCart($customerId, $productId, $qty, [], false);
                    $this->deleteWishlistForCustomer($wishlistItemId, $customerId);
                    $status = true;
                    $successMessageArr[] = $productName;
                } else {
                    $errorMessage[] = "The requested qty is not available for \"" . $productName . "\".";
                }
            }
        }
        $count = count($successMessageArr);
        $successMessageString = '"' . implode('", "', $successMessageArr) . '"';
        $successMessage = "" . $count . " product(s) have been added to shopping cart: " . $successMessageString . ".";

        return $this->showStatus($customerId, $status, $errorMessage, [$successMessage]);
    }

    /**
     * update wishlist products
     * @param string customerId
     * @param string wishlistItemId
     * @param string productId
     * @param string qty
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function updateWishlistForCustomer($customerId, $wishlistItemId, $productId, $qty)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToSelect('qty');
        $collection->getSelect()->joinInner('wishlist', 'wishlist.wishlist_id = main_table.wishlist_id', 'customer_id');
        $collection->getSelect()->joinInner('wishlist_item_option', 'wishlist_item_option.wishlist_item_id = main_table.wishlist_item_id', ['value', 'code', 'product_id', 'wishlist_item_id']);
        $collection->getSelect()->joinInner('wishlist_item', 'wishlist_item.wishlist_item_id = main_table.wishlist_item_id', ['product_id', 'qty']);
        $collection->getSelect()->where('wishlist.customer_id = ? ', $customerId)
            ->where('wishlist_item.wishlist_item_id = ? ', $wishlistItemId)
            ->where('wishlist_item_option.code = ? ', 'info_buyRequest')
            ->where('main_table.store_id = ? ', $storeId);

        if ($collection->getSize() == 0) {
            throw new NoSuchEntityException(__('The wishlist item that was requested doesn\'t exist. Verify the wishlist item and try again.', $wishlistItemId));
        }
        $errorMessage = [];
        $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);

        $successMessage = [];
        $parentIds = $this->resourceConfigurable->getParentIdsByChild($productId);
        if (!empty($parentIds)) {
            $skus = $this->resourceProduct->getProductsSku($parentIds);
            $product = $this->_productRepository->getById($skus[0]['entity_id']);
            $productName = $product->getName();

            $superCustomAttribute = $this->getChildSuperAttribute($productId);
            $buyRequest = ['qty' => $qty, 'super_attribute' => $superCustomAttribute];
            $this->deleteWishlistForCustomer($wishlistItemId, $customerId);
            $wishlist->addNewItem($product, $buyRequest);
            $wishlist->save();
        } else {
            $product = $this->_productRepository->getById($productId);
            $productName = $product->getName();
            $buyRequest = ['qty' => $qty];
            $this->deleteWishlistForCustomer($wishlistItemId, $customerId);
            $wishlist->addNewItem($product, $buyRequest);
            $wishlist->save();
        }
        $successMessage[] = "" . $productName . " has been updated in your Wish List.";

        return $this->showStatus($customerId, true, $errorMessage, $successMessage);
    }

    /**
     * update wishlist products qty
     * @param string $customerId
     * @param \Mageplaza\HelloWorld\Api\Data\CustomWishlistInterface[] $items
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function updateWishlistQty($customerId, array $items)
    {
        $errorMessage = [];
        $successMessage = [];
        $status = false;
        foreach ($items as $item) {
            $wishlistItemId = $item->getWishlistItemId();
            $qty = $item->getWishlistQty();

            $storeId = $this->storeManager->getStore()->getId();
            $collection = $this->itemCollectionFactory->create();
            $collection->addFieldToSelect('qty');
            $collection->getSelect()->joinInner('wishlist', 'wishlist.wishlist_id = main_table.wishlist_id', 'customer_id');
            $collection->getSelect()->joinInner('wishlist_item_option', 'wishlist_item_option.wishlist_item_id = main_table.wishlist_item_id', ['value', 'code', 'product_id', 'wishlist_item_id']);
            $collection->getSelect()->joinInner('wishlist_item', 'wishlist_item.wishlist_item_id = main_table.wishlist_item_id', ['product_id', 'qty']);
            $collection->getSelect()->where('wishlist.customer_id = ? ', $customerId)
                ->where('wishlist_item.wishlist_item_id = ? ', $wishlistItemId)
                ->where('wishlist_item_option.code = ? ', 'info_buyRequest')
                ->where('main_table.store_id = ? ', $storeId);

            if ($collection->getSize() !== 0) {
                foreach ($collection as $item) {
                    $productId = $item['product_id'];
                    $product = $this->_productRepository->getById($productId);
                    $productName = $product->getName();
                    try {
                        $item = $this->itemFactory->create()->load($wishlistItemId);
                        $item->setQty($qty);
                        $item->save();

                        $status = true;
                        $successMessage[] = "" . $productName . " has been updated in your Wish List.";
                    } catch (\Exception $e) {
                        $status = false;
                    }
                }
            }
        }
        return $this->showStatus($customerId, $status, $errorMessage, $successMessage);
    }

    /**
     * move item from cart to wishlist
     * @param string $customerId
     * @param string $itemId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    public function moveToWishlistFromCart($customerId, $itemId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->quoteCollectionFactory->create();
        $collection->getSelect()->joinInner('quote', 'quote.entity_id = main_table.quote_id', 'customer_id');
        $collection->getSelect()->joinInner('quote_item_option', 'quote_item_option.item_id = main_table.item_id', ['value', 'code', 'product_id', 'item_id']);
        $collection->getSelect()->joinInner('quote_item', 'quote_item.item_id = main_table.item_id', ['product_id', 'qty']);
        $collection->getSelect()->where('quote.customer_id = ? ', $customerId)
            ->where('quote_item.item_id = ? ', $itemId)
            ->where('quote_item_option.code = ? ', 'info_buyRequest')
            ->where('main_table.store_id = ? ', $storeId);

        if ($collection->getSize() == 0) {
            throw new NoSuchEntityException(__('This cart item that was requested doesn\'t exist. Verify the item and try again.', $itemId));
        }
        $errorMessage = [];
        $successMessage = [];
        $status = false;
        foreach ($collection as $item) {
            $productId = $item['product_id'];
            $value = json_decode($item['value'], true);
            $qty = $item['qty'];
            $parentItemId = $item['parent_item_id'];

            $parentQuoteItem = $this->quoteItemFactory->create()->load($parentItemId);
            $parentQty = $parentQuoteItem->getQty();
            $parentProductId = $parentQuoteItem->getProductId();
            $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);

            if (array_key_exists('super_attribute', $value)) {
                $attribute = $value['super_attribute'];
                if ($parentItemId) {
                    $product = $this->_productRepository->getById($parentProductId);
                    $buyRequest = ['qty' => $parentQty, 'super_attribute' => $attribute];
                    $wishlist->addNewItem($product, $buyRequest);
                    $wishlist->save();
                    $status = true;
                    $successMessage[] = "" . $product->getName() . " has been moved to your wish list.";
                } else {
                    $product = $this->_productRepository->getById($productId);
                    $buyRequest = ['qty' => $qty, 'super_attribute' => $attribute];
                    $wishlist->addNewItem($product, $buyRequest);
                    $wishlist->save();
                    $status = true;
                    $successMessage[] = "" . $product->getName() . " has been moved to your wish list.";
                }
            } else {
                $product = $this->_productRepository->getById($productId);
                $buyRequest = ['qty' => $qty];
                $wishlist->addNewItem($product, $buyRequest);
                $wishlist->save();
                $status = true;
                $successMessage[] = "" . $product->getName() . " has been moved to your wish list.";
            }
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActiveForCustomer($customerId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('The %1 Cart doesn\'t contain the %2 item.', $quote->getId(), $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("The item couldn't be removed from the quote."));
        }
        return $this->showStatus($customerId, $status, $errorMessage, $successMessage);
    }

    /**
     * Get Super attribute details by the child id
     * @param int $childId
     * @return array
     */
    public function getChildSuperAttribute($childId)
    {
        // $childId = 2039;
        $parentIds = $this->resourceConfigurable->getParentIdsByChild($childId);
        if (!empty($parentIds)) {
            $skus = $this->resourceProduct->getProductsSku($parentIds);
        }
        $parentProduct = $this->getProduct($skus[0]['entity_id']);
        $childProduct = $this->getProduct($childId);
        $_attributes = $parentProduct->getTypeInstance(true)->getConfigurableAttributes($parentProduct);

        $attributesPair = [];
        foreach ($_attributes as $_attribute) {
            $attributeId = (int) $_attribute->getAttributeId();
            $attributeCode = $this->getAttributeCode($attributeId);
            $attributesPair[$attributeId] = (int) $childProduct->getData($attributeCode);
        }
        return $attributesPair;
    }

    /**
     * Get attribute code by attribute id
     * @param int $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributeCode(int $id)
    {
        return $this->attributeRepository->get($id)->getAttributeCode();
    }

    /**
     * Get Product Object by id
     * @param int $id
     * @return ProductInterface|null
     */
    public function getProduct(int $id)
    {
        $product = null;
        try {
            $product = $this->_productRepository->getById($id);
        } catch (NoSuchEntityException $exception) {
            $this->logger->error(__($exception->getMessage()));
        }
        return $product;
    }

    /**
     * get wishlist total count
     * @param string $customerId
     * @return int
     */
    public function getWishlistTotalCount($customerId)
    {
        $wishlist_collection = $this->wishlist->loadByCustomerId($customerId, true)->getItemCollection();
        return $wishlist_collection->getSize();
    }

    /**
     * @param string $customerId
     * @param array $errorMessage
     * @param array $successMessage
     * @param bool $status
     * @param int $itemId
     * @return \Mageplaza\HelloWorld\Api\Data\WishlistManagementInterface
     */
    private function showStatus($customerId, $status, array $errorMessage, array $successMessage, $itemId = 0)
    {
        $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);
        $wishlistManagement = $this->wishlistManagementInterface->create();
        $wishlistManagement->setErrorMessage($errorMessage);
        $wishlistManagement->setSuccessMessage($successMessage);
        $wishlistManagement->setStatus($status);
        $wishlistManagement->setCount($wishlist->getItemsCount());
        $wishlistManagement->setWishlistItemId($itemId);
        return $wishlistManagement;
    }

    /**
     * add wishlist product to cart method
     * @param int $customerId
     * @param int $productId
     * @param int $qty
     * @param array $superAttribute
     * @param bool $isConfigurable
     * @return \Magento\Quote\Model\Quote\Item
     */
    private function addWishlistProductToCart($customerId, $productId, $qty, $superAttribute, $isConfigurable)
    {
        $quote = $this->quoteRepository->getActiveForCustomer($customerId);
        $productObj = $this->productRepository->getById($productId);
        $item = $this->quoteItemFactory->create();
        $item->setSku($productObj->getSku());
        $item->setQty($qty);
        $item->setQuoteId($quote->getId());
        if (count($superAttribute) > 0 && $isConfigurable) {
            $configurableOptions = [];
            foreach ($superAttribute as $key => $value) {
                $option = $this->configurableItemOptionValueFactory->create();
                $option->setOptionId($key);
                $option->setOptionValue($value);
                $configurableOptions[] = $option;
            }
            $productOption = $this->productOptionFactory->create();
            $extensionAttribute = $productOption->getExtensionAttributes();
            $extensionAttribute->setConfigurableItemOptions($configurableOptions);
            $productOption->setExtensionAttributes($extensionAttribute);
            $item->setProductOption($productOption);
    
        }
     
        $this->quoteItemRepository->save($item);
        return true;
    }


     /**
     * get wishlist details list for customer
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MIT\Product\Api\Data\ProductResultListInterface
     */
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
            } else {
                $wishlistItemIdList[$productId] = array($wishlistItemId, $qty);
            } 
        }

        //filter

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
            $filterGroupList[] = $this->_filterGroupBuilder
                ->addFilter($filteredSku)
                ->create();
            $filterGroupList[] = $this->_filterGroupBuilder
                ->addFilter($filteredVisibility)
                ->create();
            $this->_searchCriteriaBuilder
                ->setFilterGroups($filterGroupList)
                ->create();
            $searchCriteria = $this->_searchCriteriaBuilder
                ->setFilterGroups($filterGroupList)
                ->create();
        //filter

        

        
        $setProduct = [];
        $connection = $this->resourceConnection->getConnection();         

        $productList = $this->productApiInterface->getList(0,$searchCriteria);

        $wishlist_collection = $this->wishlist->loadByCustomerId($customerId, true)->getItemCollection();
  

        foreach ($wishlist_collection as $item) {

            $parentId = $item->getProductId();
        
            $sku = $item->getProduct()->getSku();
            $tableName = $connection->getTableName('catalog_product_entity');
            $query = $connection->select()
                    ->from($tableName, ['entity_id'])
                    ->where('sku = ?', $sku);
            $entityId = $connection->fetchOne($query);
            $childId = $entityId;
        
            echo($parentId."_".$childId."_");

            $productItemId = $parentId;

            $setWishlistItemId = $wishlistItemIdList[$productItemId][0]; //OK
            $setQty = $wishlistItemIdList[$productItemId][1]; //OK
       
            if ($childId!=$parentId) {

                $product = $this->_productRepository->getById($parentId);

                $attributeArray = $product->getExtensionAttributes()->getConfigurableProductOptions();

                // print_r($attributeArray); 

                $setProductAttributes = [];

                $count=count($attributeArray); 

                echo ("count is - ".$count); 

                foreach ($attributeArray as $attributeList) {
                    // $attributeListId = $attributeList->getId();
                    $attributeListAttributeId = $attributeList->getAttributeId();
                    $attributeLisLabel = $attributeList->getLabel();
                    $result = array();

                    
                    $table1 = $connection->getTableName('catalog_product_entity_int');
                    $table2 = $connection->getTableName('eav_attribute');
                    $table3 = $connection->getTableName('eav_attribute_option_value');
                    $table4 = $connection->getTableName('eav_attribute_option_swatch');
                    $select = $connection->select()
                        ->from(
                            ['cp_int' => $table1],
                            []
                        )
                        ->join(
                            ['eav_attribute' => $table2],
                            'cp_int.attribute_id = eav_attribute.attribute_id',
                            []
                        )
                        ->join(
                            ['eav_attribute_option_value' => $table3],
                            'cp_int.value = eav_attribute_option_value.option_id',
                            ['option_value' => 'eav_attribute_option_value.value']
                        )
                        ->joinLeft(
                            ['eav_attribute_option_swatch' => $table4],
                            'eav_attribute_option_value.option_id = eav_attribute_option_swatch.option_id',
                            ['option_code' => 'eav_attribute_option_swatch.value']
                        )
                        ->where('eav_attribute.attribute_id = ?', $attributeListAttributeId)
                        ->where('cp_int.entity_id = ?', $childId)
                        ->limit(1);
                    $rows = $connection->fetchAll($select);

                    print_r($rows);

                    foreach ($rows as $row) {
                        $result[] = array(
                            'option_value' => $row['option_value'],
                            'option_code' => $row['option_code']
                        );

                        $setOptionValue = $row['option_value'];
                        $setOptionCode = $row['option_code'];
                        $productAttributes = $this->simpleProductDataFactory->create();
                        $productAttributes->setOptionName($attributeLisLabel);
                        $productAttributes->setOptionValue($setOptionValue);
                        $productAttributes->setOptionCode($setOptionCode);
                        $setProductAttributes[] = $productAttributes;
                        $item->setWishlistOptions($setProductAttributes);
                        $item->setWishlistItemId($childId);
                        $item->setWishlistQty($setQty);

                       // return($item); 
                    }
                } 

                echo ("isset".$childId); 
                echo("<br/>"); 

                // ///////////////size start             

                //     $attributeCodeSize = 'size'; 

                //     $tableSize = $connection->getTableName('eav_attribute');
                //     $selectAttributeIdSize = $connection->select()
                //         ->from($tableSize, ['attribute_id'])
                //         ->where('attribute_code = ?', $attributeCodeSize)
                //         ->limit(1);

                //     $attributeIdSize = $connection->fetchOne($selectAttributeIdSize);

                //     $attribute_idSize=$attributeIdSize; 
                //     $result = array();
                        
                //     $table1Size = $connection->getTableName('catalog_product_entity_int');
                //     $table2Size = $connection->getTableName('eav_attribute');
                //     $table3Size = $connection->getTableName('eav_attribute_option_value');
                //     $table4Size = $connection->getTableName('eav_attribute_option_swatch');
                //     $select = $connection->select()
                //             ->from(
                //                 ['cp_int' => $table1Size],
                //                 []
                //             )
                //             ->join(
                //                 ['eav_attribute' => $table2Size],
                //                 'cp_int.attribute_id = eav_attribute.attribute_id',
                //                 []
                //             )
                //             ->join(
                //                 ['eav_attribute_option_value' => $table3Size],
                //                 'cp_int.value = eav_attribute_option_value.option_id',
                //                 ['option_value' => 'eav_attribute_option_value.value']
                //             )
                //             ->joinLeft(
                //                 ['eav_attribute_option_swatch' => $table4Size],
                //                 'eav_attribute_option_value.option_id = eav_attribute_option_swatch.option_id',
                //                 ['option_code' => 'eav_attribute_option_swatch.value']
                //             )
                //             ->where('eav_attribute.attribute_id = ?', $attribute_idSize)
                //             ->where('cp_int.entity_id = ?', $childId)
                //             ->limit(1);
                //     $rows = $connection->fetchAll($select);
                //     print_r($rows); 
                //     foreach ($rows as $row) {
                //         $result[] = array(
                //             'size_value' => $row['option_value'],
                //             'size_code' => $row['option_code']
                //         );
                //         $setSizeValue = $row['option_value'];
                //         $setSizeCode = $row['option_code'];
                //         echo ("size_value".$setSizeValue);
                //         echo ("size_code".$setSizeCode);                 
                //     }

                // /////////////size end

                // ///////////////color start             

                //     $attributeCodeColor = 'color'; 

                //     $tableColor = $connection->getTableName('eav_attribute');
                //     $selectAttributeIdColor = $connection->select()
                //         ->from($tableColor, ['attribute_id'])
                //         ->where('attribute_code = ?', $attributeCodeColor)
                //         ->limit(1);

                //     $attributeIdColor = $connection->fetchOne($selectAttributeIdColor);

                //     $attribute_idColor=$attributeIdColor; 
                //     $result = array();
                        
                //     $table1Color = $connection->getTableName('catalog_product_entity_int');
                //     $table2Color = $connection->getTableName('eav_attribute');
                //     $table3Color = $connection->getTableName('eav_attribute_option_value');
                //     $table4Color = $connection->getTableName('eav_attribute_option_swatch');
                //     $select = $connection->select()
                //             ->from(
                //                 ['cp_int' => $table1Color],
                //                 []
                //             )
                //             ->join(
                //                 ['eav_attribute' => $table2Color],
                //                 'cp_int.attribute_id = eav_attribute.attribute_id',
                //                 []
                //             )
                //             ->join(
                //                 ['eav_attribute_option_value' => $table3Color],
                //                 'cp_int.value = eav_attribute_option_value.option_id',
                //                 ['option_value' => 'eav_attribute_option_value.value']
                //             )
                //             ->joinLeft(
                //                 ['eav_attribute_option_swatch' => $table4Color],
                //                 'eav_attribute_option_value.option_id = eav_attribute_option_swatch.option_id',
                //                 ['option_code' => 'eav_attribute_option_swatch.value']
                //             )
                //             ->where('eav_attribute.attribute_id = ?', $attribute_idColor)
                //             ->where('cp_int.entity_id = ?', $childId)
                //             ->limit(1);
                //     $rows = $connection->fetchAll($select);
                //     print_r($rows); 
                //     foreach ($rows as $row) {
                //         $result[] = array(
                //             'color_value' => $row['option_value'],
                //             'color_code' => $row['option_code']
                //         );
                //         $setColorValue = $row['option_value'];
                //         $setColorCode = $row['option_code'];
                //         echo ("color_value".$setColorValue);
                //         echo ("color_code".$setColorCode);                 
                //     }

                // /////////////size end

                // /////////////image start

                //     $attributeCodeImage = 'image';
                //     $catalogProductEntityIntTable = $connection->getTableName('catalog_product_entity_int');
                //     $catalogProductEntityVarcharTable = $connection->getTableName('catalog_product_entity_varchar');
                //     $eavAttributeTable = $connection->getTableName('eav_attribute');

                //     $subquery = $connection->select()
                //         ->from($eavAttributeTable, ['attribute_id'])
                //         ->where('attribute_code = ?', $attributeCodeImage);
                //     $selectImage = $connection->select()
                //         ->from(
                //             ['cpev' => $catalogProductEntityVarcharTable],
                //             ['image_file_path' => 'cpev.value']
                //         )
                //         ->join(
                //             ['cpei' => $catalogProductEntityIntTable],
                //             'cpei.entity_id = cpev.entity_id',
                //             []
                //         )
                //         ->where('cpei.entity_id = ?', $childId)
                //         ->where('cpev.attribute_id IN (?)', new \Zend_Db_Expr($subquery))
                //         ->limit(1);

                //     $imageFilePath = $connection->fetchOne($selectImage);
                //     $baseUrl = $this->storeManager->getStore()->getBaseUrl();
                //     $simpleProductImagePath = $baseUrl . $imageFilePath;

                //     echo("------".$simpleProductImagePath."----------------"); 
                
                
                // ///////////////image end

        //     $productAttributes = $this->simpleProductDataFactory->create();
        //    // $productAttributes->setOptionName($attributeLisLabel);
        //     $productAttributes->setSizeValue($setSizeValue);
        //     $productAttributes->setSizeCode($setSizeCode);
        //     $productAttributes->setColorValue($setColorValue);
        //     $productAttributes->setColorCode($setColorCode);
        //     $productAttributes->setImagePathUrl($simpleProductImagePath);
        //     $setProductAttributes[] = $productAttributes;

            
        //     $item->setWishlistOptions($setProductAttributes);
        //     $item->setWishlistItemId($childId);
        //     $item->setWishlistQty($setQty);
               
            } else {

                // $setWishlistItemId = $wishlistItemIdList[$productItemId][0];
                // $setQty = $wishlistItemIdList[$productItemId][1];
                // $item->setWishlistItemId($setWishlistItemId);
                // $item->setWishlistQty($setQty);
                // $setProduct[] = $item;
              
            }
        
            
            
        }
      //  return $setProduct; 

    
        
        $searchResult->setItems($setProduct);
        $searchResult->setTotalCount($this->getWishlistTotalCount($customerId));
        return $searchResult;
    }


}

