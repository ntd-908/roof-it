<?php

namespace MageBig\MbLib\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use MageBig\MbLib\Model\GetVersion;

class ModuleInfo extends Field
{
    /**
     * @var GetVersion
     */
    protected $module;

    /**
     * @param Context $context
     * @param GetVersion $module
     * @param array $data
     */
    public function __construct(
        Context $context,
        GetVersion $module,
        array $data = []
    ) {
        $this->module = $module;
        parent::__construct($context, $data);
    }

    /**
     * Return info block html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $moduleCode = $this->getModuleName();
        $version = $this->module->getVersion($moduleCode);

        $html = '<div style="padding:10px;background-color:#f2f2f2;border:1px solid #ccc;margin-bottom:5px;">
            ' . $this->getModuleTitle($moduleCode);
        if ($this->getModuleUrl()) {
            $html .= '<a href="' . $this->getModuleUrl() . '" target="_blank">' . ' v' . $version . '</a>';
        } else {
            $html .= '<strong> v' . $version . '</strong>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Return extension url
     *
     * @return string
     */
    protected function getModuleUrl()
    {
        return 'https://www.magebig.com/';
    }

    /**
     * Return extension title
     *
     * @param string $code
     * @return string
     */
    protected function getModuleTitle(string $code)
    {
        return ucwords(str_replace('MageBig_', ' ', $code)) . ' Extension';
    }
}
