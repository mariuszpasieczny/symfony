<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ShopAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('address')
            ->add('postcode')
            ->add('city')
            ->add('email')
            ->add('createdAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('address')
            ->add('postcode')
            ->add('city')
            ->add('email')
            ->add('createdAt')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('shop.group.general', array(
                    'class' => 'col-md-6',
                ))
            ->add('name')
            ->add('address')
            ->add('postcode')
            ->add('city')
            ->end()
            ->with('shop.group.contact', array(
                    'class' => 'col-md-6',
                ))
                ->add('email')
                ->add('phoneNumber')
                ->add('mobileNumber')
                ->add('faxNumber')
            ->end()
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('address')
            ->add('postcode')
            ->add('city')
            ->add('email')
            ->add('phoneNumber')
            ->add('mobileNumber')
            ->add('faxNumber')
            ->add('createdAt')
        ;
    }
}
