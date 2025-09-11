<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Helper\Debug;

use Klarna\Base\Helper\Debug\StringifyDbTableData;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass \Klarna\Base\Helper\Debug\StringifyDbTableData
 */
class StringifyDbTableDataTest extends TestCase
{
    /**
     * @var StringifyDbTableData
     */
    private $stringifyDbTableData;

    protected function setUp(): void
    {
        $this->stringifyDbTableData = parent::setUpMocks(StringifyDbTableData::class);

        $this->dependencyMocks['jsonSerializer']
            ->method('serialize')
            ->willReturn('[{"id":1,"name":"John"},{"id":2,"name":"Jane"}]');
    }

    public function testStringifyDbTableDataSuccess(): void
    {
        $data = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane']
        ];

        $results = $this->stringifyDbTableData->getStringData($data);
        static::assertSame('[{"id":1,"name":"John"},{"id":2,"name":"Jane"}]', $results);
    }
}
