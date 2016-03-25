jQuery.noConflict();  
jQuery(document).ready(function (){
	var breakwordsnumber =parseInt( jQuery("input[id=breakwordsnumber]").val());
	for(i=0;i<=configuarbleProductString.length-1;i++){				
		if( !parseInt(configuarbleProductString[i].productstatus)){
			 jQuery("div[class=amconf-image-container]").each(function(index){
			 	var attribute = jQuery(this).find("img").attr("alt");
			 	var productColor = configuarbleProductString[i].productName;
			 	if(productColor.indexOf(attribute) >=0){
					var link =  jQuery(this).find("img").attr("src");
					link=link.substr(0,link.length-3);
					jQuery(this).find("img").attr("src",link+"png");
				}
			 });							
		}
	}
	if(breakwordsnumber){
		jQuery("div[class=amconf-image-container]").click(function(){		
			var content_h1=jQuery("h1[id=breakwords]");
			var breakwordstr=' ';			
			var breakwordsarr=content_h1.text().split(" ");
			for(i=0;i<breakwordsnumber;i++){
				breakwordstr+=breakwordsarr[i]+" ";
			}
			var verifystr= jQuery(this).find("img").attr("alt");
			var addtocart = jQuery("div[class=add-to-cart]");
			var qisinfocustomer = jQuery("div[class=qisinfo] p:first-child span");
			for(i=0;i<=configuarbleProductString.length-1;i++){				
				if((configuarbleProductString[i].productName).indexOf(verifystr) >=0){
					if( !parseInt(configuarbleProductString[i].productstatus)){						
						qisinfocustomer.html("out of stock");
						addtocart.hide();
					}else{
						qisinfocustomer.html("in stock");
						addtocart.show();
					}
				}
			}
			breakwordstr=breakwordstr.trim();
			content_h1.text(breakwordstr);
		})
	}else{
		jQuery("div[class=amconf-image-container]").click(function(){
			var verifystr= jQuery(this).find("img").attr("alt");
			var addtocart = jQuery("div[class=add-to-cart]");
			var qisinfocustomer = jQuery("div[class=qisinfo] p:first-child span");
			for(i=0;i<=configuarbleProductString.length-1;i++){				
				if((configuarbleProductString[i].productName).indexOf(verifystr) >=0){
					if( !parseInt(configuarbleProductString[i].productstatus)){						
						qisinfocustomer.html("out of stock");
						addtocart.hide();
					}else{
						qisinfocustomer.html("in stock");
						addtocart.show();
					}
				}
			}
		})
	}	
}); 