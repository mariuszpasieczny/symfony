<?php

namespace Application\Sonata\ProductBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

class NewInstanceAdminExtension extends AdminExtension {

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     * @param Connection               $databaseConnection
     * @param array                    $roleHierarchy
     */
    public function __construct(
    SecurityContextInterface $securityContext
    ) {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function alterNewInstance(AdminInterface $admin, $object) {
        // Don't filter for admins and for not ACL enabled classes and for command cli
        if (
                !$this->securityContext->getToken() || $admin->isGranted(sprintf($admin->getSecurityHandler()->getBaseRole($admin), 'ADMIN'))
        ) {
            return;
        }

        // Retrieve current logged user SecurityIdentity
        $user = $this->securityContext->getToken()->getUser();

        if ($shop = $user->getShop()) {
            $object->setShop($shop);
        }
    }

}
