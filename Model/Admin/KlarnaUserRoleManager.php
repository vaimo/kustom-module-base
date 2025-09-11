<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Admin;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory as RoleCollectionFactory;
use Magento\Authorization\Model\Role;
use Magento\Authorization\Model\RoleFactory;
use Magento\Authorization\Model\RulesFactory;
use Magento\Authorization\Model\UserContextInterface;

/**
 * @internal
 */
class KlarnaUserRoleManager
{
    /**
     * @var string
     */
    private string $roleName = 'Klarna Debugger';
    /**
     * @var string
     */
    private string $roleDescription = 'If you add this role to a user, they will be able to see the Klarna logs.';
    /**
     * @var RoleFactory
     */
    private RoleFactory $roleFactory;
    /**
     * @var RulesFactory
     */
    private RulesFactory $rulesFactory;
    /**
     * @var RoleCollectionFactory
     */
    private RoleCollectionFactory $roleCollectionFactory;
    /**
     * @var AclRetriever
     */
    private AclRetriever $aclRetriever;

    /**
     * @param RoleFactory $roleFactory
     * @param RulesFactory $rulesFactory
     * @param RoleCollectionFactory $roleCollectionFactory
     * @param AclRetriever $aclRetriever
     * @codeCoverageIgnore
     */
    public function __construct(
        RoleFactory           $roleFactory,
        RulesFactory          $rulesFactory,
        RoleCollectionFactory $roleCollectionFactory,
        AclRetriever           $aclRetriever
    ) {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->aclRetriever = $aclRetriever;
    }

    /**
     * Load the given role or create if it doesn't exist
     *
     * @param string $roleName
     * @param string $roleDescription
     * @return Role
     */
    public function loadOrCreateRole(string $roleName, string $roleDescription): Role
    {
        $role = $this->loadRoleByName($roleName);

        if ($role->getId()) {
            return $role;
        }

        return $this->createRole($roleName, $roleDescription);
    }

    /**
     * Load the given role by name
     *
     * @param string $roleName
     * @return Role
     */
    public function loadRoleByName(string $roleName): Role
    {
        return $this
            ->roleCollectionFactory
            ->create()
            ->addFieldToFilter('role_name', $roleName)
            ->getFirstItem();
    }

    /**
     * Create a new role with the given name
     *
     * @param string $roleName
     * @param string $roleDescription
     * @return Role
     */
    public function createRole(string $roleName, string $roleDescription): Role
    {
        $role = $this->roleFactory->create();
        $role->setName($roleName)
            ->setPid(0)
            ->setRoleType(RoleGroup::ROLE_TYPE)
            ->setUserType(UserContextInterface::USER_TYPE_ADMIN)
            ->setRoleDescription($roleDescription);
        $role->save();

        return $role;
    }

    /**
     * Check if the role should be added to the system or not
     *
     * @param string $roleName
     * @return bool
     */
    public function checkRoleDoesNotExist(string $roleName): bool
    {
        $existingRoles = $this->roleCollectionFactory->create();
        $existingRoles->addFieldToFilter('role_name', $roleName);

        return $existingRoles->count() <= 0;
    }

    /**
     * Add the given role to the system and assign the given resources to it
     *
     * @param string $roleName
     * @param array $resources
     * @return void
     */
    public function assignResourcesToRole(string $roleName, array $resources): void
    {
        $role = $this->loadRoleByName($roleName);

        if (!$role->getId()) {
            return;
        }

        $roleId = $role->getId();
        $existingResources = $this->aclRetriever->getAllowedResourcesByRole($roleId);
        $allResources = array_unique(array_merge($existingResources, $resources));

        $rules = $this->rulesFactory->create();
        $rules->setRoleId($roleId)
            ->setResources($allResources)
            ->saveRel();
    }

    /**
     * Get the role name
     *
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    /**
     * Get the role description
     *
     * @return string
     */
    public function getRoleDescription(): string
    {
        return $this->roleDescription;
    }
}
