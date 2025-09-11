<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper\Debug;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @internal
 */
class ConfigExtractor
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $config;

    /**
     * @param ScopeConfigInterface $config
     * @codeCoverageIgnore
     */
    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Extracts a configuration
     *
     * It's possible to filter by substrings in path, scope type and scope code
     *
     * @param array    $includes
     * @param array    $excludes
     * @param string   $scopeType
     * @param int|null $scopeCode
     * @return array
     */
    public function getConfig(
        array  $includes = [],
        array  $excludes = [],
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        ?int   $scopeCode = null
    ): array {
        // get all config paths
        $rawConfig = $this->config->getValue('', $scopeType, $scopeCode);
        $items = $this->convertArrayToOneDimension($rawConfig);
        $array = $this->joinItems($items);

        foreach ($includes as $include) {
            $array = $this->filterContains($include, $array);
        }

        foreach ($excludes as $exclude) {
            $array = $this->filterNotContains($exclude, $array);
        }

        ksort($array);
        return $array;
    }

    /**
     * Filter array by string occurrence in key
     *
     * @param string $include
     * @param array  $array
     * @return array
     */
    private function filterContains(string $include, array $array): array
    {
        return array_filter($array, function ($key) use ($include) {
            if (strpos($key, $include) !== false) {
                return true;
            }

            return false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Filter array by string non-occurrence in key
     *
     * @param string $exclude
     * @param array  $array
     * @return array
     */
    private function filterNotContains(string $exclude, array $array): array
    {
        return array_filter($array, function ($key) use ($exclude) {
            if (strpos($key, $exclude) === false) {
                return true;
            }

            return false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Takes out the last element of each sub-array, which is the config value, and joins the remainder items
     *
     * @param array $array
     * @return array
     */
    private function joinItems(array $array): array
    {
        $converted = [];

        foreach ($array as $item) {
            $value = array_pop($item);
            $key = join('/', $item);
            $converted[$key] = $value;
        }

        return $converted;
    }

    /**
     * The raw config array is transformed into a flat array
     *
     * @param array $array
     * @return array
     */
    private function convertArrayToOneDimension(array $array): array
    {
        $elements = [];
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $elements[] = [$key, $value];
                continue;
            }
            $subArray = $this->convertArrayToOneDimension($value);
            foreach ($subArray as $subItem) {
                array_unshift($subItem, $key);
                $elements[] = $subItem;
            }
        }
        return $elements;
    }
}
