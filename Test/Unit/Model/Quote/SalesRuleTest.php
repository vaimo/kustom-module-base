<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Model\Quote;

use Klarna\Base\Model\Quote\SalesRule;
use Magento\Quote\Model\Quote;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass  Klarna\Base\Model\Quote\SalesRule
 */
class SalesRuleTest extends TestCase
{
    /**
     * @var SalesRule
     */
    private SalesRule $salesRule;
    /**
     * @var Quote
     */
    private Quote $quoteMock;
    /**
     * @var Rule
     */
    private Rule $ruleMock;

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->salesRule = parent::setUpMocks(SalesRule::class);

        $storeMock = $this->mockFactory->create(Store::class, ['getWebsiteId']);
        $this->quoteMock = $this->mockFactory->create(
            Quote::class,
            [
                'getBillingAddress',
                'getShippingAddress',
                'isVirtual',
                'getStore'
            ]
        );

        $this->ruleMock = $this->mockFactory->create(
            Rule::class,
            [],
            [
                'getApplyToShipping',
            ]
        );

        $this->quoteMock->method('getStore')
            ->willReturn($storeMock);
    }

    public function testIsApplyToShippingUsedReturnsTrue(): void
    {
        $this->quoteMock->method('isVirtual')->willReturn(false);

        $ruleMock1 = $this->mockFactory->create(
            Rule::class,
            [],
            [
                'getApplyToShipping',
            ]
        );
        $ruleMock1->expects($this->once())
            ->method('getApplyToShipping')
            ->willReturn(false);

        $ruleMock2 = $this->mockFactory->create(
            Rule::class,
            [],
            [
                'getApplyToShipping',
            ]
        );
        $ruleMock2->expects($this->once())
            ->method('getApplyToShipping')
            ->willReturn(true);

        $this->dependencyMocks['validator']->expects($this->once())
            ->method('getRules')
            ->willReturn([$ruleMock1, $ruleMock2]);

        $result = $this->salesRule->isApplyToShippingUsed($this->quoteMock);
        static::assertSame(true, $result);
    }

    public function testIsApplyToShippingUsedReturnsFalse(): void
    {
        $this->quoteMock->method('isVirtual')->willReturn(false);

        $ruleMock1 = $this->mockFactory->create(
            Rule::class,
            [],
            [
                'getApplyToShipping',
            ]
        );
        $ruleMock1->expects($this->once())
            ->method('getApplyToShipping')
            ->willReturn(false);

        $ruleMock2 = $this->mockFactory->create(
            Rule::class,
            [],
            [
                'getApplyToShipping',
            ]
        );
        $ruleMock2->expects($this->once())
            ->method('getApplyToShipping')
            ->willReturn(false);

        $this->dependencyMocks['validator']->expects($this->once())
            ->method('getRules')
            ->willReturn([$ruleMock1, $ruleMock2]);

        $result = $this->salesRule->isApplyToShippingUsed($this->quoteMock);
        static::assertSame(false, $result);
    }

    public function testIsApplyToShippingUsedReturnsFalseForVirtualQuote(): void
    {
        $this->quoteMock->method('isVirtual')->willReturn(true);

        $this->dependencyMocks['validator']->expects($this->once())
            ->method('getRules')
            ->willReturn([]);

        $result = $this->salesRule->isApplyToShippingUsed($this->quoteMock);
        static::assertSame(false, $result);
    }

    public function testIsApplyToShippingUsedReturnsFalseForNonVirtualQuote(): void
    {
        $this->quoteMock->method('isVirtual')->willReturn(false);

        $this->dependencyMocks['validator']->expects($this->once())
            ->method('getRules')
            ->willReturn([]);

        $result = $this->salesRule->isApplyToShippingUsed($this->quoteMock);
        static::assertSame(false, $result);
    }
}