<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.2
 * @revision  268
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Feed_Generator_Pattern_Product
    extends Mirasvit_FeedExport_Model_Feed_Generator_Pattern
{
    private static $_parentProductsCache = array();
    private $_dynamicCategory            = array();

    public function getValue($pattern, $product)
    {
        $value   = null;
        $pattern = $this->parsePattern($pattern);

        $this->evalValue($pattern, $value, $product);

        if ($pattern['type'] == 'parent') {
            $product = $this->_getParentProduct($product);
        }

        if ($pattern['type'] == 'grouped') {
            $products             = $this->_getChildProducts($product);
            $values               = array();
            $childPattern         = $pattern;
            $childPattern['type'] = null;
            foreach ($products as $child) {
                $child = $child->load($child->getId());
                $value = $this->getValue($childPattern, $child);
                if ($value) {
                    $values[] = $value;
                }
            }

            $value = implode(',', $values);

            return $value;
        }

        switch($pattern['key']) {
            case 'url':
                $value = $product->getProductUrl(false);

                if ($this->getFeed()) {
                    if (strpos($value, '?') !== false) {
                        $value .= '&';
                    } else {
                        $value .= '?';
                    }
                    $value .= 'fee='.$this->getFeed()->getId().'&fep='.$product->getId();

                    $patternModel = Mage::getSingleton('feedexport/feed_generator_pattern');
                    $value .= $this->getFeed()->getGaSource()
                        ? '&utm_source='.$patternModel->getPatternValue($this->getFeed()->getGaSource(), 'product', $product) : '';
                    $value .= $this->getFeed()->getGaMedium()
                        ? '&utm_medium='.$patternModel->getPatternValue($this->getFeed()->getGaMedium(), 'product', $product) : '';
                    $value .= $this->getFeed()->getGaName()
                        ? '&utm_name='.$patternModel->getPatternValue($this->getFeed()->getGaName(), 'product', $product) : '';
                    $value .= $this->getFeed()->getGaTerm()
                        ? '&utm_term='.$patternModel->getPatternValue($this->getFeed()->getGaTerm(), 'product', $product) : '';
                    $value .= $this->getFeed()->getGaContent()
                        ? '&utm_content='.$patternModel->getPatternValue($this->getFeed()->getGaContent(), 'product', $product) : '';
                }

                break;

            case 'image':
            case 'thumbnail':
            case 'small_image':
                $this->imageValue($pattern, $value, $product);
                break;

            case 'image2':
            case 'image3':
            case 'image4':
            case 'image5':
            case 'image6':
            case 'image7':
            case 'image8':
            case 'image9':
            case 'image10':
            case 'image11':
            case 'image12':
            case 'image13':
            case 'image14':
            case 'image15':
                $this->imageGalleryValue($pattern, $value, $product);

                break;

            case 'qty':
                $stockItem = $product->getStockItem();
                if (!($stockItem && $stockItem->getData('item_id'))) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                }
                if ($stockItem && $stockItem->getData('item_id')) {
                    $product->setStockItem($stockItem);
                    $value = ceil($stockItem->getQty());
                } else {
                    $value = 0;
                }
                break;

            case 'is_in_stock':
                $stockItem = $product->getStockItem();
                if (!($stockItem && $stockItem->getData('item_id'))) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                }
                if ($stockItem) {
                    $value = $stockItem->getIsInStock();
                } else {
                    $value = 0;
                }
                break;

            case 'category_id':
                $this->_prepareProductCategory($product);
                $value = $product->getData('category_id');
                break;

            case 'category':
                $this->_prepareProductCategory($product);
                $value = $product->getCategory();
                break;

            case 'category_url':
                $this->_prepareProductCategory($product);
                if ($product->getCategoryModel()) {
                    $value = $product->getCategoryModel()->getUrl();
                }
                break;

            case 'category_path':
                $this->_prepareProductCategory($product);
                $value = $product->getCategoryPath();
                break;

            case 'price':
                $value = Mage::helper('tax')->getPrice($product, $product->getPrice());
                break;

            case 'final_price':
                if ($product->getTypeId() == 'bundle') {
                    $bundle = Mage::getModel('bundle/product_price');
                    $prices = $bundle->getTotalPrices($product);
                    if (isset($prices[0])) {
                        $value = $prices[0];
                        break;
                    }
                } else {
                    $value = Mage::helper('tax')->getPrice($product, $product->getFinalPrice());
                }

                break;

            case 'store_price':
                $value = $this->getStore()->convertPrice($product->getFinalPrice(), false, false);
                break;

            case 'base_price':
                $value = $product->getPrice();
                break;

            case 'tier_price':
                $tierPrice = $product->getTierPrice();
                if (count($tierPrice)) {
                    $value = $tierPrice[0]['price'];
                }
                break;

            case 'attribute_set':
                $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
                $attributeSetModel->load($product->getAttributeSetId());

                $value = $attributeSetModel->getAttributeSetName();
                break;

            case 'weight':
                if ($product->getTypeId() == 'bundle') {
                    $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                        $product->getTypeInstance(true)->getOptionsIds($product), $product
                    );
                    $productIds = array(0);
                    foreach($selectionCollection as $option) {
                        $productIds[] = $option->product_id;
                    }
                    $collection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('weight')
                        ->addFieldToFilter('entity_id', array('in' => $productIds));
                    $value = 0;
                    foreach ($collection as $subProduct) {
                        $value += $subProduct->getWeight();
                    }
                } else {
                    $value = $product->getData('weight');
                }
                break;

            case 'rating_summary':
                $summaryData = Mage::getModel('review/review_summary')->load($product->getId());
                $value       = $summaryData->getRatingSummary() * 0.05;
                break;

            case 'reviews_count':
                $summaryData = Mage::getModel('review/review_summary')->load($product->getId());
                $value       = $summaryData->getReviewsCount();
                break;

            default:
                $attribute = $this->_getProductAttribute($pattern['key']);
                if ($attribute) {
                    if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                        $value = $product->getResource()
                            ->getAttribute($pattern['key'])
                                ->getSource()
                                ->getOptionText($product->getData($pattern['key']));
                        $value = implode(', ', (array) $value);
                    } else {
                        $value = $product->getData($pattern['key']);
                    }
                } else {
                    if ($product->hasData($pattern['key'])) {
                        $value = $product->getData($pattern['key']);
                    }
                }
        }

        $this->dynamicAttributeValue($pattern, $value, $product);
        $this->dynamicCategoryValue($pattern, $value, $product);
        $this->amastyMetaValue($pattern, $value, $product);

        $value = $this->applyFormatters($pattern, $value);

        return $value;
    }

    public function evalValue($arPattern, &$value, $obj)
    {
        if (substr($arPattern['key'], 0, 1) == '(') {
            extract($obj->getData());
            $eval  = substr($arPattern['key'], 1, strlen($arPattern['key']) - 2);
            $value = eval($eval);
        }
    }

    public function imageValue($arPattern, &$value, $obj)
    {
        $mediaUrl = Mage::getBaseUrl('media');
        $image    = $obj->getData($arPattern['key']);
        if (!$image || $image == 'no_selection') {
            $value = '';
        } else {
            if ($arPattern['additional']) {
                $size = explode('x', $arPattern['additional']);
                $w = intval($size[0]) ? intval($size[0]) : null;
                $h = intval($size[1]) ? intval($size[1]) : null;

                $value = Mage::helper('mstcore/image')->init($obj, $arPattern['key'], 'catalog/product')->resize($w, $h)->__toString();
            } else {
                $value = $mediaUrl.'catalog/product'.$image;
            }
        }
    }

    public function imageGalleryValue($arPattern, &$value, $obj)
    {
        $mediaUrl = Mage::getBaseUrl('media');
        if (!$obj->hasData('media_gallery_images')) {
            $tmpProduct = Mage::getModel('catalog/product')->load($obj->getId());
            $obj->setData('media_gallery_images', $tmpProduct->getMediaGalleryImages());
        }
        $i = 1;
        foreach ($obj->getMediaGalleryImages() as $image) {
            if ('image'.$i == $arPattern['key']) {
                $value = $image['url'];
            }
            $i ++;
        }
    }

    public function dynamicAttributeValue($arPattern, &$value, $obj)
    {
        if ($arPattern['key'] == 'custom') {
            $customAttribute = Mage::getModel('feedexport/dynamic_attribute')->getCollection()
                ->addFieldToFilter('code', $arPattern['additional'])
                ->getFirstItem();
            $customAttribute = $customAttribute->load($customAttribute->getId());
            if ($customAttribute->getId()) {
                $value = $customAttribute->getValue($obj);
            }
        }
    }

    public function dynamicCategoryValue($arPattern, &$value, $obj)
    {
        if ($arPattern['key'] == 'mapping') {
            $this->_prepareProductCategory($obj);

            $mappingId = $arPattern['additional'];
            if (!isset($this->_dynamicCategory[$mappingId])) {
                $this->_dynamicCategory[$mappingId] = Mage::getModel('feedexport/dynamic_category')->load($mappingId);
            }

            if ($this->_dynamicCategory[$mappingId]->getId()) {
                $mappingCategory = $this->_dynamicCategory[$mappingId];

                $value = $mappingCategory->getMappingValue($obj->getData('category_id'));
            }
        }
    }

    public function amastyMetaValue($arPattern, &$value, $obj)
    {
        if ($arPattern['key'] == 'ammeta') {
            $amHelper      = Mage::helper('ammeta');
            $attributeCode = $arPattern['additional'];
            $arPattern     = Mage::getStoreConfig('ammeta/product/'.$attributeCode);

            if ($arPattern) {
                $value = $amHelper->parse($obj, $arPattern);

                $max = (int) Mage::getStoreConfig('ammeta/general/max_'.$attributeCode);

                if ($max) {
                    $value = substr($value, 0, $max);
                }
            }
        }
    }

    protected function _getParentProduct(Varien_Object $product)
    {
        if (!isset(self::$_parentProductsCache[$product->getEntityId()])) {
            $connection = Mage::getSingleton('core/resource')->getConnection('read');
            $table      = Mage::getSingleton('core/resource')->getTableName('catalog_product_relation');

            $parentProduct = null;

            $parentId = $connection->fetchOne(
                'SELECT `parent_id` FROM '.$table.' WHERE `child_id` = '.intval($product->getEntityId())
            );

            if ($parentId > 0) {
                if (!$parentProduct) {
                    $parentProduct = Mage::getModel('catalog/product')->load($parentId);
                }

                self::$_parentProductsCache[$product->getEntityId()] = $parentProduct;
            } else {
                self::$_parentProductsCache[$product->getEntityId()] = $product;
            }
        }

        return self::$_parentProductsCache[$product->getEntityId()];
    }

    protected function _getChildProducts($product)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('read');
        $table      = Mage::getSingleton('core/resource')->getTableName('catalog_product_relation');
        $childIds   = array(0);

        $rows = $connection->fetchAll(
            'SELECT `child_id` FROM '.$table.' WHERE `parent_id` = '.intval($product->getEntityId())
        );

        foreach ($rows as $row) {
            $childIds[] = $row['child_id'];
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $childIds));

        return $collection;
    }

    protected function _prepareProductCategory(&$product, $iteration = 0)
    {
        if ($iteration > 5) {
            return;
        }

        $category = null;
        foreach ($product->getCategoryIds() as $id) {
            $_category = $this->getCategory($id);
            if ($_category) {
                if (is_null($category) || ($category->getLevel() < $_category->getLevel())) {
                    $category = $_category;
                }
            }
        }

        if ($category) {
            $categoryPath = array($category->getName());
            $parentId     = $category->getParentId();

            if ($category->getLevel() > $this->getRootCategory()->getLevel()) {
                $i = 0;
                while ($_category = $this->getCategory($parentId)) {
                    // if parent category is not active, we looping by wrong tree
                    if ($_category->getIsActive() == 0) {
                        $this->_categories[$category->getId()] = false;
                        $this->_prepareProductCategory($product, $iteration + 1);
                        return;
                    }

                    if ($_category->getLevel() <= $this->getRootCategory()->getLevel()) {
                        break;
                    }
                    $categoryPath[] = $_category->getName();
                    $parentId       = $_category->getParentId();

                    $i++;
                    if ($i > 10 || $parentId == 0) {
                        break;
                    }
                }
            }

            $product->setCategory($category->getName());
            $product->setCategoryModel($category);
            $product->setCategoryId($category->getEntityId());
            $product->setCategoryPath(implode(' > ', array_reverse($categoryPath)));
        } else {
            $product->setCategory('');
            $product->setCategorySubcategory('');
        }
    }
}