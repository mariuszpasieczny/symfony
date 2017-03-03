<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Application\Sonata\ProductBundle\Entity\Catalog;
use Application\Sonata\ClassificationBundle\Entity\Category;
use Application\Sonata\MediaBundle\Entity\Media;

class AclVoter extends Voter {

    // these strings are just invented: you can use anything
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';

    private $container;

    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @var RoleHierarchy
     */
    protected $roleHierarchy;

    public function __construct($container, Connection $databaseConnection, array $roleHierarchy = array()) {
        $this->container = $container;
        $this->databaseConnection = $databaseConnection;
        $this->roleHierarchy = new RoleHierarchy($roleHierarchy);
    }

    protected function supports($attribute, $subject) {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (
                !$subject instanceof Media 
                && !$subject instanceof Category 
//                && !$subject instanceof Catalog
                ) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        // Retrieve current logged user SecurityIdentity
        $user = $token->getUser();
        $userSecurityIdentity = UserSecurityIdentity::fromAccount($user);

        // Retrieve current logged user roles
        $userRoles = $user->getRoles();

        // Find child roles
        $roles = array();
        foreach ($userRoles as $userRole) {
            $roles[] = ($userRole instanceof RoleInterface) ? $userRole : new Role($userRole);
        }

        $reachableRoles = $this->roleHierarchy->getReachableRoles($roles);

        // Get identity ACL user identifier
        $identifiers[] = sprintf('%s-%s', $userSecurityIdentity->getClass(), $userSecurityIdentity->getUsername());

        // Get identities ACL roles identifiers
        foreach ($reachableRoles as $reachableRole) {
            $role = $reachableRole->getRole();
            if (!in_array($role, $identifiers)) {
                $identifiers[] = $role;
            }
        }

        $identityStmt = $this->databaseConnection->executeQuery(
                'SELECT id FROM acl_security_identities WHERE identifier IN (?)', array($identifiers), array(Connection::PARAM_STR_ARRAY)
        );

        $identityIds = array();
        foreach ($identityStmt->fetchAll() as $row) {
            $identityIds[] = $row['id'];
        }

        // Get class ACL identifier
        $em = $this->container->get('doctrine')->getEntityManager(); 
        $classType = $em->getClassMetadata(get_class($subject))->getName();
        $classStmt = $this->databaseConnection->prepare('SELECT id FROM acl_classes WHERE class_type = :classType');
        $classStmt->bindValue('classType', $classType);
        $classStmt->execute();

        $classId = $classStmt->fetchColumn();

        if (!empty($identityIds) && $classId) {
            $entriesStmt = $this->databaseConnection->executeQuery(
                    'SELECT ae.mask FROM acl_entries AS ae JOIN acl_object_identities AS aoi ON ae.object_identity_id = aoi.id WHERE ae.class_id = ? AND ae.security_identity_id IN (?) AND object_identifier = ?', array(
                $classId,
                $identityIds,
                $subject->getId()
                    ), array(
                \PDO::PARAM_INT,
                Connection::PARAM_INT_ARRAY,
                \PDO::PARAM_INT
                    )
            );

            $mask = $entriesStmt->fetchColumn();
            
            switch ($attribute) {
                case self::VIEW:
                    return $mask == MaskBuilder::MASK_VIEW;
                case self::EDIT:
                case self::DELETE:
                    return $mask == MaskBuilder::MASK_OWNER;
            }
        }
    }

}
