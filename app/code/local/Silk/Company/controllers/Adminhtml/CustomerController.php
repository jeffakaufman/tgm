<?php

require_once 'Mage/Adminhtml/controllers/CustomerController.php';

class Silk_Company_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
    /**
     * Customers list action
     */
    public function indexAction()
    {
        if($this->getRequest()->getParam('is_b2b')){
            $this->_title($this->__('Companies'))->_title($this->__('Manage Companies'));
        }
        else
        {
            $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));
        }


        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customer/manage');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/customer', 'customer')
        );

        /**
         * Add breadcrumb item
         */
        //echo "OK".$this->getRequest()->getParam('is_b2b');
        if($this->getRequest()->getParam('is_b2b')){
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Companies'), Mage::helper('adminhtml')->__('Companies'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Companies'), Mage::helper('adminhtml')->__('Manage Companies'));
        }else{
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Customers'), Mage::helper('adminhtml')->__('Customers'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Customers'), Mage::helper('adminhtml')->__('Manage Customers'));
        }
        $this->renderLayout();
    }

	public function saveAction()
	{
		$data = $this->getRequest()->getPost();
        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', false);
            $this->_initCustomer('customer_id');

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->ignoreInvisible(false)
            ;

            $formData = $customerForm->extractData($this->getRequest(), 'account');

            // Handle 'disable auto_group_change' attribute
            if (isset($formData['disable_auto_group_change'])) {
                $formData['disable_auto_group_change'] = empty($formData['disable_auto_group_change']) ? '0' : '1';
            }

            $errors = null;
            if ($customer->getId()&& !empty($data['account']['new_password'])
                //&& Mage::helper('customer')->getIsRequireAdminUserToChangeUserPassword()
            ) {
                //Validate current admin password
                if (isset($data['account']['current_password'])) {
                    $currentPassword = $data['account']['current_password'];
                } else {
                    $currentPassword = null;
                }
                unset($data['account']['current_password']);
                $errors = '';//$this->_validateCurrentPassword($currentPassword);
            }

            if (!is_array($errors)) {
                $errors = $customerForm->validateData($formData);
            }

            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_getSession()->addError($error);
                }
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }

            $customerForm->compactData($formData);

            //if customer is b2b and role not ADMIN skip address update
            //if(!$data['account']['is_b2b'] || ($data['account']['is_b2b']&&$data['account']['role_id'] == Silk_Company_Model_Role::ADMIN))
            //{
            // Unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();
            if (!empty($data['address'])) {
                /** @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);

                foreach (array_keys($data['address']) as $index) {
                    $address = $customer->getAddressItemById($index);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                    }

                    $requestScope = sprintf('address/%s', $index);
                    $formData = $addressForm->setEntity($address)
                        ->extractData($this->getRequest(), $requestScope);

                    // Set default billing and shipping flags to address
                    $isDefaultBilling = isset($data['account']['default_billing'])
                        && $data['account']['default_billing'] == $index;
                    $address->setIsDefaultBilling($isDefaultBilling);
                    $isDefaultShipping = isset($data['account']['default_shipping'])
                        && $data['account']['default_shipping'] == $index;
                    $address->setIsDefaultShipping($isDefaultShipping);

                    $errors = $addressForm->validateData($formData);
                    if ($errors !== true) {
                        foreach ($errors as $error) {
                            $this->_getSession()->addError($error);
                        }
                        $this->_getSession()->setCustomerData($data);
                        $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array(
                            'id' => $customer->getId())
                        ));
                        return;
                    }


                    $addressForm->compactData($formData);

                    // Set post_index for detect default billing and shipping addresses
                    $address->setPostIndex($index);

                    if ($address->getId()) {
                        $modifiedAddresses[] = $address->getId();
                    } else {
                        $customer->addAddress($address);
                    }
                }
                // }

                // Default billing and shipping
                if (isset($data['account']['default_billing'])) {
                    $customer->setData('default_billing', $data['account']['default_billing']);
                }
                if (isset($data['account']['default_shipping'])) {
                    $customer->setData('default_shipping', $data['account']['default_shipping']);
                }
                if (isset($data['account']['confirmation'])) {
                    $customer->setData('confirmation', $data['account']['confirmation']);
                }
            }
            // Mark not modified customer addresses for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && !in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', true);
                }
            }

            if (Mage::getSingleton('admin/session')->isAllowed('customer/newsletter')
                && !$customer->getConfirmation()
            ) {
                $customer->setIsSubscribed(isset($data['subscription']));
            }

            if (isset($data['account']['sendemail_store_id'])) {
                $customer->setSendemailStoreId($data['account']['sendemail_store_id']);
            }

            $isNewCustomer = $customer->isObjectNew();
            try {
                $sendPassToEmail = false;
                // Force new customer confirmation
                if ($isNewCustomer) {
                    $customer->setPassword($data['account']['password']);
                    $customer->setForceConfirmed(true);
                    if ($customer->getPassword() == 'auto') {
                        $sendPassToEmail = true;
                        $customer->setPassword($customer->generatePassword());
                    }
                }

                //new customer we need create a new company and bind to customer
                if($data['account']['is_b2b'])
                {
                	if($data['account']['company_id'] && $data['account']['role_id']==Silk_Company_Model_Role::getRole('ADMIN'))
	                {

	                	$company = Mage::getModel('company/company')->load($data['account']['company_id'], 'company_id');
	                	if(!$company || !$company->getId())
	                	{

	                		$company->setCompanyId($data['account']['company_id']);
	                		$company->setName($data['account']['company_name']);
	                		//$company->setPaymentMethods($data['account']['payment_methods']);
	                		$company->save();
	                	}
	                }
	                else
	                {

	                	if($data['account']['role_id'] == Silk_Company_Model_Role::getRole('ADMIN'))
	                	{
		                	$company = Mage::getModel('company/company')->load($data['account']['company_id'], 'company_id');
		                	$company->setName($data['account']['company_name']);
	                		//$company->setPaymentMethods($data['account']['payment_methods']);
	                		$company->save();
                		}
	                }
                }
                

                Mage::dispatchEvent('adminhtml_customer_prepare_save', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                $customer->save();

                // Send welcome email
                if ($customer->getWebsiteId() && (isset($data['account']['sendemail']) || $sendPassToEmail)) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    } elseif ((!$customer->getConfirmation())) {
                        // Confirm not confirmed customer
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                if (!empty($data['account']['new_password'])) {
                    $newPassword = $data['account']['new_password'];
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The customer has been saved.')
                );
                Mage::dispatchEvent('adminhtml_customer_save_after', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $customer->getId(),
                        '_current' => true
                    ));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            } catch (Exception $e) {
                //$this->_getSession()->addException($e,
                //    Mage::helper('adminhtml')->__('An error occurred while saving the customer.'));
                $this->_getSession()->addException($e, $e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=>$customer->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/customer'));
	}	
   
}
