<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper;

/**
 * Class Logo
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper
 */
class Logo extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<table id="logo" class="logo" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<thead id="logo_thead" class="logo">' .
            '<tr class="logo">' .
            '<div class="image image-placeholder" id="logo-location">' .
            '<div class="product-image-wrapper" style="overflow: -moz-hidden-unscrollable;">' .
            '<p class="image-placeholder-text">Browse to find or drag image here</p>' .
            '<input id="magenest-upload-image-logo" type="file" name="logo">' .
            '</div></div></tr></thead></table>';
        return $html;
    }
}
