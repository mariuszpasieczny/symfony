<?php

namespace Application\Sonata\ProductBundle\Entity;

use Sonata\ProductBundle\Entity\ProductCategoryManager as BaseEntityManager;
use Sonata\ClassificationBundle\Model\CategoryInterface;

class ProductCategoryManager extends BaseEntityManager
{

    /**
     * {@inheritdoc}
     */
    public function getCategoryTree()
    {
        $qb = $this->getRepository()->createQueryBuilder('pc')
            ->select('c, pc')
            ->leftJoin('pc.category', 'c')
            ->where('pc.enabled = true')
            ->andWhere('c.enabled = true')
//            ->andWhere('c.parent IS NULL')
        ;

        $pCategories = $qb->getQuery()->execute();

        $categoryTree = array();

        foreach ($pCategories as $category) {
            $this->putInTree($category->getCategory(), $categoryTree);
        }

        return $categoryTree;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCount(CategoryInterface $category, $limit = 1000)
    {
        // Can't perform limit in subqueries with Doctrine... Hence raw SQL
        $metadata = $this->getEntityManager()->getClassMetadata($this->class);
        $productMetadata = $this->getEntityManager()->getClassMetadata($metadata->getAssociationTargetClass('product'));
        $categoryMetadata = $this->getEntityManager()->getClassMetadata($metadata->getAssociationTargetClass('category'));

        $sql = 'SELECT count(cnt.product_id) AS cntId
            FROM (
                SELECT DISTINCT pc.product_id
                FROM %s pc
                LEFT JOIN %s p ON pc.product_id = p.id
                LEFT JOIN %s c ON pc.category_id = c.id
                LEFT JOIN %s p2 ON p.id = p2.parent_id
                WHERE p.enabled = :enabled
                AND (p2.enabled = :enabled OR p2.enabled IS NULL)
                AND (c.enabled = :enabled OR c.enabled IS NULL)
                AND p.parent_id IS NULL
                AND pc.category_id = :categoryId
                LIMIT %d
                ) AS cnt';

        $sql = sprintf($sql, $metadata->table['name'], $productMetadata->table['name'], $categoryMetadata->table['name'], $productMetadata->table['name'], $limit);

        $statement = $this->getConnection()->prepare($sql);
        $statement->bindValue('enabled', 1);
        $statement->bindValue('categoryId', $category->getId());

        $statement->execute();
        $res = $statement->fetchAll();

        return $res[0]['cntid'];
    }
    
}