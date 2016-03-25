<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

class Amasty_Paction_Model_Command_Replacetext extends Amasty_Paction_Model_Command_Abstract
{

    const REPLACE_MODIFICATOR = '->';

    const REPLACE_FIELD = 'value';

    const ENTITY_TYPE = 'catalog_product';

    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label      = 'Replace Text';
        $this->_fieldLabel = 'Replace';
    }

    /**
     * @return null|string
     */
    public function getEntityTypeId()
    {
        $entityType = Mage::getModel('eav/config')->getEntityType(self::ENTITY_TYPE);
        return $entityType->getEntityTypeId();
    }

    public function execute($ids, $storeId, $val)
    {
        $searchReplace = $this->_generateReplaces($val);
        $this->_searchAndReplace($searchReplace,$ids, $storeId);

        $success = Mage::helper('ampaction')->__('Total of %d products(s) have been successfully updated.', count($ids));
        return $success;
    }

    /**
     * @param string $inputText
     * @throws Exception
     * @return array
     */
    protected function _generateReplaces($inputText)
    {
        $modificatorPosition = stripos($inputText, self::REPLACE_MODIFICATOR);
        if (false === $modificatorPosition) {
           throw new Exception(Mage::helper('ampaction')->__('Replace field must contain: search->replace'));
        }
        $result['search'] = substr($inputText, 0, $modificatorPosition);
        $result['replace'] = trim(
            substr(
                $inputText, (strlen($result['search']) + strlen(self::REPLACE_MODIFICATOR)),
                strlen($inputText)
            )
        );

        $result['search'] = trim($result['search']);
        return $result;
    }

    /**
     * @param array $searchReplace
     * @param array $ids
     * @param int $storeId
     *
     * @return string
     */
    protected function _searchAndReplace($searchReplace, $ids, $storeId)
    {

        $entityTypeId = $this->getEntityTypeId();
        $attrGroups = $this->_getAttrGroups();
        /**
         * @var Magento_Db_Adapter_Pdo_Mysql
         */
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        foreach ($attrGroups as $backendType => $attrIds) {
            if ($backendType == 'static') {
                $set = '';
                foreach($attrIds as $attrId => $attrName) {
                    $set .= sprintf(
                        '`%s` = REPLACE(`%s`, %s, %s)', $attrName,
                        $attrName, $db->quote($searchReplace['search']),
                        $db->quote($searchReplace['replace'])
                    );
                }
                $sql = sprintf('UPDATE %scatalog_product_entity
                  SET %s
                  WHERE entity_id IN(%s)
                    AND entity_type_id = %d',
                    Mage::getConfig()->getTablePrefix(), $set, implode(',', $ids),
                    $entityTypeId
                );
            } else {
                $sql = sprintf('UPDATE %scatalog_product_entity_%s
                  SET `%s` = REPLACE(`%s`, %s, %s)
                  WHERE attribute_id IN (%s)
                    AND entity_id IN(%s)
                    AND entity_type_id = %d
                    AND store_id=%d',
                    Mage::getConfig()->getTablePrefix(), $backendType, self::REPLACE_FIELD, self::REPLACE_FIELD,
                    $db->quote($searchReplace['search']), $db->quote($searchReplace['replace']),
                    implode(',', array_keys($attrIds)), implode(',', $ids),
                    $entityTypeId, $storeId
                );
            }
            $db->query($sql);
        }

        return true;

    }

    /**
     * @return array
     */
    protected function _getAttrGroups()
    {
        $productAttributes = Mage::getStoreConfig('ampaction/general/replace_in_attr');
        $productAttributes = explode(',', $productAttributes);

        $attrGroups = array();
        foreach($productAttributes as $item) {
            $attribute = Mage::getSingleton('eav/config')->getAttribute(self::ENTITY_TYPE, $item);
            $attrGroups[$attribute->getBackendType()][$attribute->getId()] = $attribute->getName();
        }
        return $attrGroups;
    }

}

