<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.2
 * @revision  268
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_Rules_Create extends Mage_Adminhtml_Block_Widget_Button
{
    protected $_config = null;

    public function getConfig()
    {
        if (is_null($this->_config)) {
           $this->_config = new Varien_Object();
        }

        return $this->_config;
    }

    protected function _beforeToHtml()
    {
        $this->setId('create_attribute_' . $this->getConfig()->getGroupId())
            ->setOnClick($this->getJsObjectName() . '.create();')
            ->setType('button')
            ->setClass('add')
            ->setLabel($this->getConfig()->getLabel());

        $this->getConfig()
            ->setUrl($this->getUrl(
                '*/adminhtml_rule/new',
                array(
                    'feed'  => $this->getConfig()->getFeed(),
                    'group' => $this->getConfig()->getGroupId(),
                    'type'  => $this->getConfig()->getType(),
                    'popup' => 1
                )
            ));

        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        $this->setCanShow(true);
        Mage::dispatchEvent('adminhtml_catalog_product_edit_tab_attributes_create_html_before', array('block' => $this));
        if (!$this->getCanShow()) {
            return '';
        }

        $html = parent::_toHtml();
        $html .= Mage::helper('adminhtml/js')->getScript(
            "var {$this->getJsObjectName()} = new Product.Attributes('{$this->getId()}');\n"
            . "{$this->getJsObjectName()}.setConfig(" . Mage::helper('core')->jsonEncode($this->getConfig()->getData()) . ");\n"
        );

        return $html;
    }

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
}