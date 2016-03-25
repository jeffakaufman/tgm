<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
class Amasty_Paction_Model_Command_Unrelate extends Amasty_Paction_Model_Command_Abstract 
{ 
    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label = 'Remove Relations';
    }
    
    public function getLinkType()
    {
        $types = array(
            'uncrosssell' => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
            'unupsell'    => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
            'unrelate'    => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        );
        return $types[$this->_type];
    }
    
    /**
     * Executes the command
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     *
     * @throws Exception
     * @return string success message if any
     */
    public function execute($ids, $storeId, $val)
    {
        $this->_errors = array();
        
        $hlp = Mage::helper('ampaction');
        if (!is_array($ids)) {
            throw new Exception($hlp->__('Please select product(s)')); 
        }
        
        $db    = Mage::getSingleton('core/resource')->getConnection('core_write');  
        $table = Mage::getSingleton('core/resource')->getTableName('catalog/product_link');
        
        if (0 == $val) { // between selected
            $where = array(
                'product_id IN(?)'        => $ids,
                'linked_product_id IN(?)' => $ids,
            );
        } elseif (1 == $val) { // selected products from all
            $where = array(
                'linked_product_id IN(?)' => $ids,
            );
        } else { // Remove all relations from selected products
            $where = array(
                'product_id IN(?)' => $ids,
            );
        }
        
        $db->delete($table, array_merge($where, array('link_type_id = ?' => $this->getLinkType())));
        
        $success = $hlp->__('Product associations have been successfully deleted.');
        
        return $success;
    }
    
    protected function _getAlgorithms()
    {
        $hlp = Mage::helper('ampaction');
        $options = array(
            array('value'=> 0, 'label' => $hlp->__('Remove relations between selected products only')),
            array('value'=> 1, 'label' => $hlp->__('Remove selected products from ALL relations in the catalog')),
            array('value'=> 2, 'label' => $hlp->__('Remove all relations from selected products')),
        );

        return $options;
    }
    
    /**
     * Returns value field options for the mass actions block
     *
     * @param string $title field title
     * @return array
     */
    protected function _getValueField($title)
    {
        $field = parent::_getValueField($title);
        
        $field['ampaction_value']['label']  = Mage::helper('ampaction')->__('Algorithm');
        $field['ampaction_value']['type']   = 'select';
        $field['ampaction_value']['values'] = $this->_getAlgorithms();
        
        return $field;       
    }
}