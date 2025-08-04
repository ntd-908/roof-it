<?php

namespace MageBig\MbLib\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Module\ModuleListInterface;

class ExtensionsInfo extends Field
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param Curl $curl
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        Curl $curl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleList = $moduleList;
        $this->curl = $curl;
    }

    /**
     * Render html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $products = $this->getProductData();

        if (!$products) {
            return '';
        }

        $html = '<div class="mb-product-wrap" style="display: flex; flex-wrap: wrap">';
        foreach ($products as $productKey => $product) {
            $html .= '<div class="mb-product" style="max-width: 300px; margin: 10px">
                <a href="' . $product->product_url . '">
                <img src="' . $product->img_url . '" alt="" width="300" height="300">
                </a>
                <h2 style="margin: 0"><a href="' . $product->product_url . '">' . $product->product_name . '</a></h2>
                <div class="version">Version: ' . $product->version . '</div>
            </div>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Get product data
     *
     * @return mixed
     */
    public function getProductData()
    {
        try {
            //$this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $type = "application/json";
            $headers = ["Content-Type" => $type];
            $this->curl->setHeaders($headers);
            $url = 'https://www.magebig.com/media/product-data.json';
            $this->curl->get($url);
            $body = $this->curl->getBody();

            return json_decode($body);
        } catch (\Exception $e) {
            return false;
        }
    }
}
