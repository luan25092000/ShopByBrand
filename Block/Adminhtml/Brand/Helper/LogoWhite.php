<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper;

/**
 * Class LogoWhite
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper
 */
class LogoWhite extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<table id="logo-white" class="logo" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<thead id="logo_thead_white" class="logo">' .
            '<tr class="logo">' .
            '<div class="image image-placeholder" id="logo-white-location">' .
            '<div class="product-image-wrapper" style="overflow: -moz-hidden-unscrollable;">' .
            '<p class="image-placeholder-text">Browse to find or drag image here</p>' .
            '<input id="magenest-upload-image-logo-white" type="file" name="logo_white">' .
            '</div></div></tr></thead></table>';
        return $html;
    }
}
