<?php

namespace Application\Sonata\ProductBundle\Menu;

use Sonata\ProductBundle\Menu\ProductMenuBuilder as MenuBuilder;
use Sonata\ClassificationBundle\Model\CategoryInterface;

class ProductMenuBuilder extends MenuBuilder{

    /**
     * Gets the HTML associated with the category menu title.
     *
     * @param CategoryInterface $category A category instance
     * @param int               $limit    A limit for calculation (fixed to 500 by default)
     *
     * @return string
     */
    protected function getCategoryTitle(CategoryInterface $category, $limit = 500) {
        $count = $this->categoryManager->getProductCount($category, $limit);

        return $category->getName();
    }

}
