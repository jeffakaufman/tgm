<?php

class Silk_Company_Model_Customer extends Mage_Customer_Model_Customer
{
    public function getEmployeeIds()
    {
        if($this->getRoleId()==Silk_Company_Model_Role::getRole('ADMIN') || $this->getRoleId()==Silk_Company_Model_Role::getRole('MANAGER'))
        {
            $employees = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToFilter('company_id',array('eq'=>$this->getCompanyId()));
            $ids = array();

            foreach($employees as $emp)
            {
                $ids[] = $emp->getId();
            }
            return $ids;
        }
        else
        {
            return array();
        }
    }

	/**
     * Get attribute text by its code
     *
     * @param $attributeCode Code of the attribute
     * @return string
     */
    public function getAttributeText($attributeCode)
    {
        return $this->getResource()
            ->getAttribute($attributeCode)
                ->getSource()
                    ->getOptionText($this->getData($attributeCode));
    }

    /**
     * Retrieve not default addresses
     *
     * @return array
     */
    public function getAdditionalAddresses()
    {
        if((int)Mage::app()->getStore()->getId()===0) return parent::getAdditionalAddresses();

        $addresses = array();
        if(!$this->getIsB2b() || ($this->getIsB2b()&& $this->getRoleId()==Silk_Company_Model_Role::getRole('ADMIN')))
        {
            $primatyIds = $this->getPrimaryAddressIds();
            foreach ($this->getAddressesCollection() as $address) {
                //if (!in_array($address->getId(), $primatyIds)) {
                    $addresses[] = $address;
                //}
            }
        }
        else
        {
            $admin = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToFilter('company_id', array('eq'=>$this->getCompanyId()))
                ->addAttributeToFilter('role_id',array('eq'=>Silk_Company_Model_Role::getRole('ADMIN')))->getFirstItem();

            foreach($admin->getAddressesCollection() as $address)
            {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     * Retrieve customer address array
     *
     * @return array
     */
    public function getAddresses()
    {
        if((int)Mage::app()->getStore()->getId()===0) return parent::getAddresses();
            
        if($this->getIsB2b()) return $this->getAdditionalAddresses();
        return parent::getAddresses();
    }

    public function isB2b()
    {
        return $this->getIsB2b();
    }

    public function isAdmin()
    {
        if($this->getIsB2b() && $this->getRoleId()==Silk_Company_Model_Role::getRole('ADMIN'))
        {
            return true;
        }
        return false;
    }

    public function isManager()
    {
        if($this->getIsB2b() && $this->getRoleId()==Silk_Company_Model_Role::getRole('MANAGER'))
        {
            return true;
        }
        return false;
    }

    public function isSales()
    {
        if($this->getIsB2b() && $this->getRoleId()==Silk_Company_Model_Role::getRole('SALES'))
        {
            return true;
        }
        return false;
    }

 }
