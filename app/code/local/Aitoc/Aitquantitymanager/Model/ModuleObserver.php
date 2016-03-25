<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_ModuleObserver
{
    public function __construct()
    {
        
    }

    public function onAitocModuleLoad()
    {
        if (Mage::registry('aitoc_inventory_loaded')) return false;

        $oDb     = Mage::getModel('sales_entity/order')->getReadConnection();
        /* @var $oDb Varien_Db_Adapter_Pdo_Mysql */
        
        // check default website        
        
        $sAitocDefaultWebsite = 'aitoccode';
        
        $oSelect = $oDb->select();
        /* @var $oSelect Varien_Db_Select */
        
        $oSelect->from(array('website' => Mage::getSingleton('core/resource')->getTableName('core/website')), '*'
                  )
                ->where('website.code = "' . $sAitocDefaultWebsite .'"')
        ;
        
        if (!$oDb->fetchOne($oSelect))
        {
            $sSql = "INSERT INTO `" . Mage::getSingleton('core/resource')->getTableName('core/website') . "` (
`website_id` ,
`code` ,
`name` ,
`sort_order` ,
`default_group_id` ,
`is_default`
)
VALUES (
NULL , '" . $sAitocDefaultWebsite . "', '', '0', '0', '0'
);";
            
            $oDb->query($sSql);
        }
        
        $iDefaultWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId();
        
        // check database transform
        
        $oSelect = $oDb->select();
        /* @var $oSelect Varien_Db_Select */
        
        $oSelect->from(array('main' => Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_settings')), array('value')
                  )
                ->where('main.code = "default_website_id"')
        ;
        
        $iOldWebsiteId = $oDb->fetchOne($oSelect);
        
        $oSelect = $oDb->select();
        /* @var $oSelect Varien_Db_Select */
        
        $oSelect->from(array('main' => Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_settings')), array('value')
                  )
                ->where('main.code = "inventory_convert_completed"')
        ;
        
        $bConvertCompleted = $oDb->fetchOne($oSelect);
        
#        if (22 === 444 AND $iOldWebsiteId = $oDb->fetchOne($oSelect))
        if (!$bConvertCompleted)
        {
            $sTableAitocItem   = Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_item');
            
            $sTableMagentoItem = $sTableAitocItem;
            
            if (strpos($sTableMagentoItem, 'aitoc_') !== false)
            {
                $sTableMagentoItem = str_replace('aitoc_', '', $sTableMagentoItem);
            }
            
            $sTableAitocStatus   = Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_status');
            
            $sTableMagentoStatus = $sTableAitocStatus;
            
            if (strpos($sTableMagentoStatus, 'aitoc_') !== false)
            {
                $sTableMagentoStatus = str_replace('aitoc_', '', $sTableMagentoStatus);
            }
            
            if ($iOldWebsiteId)
            {
            
                // update default website_id
                
                $sSql = "
                  UPDATE `" . $sTableAitocItem . "` SET website_id=" . $iDefaultWebsiteId . " WHERE website_id = " . $iOldWebsiteId . "
                ";
                
                $oDb->query($sSql);
                
                $sSql = "
                  UPDATE `" . $sTableAitocStatus . "` SET website_id=" . $iDefaultWebsiteId . " WHERE website_id = " . $iOldWebsiteId . "
                ";
            
                $oDb->query($sSql);
            
            }
            
            // delete temp connection with default website to products 
            
            $sSql = " DELETE FROM `" . Mage::getSingleton('core/resource')->getTableName('catalog/product_website') . "` 
                       WHERE website_id = " . $iDefaultWebsiteId . "
            ";
            
            $oDb->query($sSql);
            
            // insert temp connection with default website to products 
            
            $sSql = "
INSERT into `" . Mage::getSingleton('core/resource')->getTableName('catalog/product_website') . "` (`product_id`, `website_id`)

(SELECT 
`main_table`.`entity_id`, 
" . $iDefaultWebsiteId . " as `website_id`
FROM `" . Mage::getSingleton('core/resource')->getTableName('catalog/product') . "` AS `main_table` WHERE `main_table`.`entity_id` > 0)
            ";
            
            $oDb->query($sSql);
            
            // insert inventory items
 $isGreater = version_compare(Mage::getVersion(), '1.11.0.0',"ge"); 
 $isGreater112 = version_compare(Mage::getVersion(), '1.12.0.0',"ge"); 
 
            $sSql = "
INSERT into `" . $sTableAitocItem . "`

(SELECT 
null as 'item_id',

`p`.`website_id`, 

`main_table`.`product_id`, 
`main_table`.`stock_id`, 
`main_table`.`qty`, 
`main_table`.`min_qty`, 
`main_table`.`use_config_min_qty`, 
`main_table`.`is_qty_decimal`, 
`main_table`.`backorders`, 
`main_table`.`use_config_backorders`, 
`main_table`.`min_sale_qty`, 
`main_table`.`use_config_min_sale_qty`, 
`main_table`.`max_sale_qty`, 
`main_table`.`use_config_max_sale_qty`, 
`main_table`.`is_in_stock`, 
`main_table`.`low_stock_date`, 
`main_table`.`notify_stock_qty`, 
`main_table`.`use_config_notify_stock_qty`, 
`main_table`.`manage_stock`, 
`main_table`.`use_config_manage_stock`,  

1 as 'use_default_website_stock',

`main_table`.`use_config_qty_increments`, 
`main_table`.`qty_increments`, 
`main_table`.`enable_qty_increments`";

if($isGreater)
{    
    $sSql .=  ",`main_table`.`use_config_enable_qty_inc`";    
    $sSql .=  ",`main_table`.`stock_status_changed_auto`"; 
}
else
{
    $sSql .=  ",`main_table`.`stock_status_changed_automatically`";
    $sSql .=  ",`main_table`.`use_config_enable_qty_increments`";
}                
if($isGreater112)
{
    $sSql .= ", `main_table`.`is_decimal_divided`";
}
                
$sSql .= "FROM `" . $sTableMagentoItem . "` AS `main_table`
INNER JOIN `" . Mage::getSingleton('core/resource')->getTableName('catalog/product_website') . "` AS `p` ON main_table.product_id=p.product_id
LEFT JOIN `" . $sTableAitocItem . "` AS `ait_item` ON ait_item.product_id=p.product_id AND ait_item.website_id = p.website_id

WHERE (ait_item.website_id IS NULL))            
            ";
            $oDb->query($sSql);
            
            // insert inventory status
            
            $sSql = "
INSERT into `" . $sTableAitocStatus . "`

(SELECT 

`main_table`.`product_id`, 

`p`.`website_id`, 

`main_table`.`stock_id`, 
`main_table`.`qty`, 
`main_table`.`stock_status` 

FROM `" . $sTableMagentoStatus . "` AS `main_table`
INNER JOIN `" . Mage::getSingleton('core/resource')->getTableName('catalog/product_website') . "` AS `p` ON (main_table.product_id=p.product_id)
LEFT JOIN `" . $sTableAitocStatus . "` AS `ait_status` ON ait_status.product_id=p.product_id AND ait_status.website_id = p.website_id

WHERE (ait_status.website_id IS NULL AND main_table.website_id = 1))            
            ";

            $oDb->query($sSql);
            
            // delete temp connection with default website to products 
            
            $sSql = " DELETE FROM `" . Mage::getSingleton('core/resource')->getTableName('catalog/product_website') . "` 
                       WHERE website_id = " . $iDefaultWebsiteId . "
            ";
            
            $oDb->query($sSql);
            
            // delete old default website id 
            $sSql = 'DELETE FROM ' . Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_settings') . ' WHERE code="default_website_id"';
            
            $oDb->query($sSql);
            
            // set settings
            
            $sSql = "INSERT INTO `" . Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_settings') . "` (
`code` ,
`value`
)
VALUES (
'inventory_convert_completed' , 1
);";
            
            $oDb->query($sSql);
            
        }
        
        Mage::register('aitoc_inventory_loaded', true);
    }    
    
    public function onAitocModuleDisableBefore($observer)
    {
        if ('Aitoc_Aitquantitymanager' == $observer->getAitocmodulename())
        {
            $oInstaller = $observer->getObject();
            /* @var $oInstaller Aitoc_Aitsys_Model_Aitsys */

#            $iDisableMode  = Mage::getStoreConfig('cataloginventory/aitoc_settings/disable_transform_mode');
            $iDisableMode  = 1;
            
            if (!$iDisableMode)
            {
                $oInstaller->addCustomError('Please go to "System > Configuration > Catalog > Inventory > Aitoc Inventory Options" and choose either "Default value" or "Sum of all website values" option in "During Module Deactivation Convert Product Quantity To" setting to deactivate the module. If you are not sure about which option to choose, please read the user manual or contact us.');
            }
            else 
            {
                // process database transformation
                
                $oDb     = Mage::getModel('sales_entity/order')->getReadConnection();
                /* @var $oDb Varien_Db_Adapter_Pdo_Mysql */
                
                // insert inventory items
                
                $sTableAitocItem   = Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_item');
                
                $sTableMagentoItem = $sTableAitocItem;
                
                if (strpos($sTableMagentoItem, 'aitoc_') !== false)
                {
                    $sTableMagentoItem = str_replace('aitoc_', '', $sTableMagentoItem);
                }
                
                
                $sSql = 'DELETE FROM ' . $sTableMagentoItem . '';
                $oDb->query($sSql);
                
                if ($iDisableMode == 1) // use default values
                {
 $isGreater = version_compare(Mage::getVersion(), '1.11.0.0',"ge");               
 $isGreater112 = version_compare(Mage::getVersion(), '1.12.0.0',"ge");               
                    $sSql = "
                
INSERT into `" . $sTableMagentoItem . "`

(SELECT 

null as `item_id`,
`main_table`.`product_id`, 
`main_table`.`stock_id`, 
`main_table`.`qty`, 
`main_table`.`min_qty`, 
`main_table`.`use_config_min_qty`, 
`main_table`.`is_qty_decimal`, 
`main_table`.`backorders`, 
`main_table`.`use_config_backorders`, 
`main_table`.`min_sale_qty`, 
`main_table`.`use_config_min_sale_qty`, 
`main_table`.`max_sale_qty`, 
`main_table`.`use_config_max_sale_qty`, 
`main_table`.`is_in_stock`, 
`main_table`.`low_stock_date`, 
`main_table`.`notify_stock_qty`, 
`main_table`.`use_config_notify_stock_qty`, 
`main_table`.`manage_stock`, 
`main_table`.`use_config_manage_stock`,
`main_table`.`use_config_qty_increments`, 
`main_table`.`qty_increments`,
`main_table`.`enable_qty_increments`"; 

if($isGreater)
{    
    $sSql .=  ",`main_table`.`use_config_enable_qty_inc`";    
    $sSql .=  ",`main_table`.`stock_status_changed_auto`"; 
}
else
{
    $sSql .=  ",`main_table`.`stock_status_changed_automatically`";
    $sSql .=  ",`main_table`.`use_config_enable_qty_increments`"; 
}
if($isGreater112)
{
    $sSql .= ", `main_table`.`is_decimal_divided`";
}


$sSql .= " FROM `" . $sTableAitocItem . "` AS `main_table`
WHERE (main_table.website_id = " . Mage::helper('aitquantitymanager')->getHiddenWebsiteId() . "))
                   ";
                }
                else // use sum values
                {
                    
                }
                #echo $sSql;exit;
                $oDb->query($sSql);
                
                // insert inventory status
                
                $sTableAitocStatus   = Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_status');
                
                $sTableMagentoStatus = $sTableAitocStatus;
                
                if (strpos($sTableMagentoStatus, 'aitoc_') !== false)
                {
                    $sTableMagentoStatus = str_replace('aitoc_', '', $sTableMagentoStatus);
                }
                
                
                $sSql = 'DELETE FROM ' . $sTableMagentoStatus . '';
                $oDb->query($sSql);
                
                $sSql = "
                
INSERT into `" . $sTableMagentoStatus . "`

(SELECT 

`main_table`.`product_id`, 
`p`.`website_id`, 
`main_table`.`stock_id`, 
`main_table`.`qty`, 
`main_table`.`stock_status`

FROM `" . $sTableAitocStatus . "` AS `main_table`
INNER JOIN `" . Mage::getSingleton('core/resource')->getTableName('catalog/product_website') . "` AS `p` ON main_table.product_id=p.product_id
WHERE (main_table.website_id = " . Mage::helper('aitquantitymanager')->getHiddenWebsiteId() . "))
                ";
                
                $oDb->query($sSql);
                
                // set settings
                
                $sSql = "INSERT INTO `" . Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_settings') . "` (
`code` ,
`value`
)
VALUES (
'default_website_id' , '" . Mage::helper('aitquantitymanager')->getHiddenWebsiteId() . "'
);";
            
                $oDb->query($sSql);
                
                $sSql = 'DELETE FROM ' . Mage::getSingleton('core/resource')->getTableName('aitquantitymanager/stock_settings') . ' WHERE code="inventory_convert_completed"';
                $oDb->query($sSql);
                
                
                // delete default website
                
                $sSql = 'DELETE FROM ' . Mage::getSingleton('core/resource')->getTableName('core/website') . ' WHERE code="aitoccode"';
                $oDb->query($sSql);
                
            }
        }
    }
    
}