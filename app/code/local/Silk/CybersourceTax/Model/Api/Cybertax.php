 
<?php

/**
 * CyberSourceTax SOAP communications class.
 *
 * @copyright 2013, Silk SoftWare
 * @version 1.0, 06.14.2010
 * @link https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.53.xsd
 * @license GPL
 * @todo Add reporting functionality
 */

 class Silk_CybersourceTax_Model_Api_Cybertax extends SoapClient
 {

    /**
     * The reply object flattened for simplicity.
     *
     * @var Object
     */
     public $reply;
    
    /**
     * A transaction success flag for the current action.
     *
     * @var Boolean
     */
     public $success = FALSE;
    
    /**
     * The CyberSource merchant id.
     *
     * @var String
     */
     private $user;
    
    /**
     * The merchant password.
     *
     * This is usually the transaction key obtained from CyberSource.
     *
     * @var String
     */
     private $password;
    
    /**
     * A currency code.
     *
     * Default is US dollars.
     *
     * @var String
     */
     private $currency = 'USD';
    
    /**
     * Flag to indicate if the transaction was successfully authorized.
     *
     * @var Boolean
     */
     private $isAuthorized = FALSE;
    
    /**
     * Flag to indicate if the transaction was successfully captured.
     *
     * @var Boolean
     */
     private $isCaptured = FALSE;
    
    /**
     * Flag to indicate if the transaction was successfully reversed.
     *
     * @var Boolean
     */
     private $isReversed = FALSE;
    
    /**
     * Flag to indicate if the transaction was succesfully credited.
     *
     * @var Boolean
     */
     private $isCredited = FALSE;
    
    /**
     * Internal raw SOAP reply.
     *
     * @var String
     */
     private $rawReply;
    
    /**
     * Internal request object.
     *
     * @var Object
     */
     private $obj;
    
    /**
     * SOAP header.
     *
     * This is provided by CyberSource from their documentation.
     *
     * @var String
     */
    
    private $soapHeader = "<SOAP-ENV:Header xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\"><wsse:Security SOAP-ENV:mustUnderstand=\"1\"><wsse:UsernameToken><wsse:Username>%s</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">%s</wsse:Password></wsse:UsernameToken></wsse:Security></SOAP-ENV:Header>";
    
    /**
     * An array of preset conversion values.
     *
     * The merchant reference code can be assembled using these values from the associated keys passed
       by the user.
     *
     * @var Array
     */
     private $rcArray = array();
    
    /**
     * The minimum length of the random number.
     *
     * @var Int
     */
     private $rndmMin = 6;
    
    /**
     * The maximum length of the random number.
     *
     * This cannot be longer than the length of the largest possible random number.
     *
     * @var Int
     */
     private $rndmMax;
    
    /**
     * An array of basic core objects that should not be sent again when requesting a follow-on service.
     *
     * @var Array
     */
     private $unsetArray = array('card',
     'billTo',
     'shipTo',
     'item',
     'merchantDefinedData',
     'merchantSecureData',
     'ccAuthService');
    
    /**
     * The schema help object.
     *
     * @var Object
     */
     private $help = NULL;
    
    /**
     * The URL for the CyberSource transaction processor schema information.
     *
     * @var String
     */
     private $helpURL;
    
    /**
     * The XML schema element label that matches the complex data types.
     *
     * @var String
     */
     private $complexLabel = '<xsd:complexType';
    
    /**
     * The Merchant Defined Data size limitation as determined by CyberSource.
     *
     * @var Const
     */
     const MDD_LIMIT = 4;
    
    /**
     * The Merchant Defined Data element name as determined by CyberSource.
     *
     * @var Const
     */
     const MDD_ELEMENT_NAME = 'field';
    
    /**
     * Client library value used by CyberSource for debugging.
     *
     * @var Const
     */
     const CLIENT_LIBRARY = "PHP";
     
     const WSDL_URL_TEST = 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.53.wsdl';
     const WSDL_URL_LIVE = 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.53.wsdl';
    
    /**
     * Class constructor.
     *
     * @param String $url
     * @param String $user
     * @param String $password
     * @see CLIENT_LIBRARY
     * @throws Exception
     */
     public function __construct()
     {
       
         $user = Mage::helper('core')->decrypt(Mage::getStoreConfig('tax/cybersourcetax_soap/merchant_id'));
         $password = Mage::helper('core')->decrypt(Mage::getStoreConfig('tax/cybersourcetax_soap/security_key'));
         $url = Mage::getStoreConfig('tax/cybersourcetax_soap/test') ? self::WSDL_URL_TEST  : self::WSDL_URL_LIVE;
          
         $this->obj = new stdClass();
         try
         {
             if (!extension_loaded('openssl'))
               throw new Exception (__FUNCTION__ . '::OpenSSL extension is not loaded.');
             parent::__construct($url);
             $this->user = $user;
             $this->password = $password;
             if (empty($user) || empty($password))
              throw new Exception (__FUNCTION__ . '::Username and Password must be provided.');
             $this->rcArray = array('YY'=>date('y'),'YYYY'=>date('Y'),'MM'=>date('m'),'M'=>date('n'),
                                    'DD'=>date('d'),'D'=>date('j'),'J'=>date('z')+1,'W'=>date('W'),
                                    'HH'=>date('H'),'H'=>date('h'),'I'=>date('i'),'AP'=>date('A'),
                                    'RNDM'=>mt_rand());
             $this->rndmMax = strlen(mt_getrandmax());
             $this->helpURL = dirname($url) . '/';
            
             if (is_object($this->obj))
             {
                 $this->obj->merchantID = $this->user;
                 $this->obj->clientLibrary = self::CLIENT_LIBRARY;
                 $this->obj->clientLibraryVersion = phpversion();
                 $this->obj->clientEnvironment = php_uname();
             }
             else throw new Exception (__FUNCTION__ . '::Request object could not be created.');
         }
         catch (Exception $e)
         {
            exit($e->getMessage());
         }
     }
    
    /**
     * SOAP request override method.
     *
     * Creates the SOAP request object before being sent over to the server for processing.
     *
     * @param Object $request
     * @param String $location
     * @param String $action
     * @param String $version
     * @throws DOMException
     * @link http://www.php.net/soapclient
     * @return Object
     */
     public function __doRequest($request, $location, $action, $version, $oneWay = 0)
     {
         $header = sprintf("$this->soapHeader",$this->user,$this->password);
         $requestDOM = new DOMDocument('1.0');
         $soapHeaderDOM = new DOMDocument('1.0');
         try
         {
             $requestDOM->loadXML($request);
             $soapHeaderDOM->loadXML($header);
             $node = $requestDOM->importNode($soapHeaderDOM->firstChild, true);
             $requestDOM->firstChild->insertBefore($node, $requestDOM->firstChild->firstChild);
             $request = $requestDOM->saveXML();
             $this->rawReply = parent::__doRequest($request, $location, $action, $version);
         }
         catch (DOMException $e)
         {
             exit(__FUNCTION__ . '::Error adding UsernameToken: ' . $e->code);
         }
         return $this->rawReply;
     }
    
    
    
    /**
     * Gets the raw SOAP reply data as XML text formatted with newlines.
     *
     * @return String
     */
     public function getSoapReply()
     {
         $this->rawReply = trim($this->rawReply);
         return (empty($this->rawReply)) ? NULL : str_replace('><',">\n<",$this->rawReply);
     }
    
    /**
     * Sets the merchant defined data fields.
     *
     * Only array values are needed as the key names are configured automatically.
     *
     * @param Array $array
     * @throws Exception
     * @see MDD_LIMIT
     * @see MDD_ELEMENT_NAME
     */
     public function setMerchantDefinedData($array = array())
     {
         $obj = (count($array) > 0) ? new stdClass() : NULL;
         try
         {
             if (count($array) > self::MDD_LIMIT) throw new Exception(__FUNCTION__ . '::Custom array is too large.');
             if (is_object($obj))
             {
                 for ($z = 0; $z < count($array); $z++)
                 {
                     $elem = sprintf("%s%d",self::MDD_ELEMENT_NAME,$z + 1);
                     $obj->$elem = $array[$z];
                 }
                 $this->obj->merchantDefinedData = $obj;
             }
             else throw new Exception(__FUNCTION__ . '::Custom data array is empty.');
         }
         catch (Exception $e)
         {
            exit($e->getMessage());
         }
     }
    
    /**
     * Gets the internal request object as is was sent to the server.
     *
     * @return Object
     */
     public function getSoapRequest()
     {
     return $this->obj;
     }
    
    /**
     * Sets the merchant reference code.
     *
     * This allows the user to quickly and dynamically create a merchant reference code based upon what
       keys are passed into the array.
     * Any values not present as array keys are assumed to be literal strings that the user wants in
       the reference code.
     *
     * Preset keys and their associated values are:
     *
     *   YY = 2 digit year.
     *
     *   YYYY = 4 digit year.
     *
     *   MM = Month with leading zero.
     *
     *   M = Month without leading zero.
     *
     *   DD = Day of the month with leading zero.
     *
     *   D = Day of the month without leading zero.
     *
     *   J = Julian day of the year (1 - 366).
     *
     *   W = Week number of the year (starting on Monday).
     *
     *   HH = 24 hour clock with leading zeros.
     *
     *   H = 12 hour clock with leading zeros.
     *
     *   I = Minutes with leading zeros.
     *
     *   AP = AM or PM (uppercase).
     *
     *      RNDM = Random number.
     *
     * The RNDM key may be passed as a String or Array.  If passed as an Array, then the following apply:
     *
     *     RNDM[0] = 'RNDM'.
     *
     *     RNDM[1] = Minimum length of the random number [Optional].
     *
     *     RNDM[2] = Maximum length of the random number [Optional].
     *
     * Examples:
     *
     *         array('EPS','-','YYYY','J','-',array('RNDM',6,8)).
     *
     *         array('YY','MM','DD','AP','-','RNDM').
     *
     * All keys are case sensitive.
     *
     * @param Array $code
     * @throws Exception
     */
     public function setReferenceCode($code = array())
     {
         $ref_code = '';
         try
         {
             if (!is_array($code)) throw new Exception(__FUNCTION__ . '::Input parameter is not an array.');
             else
             {
                 if (count($code) == 1) $ref_code = $code[0];
                 else
                 {
                     foreach ($code as $k)
                     {
                         if (!is_array($k))
                         {
                             if (array_key_exists($k,$this->rcArray)) $ref_code .= sprintf("%s",$this->rcArray[$k]);
                             else $ref_code .= sprintf("%s",$k);
                         }
                         else
                         {
                             if ($k[0] == 'RNDM')
                             {
                                 $min = (array_key_exists(1,$k)) ? $k[1] : $this->rndmMin;
                                 $max = (array_key_exists(2,$k)) ? $k[2] : $this->rndmMax;
                                 if ($min > $max)
                                 throw new Exception(__FUNCTION__ . '::MIN random number limit is larger than MAX random number limit.');
                                 else if ($min < 1)
                                 throw new Exception(__FUNCTION__ . '::MIN random number limit is invalid.');
                                 else if ($max > $this->rndmMax)
                                 throw new Exception(__FUNCTION__ . '::MAX random number limit is invalid.');
                                 else
                                 {
                                     do $rand = sprintf("%s",substr(mt_rand(),0,$max));
                                     while (strlen($rand) < $min);
                                     $ref_code .= $rand;
                                 }
                             }
                             else throw new Exception(__FUNCTION__ . '::Array key of ' . $k[0] . ' is not valid.');
                         }
                     }
                 }
                 $this->obj->merchantReferenceCode = $ref_code;
             }
         }
         catch (Exception $e)
         {
         exit($e->getMessage());
         }
     }
    
    
    
     /**
     *  Request to cybersource
     *
     * @param String $billArray, $itemArray
     * @throws Exception
     */
     public function setTaxRequest($billArray = NULL,   $itemArray = NULL)
     {
        
        $taxService = new stdClass();
        $taxService->run = 'true';
        $this->obj->taxService = $taxService;
            
         try
         {
             if (is_array($billArray)) $this->obj->billTo = self::setBillingData($billArray);
             else throw new Exception(__FUNCTION__ . '::Billing information must be an array.');
            
             if (is_array($itemArray)) $this->obj->item = self::setItemData($itemArray);
             else throw new Exception(__FUNCTION__ . '::Item information must be an array.');
         }
         catch (Exception $e)
         {
            exit($e->getMessage());
         }
     }
     
     
     
    /**
     * Sets the currency code.
     *
     * If called, it must be after a CC request object is created.
     * Calling this before the purchaseTotals object is created (which is typically at the end of
       the request object creation process),
     * will have no affect on changing the currency (i.e. from USD to CAD).
     *
     * @param String $code
     * @throws Exception
     */
     public function setCurrency($code = NULL)
     {
         try
         {
             if (empty($code)) throw new Exception(__FUNCTION__ . '::Currency code is empty.');
             if (!is_object($this->obj->purchaseTotals))
             throw new Exception(__FUNCTION__ . '::Currency cannot be set before the request object is created.');
             else $this->obj->purchaseTotals->currency = strtoupper(trim($code));
         }
         catch (Exception $e)
         {
            exit($e->getMessage());
         }
     }
    
    
    function runTax() {
         $this->reply = $this->runTransaction($this->obj);
         return $this->reply->taxReply;
    }
    
    
    /**
     * Sets the billing data object.
     *
     * Cycles through the key/value pairs and assigns them to the billTo object.
     *
     * @param Array $array
     * @throws Exception
     * @return Object
     */
     private function setBillingData($array = array())
     {
         $obj = (count($array) > 0) ? new stdClass() : NULL;
         try
         {
             if (is_object($obj))
             {
                foreach ($array as $k => $v) $obj->$k = $v;
             }
             else throw new Exception(__FUNCTION__ . '::Billing data array is empty.');
         }
         catch (Exception $e)
         {
            exit($e->getMessage());
         }
         return $obj;
     }
    
    /**
     * Sets the shipping data object.
     *
     * Cycles through the key/value pairs and assigns them to the shipTo object.
     *
     * @param Array $array
     * @throws Exception
     * @return Object
     */
     private function setShippingData($array = array())
     {
         $obj = (count($array) > 0) ? new stdClass() : NULL;
         if (!is_null($array))
         {
             try
             {
                 if (is_object($obj))
                 {
                    foreach ($array as $k => $v) $obj->$k = $v;
                 }
                 else throw new Exception(__FUNCTION__ . '::Shipping data array is empty.');
             }
             catch (Exception $e)
             {
                 exit($e->getMessage());
             }
         }
         return $obj;
     }
    
    
    
    /**
     * Sets the item data object.
     *
     * Cycles through the key/value pairs and assigns them to the item object.
     *
     * @param Array $array
     * @throws Exception
     * @return Object
     */
     private function setItemData($array = array())
     {
         $items = array();
         $i = 0;
         try
         {
             if (count($array) > 0)
             {
                 foreach ($array as $item)
                 {
                     $obj = new stdClass();
                     foreach ($item as $k => $v)
                     {
                         $obj->id = $i;
                         if ($k == 'unitPrice') $obj->$k = sprintf("%01.2f",$v);
                         else $obj->$k = $v;
                     }
                     $items[] = $obj;
                     $i++;
                     unset($obj);
                 }
             }
             //else throw new Exception(__FUNCTION__ . '::Item data is empty.');
         }
         catch (Exception $e)
         {
         exit($e->getMessage());
         }
         return $items;
     }
    
    /**
     * Sets the purchase totals object.
     *
     * If the item object is available, it uses this to calcuate purchase total cost.
     * For reversals, an amount value must be passed in since no items are available.
     *
     * @param String $amount
     * @throws Exception
     * @return Object
     */
     private function setPurchaseTotals($amount = NULL)
     {
         $obj = new stdClass();
         try
         {
            if (is_object($obj))
            {
             if (empty($obj->currency)) $obj->currency = $this->currency;
             if (is_null($amount))
             {
                 foreach ($this->obj->item as $item)
                 {
                     foreach ($item as $k => $v)
                     {
                         if ($k == 'unitPrice') $price = sprintf("%01.2f",$v);
                         if ($k == 'quantity') $count = sprintf("%d",$v);
                     }
                     $obj->grandTotalAmount = sprintf("%01.2f",($price * $count) + $obj->grandTotalAmount);
                 }
             }
                else $obj->grandTotalAmount = sprintf("%01.2f",$amount);
             }
             else throw new Exception(__FUNCTION__ . '::Purchase totals object could not be created.');
             }
         catch (Exception $e)
         {
             exit($e->getMessage());
         }
         return $obj;
     }


 }
?>
