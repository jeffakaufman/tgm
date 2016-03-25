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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_Rules extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);

        $headerBar = $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_rules_create');

        $productFieldset = $form->addFieldset('feed_tab_rule_product', array('legend' => __('Product Filters')));

        $headerBar->getConfig()
            ->setLabel(__('Create New Product Filter'))
            ->setType(Mirasvit_FeedExport_Model_Rule::TYPE_ATTRIBUTE)
            ->setFeed($model->getId())
            ->setGroupId('feed_tab_rule_product');

        $productFieldset->setHeaderBar($headerBar->toHtml());

        $collection = Mage::getModel('feedexport/rule')->getCollection()->addTypeAttributeFilter();
        foreach ($collection as $rule) {
            $this->_addRuleToFieldset($rule, $productFieldset, $model);
        }

        $performanceFieldset = $form->addFieldset('feed_tab_rule_performance', array('legend' => __('Performance Filters')));

        $headerBar->getConfig()
            ->setLabel(__('Create New Performance Filter'))
            ->setType(Mirasvit_FeedExport_Model_Rule::TYPE_PERFORMANCE)
            ->setFeed($model->getId())
            ->setGroupId('feed_tab_rule_performance');

        $performanceFieldset->setHeaderBar($headerBar->toHtml());

        $collection = Mage::getModel('feedexport/rule')->getCollection()->addTypePerformanceFilter();
        foreach ($collection as $rule) {
            $this->_addRuleToFieldset($rule, $performanceFieldset, $model);
        }

        return parent::_prepareForm();
    }

    protected function _addRuleToFieldset($rule, $fieldset, $feed)
    {
        $fieldset->addField('rule'.$rule->getId(), 'checkbox', array(
            'label'    => $rule->getName(),
            'name'     => 'rule_ids['.$rule->getId().']',
            'checked'  => is_array($feed->getRuleIds()) ? in_array($rule->getId(), $feed->getRuleIds()) : false,
            'required' => false,
            'note'     => $rule->toString(),
        ));

        return $this;
    }
}
