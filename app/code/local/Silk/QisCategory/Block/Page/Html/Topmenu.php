<?php
class Silk_QisCategory_Block_Page_Html_Topmenu extends Mage_Page_Block_Html_Topmenu{
	/**
	 * Generates string with all attributes that should be present in menu item element
	 *
	 * @param Varien_Data_Tree_Node $item
	 * @return string
	 */
	protected function _getRenderedMenuItemAttributes(Varien_Data_Tree_Node $item)
	{
		$html = '';
		$attributes = $this->_getMenuItemAttributes($item);
		foreach ($attributes as $attributeName => $attributeValue) {
			$html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
		}
		if($item->getLevel()==1){
			$html .= ' ' .$this->getMenuImg($item->getId());
		}
		return $html;
	}
	
	
	/**
	 * @desc 
	 * @param string $str
	 */
	protected function getMenuImg($str=''){
		$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."catalog/category/";
		$tempArr = explode("-",$str);
		$categoryId = array_pop($tempArr);
		$category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($categoryId);
		$inactive = '';
		$currentCategoty = '';
		$active = '';
		if($category){
			$active = $category->getData('active');
			$inactive = $category->getData('inactive');
			$active = empty($active)?"":$mediaUrl.$active;
			$inactive = empty($inactive)?"":$mediaUrl.$inactive;
			$currentCategoty = "";
			if (in_array($category->getId(), Mage::getSingleton('catalog/layer')->getCurrentCategory()->getPathIds())) {
				$currentCategoty = 'currentCategory';	
			}
		}
		return "hover-img='{$active}'   general-img='{$inactive}'  active-category='{$currentCategoty}'";
	}
}