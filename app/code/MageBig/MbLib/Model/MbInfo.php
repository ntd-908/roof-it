<?php

namespace MageBig\MbLib\Model;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;

class MbInfo
{
    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Curl $curl
     */
    private $curl;

    /**
     * @param ProductMetadataInterface $metadata
     * @param StoreManagerInterface $storeManager
     * @param Curl $curl
     */
    public function __construct(
        ProductMetadataInterface $metadata,
        StoreManagerInterface $storeManager,
        Curl $curl
    ) {
        $this->metadata = $metadata;
        $this->storeManager = $storeManager;
        $this->curl = $curl;
    }

    /**
     * Load data
     *
     * @param array $data
     * @return false|mixed
     */
    public function load(array $data)
    {
        try {
            //$this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $mu1iH = "\141\x70\160\x6c\x69\143\x61\164\x69\x6f\x6e\57\152\163\157\156";
            $kZo7h = "\115\157\172\x69\154\x6c\x61\x2f\65\56\60\x20" .
                "\50\127\x69\x6e\144\157\x77\163\x20\x4e\124\40\x31\x30\x2e\x30\x3b\x20" .
                "\x57\x69\156\66\64\73\x20\170\66\x34\x29\x20" .
                "\x43\150\162\157\x6d\x65\x2f\x31\63\x31\56\60\x2e\60\x2e\x30\x20" .
                $data["\144\x6f\x6d\x61\x69\156\x73"];
            $fu2bq = ["\x43\157\x6e\x74\145\x6e\164\x2d\x54\x79\x70\145" => $mu1iH];
            $fu2bq["\x75\x73\x65\162\x2d\141\147\145\x6e\x74"] = $kZo7h;
            $this->curl->setHeaders($fu2bq);
            $data["\x65\144\x69\164\x69\x6f\156"] = $this->metadata->getEdition();
            $d4GnZ = "\x68\164\x74\160\x73\x3a\57\x2f\x77\x77\x77\x2e\x6d\x61\147\145\142\151\147" .
                "\x2e\x63\157\155\57\162\x65\163\x74\57\x64\x65\x66\x61\165\154\x74" .
                "\x2f\x56\61\x2f\155\x61\x67\x65\142\151\147\57\x6c\x69\143\145\x6e\163\x65";
            $KTMLs = json_encode($data);
            $this->curl->post($d4GnZ, $KTMLs);
            $NoZun = $this->curl->getBody();
            return json_decode($NoZun, true);
        } catch (\Exception $e) {
            return false;
        }
    }
}
