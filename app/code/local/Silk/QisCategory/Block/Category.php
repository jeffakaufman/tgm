<?php
class Silk_QisCategory_Block_Category extends Mage_Core_Block_Template{
	public function __construct(){}
	
	
	protected $_menu;
	
	/**
	 * Init top menu tree structure
	 */
	public function _construct()
	{
		$this->_menu = new Varien_Data_Tree_Node(array(), 'root', new Varien_Data_Tree());
	}
	
	
	public function getHtml(){
		$this->_menu->setOutermostClass($outermostClass);
		$this->_menu->setChildrenWrapClass($childrenWrapClass);
		
		$html = $this->_getHtml($this->_menu, $childrenWrapClass);
		return $html;
	}
	
	/**
	 * Recursively generates top menu html from data that is specified in $menuTree
	 *
	 * @param Varien_Data_Tree_Node $menuTree
	 * @param string $childrenWrapClass
	 * @return string
	 */
	protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
	{
		$html = '';
	
		$children = $menuTree->getChildren();
		$parentLevel = $menuTree->getLevel();
		$childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;
	
		$counter = 1;
		$childrenCount = $children->count();
	
		$parentPositionClass = $menuTree->getPositionClass();
		$itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';
	
		foreach ($children as $child) {
	
			$child->setLevel($childLevel);
			$child->setIsFirst($counter == 1);
			$child->setIsLast($counter == $childrenCount);
			$child->setPositionClass($itemPositionClassPrefix . $counter);
	
			$outermostClassCode = '';
			$outermostClass = $menuTree->getOutermostClass();
	
			if ($childLevel == 0 && $outermostClass) {
				$outermostClassCode = ' class="' . $outermostClass . '" ';
				$child->setClass($outermostClass);
			}
	
			$html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
			$html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
					. $this->escapeHtml($child->getName()) . '</span></a>';
	
			if ($child->hasChildren()) {
				if (!empty($childrenWrapClass)) {
					$html .= '<div class="' . $childrenWrapClass . '">';
				}
				$html .= '<ul class="level' . $childLevel . '">';
				$html .= $this->_getHtml($child, $childrenWrapClass);
				$html .= '</ul>';
	
				if (!empty($childrenWrapClass)) {
					$html .= '</div>';
				}
			}
			$html .= '</li>';
	
			$counter++;
		}
	
		return $html;
	}
	
	/**
	 * @desc general html
	 */
	public function getHtmlqqq(){
		$html = "";
		$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/category/';
		$categoryRootId = Mage::app()->getStore()->getRootCategoryId();
		$categoryRootObj = Mage::getModel("catalog/category")->load($categoryRootId);
		$categoryRootChildrens = $categoryRootObj->getChildrenCategories();
		if($categoryRootChildrens->count()){
			foreach ($categoryRootChildrens as $key=>$val){
				if($val->getIsActive()) {
					$_resource = $val->getResource();
					$html .= "<li>";
					$html .= "<a href='".$val->getUrl()."'>".$val->getName()."</a>";
					$html .= "<li>";
					$tempChildren = $val->getChildrenCategories();
					if($tempChildren->count()){
						foreach ($tempChildren as $kk=>$vv){
							if($vv->getIsActive()){
								$activeValue = $_resource->getAttributeRawValue($vv->getId(), 'active', Mage::app()->getStore());
								$inActiveValue = $_resource->getAttributeRawValue($vv->getId(), 'inactive', Mage::app()->getStore());
								$activeValue = empty($activeValue)?" ":$baseUrl.$activeValue;
								$inActiveValue = empty($inActiveValue)?" ":$baseUrl.$inActiveValue;
								$html .= "<li class='sub_cat'>";
								$html .= "<a href='".$vv->getUrl()."' hover-img='".$activeValue."' general-img='".$inActiveValue."'>".$vv->getName()."</a>";
								$html .= "<li>";
							}
						}
					}
				}
			}
		}
		return $html;
	}
}