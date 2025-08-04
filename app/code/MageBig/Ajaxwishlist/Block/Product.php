<?php

namespace MageBig\Ajaxwishlist\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Wishlist\Model\Item;

class Product extends AbstractProduct
{
    /** @var ItemResolverInterface */
    private $itemResolver;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        array $data = [],
        ItemResolverInterface $itemResolver = null
    ){
        $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(ItemResolverInterface::class);

        parent::__construct(
            $context,
            $data
        );
    }
    /**
     * Retrieve current product model
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->_coreRegistry->registry('product_wishlist');
    }

    /**
     * Identify the product from which thumbnail should be taken.
     *
     * @param Item $item
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductForThumbnail(Item $item)
    {
        return $this->itemResolver->getFinalProduct($item);
    }
}
