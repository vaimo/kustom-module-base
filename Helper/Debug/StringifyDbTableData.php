<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper\Debug;

use Magento\Framework\Serialize\Serializer\Json;

/**
 * @internal
 */
class StringifyDbTableData
{
    /**
     * @var Json
     */
    private Json $jsonSerializer;

    /**
     * @param Json $jsonSerializer
     * @codeCoverageIgnore
     */
    public function __construct(Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Returns a string representation of the data from the specified table
     *
     * @param array|null $data
     * @return string
     */
    public function getStringData(array|null $data): string
    {
        return $this->jsonSerializer->serialize($data);
    }
}
