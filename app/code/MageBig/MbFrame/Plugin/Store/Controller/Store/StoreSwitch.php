<?php

namespace MageBig\MbFrame\Plugin\Store\Controller\Store;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class StoreSwitch
{
    /**
     * @var StoreCookieManagerInterface
     */
    protected $storeCookieManager;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreCookieManagerInterface $storeCookieManager,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->storeCookieManager = $storeCookieManager;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
    }

    /**
     * Set store code before execute
     *
     * @param $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\State\InitException
     */
    public function beforeExecute($subject)
    {
        $fromStoreCode = $this->_request->getParam(
            '___from_store',
            $this->storeCookieManager->getStoreCodeFromCookie()
        );

        if (!$fromStoreCode) {
            $this->_request->setParams(['___from_store' => $this->_storeManager->getStore()->getCode()]);
        }
    }
}
