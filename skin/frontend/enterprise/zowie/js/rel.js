jQuery.noConflict();
/*
 *function chatUs(){
 *    window.onscroll=function ()
 *{
 *    var oDiv=document.getElementById('chatus');
 *    var scrollTop=document.documentElement.scrollTop||document.body.scrollTop;
 *    
 *    var t=scrollTop+(document.documentElement.clientHeight-oDiv.offsetHeight)/2;
 *    
 *    startMove(parseInt(t));
 *}
 */

var timer=null;

function startMove(iTarget)
{
	var oDiv=document.getElementById('chatus');
	
	clearInterval(timer);
	timer=setInterval(function (){
		var iSpeed=(iTarget-oDiv.offsetTop)/8;
		iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
		
		if(oDiv.offsetTop==iTarget)
		{
			clearInterval(timer);
		}
		else
		{
			oDiv.style.top=oDiv.offsetTop+iSpeed+'px';
		}
		
	}, 10);
}
//}
jQuery(document).ready(function(){
	//chatUs();
	function adpic(){
		var len  = jQuery(".num > li").length;
		var index = 0;
		var adTimer;
		jQuery(".num li").mouseover(function(){
			index  =   jQuery(".num li").index(this);
			showImg(index);
		 }).eq(0).mouseover();	
		jQuery('#adPicM').hover(function(){
				 clearInterval(adTimer);
			 },function(e){
				 adTimer = setInterval(function(){
					showImg(index)
					index++;
					if(index==len){index=0;}
				  } , 4000);
				  e.stopPropagation();
		 }).trigger("mouseleave");	
		function showImg(index){
			var adWidth = jQuery("#adPicM").width();
			var sliderWidth = adWidth * len;
			jQuery(".slider").width(sliderWidth);
			jQuery(".slider").stop(true,false).animate({left : -adWidth*index},500);
			jQuery(".num li").removeClass("on").eq(index).addClass("on");
		}	
	}
	adpic();
 // function pricefont(){
 // 	var aPrice=jQuery(".special-price .price,.catalog-category-view .price-box .regular-price .price");
 // 	aPrice.each(function(){
	// var str = jQuery.trim(jQuery(this).html());
	// var num = str.substr(str.length-3,str.length-1);
	// jQuery(jQuery(this)).html(str.replace(num,"<span>"+num+"</span>"));
	// })
 // }
 // pricefont();
 function backtop(){
	 jQuery("#back-to-top").hide();
	jQuery(function () {
	jQuery(window).scroll(function(){
	if (jQuery(window).scrollTop()>500)
		{jQuery("#back-to-top").fadeIn(200);}
	else
		{jQuery("#back-to-top").fadeOut(200);}
	});
	jQuery("#back-to-top").click(function(){
	jQuery('body,html').animate({scrollTop:0},500);
	return false;
	});
	});
}
backtop();
});
