<?php

namespace AppBundle\Block;

use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class PageBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'app.page.block.breadcrumb';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext) {

        $menu = $this->getRootMenu($blockContext);

        if ($page = $blockContext->getBlock()->getSetting('page')) {
            $menu->addChild(sprintf("app_page_block_breadcrumb_%s", $page), array(
                'route' => $page,
                'extras' => array('translation_domain' => 'AppBundle'),
            ));
        }

        return $menu;
    }

}
