<?php

namespace AppBundle\Block;

use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class AboutBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'app.block.breadcrumb_about';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext) {

        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('app_block_breadcrumb_about', array(
            'route' => 'about',
            'extras' => array('translation_domain' => 'AppBundle'),
        ));

        return $menu;
    }

}
