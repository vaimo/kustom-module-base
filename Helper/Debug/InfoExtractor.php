<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper\Debug;

use Klarna\Support\Model\StoreTreeExtractor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @internal
 */
class InfoExtractor
{
    /**
     * @var ConfigExtractor
     */
    private ConfigExtractor $configExtractor;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param ConfigExtractor       $configExtractor
     * @param StoreManagerInterface $storeManager
     * @codeCoverageIgnore
     */
    public function __construct(
        ConfigExtractor $configExtractor,
        StoreManagerInterface $storeManager
    ) {
        $this->configExtractor = $configExtractor;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns klarna information
     *
     * @return array
     */
    public function getAllKlarnaConfigs(): array
    {
        return $this->getConfigs('klarna', ['klarna'], ['shared_secret']);
    }

    /**
     * Returns tax information
     *
     * @return array
     */
    public function getTaxInfo(): array
    {
        return $this->getConfigs('tax', ['tax']);
    }

    /**
     * Extracts default config and all scope configs
     *
     * @param string $type
     * @param array  $contains
     * @param array  $excludes
     * @return array
     */
    public function getConfigs(string $type, array $contains = [], array $excludes = []): array
    {
        $configs = [];

        $config = $this->configExtractor->getConfig(
            $contains,
            $excludes,
        );
        $configs[$this->getFileName($type, 'default')] = $config;

        $scopes = [
            ScopeInterface::SCOPE_WEBSITE => $this->storeManager->getWebsites(),
            ScopeInterface::SCOPE_STORE => $this->storeManager->getStores()
        ];

        foreach ($scopes as $scope => $entries) {
            $scopeConfigs = $this->getScopeConfigs(
                $type,
                $scope,
                $entries,
                $contains,
                $excludes
            );
            foreach ($scopeConfigs as $key => $config) {
                $configs[$key] = $config;
            }
        }

        return $configs;
    }

    /**
     * Extracts scope configs
     *
     * @param string $type
     * @param string $scope
     * @param array  $entries
     * @param array  $includes
     * @param array  $excludes
     * @return array
     */
    private function getScopeConfigs(
        string $type,
        string $scope,
        array  $entries,
        array  $includes,
        array  $excludes
    ): array {
        $configs = [];

        foreach (array_keys($entries) as $id) {
            $fileName = $this->getFileName($type, $scope, $id);
            $configs[$fileName] = $this->configExtractor->getConfig(
                $includes,
                $excludes,
                $scope,
                $id
            );
        }

        return $configs;
    }

    /**
     * Get file name
     *
     * @param string   $type
     * @param string   $scope
     * @param int|null $id
     * @return string
     */
    private function getFileName(string $type, string $scope, ?int $id = null): string
    {
        return implode('_', array_filter([$type, $scope, $id]));
    }
}
