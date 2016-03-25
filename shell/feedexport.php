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


require_once 'abstract.php';

class Mirasvit_Shell_FeedExport extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        set_time_limit(36000);

        if ($this->getArg('generate')) {
            $feeds = $this->_parseFeedString($this->getArg('generate'));
            foreach ($feeds as $feed) {
                $this->generate($feed);
            }
        } elseif ($this->getArg('control')) {
            $method = $this->getArg('method');
            $feedId = $this->getArg('feed');
            $feed = Mage::getModel('feedexport/feed')->load($feedId);
            call_user_func(array($feed, $method));
        } elseif ($this->getArg('ping')) {
            echo '1';
        } else {
            echo $this->usageHelp();
        }
    }

    protected function _parseFeedString($string)
    {
        $feeds = array();
        if ($string == 'all') {
            $collection = Mage::getModel('feedexport/feed')->getCollection()
                ->addFieldToFilter('is_active', 1);
            foreach ($collection as $feed) {
                $feed = $feed->load($feed->getId());
                $feeds[] = $feed;
            }
        } else if (!empty($string)) {
            $ids = explode(',', $string);
            foreach ($ids as $feedId) {
                $feed = Mage::getModel('feedexport/feed')->load(trim($feedId));
                if (!$feed) {
                    echo 'Warning: Unknown feed with id ' . trim($feedId) . "\n";
                } else {
                    $feeds[] = $feed;
                }
            }
        }

        return $feeds;
    }

    public function generate($feed)
    {
        $ts = microtime(true);

        $name = '['.$feed->getId().'] '.$feed->getName();
        echo $name.str_repeat('.', 50 - strlen($name)).PHP_EOL;
        $status = null;
        $feed->getGenerator()->getState()->reset();
        $feed->generateCli(true);
        echo 'done'.PHP_EOL;
    }

    public function _validate()
    {

    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f feedexport.php -- [options]

  --generate all      Generate all active feeds
  --generate <id>     Generate Feed with ID <id>

USAGE;
    }
}

$shell = new Mirasvit_Shell_FeedExport();
$shell->run();