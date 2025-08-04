<?php

namespace MageBig\WidgetPlus\Block;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;

class StaticPlus extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    const CACHE_GROUP = 'WIDGETPLUS_STATICPLUS';

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * Block factory
     *
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param BlockFactory $blockFactory
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        FilterProvider $filterProvider,
        BlockFactory $blockFactory,
        Json $json,
        array $data = []
    ) {
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
        $this->json = $json;
        parent::__construct($context, $data);

        $this->addData([
            'cache_tags' => [self::CACHE_GROUP, AbstractBlock::CACHE_GROUP],
        ]);
    }

    public function getStatic()
    {
        $blockId = $this->getData('block_id');
        $html = '';

        if ($blockId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockId);
            if ($block->isActive()) {
                $html = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
            }
        }

        return $html;
    }

    public function getJsonConfig()
    {
        $data = $this->getData();
        $data['template'] = $data['module_name'] . '::' . $this->getTemplate();
        $data['is_ajax'] = 0;
        unset($data['cache_tags']);
        unset($data['module_name']);

        return $this->json->serialize($data);
    }

    public function getAjaxUrl () {
        return $this->getUrl('widgetplus/index/ajax');
    }

    public function getOffset () {
        $value = (int) $this->getData('offset');

        return $value ?: 200;
    }

    public function getPlaceholder () {
        $value = $this->getData('placeholder');

        if ($value == "" || (int) $value < 0) {
            return 300;
        }

        return (int) $value;
    }

    public function getWidgetId()
    {
        $widgetId = crc32($this->getData('block_id'));
        $widgetId = 'widgetplus-block-' . $widgetId;

        return $widgetId;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_' . $this->getData('block_id')];
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            self::CACHE_GROUP,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->getWidgetId(),
            serialize($this->getRequest()->getParams()),
        ];
    }
}
