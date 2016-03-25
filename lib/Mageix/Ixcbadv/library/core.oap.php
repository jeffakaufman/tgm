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


		require_once "Crypt/HMAC.php";
		
		class Core
		{
			
				static function generate_base64_hmac_sha1($secretkey, $strToSign)
				{	
					return base64_encode(hash_hmac("sha256", $strToSign, $secretkey, True));
				}
		
				static function generateSignature($secret, $paramsArray, $hosturl, $method, $uri)
				{

					$sorted_string_to_encode = Core::sortedParams($paramsArray,true);


                                        if ($method == '') {					
                                            $method = "POST";
                                         }
           
					if(isset($hosturl) && $hosturl != '') {
						$host   = $hosturl;
					} else {
						$host   = "mws.amazonservices.com";
					}
					
					$str = $method."\n".$host."\n".$uri."\n".$sorted_string_to_encode;

					$signature = Core::generate_base64_hmac_sha1($secret, $str);
					return $signature;
				}
				
				
				
				static function sortedParams($paramArray, $isUrl)
		 		{
				 		$first = true;
				 		$sortedQuery="";
				 		$sorted_hmac_data="";
						foreach ($paramArray as $key => $value) 
						{
							if ($first) 
							{
								$first = false;
							} else 
							{
								$sortedQuery .= "&";
							}
							
							$sorted_hmac_data .= $key . $value;
							 if(Core::isSimulationCommand($key) == "true"){
								$sortedQuery .= $key . "=" . $value;
							}else {
							$sortedQuery .= $key . "=" . rawurlencode($value);
						    }

						}
						
							if($isUrl){
								return $sortedQuery;
							}else{
								return $sorted_hmac_data;
								//return $sortedQuery;
				             }
					}
					
		      static function isSimulationCommand($key)
                {

                $encoded_values = array();
                $encoded_values = array('SellerAuthorizationNote');

                foreach ($encoded_values as $encoded_value) {
 
                    $found = stripos($key, $encoded_value);
                    $encoded = "false";
                    
                    if($found !== false){
                     $encoded = "true";
                    break;
                    }
         
                  }
                  
                  return $encoded;
        
               }	

}


?>