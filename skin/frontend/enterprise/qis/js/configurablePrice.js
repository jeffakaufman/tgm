jQuery.noConflict();
jQuery(document).ready(function (){
	var configurablemax = jQuery("input[id=configurableMax]").val();
	var configurablemin = jQuery("input[id=configurableMin]").val();
	var configurableonlyone=jQuery("input[id=configurableOnlyOne]").val();
	var content=jQuery("div[class=price-box] span[class=regular-price] span[class=price]");
	var currency=content.html().substr(0,1);
	configurablemax=((parseFloat(configurablemax/100).toFixed(4))*100).toFixed(2);
	configurablemin=((parseFloat(configurablemin/100).toFixed(4))*100).toFixed(2);
	configurableonlyone=((parseFloat(configurableonlyone/100).toFixed(4))*100).toFixed(2);
	if(parseInt(configurablemax) >0 && parseInt(configurablemin) >0){
		if( configurablemax==configurablemin){	
			content.html(currency+configurablemax)	
		}else{
			content.html("From "+currency+configurablemin+" To "+currency+configurablemax)
		}	
	}
	if(parseInt(configurableonlyone) >0 ){
		content.html(currency+configurableonlyone);
	}
	
})