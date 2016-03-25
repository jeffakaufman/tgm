<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

class Amasty_Paction_Model_Command_Appendtext extends Amasty_Paction_Model_Command_Abstract
{

    const MODIFICATOR = '->';

    const FIELD = 'value';

    const ENTITY_TYPE = 'catalog_product';

    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label      = 'Append Text';
        $this->_fieldLabel = 'Append';
    }

    public function execute($ids, $storeId, $val)
    {
        $appendRow = $this->_generateAppend($val);
        $this->_appendText($appendRow,$ids, $storeId);

        $success = Mage::helper('ampaction')->__('Total of %d products(s) have been successfully updated.', count($ids));
        return $success;
    }

    /**
     * @param string $inputText
     * @throws Exception
     * @return array
     */
    protected function _generateAppend($inputText)
    {
        $modificatorPosition = stripos($inputText, self::MODIFICATOR);
        if (false === $modificatorPosition) {
           throw new Exception(Mage::helper('ampaction')->__('Append field must contain: Attribute Code->Text to Append'));
        }
        $attributeCode = substr($inputText, 0, $modificatorPosition);

        $appendText = substr(
            $inputText, (strlen($attributeCode) + strlen(self::MODIFICATOR)),
            strlen($inputText)
        );

        $attributeCode = trim($attributeCode);

        return array($attributeCode, $appendText);
    }

    /**
     * @param $attributeCode
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function getAttributeByCode($attributeCode)
    {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(self::ENTITY_TYPE, $attributeCode);
        if (!$attribute) {
            throw new Exception(Mage::helper('ampaction')
                ->__(sprintf('Attribute was not found by code %s.', $attributeCode)));
        }
        return $attribute;
    }

    /**
     * @param array $searchReplace
     * @param array $ids
     * @param int $storeId
     *
     * @return void
     */
    protected function _appendText($searchReplace, $ids, $storeId)
    {

        list($attributeCode, $appendText) = $searchReplace;
        $attribute = $this->getAttributeByCode($attributeCode);

        /**
         * @var Magento_Db_Adapter_Pdo_Mysql
         */
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');

        $set = $this->_addSetSql($db->quote($appendText), $storeId);

        if ($attribute->getBackendType() == 'static') {

            $sql = sprintf('UPDATE %scatalog_product_entity
              SET %s
              WHERE entity_id IN(%s)
                AND entity_type_id = %d',
                Mage::getConfig()->getTablePrefix(), $set, implode(',', $ids),
                $attribute->getEntityTypeId()
            );
        } else {
            $sql = sprintf('UPDATE %scatalog_product_entity_%s
              SET %s
              WHERE attribute_id = %s
                AND entity_id IN(%s)
                AND entity_type_id = %d
                AND store_id=%d',
                Mage::getConfig()->getTablePrefix(), $attribute->getBackendType(),
                $set,
                $attribute->getId(), implode(',', $ids),
                $attribute->getEntityTypeId(), $storeId
            );
        }
        $db->query($sql);

    }

    /**
     * @param string $attributeCode
     * @param string $appendText
     * @param int $storeId
     *
     * @return string
     */
    protected function _addSetSql($appendText, $storeId)
    {
        $position = Mage::getStoreConfig('ampaction/general/append_text_position', $storeId);

        if ($position == Mage::helper('ampaction')->getAppendTextBefore()) {
            $firstPart = $appendText;
            $secondPart = self::FIELD;
        } else {
            $firstPart = self::FIELD;
            $secondPart = $appendText;
        }

        return sprintf(
            '`%s` = CONCAT(%s, %s)', self::FIELD, $firstPart, $secondPart
        );
    }

}

