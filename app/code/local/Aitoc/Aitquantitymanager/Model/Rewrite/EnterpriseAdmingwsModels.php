<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitquantitymanager_Model_Rewrite_EnterpriseAdmingwsModels extends Enterprise_AdminGws_Model_Models
{
    /**
     * Catalog product initialize after loading
     *
     * @param Mage_Catalog_Model_Product $model
     * @return void
     */
    public function catalogProductLoadAfter($model)
    {
        if (!$model->getId()) {
            return;
        }

        if (!$this->_role->hasWebsiteAccess($model->getWebsiteIds())) {
            $this->_throwLoad();
        }

        if (!$this->_role->hasExclusiveAccess($model->getWebsiteIds())) {
            $model->unlockAttributes();

            $attributes = $model->getAttributes();
            foreach ($attributes as $attribute) {
                /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                if ($attribute->isScopeGlobal() ||
                    ($attribute->isScopeWebsite() && count($this->_role->getWebsiteIds())==0) ||
                    !in_array($model->getStore()->getId(), $this->_role->getStoreIds())) {
                    $model->lockAttribute($attribute->getAttributeCode());
                }
            }
            /*AITOC FIX START*/
            if(!in_array($model->getStore()->getWebsiteId(),$this->_role->getWebsiteIds()))
            {
                $model->setInventoryReadonly(true);
            }
            /*AITOC FIX END*/
            $model->setRelatedReadonly(true);
            $model->setCrosssellReadonly(true);
            $model->setUpsellReadonly(true);
            $model->setWebsitesReadonly(true);
            $model->lockAttribute('website_ids');
            $model->setOptionsReadonly(true);
            $model->setCompositeReadonly(true);
            $model->setDownloadableReadonly(true);
            $model->setGiftCardReadonly(true);
            $model->setIsDeleteable(false);
            $model->setIsDuplicable(false);
            $model->unlockAttribute('category_ids');

            foreach ($model->getCategoryCollection() as $category) {
                $path = implode("/", array_reverse($category->getPathIds()));
                if(!$this->_role->hasExclusiveCategoryAccess($path)) {
                    $model->setCategoriesReadonly(true);
                    $model->lockAttribute('category_ids');
                    break;
                }
            }

            if (!$this->_role->hasStoreAccess($model->getStoreIds())) {
                $model->setIsReadonly(true);
            }
        } else {
            /*
             * We should check here amount of websites to which admin user assigned
             * and not to those product itself. So if admin user assigned
             * only to one website we will disable ability to unassign product
             * from this one website
             */
            if (count($this->_role->getWebsiteIds()) == 1) {
                $model->setWebsitesReadonly(true);
                $model->lockAttribute('website_ids');
            }
        }
    }        
}
