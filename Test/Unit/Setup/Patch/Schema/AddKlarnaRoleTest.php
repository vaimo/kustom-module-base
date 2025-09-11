<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Setup\Patch\Schema;

use Klarna\Base\Setup\Patch\Schema\AddKlarnaRole;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @coversDefaultClass \Klarna\Base\Setup\Patch\Schema\AddKlarnaRole
 */
class AddKlarnaRoleTest extends TestCase
{
    /**
     * @var object
     */
    private object $addKlarnaUserAndRole;

    protected function setUp(): void
    {
        $this->addKlarnaUserAndRole = parent::setUpMocks(AddKlarnaRole::class);

        $connection = $this->createMock(AdapterInterface::class);
        $this->dependencyMocks['moduleDataSetup']
            ->method('getConnection')
            ->willReturn($connection);
    }

    public function testApplyDoesNotCreateOrLoadARoleIfItShouldNotBeAdded(): void
    {
        $this->dependencyMocks['klarnaUserRoleManager']
            ->method('checkRoleDoesNotExist')
            ->willReturn(false);

        $this->dependencyMocks['klarnaUserRoleManager']
            ->expects(static::never())
            ->method('loadOrCreateRole');

        $this->addKlarnaUserAndRole->apply();
    }

    public function testApplyDoesCreateOrLoadARoleIfItShouldBeAdded(): void
    {
        $this->dependencyMocks['klarnaUserRoleManager']
            ->method('checkRoleDoesNotExist')
            ->willReturn(true);

        $this->dependencyMocks['klarnaUserRoleManager']
            ->expects(static::once())
            ->method('loadOrCreateRole');
        $this->addKlarnaUserAndRole->apply();
    }

    public function testThereIsNoDependenciesForThisPatch(): void
    {
        $this->assertEquals($this->addKlarnaUserAndRole->getDependencies(), []);
    }

    public function testThereIsNoAliasesForThisPatch(): void
    {
        $this->assertEquals($this->addKlarnaUserAndRole->getAliases(), []);
    }
}
