<?php
declare(strict_types=1);

namespace MageBig\MbLib\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\ModuleListInterface;

class GetVersion
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var array
     */
    private $version = [];

    /**
     * @param SerializerInterface $serializer
     * @param File $file
     * @param Reader $moduleReader
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        SerializerInterface $serializer,
        File $file,
        Reader $moduleReader,
        ModuleListInterface $moduleList
    ) {
        $this->serializer = $serializer;
        $this->file = $file;
        $this->moduleReader = $moduleReader;
        $this->moduleList = $moduleList;
    }

    /**
     * Get module version
     *
     * @param string $moduleCode
     * @return string
     */
    public function getVersion(string $moduleCode): string
    {
        if (!isset($this->version[$moduleCode])) {
            $module = $this->moduleList->getOne($moduleCode);
            if (!$module) {
                $this->version[$moduleCode] = '';
            } else {
                $fileDir = $this->moduleReader->getModuleDir('', $moduleCode) . '/composer.json';
                $data = $this->file->read($fileDir);

                try {
                    $data = $this->serializer->unserialize($data);
                } catch (\Exception $e) {
                    $data['version'] = null;
                }

                if (empty($data['version'])) {
                    $data['version'] = !empty($module['setup_version']) ? $module['setup_version'] : '';
                }

                $this->version[$moduleCode] = $data['version'];
            }
        }

        return $this->version[$moduleCode];
    }
}
