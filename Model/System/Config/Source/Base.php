<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Config\Source;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * @internal
 */
class Base implements OptionSourceInterface
{
    /**
     * @var DataInterface
     */
    private $config;
    /**
     * Determines which config option to pull from
     *
     * @var string
     */
    private $optionName;

    /**
     * Base constructor.
     *
     * @param DataInterface $config
     * @param string        $optionName
     * @codeCoverageIgnore
     */
    public function __construct(DataInterface $config, string $optionName = '')
    {
        $this->config = $config;
        $this->optionName = $optionName;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $values = $this->config->get($this->optionName);

        if ($values) {
            foreach ($values as $name => $value) {
                $options[] = [
                    'label' => $value['label'],
                    'value' => $name
                ];
            }
        }

        return $options;
    }

    /**
     * Getting back the option name.
     *
     * @return string
     */
    public function getOptionName(): string
    {
        return $this->optionName;
    }

    /**
     * Setting the option name.
     *
     * @param string $optionName
     */
    public function setOptionName(string $optionName): void
    {
        $this->optionName = $optionName;
    }
}
