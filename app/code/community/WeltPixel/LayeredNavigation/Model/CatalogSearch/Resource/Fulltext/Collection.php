<?php

class WeltPixel_LayeredNavigation_Model_CatalogSearch_Resource_Fulltext_Collection extends WeltPixel_LayeredNavigation_Model_Catalog_Resource_Product_Collection
{
    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    protected function _getQuery()
    {
        return Mage::helper('catalogsearch')->getQuery();
    }

    /**
     * Add search query filter
     *
     * @param string $query
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function addSearchFilter($query)
    {
        Mage::getSingleton('catalogsearch/fulltext')->prepareResult();

        $this->getSelect()->joinInner(
            array('search_result' => $this->getTable('catalogsearch/result')),
            $this->getConnection()->quoteInto(
                'search_result.product_id=e.entity_id AND search_result.query_id=?',
                $this->_getQuery()->getId()
            ),
            array('relevance' => 'relevance')
        );

        return $this;
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        if ($attribute == 'relevance') {
            $this->getSelect()->order("relevance {$dir}");
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Stub method for campatibility with other search engines
     *
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function setGeneralDefaultQuery()
    {
        return $this;
    }
}
