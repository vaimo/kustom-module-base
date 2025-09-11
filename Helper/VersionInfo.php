<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper;

use Composer\InstalledVersions;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\ResourceInterface;

/**
 * @internal
 */
class VersionInfo
{
    public const M2_KLARNA = 'klarna/m2-klarna';

    /**
     * @var State
     */
    private $appState;

    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var InstalledVersions
     */
    private InstalledVersions $installedVersions;

    /**
     * @var File
     */
    private File $fileDriver;

    /**
     * VersionInfo constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     * @param State $appState
     * @param ResourceInterface $resource
     * @param InstalledVersions $installedVersions
     * @param File $fileDriver
     * @codeCoverageIgnore
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        State $appState,
        ResourceInterface $resource,
        InstalledVersions $installedVersions,
        File $fileDriver
    ) {
        $this->appState = $appState;
        $this->productMetadata = $productMetadata;
        $this->resource = $resource;
        $this->installedVersions = $installedVersions;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Get module version info
     *
     * @param string $packageName
     * @return string|false
     */
    public function getVersion(string $packageName)
    {
        return $this->resource->getDataVersion($packageName);
    }

    /**
     * Gets the current MAGE_MODE setting
     *
     * @return string
     */
    public function getMageMode(): string
    {
        return $this->appState->getMode();
    }

    /**
     * Gets the current Magento version
     *
     * @return string
     */
    public function getMageVersion(): string
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Gets the Magento name
     *
     * @return string
     */
    public function getMageName(): string
    {
        return $this->productMetadata->getName();
    }

    /**
     * Gets the current Magento Edition
     *
     * @return string
     */
    public function getMageEdition(): string
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Creates the module version string
     *
     * @param string $version
     * @param string $caller
     * @return string
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function getModuleVersionString(string $version, string $caller): string
    {
        return sprintf(
            "%s;Base/%s",
            $version,
            $this->getVersion('Klarna_Base')
        );
    }

    /**
     * Get Magento information
     *
     * @return string
     */
    public function getMageInfo(): string
    {
        return sprintf('Magento %s/%s %s mode', $this->getMageEdition(), $this->getMageVersion(), $this->getMageMode());
    }

    /**
     * Getting back the full m2-klarna version: m2-klarna:X.Y.Z
     *
     * @return string
     */
    public function getFullM2KlarnaVersion(): string
    {
        $version = $this->getM2KlarnaVersion();
        return sprintf('%s/%s', self::M2_KLARNA, $version);
    }

    /**
     * Getting back the m2-klarna version: X.Y.Z
     *
     * @return string
     */
    public function getM2KlarnaVersion(): string
    {
        $composerVersion = $this->getComposerPackageVersion(self::M2_KLARNA);
        return !empty($composerVersion) ? $composerVersion : $this->getFallbackExtensionVersion();
    }

    /**
     * Return a given composer package version if it exists, empty string otherwise.
     *
     * @param string $packageName
     * @return string
     */
    public function getComposerPackageVersion(string $packageName): string
    {
        if ($this->installedVersions->isInstalled($packageName)) {
            return $this->installedVersions->getPrettyVersion($packageName);
        }

        return '';
    }

    /**
     * Reads the "version" key in the m2-klarna-version.json if it exists, otherwise returns empty string
     *
     * @return string
     */
    public function getFallbackExtensionVersion(): string
    {
        try {
            $versionFilePath = __DIR__ . '/m2-klarna-version.json';
            $jsonData = $this->fileDriver->fileGetContents($versionFilePath);
            if ($this->fileDriver->isExists($versionFilePath) && $jsonData) {
                $data = json_decode($jsonData, true) ?? [];
                if (isset($data['version'])) {
                    return $data['version'];
                }
            }
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }
}
