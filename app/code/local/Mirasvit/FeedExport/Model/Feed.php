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


class Mirasvit_FeedExport_Model_Feed extends Mage_Core_Model_Abstract
{
    protected $_store       = null;
    protected $_rule        = null;
    protected $_trackerRule = null;
    protected $_generator   = null;
    protected $_state       = null;

    protected function _construct()
    {
        $this->_init('feedexport/feed');
    }

    public function getStore()
    {
        if (!$this->_store) {
            $this->_store = Mage::getModel('core/store')->load($this->getStoreId());
        }

        return $this->_store;
    }

    public function getRuleIds()
    {
        if ($this->hasData('rule_ids') || is_array($this->getData('rule_ids'))) {
            return $this->getData('rule_ids');
        }

        return array();
    }

    public function getNotificationEvents()
    {
        if (!is_array($this->getData('notification_events'))) {
            $this->setNotificationEvents(explode(',', $this->getData('notification_events')));
        }

        return $this->getData('notification_events');
    }

    public function getGenerator()
    {
        if (!$this->_generator) {
            $this->_generator = Mage::getModel('feedexport/feed_generator');
            $this->_generator->setFeed($this)
                ->init();
        }

        return $this->_generator;
    }

    public function getUrl()
    {
        if (file_exists(Mage::getBaseDir('media').DS.'feed'.DS.$this->getFilenameWithExt())) {
            return Mage::getBaseUrl('media').'feed'.DS.$this->getFilenameWithExt();
        }

        return false;
    }

    public function getFormat()
    {
        $this->_format = Mage::helper('feedexport/format')
            ->parseFormat($this->getXmlFormat());

        return $this->_format;
    }

    public function generate()
    {
        $generator = $this->getGenerator();

        if (Mage::registry('current_state')) {
            Mage::unregister('current_state');
        }
        Mage::register('current_state', $generator->getState());

        $appEmulation           = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->getStore()->getId());
        Mage::getConfig()->loadEventObservers('frontend');
        Mage::app()->addEventArea('frontend');

        $generator->process();

        Mage::helper('feedexport')->resetCurrentStore();

        if ($generator->getState()->isReady()) {
            $this->setGeneratedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setGeneratedCnt($generator->getState()->getChainItemValue('iterator_product', 'size'))
                ->setGeneratedTime(Mage::getSingleton('core/date')->gmtTimestamp() - $generator->getState()->getCreatedAt())
                ->save();

            Mage::dispatchEvent('feedexport_generation_success', array('feed' => $this));
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $generator->getState()->getStatus();
    }

    public function generateCli($verbose = false)
    {
        $requestHelper = Mage::helper('feedexport/request');
        $status        = $this->getGenerator()->getState()->getStatus();

        $this->getGenerator()->getState()->reset();

        $status = null;
        while ($status != Mirasvit_FeedExport_Model_Feed_Generator_State::STATUS_READY) {
            $requestHelper->request('generate', $this);
            $status = $this->getGenerator()->getState()->getStatus();

            if ($status == Mirasvit_FeedExport_Model_Feed_Generator_State::STATUS_ERROR) {
                break;
            }

            if ($verbose) {
                echo $this->getGenerator()->getState()->__toString().PHP_EOL;
            }

            $this->getGenerator()->getState()->resetTimeout();
        }
    }

    public function delivery()
    {
        if (!$this->getFtp()) {
            return false;
        }

        try {
            Mage::helper('feedexport/io')->uploadFile(
                $this->getFtpHost(),
                $this->getFtpUser(),
                $this->getFtpPassword(),
                $this->getFtpPassiveMode(),
                $this->getFtpPath(),
                Mage::getSingleton('feedexport/config')->getBasePath(),
                $this->getFilenameWithExt()
            );

            $this->setUploadedAt(Mage::getSingleton('core/date')->gmtDate())
                ->save();
            Mage::dispatchEvent('feedexport_delivery_success', array('feed' => $this));
        } catch (Exception $e) {
            Mage::dispatchEvent('feedexport_delivery_fail', array('feed' => $this, 'error' => $e->getMessage()));
            Mage::throwException($e);
        }

        Mage::helper('feedexport')->addToHistory($this, __('Delivery'), __('Success delivery to %s', $this->getFtpHost()));

        return true;
    }

    public function getFilenameWithExt()
    {
        $file = $this->getData('filename');
        if (strpos($file, '.') === false) {
            $file .= '.'.$this->getData('type');
        }

        return $file;
    }

    public function getHistoryCollection()
    {
        return Mage::getModel('feedexport/feed_history')->getCollection()
            ->addFieldToFilter('feed_id', $this->getId())
            ->setOrder('created_at', 'desc');
    }

    public function addToHistory($title, $message = null, $type = null)
    {
        Mage::getModel('feedexport/feed_history')
                ->setFeedId($this->getId())
                ->setTitle($title)
                ->setMessage($message)
                ->setType($type)
                ->save();

        return $this;
    }

    /**
     * @todo rename to loadFromTemplate
     */
    public function fromTemplate($templateId)
    {
        $template = Mage::getModel('feedexport/template')->load($templateId);
        $this->addData($template->getData());

        return $this;
    }

    public function canRunCron()
    {
        $result = false;
        $day    = Mage::getSingleton('core/date')->date('w');
        $time   = Mage::getSingleton('core/date')->date('G') * 60 + Mage::getSingleton('core/date')->date('m');

        if (in_array($day, $this->getCronDay())) {
            foreach ($this->getCronTime() as $cronTime) {
                if (abs($cronTime - $time) < 25) {
                    $result = $cronTime;
                    break;
                }
            }
        }

        return $result;
    }
}