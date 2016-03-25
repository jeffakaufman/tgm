<?php
/**
 * Mageix LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Mageix LLC's  End User License Agreement
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://ixcba.com/index.php/license-guide/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@mageix.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * 
 * Magento Mageix IXCBADV Module
 *
 * @category	Checkout & Payments, Customer Registration & Login
 * @package 	Mageix_Ixcbadv
 * @copyright   Copyright (c) 2014 -  Mageix LLC 
 * @author      Brian Graham
 * @license	    http://ixcba.com/index.php/license-guide/   End User License Agreement
 *
 *
 *
 * Magento Mageix IXCBA Module
 * 
 * @category   Checkout & Payments
 * @package	   Mageix_Ixcba
 * @copyright  Copyright (c) 2011 Mageix LLC
 * @author     Brian Graham
 * @license   http://mageix.com/index.php/license-guide/   End User License Agreement
 */

	
		require_once "HTTP/Request.php";
		require_once "core.oap.php";
		
		
class OAP
{
	var $_key        	= "";
	var $_secret     	= "";
	var $_endpoint   	= "";
	var $_date       	= null;
	var $_error      	= null;
	var $_corehost      = null;
	
	
	function OAP($key = null, $secret = null, $url = null, $urlcore = null)
	{
		if($key && $secret && $url)
		{
			$this->_key = $key;
			$this->_secret = $secret;
			$this->_endpoint = $url;
			$this->_corehost = $urlcore;
		}
	}
	
	function postHTTPRequest($setRequest, $path, $urlhost, $endpoint,$secret_key)
		{
			
			$query = $setRequest;
			
	
			
			$headers = "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
                        $headers .= "Content-Length: " . strlen($query) . "\r\n";

                        
                        $post = "POST " . $path . " HTTP/1.0\r\n";
                        $post .= "Host: " . $urlhost . "\r\n";
                        $post .= $headers;
                        $post .= "\r\n";
                        $post .= $query;

                        $scheme = '';
                        $endpointParts = parse_url($endpoint);

        switch ($endpointParts['scheme']) {
            case 'https':
                $scheme = 'ssl://';
                $port = $port === null ? 443 : $port;
                break;
            default:
                $scheme = '';
                $port = $port === null ? 80 : $port;
        }
        
        $response = '';
        if ($socket = @fsockopen($scheme . $urlhost, $port, $errno, $errstr, 10)) {
            fwrite($socket, $post);
            while (!feof($socket)) {
                $response .= fgets($socket, 1160);
            }
            fclose($socket);
            list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
            $other = preg_split("/\r\n|\n|\r/", $other);
            list($protocol, $code, $text) = explode(' ', trim(array_shift($other)), 3);

        } else {

            Mage::helper('ixcbadv')->throwException(
                Mage::helper('ixcbadv')->__('Unable to establish connection to host %s: %s', $urlhost, $errstr),
                $errno,
                array('area' => $this->_area)
            );
        }

        		$xmlresponse = substr_replace($response, '', 0, strpos($response, "\r\n\r\n")+4); 
			
			$xmlresponse = simplexml_load_string($xmlresponse);


				return 	$xmlresponse;

	}
	




               
		//makes URL from Array, signs, returns xml object response
		
		function sendPostRequest($reqArray, $path, $urlhost, $endpoint, $secret_key, $access_key)
		{

		$currentTimezone = @date_default_timezone_get();
		@date_default_timezone_set('GMT');
		$timestamp = date("Y-m-d\TH:i:s\Z");
		@date_default_timezone_set($currentTimezone);
		$SERVICE_VERSION = "2013-01-01";
		$SIGNATURE_VERSION = "2";
			
			$array1 = array();
			        
				$array1["AWSAccessKeyId"] = $access_key;
				$array1["Timestamp"] = $timestamp; 
				$array1["Version"] = $SERVICE_VERSION;
				$array1["SignatureMethod"] = "HmacSHA256";
				$array1["SignatureVersion"] = $SIGNATURE_VERSION;
				
			
			$mainarray = array_merge ($array1, $reqArray);

			ksort($mainarray);
			$method = "POST";
			
			$sorted_url = Core::sortedParams($mainarray, true);

			$signature = Core::generateSignature($secret_key, $mainarray, $urlhost, $method, $path);
			
			$setRequest = $sorted_url."&Signature=".urlencode($signature);
	
			
		$xmlresponse = $this->postHTTPRequest($setRequest, $path, $urlhost, $endpoint,$secret_key);

		return 	$xmlresponse;
		

			
		}
		
		
	
	
	function postAction($paramArray,$urlarray,$action)
	{

            $endpoint = $urlarray["endpoint"];
			$urlhost = $urlarray["urlhost"];
			$access_key = $urlarray["access_key"];
			$secret_key = $urlarray["secret_key"];
			$path = $urlarray["path"];

		$array = array();
		
		if ($action == "SetOrderReferenceDetails"){
                    if(!isset($array["OrderReferenceAttributes.PlatformId"]) || $array["OrderReferenceAttributes.PlatformId"] == ''){
		     $array["OrderReferenceAttributes.PlatformId"]  = "AONH3T53OTPEP";
		   }
		}

		if ($action == "Authorize"){
			if (Mage::getStoreConfig('payment/ixcbadv/order_note') != ''){
				
				$array["SellerAuthorizationNote"] = Mage::getStoreConfig('payment/ixcbadv/order_note');
			}
		  if (isset($paramArray["TransactionTimeout"]) && $paramArray["TransactionTimeout"] != ''){
		  	
		    $array["TransactionTimeout"] = $paramArray["TransactionTimeout"];
			  
		    unset($paramArray["TransactionTimeout"]);
		  }else {
		    $array["TransactionTimeout"] = 0;
		  }
		}
		
		$array["Action"] = $action;
		
		
		$reqArray = array_merge ($array, $paramArray);


		$xmlresponse = $this->sendPostRequest($reqArray, $path, $urlhost, $endpoint, $secret_key, $access_key);
		
		return 	$xmlresponse;
    
	}
	
	
}

?>