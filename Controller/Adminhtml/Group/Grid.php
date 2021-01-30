<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ShopByBrand extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ShopByBrand
 */
namespace Magenest\ShopByBrand\Controller\Adminhtml\Group;

use Magenest\ShopByBrand\Controller\Adminhtml\Group;

/**
 * Class Grid
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Group
 */
class Grid extends Group
{
    /**
     * Managing newsletter grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ShopByBrand::brand');
    }
}
