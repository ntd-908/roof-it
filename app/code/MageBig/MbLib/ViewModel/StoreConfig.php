<?php

namespace MageBig\MbLib\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Module\Manager;

class StoreConfig implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    public $config;

    /**
     * @var Manager
     */
    public $manager;

    /**
     * @param ScopeConfigInterface $config
     * @param Manager $manager
     */
    public function __construct(
        ScopeConfigInterface $config,
        Manager $manager
    ) {
        $this->config = $config;
        $this->manager = $manager;
    }

    /**
     * Get store value
     *
     * @param string $path
     * @return mixed
     */
    public function getValue(string $path)
    {
        return $this->config->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check enabled module
     *
     * @param string $module
     * @return bool
     */
    public function isEnableModule(string $module)
    {
        return $this->manager->isEnabled($module);
    }
}
