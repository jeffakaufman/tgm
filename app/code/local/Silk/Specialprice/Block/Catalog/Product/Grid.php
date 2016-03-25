<?php
/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Silk_Specialprice_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid {
	/**
	 * [_prepareColumns description]
	 * @return [object] [grid table]
	 */
	protected function _prepareColumns() {
		$store = $this->_getStore();
		$this->addColumnAfter('special_price', array(
				'header'        => Mage::helper('catalog')->__('Special Price'),
				'type'          => 'price',
				'currency_code' => $store->getBaseCurrency()->getCode(),
				'index'         => 'special_price',
			), 'price');

		return parent::_prepareColumns();
	}

	/**
	 * Sets sorting order by some column
	 *
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _setCollectionOrder($column) {
		$collection = $this->getCollection();
		if ($collection) {
			$columnIndex = $column->getFilterIndex()?$column->getFilterIndex():$column->getIndex();
			$sort        = strtoupper($column->getDir());
			if ($columnIndex == 'special_price') {
				$collection->addAttributeToSort($columnIndex, $sort);
			} else {
				$collection->setOrder($columnIndex, $sort);
			}
		}

		return $this;
	}

	protected function _prepareCollection() {
		$store      = $this->_getStore();
		$collection = Mage::getModel('catalog/product')->getCollection()
		                                               ->addAttributeToSelect('sku')
		                                               ->addAttributeToSelect('name')
		                                               ->addAttributeToSelect('attribute_set_id')
		                                               ->addAttributeToSelect('type_id');

		if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
			$collection->joinField('qty',
				'cataloginventory/stock_item',
				'qty',
				'product_id=entity_id',
				'{{table}}.stock_id=1',
				'left');
		}
		if ($store->getId()) {
			//$collection->setStoreId($store->getId());
			$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
			$collection->addStoreFilter($store);
			$collection->joinAttribute(
				'name',
				'catalog_product/name',
				'entity_id',
				null,
				'inner',
				$adminStore
			);
			$collection->joinAttribute(
				'custom_name',
				'catalog_product/name',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute(
				'status',
				'catalog_product/status',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute(
				'visibility',
				'catalog_product/visibility',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute(
				'price',
				'catalog_product/price',
				'entity_id',
				null,
				'left',
				$store->getId()
			);
			$collection->joinAttribute(
				'special_price',
				'catalog_product/special_price',
				'entity_id',
				null,
				'left',
				$store->getId()
			);
		} else {
			$collection->addAttributeToSelect('price');
			$collection->addAttributeToSelect('special_price');
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
		}

		$this->setCollection($collection);

		Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
		$this->getCollection()->addWebsiteNamesToResult();

		return $this;
	}
}
