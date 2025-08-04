<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace MageBig\WidgetPlus\Block;

use MageBig\WidgetPlus\Model\ResourceModel\Widget\CollectionFactory;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\Http\Context as ContextHttp;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Catalog Products List widget block
 * Class ProductsList.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
    const CACHE_TAGS = 'WIDGETPLUS_PRODUCT';

    /**
     * @var ContextAlias
     */
    protected $httpContext;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var
     */
    protected $_productCollection;

    /**
     * @var Category
     */
    protected $categoryModel;

    /**
     * @var Json
     */

    /**
     * @var FormKey
     */
    private $formKey;

    private $serializer;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param Context $context
     * @param ContextHttp $httpContext
     * @param CollectionFactory $collectionFactory
     * @param Category $categoryModel
     * @param FormKey $formKey
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        Context $context,
        ContextHttp $httpContext,
        CollectionFactory $collectionFactory,
        Category $categoryModel,
        FormKey $formKey,
        array $data = [],
        Json $serializer = null
    ) {
        $this->httpContext = $httpContext;
        $this->_collectionFactory = $collectionFactory;
        $this->categoryModel = $categoryModel;
        $this->formKey = $formKey;
        parent::__construct($context, $data);
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)->addColumnCountLayoutDepend(
            '1column',
            5
        )->addColumnCountLayoutDepend('2columns-left', 4)->addColumnCountLayoutDepend(
            '2columns-right',
            4
        )->addColumnCountLayoutDepend('3columns', 3);
    }

    /**
     * Get block cache lifetime
     *
     * @return int|bool|null
     */
    protected function getCacheLifetime()
    {
        if (!$this->hasData('cache_lifetime')) {
            return null;
        }

        $cacheLifetime = $this->getData('cache_lifetime');
        if (false === $cacheLifetime || null === $cacheLifetime) {
            return $cacheLifetime;
        }

        return (int)$cacheLifetime;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCacheKeyInfo()
    {
        return [
            'MAGEBIG_WIDGETPLUS_PRODUCT',
            $this->getPriceCurrency()->getCurrency()->getCode(),
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            $this->serializer->serialize($this->getRequest()->getParams()),
            $this->getWidgetId(),
            $this->getTemplateFile(),
            'base_url' => $this->getBaseUrl()
        ];
    }

    /**
     * @return PriceCurrencyInterface|mixed
     */
    private function getPriceCurrency()
    {
        if ($this->priceCurrency === null) {
            $this->priceCurrency = \Magento\Framework\App\ObjectManager::getInstance()->get(PriceCurrencyInterface::class);
        }
        return $this->priceCurrency;
    }

    public function getJsonConfig()
    {
        $data = $this->getData();
        $productType = $this->getData('product_type');

        if ($data['is_ajax'] && ($productType == 'related' || $productType == 'upsell')) {
            $data['current_product_id'] = $this->getProduct()->getId();
        }

        $data['template'] = $data['module_name'] . '::' . $this->getTemplate();
        $data['is_ajax'] = 0;
        unset($data['cache_tags']);
        unset($data['module_name']);

        return $this->serializer->serialize($data);
    }

    public function getAjaxUrl()
    {
        return $this->getUrl('widgetplus/index/ajax');
    }

    public function getOffset()
    {
        $value = (int)$this->getData('offset');

        return $value ?: 200;
    }

    public function getPlaceholder()
    {
        $value = $this->getData('placeholder');

        if ($value == "" || (int)$value < 0) {
            return 300;
        }

        return (int)$value;
    }

    public function getBlockHtml($name)
    {
        if ($name == 'formkey') {
            return '<input name="form_key" type="hidden" value="' . $this->formKey->getFormKey() . '" >';
        }
        return parent::getBlockHtml($name); // TODO: Change the autogenerated stub
    }

    /**
     * @return int|string
     */
    public function getWidgetId()
    {
        $widgetId = crc32($this->serializer->serialize($this->getData()));
        $widgetId = 'widgetplus-' . $widgetId;

        return $widgetId;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        return $priceRender->render(
            FinalPrice::PRICE_CODE,
            $product,
            $arguments
        );
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null|void
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->initializeProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null|void
     */
    protected function initializeProductCollection()
    {
        $limit = (int)$this->getData('limit');
        $value = $this->getData('product_type');
        $params = [];

        if ($this->getData('period')) {
            $params['period'] = $this->getData('period');
        }
        if ($this->getData('category_ids')) {
            $params['category_ids'] = explode(',', $this->getData('category_ids'));
        }
        if ($this->getData('product_ids')) {
            $params['product_ids'] = explode(',', $this->getData('product_ids'));
        }
        if ($this->getCustomerId()) {
            $params['customer_id'] = $this->getCustomerId();
        }

        $collection = $this->_collectionFactory->create()->getProducts('product', $value, $params, $limit);

        return $collection;
    }

    /**
     * Retrieve loaded collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * @param $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;

        return $this;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->_getProductCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }

        return $identities;
    }
}
