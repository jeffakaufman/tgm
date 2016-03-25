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


class Mirasvit_FeedExport_Model_Feed_Generator_Action_Iterator_Entity
    extends Mirasvit_FeedExport_Model_Feed_Generator_Action_Iterator_Abstract
{
    protected $_format = null;
    protected $_type   = null;

    public function init()
    {
        $this->_format = $this->getFeed()->getFormat();
        $this->_type   = $this->getType();

        return isset($this->_format['entity'][$this->_type]);
    }

    public function getCollection()
    {
        if ($this->_type == 'product') {
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->joinField('qty', 'cataloginventory/stock_item', 'qty',
                    'product_id=entity_id', '{{table}}.stock_id=1', 'left')
                ->addStoreFilter();

            if (count($this->getFeed()->getRuleIds()) || Mage::app()->getRequest()->getParam('skip')) {
                $collection->getSelect()->joinLeft(
                        array('rule' => Mage::getSingleton('core/resource')->getTableName('feedexport/feed_product')),
                            'e.entity_id=rule.product_id', array())
                    ->where('rule.feed_id = ?', $this->getFeed()->getId())
                    ->where('rule.is_new = 1');
            }
        } elseif ($this->_type == 'category') {
            $root = Mage::getModel('catalog/category')->load($this->getFeed()->getStore()->getRootCategoryId());

            $collection = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', array('nin' => array(1, 2)));

            if (method_exists($collection, 'addPathFilter')) {
                $collection->addPathFilter($root->getPath());
            }
        }

        return $collection;
    }

    public function callback($row)
    {
        $this->_patternModel  = Mage::getSingleton('feedexport/feed_generator_pattern');
        $this->_patternModel->setFeed($this->getFeed());
        $this->_patternModel->setData('value_preparer', array($this, 'valuePreparer'));

        $model = Mage::getModel('catalog/'.$this->_type)->load($row['entity_id']);
        $result = $this->_patternModel->getPatternValue($this->_format['entity'][$this->_type], $this->_type, $model);

        return $result;
    }

    public function save($result)
    {
        $content = implode(PHP_EOL, $result).PHP_EOL;

        $filePath = Mage::getSingleton('feedexport/config')->getTmpPath($this->getFeed()->getId()).DS.$this->_type.'.dat';
        Mage::helper('feedexport/io')->write($filePath, $content, 'a');
    }

    public function valuePreparer($value)
    {
        if (!is_object($value)) {
            $value = html_entity_decode($value, ENT_COMPAT, 'UTF-8');
            $value = str_replace('', '', $value);
            $value = str_replace('', '', $value);
            $value = str_replace('', '', $value);
            $value = str_replace('', '', $value);
            $value = str_replace('', '', $value);
        }

        return $value;
    }

    public function start()
    {
    }

    public function finish()
    {

    }
}