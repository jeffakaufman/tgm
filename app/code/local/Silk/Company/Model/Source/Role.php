<?php

class Silk_Company_Model_Source_Role extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	/**
     * Option values
     */
    //const ADMIN = 1;
    //const MANAGER = 2;
    //const SALES = 3;

	/**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        //if (is_null($this->_options)) {
            //$this->_options = array(
                //array(
                    //'label' => Mage::helper('company')->__('Admin'),
                    //'value' => self::ADMIN
                //),
                //array(
                    //'label' => Mage::helper('eav')->__('Manager'),
                    //'value' => self::MANAGER
                //),
                //array(
                    //'label' => Mage::helper('eav')->__('Sales'),
                    //'value' => self::SALES
                //),
            //);
        //}

        $attribute = Mage::getModel('eav/config')->getAttribute('customer', 'role_id');
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) { 
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }


    public function getRoleId($name)
    {   
        $attribute = Mage::getModel('eav/config')->getAttribute('customer', 'role_id');
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) { 
            $options[$option['label']] = $option['value'];
        }

        return $options[$name];
    }  

    /**
     * Retrieve option array
     *
     * @return array
     */
    //public function getOptionArray()
    //{
        //$_options = array();
        //foreach ($this->getAllOptions() as $option) {
            //$_options[$option['value']] = $option['label'];
        //}
        //return $_options;
    //}

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }


   
}
