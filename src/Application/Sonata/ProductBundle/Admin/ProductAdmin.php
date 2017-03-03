<?php

namespace Application\Sonata\ProductBundle\Admin;

use Sonata\ProductBundle\Admin\ProductAdmin as AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class ProductAdmin extends AbstractAdmin {

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $list) {
        $list
                ->addIdentifier('sku')
                ->addIdentifier('name');

        if ($this->isGranted('ROLE_ADMIN')) {
            $list->add('shop');
        }

        $list->add('enabled', null, array('editable' => true))
                ->add('price', 'currency', array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
                ->add('productCategories', null, array('associated_tostring' => 'getCategory'))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     */
    public function configureDatagridFilters(DatagridMapper $filter) {
        $filter
                ->add('name')
                ->add('sku');

        if ($this->isGranted('ROLE_ADMIN')) {
            $filter->add('shop');
        }

        $filter->add('enabled')
                ->add('productCategories.category', null, array('field_options' => array('expanded' => false, 'multiple' => true)))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null) {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');
        $product = $this->getObject($id);

        $menu->addChild(
                $this->trans('product.sidemenu.link_product_edit', array(), 'SonataProductBundle'), array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
                $this->trans('product.sidemenu.view_categories', array(), 'SonataProductBundle'), array('uri' => $admin->generateUrl('sonata.product.admin.product.category.list', array('id' => $id)))
        );
    }

}
