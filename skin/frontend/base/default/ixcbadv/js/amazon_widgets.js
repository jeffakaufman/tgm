/**
 * Mageix LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Mageix LLCs  End User License Agreement
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

var AmazonWidgets = function (merchant_id, module_base_path){
	this.merchantId = merchant_id;
	
	
	this.createStandardWidget = function(orderpath, form, standardcallback) {		
		this.orderpath = orderpath;
		this.formelement = form;
		this.standardcallback = standardcallback;
		this.standardWidget = new CBA.Widgets.StandardCheckoutWidget({
			merchantId:this.merchantId,
				orderInput: {
					format: "HTML",
					value: this.formelement
				},
			onOpen : standardcallback
		});	
	}
	
	
	this.createInlineButton = function(ghhg,rud,sizeval,colorval,limit,scopeval){
               var seller = this.merchantId;
               OffAmazonPayments.Button(ghhg, seller, {
               type: rud,
               color: colorval,
               size: sizeval,
                    useAmazonAddressBook: true,
                         authorization: function() {
                            loginOptions = {scope: scopeval};
                            authRequest = amazon.Login.authorize(loginOptions);
                         },
                       onSignIn: function(orderReference) {
                           authRequest.onComplete(limit+"?session="+ orderReference.getAmazonOrderReferenceId());
                       },
                       onError: function(error) {
                           // your error handling code
                           alert(error.getErrorCode() + ": " + error.getErrorMessage());
                       }
               });
         }

	
	
	
	
        this.getUrlVars = function() {
                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
                });
        return vars;
        }


	this.createAddressWidget = function(path,target,widthval,heightval,displayMode,amazonOrderReferenceId,kuvak) {
		var seller = this.merchantId;
		amazonOrderReferenceId = this.getUrlVars()["session"];
        new OffAmazonPayments.Widgets.AddressBook({
		  sellerId: seller,
		  displayMode: displayMode,
		  design: {
		  size : {width:widthval, height:heightval}
		  },
		  amazonOrderReferenceId: amazonOrderReferenceId,  
		  onAddressSelect: function(orderReference) {
			addressSelected (path, target);
		  },
		  
		  onError: function(error) {
		   // your error handling code
		  }
		}).bind(kuvak);
	};
	
	
	
	
	
	this.createWalletWidget = function(widthval,heightval,displayMode,amazonOrderReferenceId,kuvak) {
		var seller = this.merchantId;
		amazonOrderReferenceId = this.getUrlVars()["session"];
        new OffAmazonPayments.Widgets.Wallet({
		  sellerId: seller,
		  displayMode: displayMode,
		  design: {
		  size : {width:widthval, height:heightval}
		  },
		  amazonOrderReferenceId: amazonOrderReferenceId,  
		  onPaymentSelect: function(orderReference) {
			document.getElementById("confirmAction").style.display = "inline-block";
			document.getElementById("confirmMessagePayment").style.display = "none";
		  },
		  
		  onError: function(error) {
		   // your error handling code
		  }
		}).bind(kuvak);
	};
	
	
	
	
	
	
	this.setAddressId = function (address_id){
		this.address_id = address_id;
	}
	
	this.renderWidget = function (widget,kvid){
		
	}



};



var checkHanleClose = '';var checkHanleCloseOnlySuccess = '';var yesvarexistsornot = ''; var notlcoseexistance = ''; var yesopenexistance = '';
function checkExistanceVar() { for(var A in CBA.Widgets.mediator.registry) { yesvarexistsornot = 'loaded'; var B=CBA.Widgets.mediator.registry[A];
if(typeof (B.popupWindow)!="undefined"&&typeof (B.popupWindowName)!="undefined"&&!B.popupWindow.closed) { yesopenexistance = 'yes'; } else { setTimeout("checkExistanceVar()",2500); } }	 if(yesvarexistsornot == '') { setTimeout("checkExistanceVar()", 2500); } if(yesopenexistance == 'yes') { notlcoseexistance = ''; setTimeout("isExistanceVarClose('checkExistanceVar')", 2500); yesopenexistance = ''; } }
function isExistanceVarClose(valueget) { for(var A in CBA.Widgets.mediator.registry) { var B=CBA.Widgets.mediator.registry[A];
if(typeof (B.popupWindow)!="undefined"&&typeof (B.popupWindowName)!="undefined"&&!B.popupWindow.closed) {} else { notlcoseexistance = 'yes'; yesvarexistsornot = ''; accordion.openSection('ixcba-orderitems'); } } if(notlcoseexistance == '') {setTimeout("isExistanceVarClose('isExistanceVarClose')", 2500);} }

