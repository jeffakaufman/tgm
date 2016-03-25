<?php 
class Silk_QisCategory_Block_Navigation extends Mage_Catalog_Block_Navigation{
	/**
	 * @desc get menu icon
	 * @param string $str
	 */
	public function getMenuImg($str){
		$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."catalog/category/";
		$category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($str);
		$active = $category->getData('active');
		$inactive = $category->getData('inactive');
		$active = empty($active)?"":$mediaUrl.$active;
		$inactive = empty($inactive)?"":$mediaUrl.$inactive;
		$currentCategoty = "";
		if (in_array($category->getId(), Mage::getSingleton('catalog/layer')->getCurrentCategory()->getPathIds())) {
			$currentCategoty = 'currentCategory';
		}
		return "hover-img='{$active}'   general-img='{$inactive}'    active-category='{$currentCategoty}'";
	}
	
	
	/**
     * Retrieve child categories of current category
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getCurrentChildCategories()
    {
        $layer = Mage::getSingleton('catalog/layer');
        $category   = $layer->getCurrentCategory();
        if($category->getLevel()==3){
        	$category = $category->getParentCategory();
        }
        /* @var $category Mage_Catalog_Model_Category */
        $categories = $category->getChildrenCategories();
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $layer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($categories);
        return $categories;
    }
    
    /**
     * @desc  get current store all category
     */
    public function getAllCategory(){
    	$rootCategoryId = Mage::app()->getStore(Mage::app()->getStore()->getId())->getRootCategoryId();
    	$categoryObj = Mage::getModel('catalog/category');
    	$categoriesID = $categoryObj->load($rootCategoryId)->getAllChildren(true);
    	$arr = array();
    	foreach($categoriesID as $val){
    		$temp = Mage::getModel('catalog/category')->load($val);
    		if($temp->getLevel()==1){
    			continue;
    		}else{
    			$arr[] = $temp;
    		}
    	}
   		return $arr;
    }
}