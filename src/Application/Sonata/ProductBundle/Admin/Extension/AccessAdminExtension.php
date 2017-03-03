<?php

namespace Application\Sonata\ProductBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

class AccessAdminExtension extends AdminExtension {

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @param SecurityContextInterface $securityContext
     * @param Connection               $databaseConnection
     */
    public function __construct(
    SecurityContextInterface $securityContext, Connection $databaseConnection
    ) {
        $this->securityContext = $securityContext;
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function configureQuery(AdminInterface $admin, ProxyQueryInterface $query, $context = 'list') {
        // Don't filter for admins and for not ACL enabled classes and for command cli
        if (
                !$this->securityContext->getToken() || $admin->isGranted(sprintf($admin->getSecurityHandler()->getBaseRole($admin), 'ADMIN'))
        ) {
            return;
        }

        // Retrieve current logged user SecurityIdentity
        $user = $this->securityContext->getToken()->getUser();

        if ($shop = $user->getShop()) {
            $query
                    ->andWhere('o.shop = :id')
                    ->setParameter('id', $shop->getId())
            ;

            return;
        }

        // Display an empty list
        $query->andWhere('1 = 2');
    }

}
