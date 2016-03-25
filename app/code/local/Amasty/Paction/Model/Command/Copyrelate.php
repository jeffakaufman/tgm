<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
class Amasty_Paction_Model_Command_Copyrelate extends Amasty_Paction_Model_Command_Abstract
{
    protected $_link_attribute_id = null;
    protected $_link_attribute_data_type = null;

    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label = 'Copy Relations';
        
        $this->_fieldLabel = 'From';
    }
    
    public function getLinkType()
    {
        $types = array(
            'copycrosssell' => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
            'copyupsell'    => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
            'copyrelate'    => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        );
        return $types[$this->_type];
    }
    
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @throws Exception
     * @return string success message if any
     */    
    public function execute($ids, $storeId, $val)
    {
        $success = parent::execute($ids, $storeId, $val);
        $hlp = Mage::helper('ampaction');

        $fromId = intVal(trim($val));
        if (!$fromId) {
            throw new Exception($hlp->__('Please provide a valid product ID'));
        }

        if (in_array($fromId, $ids)) {
            throw new Exception($hlp->__('Please remove source product from the selected products'));
        }

        $records = $this->_getRelations($fromId);

        if (empty($records)) {
            throw new Exception($hlp->__('Source product has no relations'));
        }

        $num = 0;
        foreach ($ids as $id) {
            foreach ($records as $record) {
                if ($id == $record['linked_product_id']) {
                    continue;
                }
                $num += $this->_createNewLink($id, $record['linked_product_id'], $record['link_id']);
            }
        }

        if ($num){
            if (1 == $num)
                $success = $hlp->__('Product association has been successfully added.');
            else {
                $success = $hlp->__('%d product associations have been successfully added.', $num);
            }
        }
        
        return $success; 
    }

    protected function _getRelations($productId)
    {
        $db     = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table  = Mage::getSingleton('core/resource')->getTableName('catalog/product_link');

        $select = $db->select()->from($table)
            ->where('link_type_id=?', $this->getLinkType())
            ->where('product_id =?', $productId);

        $rows = $db->fetchAll($select);

        return $rows;
    }

    protected function _getLinkAttributeId($code = 'position')
    {
        if (is_null($this->_link_attribute_id)) {
            $db    = Mage::getSingleton('core/resource')->getConnection('core_write');
            $table = Mage::getSingleton('core/resource')->getTableName('catalog/product_link_attribute');

            $select = $db->select()->from($table)
                ->where('link_type_id=?', $this->getLinkType())
                ->where('product_link_attribute_code=?', $code);
            $row = $db->fetchRow($select);

            $this->_link_attribute_id = $row['product_link_attribute_id'];
            $this->_link_attribute_data_type = $row['data_type'];
        }
        return $this->_link_attribute_id;
    }
    
    protected function _createNewLink($productId, $linkedProductId, $parentLinkId)
    {
        $db     = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table  = Mage::getSingleton('core/resource')->getTableName('catalog/product_link');

        $select = $db->select()->from($table)
            ->where('link_type_id=?', $this->getLinkType())
            ->where('product_id =?', $productId)
            ->where('linked_product_id =?', $linkedProductId);
        $row = $db->fetchRow($select);

        $insertedCnt = 0;
        if (!$row) {
            $insertedCnt = $db->insert($table, array(
                'product_id'        => $productId,
                'linked_product_id' => $linkedProductId,
                'link_type_id'      => $this->getLinkType(),
            ));
            $newLinkId = $db->lastInsertId();

            $linkAttributeId = $this->_getLinkAttributeId();
            $table = Mage::getSingleton('core/resource')->getTableName('catalog/product_link_attribute_' . $this->_link_attribute_data_type);
            $select = $db->select()->from($table)
                ->where('link_id=?', $parentLinkId)
                ->where('product_link_attribute_id=?', $linkAttributeId);
            $row = $db->fetchRow($select);
            
            if ($row) {
                $db->insert($table, array(
                    'product_link_attribute_id' => $linkAttributeId,
                    'link_id' => $newLinkId,
                    'value'   => $row['value'],
                ));
            }
        }

        return $insertedCnt;
    }    
    
    protected function _getValueField($title)
    {
        return parent::_getValueField($title);
    }
}