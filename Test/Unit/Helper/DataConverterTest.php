<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Helper;

use Klarna\Base\Helper\DataConverter;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass  \Klarna\Base\Helper\DataConverter
 */
class DataConverterTest extends TestCase
{
    /**
     * @var DataConverter
     */
    private DataConverter $dataConverter;

    public function testToApiFloat(): void
    {
        $param = 13.37;
        $expectedResult = 1337;
        static::assertEquals($expectedResult, $this->dataConverter->toApiFloat($param));
    }

    public function testToShopFloat(): void
    {
        $param = 1337;
        $expectedResult = 13.37;
        static::assertEquals($expectedResult, $this->dataConverter->toShopFloat($param));
    }

    protected function setUp(): void
    {
        $this->dataConverter = parent::setUpMocks(DataConverter::class);
    }
}