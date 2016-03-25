jQuery.Macro = {
	silder:function(param){
		var parent = param['parent'];
		var child  = param['child'];
		var prev   = param['prev'];
		var next   = param['next'];
		var showArea = param['showArea'];
		var iNow = 0;
		var childWidthUnit = jQuery(parent + ">" + child + ':first').outerWidth(true);
		var arrowWidth = 62;
		resizeShowArea();
		jQuery(parent).append((jQuery(parent).html()));
		var childeLen = jQuery(parent + ">" + child).size();
		jQuery(parent).width(childWidthUnit*childeLen);
		jQuery(prev).click(function(){
			if(iNow==0){
				iNow = childeLen/2;
				jQuery(parent).css('left',-(jQuery(parent).width()/2));
			}
			move(jQuery(parent),-(iNow)*childWidthUnit,-(iNow-1)*childWidthUnit);
			iNow--;
		});
		jQuery(next).click(function(){
			if(iNow==childeLen/2){
				iNow = 0;
				jQuery(parent).css('left',0);
			}
			move(jQuery(parent),-(iNow)*childWidthUnit,-(iNow+1)*childWidthUnit);
			iNow++;
		});
		//Calculation of move
		function move(obj,old,now){
			clearInterval(obj.timer);
			obj.timer = setInterval(function(){
				var iSpeed = (now-old)/10;
				iSpeed = iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
				if(now == old){
					clearInterval(obj.timer);
				}else{
					old += iSpeed;
					obj.css('left',old);
				}
			},30);
		};
		//Calculate the size of the display area
		function resizeShowArea(){
			var currentWindowWidth = jQuery(showArea).parent().width();
			var items = Math.floor(currentWindowWidth/childWidthUnit);
			jQuery(showArea).width(childWidthUnit*items);
			jQuery(showArea).css('text-align','center');
		}
	}
};