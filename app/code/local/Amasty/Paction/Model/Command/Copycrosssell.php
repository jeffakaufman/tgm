<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
class Amasty_Paction_Model_Command_Copycrosssell extends Amasty_Paction_Model_Command_Copyrelate
{
    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label = 'Copy Cross-sells';

        $this->_fieldLabel = 'From';
    }
}