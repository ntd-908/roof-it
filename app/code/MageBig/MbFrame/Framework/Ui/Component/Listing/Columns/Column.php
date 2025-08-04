<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageBig\MbFrame\Framework\Ui\Component\Listing\Columns;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * @api
 * @since 100.0.2
 */
class Column extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     * @throws LocalizedException
     */
    public function prepare()
    {
        if (!$this->isMsrpPriceEnabled()) {
            $dataType = $this->getData('config/dataType');

            if ($dataType && str_contains($this->getData('config/component'), 'js/product/list/columns/msrp-price')) {
                $this->setData(
                    'config',
                    array_replace_recursive(
                        (array)$this->getData('config'),
                        [
                            'component' => 'Magento_Catalog/js/product/list/columns/price-box',
                            'bodyTmpl' => 'Magento_Catalog/product/price/price_box'
                        ]
                    )
                );
            }
        }

        parent::prepare();
    }

    /**
     * Is Msrp Price enabled
     *
     * @return bool
     */
    private function isMsrpPriceEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            \Magento\Msrp\Model\Config::XML_PATH_MSRP_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
