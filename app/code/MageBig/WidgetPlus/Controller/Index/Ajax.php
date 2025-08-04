<?php

namespace MageBig\WidgetPlus\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Ajax extends \Magento\Framework\App\Action\Action
{
    protected $layout;
    protected $json;
    protected $registry;
    protected $productRepository;

    /**
     * @param Context $context
     * @param LayoutInterface $layout
     * @param Json $json
     */
    public function __construct(
        Context $context,
        LayoutInterface $layout,
        ProductRepositoryInterface $productRepository,
        Registry $registry,
        Json $json
    ) {
        parent::__construct($context);
        $this->layout = $layout;
        $this->json = $json;
        $this->registry = $registry;
        $this->productRepository = $productRepository;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $html = '';

        try {
            $arguments = $this->getRequest()->getParam('parameters');
            if ($arguments) {
                $data = $this->json->unserialize($arguments);

                if (isset($data['current_product_id']) && $data['current_product_id']) {
                    $product = $this->productRepository->getById($data['current_product_id']);
                    $this->registry->register('product', $product);
                    $this->registry->register('current_product', $product);
                    unset($data['current_product_id']);
                }

                $type = $data['type'];
                unset($data['type']);

                $blockName = 'widget-ajax-' . uniqid();
                $html = $this->layout->createBlock(
                    $type,
                    $blockName,
                    ['data' => $data]
                )->toHtml();
            }
        } catch (\Exception $e) {
        }

        $this->getResponse()->setBody($html);
    }
}
