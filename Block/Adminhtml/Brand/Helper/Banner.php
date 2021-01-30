<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper;

/**
 * Class Banner
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper
 */
class Banner extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<table id="banner" class="banner" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<thead id="banner_thead" class="banner">' .
            '<tr class="banner">' .
            '<div class="image image-placeholder" id="banner-location">' .
            '<div class="product-image-wrapper" style="overflow: -moz-hidden-unscrollable;">' .
            '<p class="image-placeholder-text">Browse to find or drag image here</p>' .
            '<input id="magenest-upload-image-banner" type="file" name="banner">' .
            '</div></div></tr></thead></table>';
        return $html;
    }
}
