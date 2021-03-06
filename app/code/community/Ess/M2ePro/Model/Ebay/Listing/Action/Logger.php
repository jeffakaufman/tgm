<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Ebay_Listing_Action_Logger
{
    protected $action = NULL;
    protected $actionId = NULL;
    protected $initiator = Ess_M2ePro_Helper_Data::INITIATOR_UNKNOWN;

    protected $storeMode = false;
    protected $storedMessages = array();

    protected $status = Ess_M2ePro_Helper_Data::STATUS_SUCCESS;

    //########################################

    /**
     * @param int $id
     */
    public function setActionId($id)
    {
        $this->actionId = (int)$id;
    }

    /**
     * @return null|int
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    // ---------------------------------------

    /**
     * @param int $value
     */
    public function setAction($value)
    {
        $this->action = (int)$value;
    }

    /**
     * @param int $value
     */
    public function setInitiator($value)
    {
        $this->initiator = (int)$value;
    }

    //########################################

    public function setStoreMode($value)
    {
        $this->storeMode = (bool)$value;
    }

    public function getStoredMessages()
    {
        return $this->storedMessages;
    }

    // ---------------------------------------

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        if ($status == Ess_M2ePro_Helper_Data::STATUS_ERROR) {
            $this->status = Ess_M2ePro_Helper_Data::STATUS_ERROR;
            return;
        }

        if ($this->status == Ess_M2ePro_Helper_Data::STATUS_ERROR) {
            return;
        }

        if ($status == Ess_M2ePro_Helper_Data::STATUS_WARNING) {
            $this->status = Ess_M2ePro_Helper_Data::STATUS_WARNING;
            return;
        }

        if ($this->status == Ess_M2ePro_Helper_Data::STATUS_WARNING) {
            return;
        }

        $this->status = Ess_M2ePro_Helper_Data::STATUS_SUCCESS;
    }

    //########################################

    protected function initLogType($messageDataType)
    {
        switch ($messageDataType) {
            case Ess_M2ePro_Model_Connector_Protocol::MESSAGE_TYPE_ERROR:
                    $resultType = Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR;
                    $this->setStatus(Ess_M2ePro_Helper_Data::STATUS_ERROR);
                break;
            case Ess_M2ePro_Model_Connector_Protocol::MESSAGE_TYPE_WARNING:
                    $resultType = Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING;
                    $this->setStatus(Ess_M2ePro_Helper_Data::STATUS_WARNING);
                break;
            case Ess_M2ePro_Model_Connector_Protocol::MESSAGE_TYPE_SUCCESS:
                    $resultType = Ess_M2ePro_Model_Log_Abstract::TYPE_SUCCESS;
                    $this->setStatus(Ess_M2ePro_Helper_Data::STATUS_SUCCESS);
                break;
            case Ess_M2ePro_Model_Connector_Protocol::MESSAGE_TYPE_NOTICE:
                    $resultType = Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE;
                    $this->setStatus(Ess_M2ePro_Helper_Data::STATUS_SUCCESS);
                break;
            default:
                    $resultType = Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR;
                    $this->setStatus(Ess_M2ePro_Helper_Data::STATUS_ERROR);
                break;
        }

        return $resultType;
    }

    //########################################
}