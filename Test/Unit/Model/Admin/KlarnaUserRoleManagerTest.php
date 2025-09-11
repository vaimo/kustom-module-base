<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Admin;

use Klarna\Base\Model\Admin\KlarnaUserRoleManager;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\ResourceModel\Role\Collection as RoleCollection;
use Magento\Authorization\Model\Role;
use Magento\Authorization\Model\Rules;
use Magento\Authorization\Model\UserContextInterface;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @coversDefaultClass \Klarna\Base\Model\Admin\KlarnaUserRoleManager
 */
class KlarnaUserRoleManagerTest extends TestCase
{
    /**
     * @var KlarnaUserRoleManager
     */
    private KlarnaUserRoleManager $klarnaUserRoleManager;

    protected function setUp(): void
    {
        $this->klarnaUserRoleManager = parent::setUpMocks(KlarnaUserRoleManager::class);
    }

    public function testLoadOrCreateRoleLoadsTheRoleWhenItExists(): void
    {
        $role = $this->mockFactory->create(Role::class);
        $role->method('getId')->willReturn(1);

        $klarnaUserRoleManagerMock = $this
            ->createPartialMock(KlarnaUserRoleManager::class, ['loadRoleByName', 'createRole']);

        $klarnaUserRoleManagerMock
            ->expects($this->once())
            ->method('loadRoleByName')
            ->willReturn($role);

        $klarnaUserRoleManagerMock
            ->expects($this->never())
            ->method('createRole');

        $klarnaUserRoleManagerMock->loadOrCreateRole('role_name', 'role_description');
    }

    public function testLoadOrCreateRoleCreatesARoleWhenNoRoleExists(): void
    {
        $role = $this->mockFactory->create(Role::class);

        $klarnaUserRoleManagerMock = $this
            ->createPartialMock(KlarnaUserRoleManager::class, ['loadRoleByName', 'createRole']);

        $klarnaUserRoleManagerMock
            ->expects($this->once())
            ->method('loadRoleByName')
            ->willReturn($role);

        $klarnaUserRoleManagerMock
            ->expects($this->once())
            ->method('createRole');

        $klarnaUserRoleManagerMock->loadOrCreateRole('role_name', 'role_description');
    }

    public function testLoadRoleByNameCallsTheNecessaryMethodsToLoadARole(): void
    {
        $role = $this->mockFactory->create(Role::class);
        $roleCollection = $this->mockFactory->create(RoleCollection::class);

        $this->dependencyMocks['roleCollectionFactory']
            ->method('create')
            ->willReturn($roleCollection);

        $roleCollection->method('addFieldToFilter')->willReturnSelf();
        $roleCollection->method('getFirstItem')->willReturn($role);

        $this->dependencyMocks['roleCollectionFactory']
            ->expects($this->once())
            ->method('create');
        $roleCollection
            ->expects($this->once())
            ->method('addFieldToFilter');
        $roleCollection
            ->expects($this->once())
            ->method('getFirstItem');

        $this->klarnaUserRoleManager->loadRoleByName('role_name');
    }

    public function testCreateRoleCallsTheNecessaryMethodsToLoadARole(): void
    {
        $roleName = 'role name';
        $roleDescription = 'role description';
        $role = $this->mockFactory->create(
            Role::class,
            ['save'],
            ['setName', 'setPid', 'setRoleType', 'setUserType', 'setRoleDescription']
        );

        $this->dependencyMocks['roleFactory']
            ->method('create')
            ->willReturn($role);

        $this->dependencyMocks['roleFactory']
            ->expects($this->once())
            ->method('create');
        $role
            ->expects($this->once())
            ->method('setName')
            ->willReturnSelf()
            ->with($this->equalTo($roleName));
        $role
            ->expects($this->once())
            ->method('setPid')
            ->willReturnSelf()
            ->with($this->equalTo(0));
        $role
            ->expects($this->once())
            ->method('setRoleType')
            ->willReturnSelf()
            ->with($this->equalTo(RoleGroup::ROLE_TYPE));
        $role
            ->expects($this->once())
            ->method('setUserType')
            ->willReturnSelf()
            ->with($this->equalTo(UserContextInterface::USER_TYPE_ADMIN));
        $role
            ->expects($this->once())
            ->method('setRoleDescription')
            ->willReturnSelf()
            ->with($this->equalTo($roleDescription));
        $role
            ->expects($this->once())
            ->method('save');

        $this->klarnaUserRoleManager->createRole($roleName, $roleDescription);
    }

    public function testCheckRoleDoesNotExistReturnsTrueIfTheRoleDoesNotExist(): void
    {
        $roleCollection = $this->mockFactory->create(RoleCollection::class);
        $roleCollection->method('addFieldToFilter')->willReturnSelf();
        $roleCollection->method('count')->willReturn(0);

        $this->dependencyMocks['roleCollectionFactory']
            ->method('create')
            ->willReturn($roleCollection);

        $this->dependencyMocks['roleCollectionFactory']
            ->expects($this->once())
            ->method('create');

        $this->assertTrue($this->klarnaUserRoleManager->checkRoleDoesNotExist('role_name'));
    }

    public function testCheckRoleDoesNotExistReturnsFalseIfTheRoleAlreadyExists(): void
    {
        $roleCollection = $this->mockFactory->create(RoleCollection::class);
        $roleCollection->method('addFieldToFilter')->willReturnSelf();
        $roleCollection->method('count')->willReturn(1);

        $this->dependencyMocks['roleCollectionFactory']
            ->method('create')
            ->willReturn($roleCollection);

        $this->dependencyMocks['roleCollectionFactory']
            ->expects($this->once())
            ->method('create');

        $this->assertFalse($this->klarnaUserRoleManager->checkRoleDoesNotExist('role_name'));
    }

    public function testAssignResourcesToRoleWhenRoleDoesNotExist()
    {
        $roleMock = $this->mockFactory->create(Role::class);
        $roleMock->method('getId')->willReturn(null);

        $klarnaUserRoleManagerMock = $this
            ->createPartialMock(KlarnaUserRoleManager::class, ['loadRoleByName', 'createRole']);

        $klarnaUserRoleManagerMock
            ->expects($this->once())
            ->method('loadRoleByName')
            ->willReturn($roleMock);

        $result = $klarnaUserRoleManagerMock->assignResourcesToRole('Non Existing Role', ['resource1', 'resource2']);

        $this->assertNull($result, 'The method should return null when the role does not exist');
    }

    public function testAssignResourcesToRoleWhenRoleExists()
    {
        $roleMock = $this->mockFactory->create(Role::class);
        $roleMock->method('getId')->willReturn(1);

        $rulesMock = $this->mockFactory->create(
            Rules::class,
            ['saveRel'],
            ['create', 'setRoleId', 'setResources']
        );
        $rulesMock
            ->expects($this->once())
            ->method('setRoleId')
            ->with(1)
            ->willReturnSelf();
        $rulesMock
            ->expects($this->once())
            ->method('setResources')
            ->with(['resource1', 'resource2', 'resource3'])
            ->willReturnSelf();
        $rulesMock
            ->expects($this->once())
            ->method('saveRel');

        $this->dependencyMocks['rulesFactory']
            ->method('create')
            ->willReturn($rulesMock);

        $this->dependencyMocks['aclRetriever']
            ->method('getAllowedResourcesByRole')
            ->willReturn(['resource1', 'resource2']);

        $klarnaUserRoleManagerMock = $this
            ->getMockBuilder(KlarnaUserRoleManager::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                $this->dependencyMocks['roleFactory'],
                $this->dependencyMocks['rulesFactory'],
                $this->dependencyMocks['roleCollectionFactory'],
                $this->dependencyMocks['aclRetriever']
            ])
            ->onlyMethods(['loadRoleByName'])
            ->getMock();

        $klarnaUserRoleManagerMock
            ->method('loadRoleByName')
            ->willReturn($roleMock);

        $klarnaUserRoleManagerMock->assignResourcesToRole('Existing Role', ['resource3']);
    }

    public function testGetRoleNameReturnsCorrectRoleName()
    {
        $expectedRoleName = 'Klarna Debugger';

        // Call the getRoleName method
        $result = $this->klarnaUserRoleManager->getRoleName();

        // Assert that the result is exactly what we expect
        $this->assertEquals($expectedRoleName, $result, "The role name should match 'Klarna Debugger'");
    }

    public function testGetRoleDescriptionReturnsCorrectRoleName()
    {
        $expectedRoleDescription = 'If you add this role to a user, they will be able to see the Klarna logs.';

        // Call the getRoleName method
        $result = $this->klarnaUserRoleManager->getRoleDescription();

        // Assert that the result is exactly what we expect
        $this->assertEquals($expectedRoleDescription, $result, "The role name should match 'Klarna Debugger'");
    }
}
