<?php


class Silk_Company_EmployeeController extends Mage_Core_Controller_Front_Action
{
    public function listAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function saveAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if($customer->getRoleId() != Silk_Company_Model_Role::getRole('ADMIN')) {
            $this->norouteAction();
			return;
        }
        $id = $this->getRequest()->getParam('id');
        if($id)
        {
            $customer = Mage::getModel("customer/customer")->load($id);
            $errors = array();
            if ($this->getRequest()->getParam('change_password')) {
                $newPass    = $this->getRequest()->getPost('password');
                $confPass   = $this->getRequest()->getPost('confirmation');

                if (strlen($newPass)) {
                    /**
                     * Set entered password and its confirmation - they
                     * will be validated later to match each other and be of right length
                     */
                    $customer->setPassword($newPass);
                    $customer->setPasswordConfirmation($confPass);
                    $customer->sendPasswordReminderEmail();
                } else  {
                    $errors[] = $this->__('New password field cannot be empty.');
                }
            }

            if($errors)
            {
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
            }
        }
        else
        {
            //new Customer
            $customer = Mage::getModel("customer/customer");
            $customer->setWebsite(Mage::app()->getWebsite());
            $customer->setCompanyId($this->_getSession()->getCustomer()->getCompanyId());
            $customer->setCompanyName($this->_getSession()->getCustomer()->getCompanyName());
            $customer->setGroupId($this->_getSession()->getCustomer()->getGroupId());
            $customer->setIsB2b(1);
            $newPass = $this->getRequest()->getPost('password');
            $confPass = $this->getRequest()->getPost('confirmation');
            $customer->setPassword($newPass);
            $customer->setPasswordConfirmation($confPass);
            $customer->setEmail($this->getRequest()->getParam('email'));
            $customer->sendNewAccountEmail();
            $customer->setActive(1);
        }
        $customer->setFirstname($this->getRequest()->getParam('firstname'));
        $customer->setMiddlename($this->getRequest()->getParam('middlename'));
        $customer->setLastname($this->getRequest()->getParam('lastname'));
        $customer->setRoleId($this->getRequest()->getParam('role_id'));
        try
        {
            $customer->cleanAllAddresses();
            $customer->save();
            $this->_getSession()->addSuccess("Employee was saved!");
            $this->_redirect('*/*/list');
        }
        catch(Exception $e)
        {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/list');
        }
    }

    public function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
