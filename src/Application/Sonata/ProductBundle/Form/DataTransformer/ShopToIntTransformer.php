<?php

namespace Application\Sonata\ProductBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Form\DataTransformer\EntityToIntTransformer;

class ShopToIntTransformer extends EntityToIntTransformer
{
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("AppBundle\\Entity\\Shop");
        $this->setEntityRepository("AppBundle:Shop");
        $this->setEntityType("shop");
    }

}