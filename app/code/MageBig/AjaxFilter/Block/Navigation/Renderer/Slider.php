<?php

namespace MageBig\AjaxFilter\Block\Navigation\Renderer;

use MageBig\AjaxFilter\Model\Layer\Filter\Decimal;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;

class Slider extends AbstractRenderer
{
    /**
     * The Data role, used for Javascript mapping of slider Widget
     *
     * @var string
     */
    protected $dataRole = "range-slider";

    /**
     * @var Json
     */
    private $jsonEncoder;

    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param CatalogHelper $catalogHelper
     * @param Json $jsonEncoder
     * @param FormatInterface $localeFormat
     * @param CollectionFactory $collection
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        CatalogHelper $catalogHelper,
        Json $jsonEncoder,
        FormatInterface $localeFormat,
        CollectionFactory $collection,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $catalogHelper, $data);

        $this->jsonEncoder = $jsonEncoder;
        $this->localeFormat = $localeFormat;
        $this->collectionFactory = $collection;
        $this->registry = $registry;
    }

    public function getPriceRange()
    {
        $collection = $this->collectionFactory->create();

        $collection->addAttributeToSelect('price');
        $collection->setOrder('price', 'DESC');
        $collection->setVisibility([2, 4]);

        $category = $this->registry->registry('current_category');
        if (!($category instanceof \Magento\Catalog\Model\Category)) {
            $category = $this->registry->registry('current_category_filter');
        }

        if ($category instanceof \Magento\Catalog\Model\Category) {
            $collection->addCategoryFilter($category);
        }

        $max = $collection->getMaxPrice();
        $min = $collection->getMinPrice();

        return [
            'max' => $max ?: 0,
            'min' => $min ?: 0
        ];
    }

    /**
     * Return the config of the price slider JS widget.
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = $this->getConfig();

        return $this->jsonEncoder->serialize($config);
    }

    /**
     * Retrieve the data role
     *
     * @return string
     */
    public function getDataRole()
    {
        $filter = $this->getFilter();

        return $this->dataRole . "-" . $filter->getRequestVar();
    }

    /**
     * {@inheritDoc}
     */
    protected function canRenderFilter()
    {
        return $this->getFilter() instanceof Decimal;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getFieldFormat()
    {
        $format = $this->localeFormat->getPriceFormat();

        $attribute = $this->getFilter()->getAttributeModel();

        $format['pattern'] = (string)$attribute->getDisplayPattern();
        $format['precision'] = (int)$attribute->getDisplayPrecision();
        $format['requiredPrecision'] = (int)$attribute->getDisplayPrecision();
        $format['integerRequired'] = (int)$attribute->getDisplayPrecision() > 0;

        return $format;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getConfig()
    {
        $price = $this->getPriceRange();

        $config = [
            'minValue' => $price['min'],
            'maxValue' => $price['max'] + 1,
            'currentValue' => $this->getCurrentValue(),
            'fieldFormat' => $this->getFieldFormat(),
            'actionUrl' => $this->getActionUrl(),
            'code' => $this->getCode()
        ];

        return $config;
    }

    /**
     * Returns min value of the slider.
     *
     * @return int
     */
    public function getMinValue()
    {
        return $this->getFilter()->getMinValue();
    }

    /**
     * Returns max value of the slider.
     *
     * @return int
     */
    public function getMaxValue()
    {
        return $this->getFilter()->getMaxValue() + 1;
    }

    /**
     * Returns values currently selected by the user.
     *
     * @return array
     */
    public function getCurrentValue()
    {
        $currentValue = $this->getFilter()->getCurrentValue();

        if (!is_array($currentValue)) {
            $currentValue = [];
        }

        if (!isset($currentValue['from']) || $currentValue['from'] === '') {
            $currentValue['from'] = $this->getMinValue();
        }

        if (!isset($currentValue['to']) || $currentValue['to'] === '') {
            $currentValue['to'] = $this->getMaxValue();
        }

        return $currentValue;
    }
}
