<?php

namespace AppBundle\Block;

use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class ContactBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'app.block.breadcrumb_contact';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext) {

        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('app_block_breadcrumb_contact', array(
            'route' => 'contact',
            'extras' => array('translation_domain' => 'AppBundle'),
        ));

        return $menu;
    }

}
