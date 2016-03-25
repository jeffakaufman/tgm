<?php
class Silk_Company_Model_Role
{
    static $roles = array('ADMIN'=>'Admin', 'MANAGER'=>'Manager', 'SALES'=>'Sales');
    static public function getRole($key) {
        return Mage::getModel('company/source_role')->getRoleId(self::$roles[$key]);
    }


}
