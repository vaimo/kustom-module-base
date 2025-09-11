<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper\Debug;

/**
 * @internal
 */
class DebugDataObject
{
    /**
     * @var array
     */
    private array $data = [];
    /**
     * @var array
     */
    private array $modulesToIgnore = [];

    /**
     * Get the data object
     *
     * @return array
     */
    public function getData(): array
    {
        return array_filter($this->data, function ($modulesName) {
            return $this->dataShouldBeCollected($modulesName);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Add the given data to the data object if the module is not in the ignore list
     *
     * @param string $moduleName
     * @param string $data TODO:: we should change this to array
     * @return void
     */
    public function addData(string $moduleName, string $data): void
    {
        if ($this->dataShouldBeCollected($moduleName)) {
            $this->data[$moduleName] = $data;
        }
    }

    /**
     * Add the given modules to the ignore list
     *
     * @param string[] $modules
     * @return void
     */
    public function ignoreModules(array $modules): void
    {
        $this->modulesToIgnore = array_merge($this->modulesToIgnore, $modules);
    }

    /**
     * Check if the data should be collected or ignored
     *
     * @param string $moduleName
     * @return bool
     */
    private function dataShouldBeCollected(string $moduleName): bool
    {
        return !in_array(strtolower($moduleName), array_map('strtolower', $this->modulesToIgnore));
    }
}
