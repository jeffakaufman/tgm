jQuery.noConflict();
( function( $ ) {
$( document ).ready(function() {
	var isMobile;
	var wW, windowSize = function() {
		var is_windows = navigator.appVersion.indexOf("Win") != -1;
		var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

		if(is_windows) wW = $(window).width();
		else if(device.desktop()) wW = window.outerWidth;
		else wW = $(window).width();
		
		if (is_windows && is_chrome) wW = parseInt(wW) - 16;

		$(window).resize(function() {
			if (is_windows && is_chrome) wW = parseInt(wW) - 16;
		});
		//console.log(is_windows);
		//console.log('wW: '+wW);
	}
	windowSize();
	//console.log('wW: '+wW);
	
    var mobilize = function() {
    	var pageTitle = $('.category-view h1.title').text();
    	if (wW <= 640 || device.mobile()) {
    		isMobile = true;
    		$('html').addClass('mobile2');
    		$('.category-view h1.title').show();
    		$('.block-layered-nav .block-title strong span').html('Filter');
    		$('#megamenu').appendTo( $('#mobilemenu .menu') );
	    	$('.checkout-link ul.links').appendTo( $('#mobilemenu .menu') );
	    	$('#mobilemenu #megamenu').removeClass('megamenu').addClass('mobilemenu').removeAttr('id');
	    	$('.block-layered-nav, .block-account').prependTo( $('.col-main') );
	    	$('.product-shop .product-name').prependTo( $('.product-essential') );
	    	$('.product-img-box .more-views').prependTo( $('.product-img-box') );
	    	$('.product-shop .product-ids, .product-shop .ratings').insertBefore('.availability');
	    	$('.cart .totals').insertAfter( $('.cart .cart-collaterals') );
	    	if (!$('.categories_list h2 .down, .categories_list h4 .down, #mobilemenu a.parent .down, .block-layered-nav .block-title .down, .block-account .block-title .down, .block-layered-nav dt .down').length)
    			$('.categories_list h2, .categories_list h4, #mobilemenu a.parent, .block-layered-nav .block-title, .block-account .block-title, .block-layered-nav dt').append('<span class="down fa fa-plus"></span>');
	    	$('#mobilemenu .dropdown-container .title a').text('All');
	    	var numThumbs = $('.more-views li').length;
			if (numThumbs > 5 && numThumbs <= 10) $('.more-views, .product-view .product-img-box .product-image-zoom').addClass('cols2');
			if (numThumbs > 10) $('.more-views, .product-view .product-img-box .product-image-zoom').addClass('cols3');
			$('.cart-footer').insertAfter( $('.totals .checkout-types .btn-proceed-checkout').parent('li') ) ;
	    } else {
	    	isMobile = false;
	    	$('html').removeClass('mobile2');
	    	$('.block-layered-nav .block-title strong span').text(pageTitle);
	    	$('.category-view h1.title').hide();
	    	$('.categories_list h2 .down, .categories_list h4 .down, #mobilemenu a.parent .down, .block-layered-nav .block-title .down, .block-account .block-title .down, .block-layered-nav dt .down').remove();
	    	$('#mobilemenu ul.links').insertAfter( $('.checkout-link .welcome-msg') );
	    	$('#mobilemenu ul.mobilemenu').removeClass('mobielmenu').addClass('megamenu').attr('id','megamenu').appendTo( $('.nav-inner-inner') );
	    	$('.block-layered-nav, .block-account').prependTo( $('.col-left') );
	    	$('.product-essential .product-name').prependTo( $('.product-shop') ); 
	    	$('.product-img-box .more-views').appendTo( $('.product-img-box') );
	    	$('.product-shop .product-ids, .product-shop .no-rating, .product-shop .ratings').insertBefore('.short-description');
	    	$('.cart .cart-collaterals').insertAfter( $('.cart .totals') );
	    	$('.cart-footer').insertAfter( $('#shopping-cart-table .item:last-child') ) ;
	    }
    }
    mobilize();

    var cartLayout = function() {
    	if (wW <= 1000) {
    		$('.cart .crosssell').appendTo( $('.cart .cross-inside') );
    	} else {
    		$('.cart .crosssell').prependTo( $('.cart .cross-inside') );
    	}
    }
    cartLayout();

    $(window).resize(function() {
    	windowSize();
    	mobilize();
    	cartLayout();
    });

	// $('ul.mobilemenu > li').click(function(){
 //       $(this).toggleClass('over');
 //    });

	// var elementPosition = $('#product_tab-nav').offset();
	// console.log('elementPosition.top: '+elementPosition.top)
	// $(window).scroll(function(){
	// 	console.log($(window).scrollTop());
	//     if($(window).scrollTop() > elementPosition.top){
	//         $('#product_tab-nav').addClass('fixed');
	//     } else {
	//         $('#product_tab-nav').removeClass('fixed');
	//     }    
	// });

	$('.totals .checkout-types .btn-proceed-checkout').parent('li').insertBefore( $('.totals .checkout-types .paypal-logo').parent('li') ) ;

	$('#checkoutSteps .button').click(function() {
		$('html,body').animate({
			scrollTop: $('.col-main').offset().top
		}, 500);
		setInterval(function(){
			if($('.sp-methods input').is(':checked')) $('.sp-methods .title').slideDown();
		}, 1000);
	});
	
	$('.fa-search').click(function() {
		$('#search_mini_form').toggleClass('opened');
		// if ($('#search_mini_form').hasClass('opened')) {
		// 	$('#search_mini_form').removeClass('opened').slideUp();
		// } else {
		// 	$('#search_mini_form').addClass('opened').slideDown();
		// }
	});

    $('#mobile-btn span').click(function() {
    	if ($('#mobilemenu').hasClass('over')) {
    		$('#mobilemenu').removeClass('over');
    		$('#overlay').hide();
    		$('#mobilemenu .menu').slideUp();
    	} else {
    		$('#search_mini_form').removeClass('opened');
	    	$('#mobilemenu').addClass('over');
	    	$('#mobilemenu .menu').slideDown();
	    	$('#overlay').show();
	    }
    });

    $('#warranty-options').change(function() {
    	$('#warranty-options option:not(:first-child)').each(function() {
    		if($(this).is(':selected')) {
    			$('.warranty-term').slideDown();
    		}
    	});
    	if ($('#warranty-options option:first-child').is(':selected')) {
    		$('.warranty-term').slideUp();
    	}
    });
    // $('.mobile ul.megamenu li a').click(function(e) {
    // 	e.preventDefault();
    // 	$('.dropdown-container').removeClass('open').hide();
    // 	$(this).next('.dropdown-container').show().addClass('open');
    // });

  //   $('#mobilemenu li a.parent').click(function(event) {
		// event.preventDefault();
		// if ($(this).next('.dropdown-container').hasClass('open')) {
		// 	$(this).next('.dropdown-container').removeClass('open');
		// } else {
		// 	$('.dropdown-container').removeClass('open');
		// 	$(this).delay(1000).next('.dropdown-container').addClass('open');
		// 	$('html,body').animate({
		// 		scrollTop: $('#mobilemenu').offset().top
		// 	}, 500);
		// }
    	
  //   });
	
	if (isMobile) {

	    $('#mobilemenu a.parent').click(function(event){
			event.preventDefault();
			var element = $(this).parent('li');
			if (element.hasClass('open')) {
				element.removeClass('open');
				$(this).find('.down').removeClass('fa-minus').addClass('fa-plus');
				element.find('li').removeClass('open');
				element.find('.dropdown-container').slideUp();
			} else {
				element.addClass('open');
				element.children('.dropdown-container').slideDown();
				element.siblings('li').children('.dropdown-container').slideUp();
				element.siblings('li').find('.down').removeClass('fa-minus').addClass('fa-plus');
				element.siblings('li').removeClass('open');
				element.siblings('li').find('li').removeClass('open');
				element.siblings('li').find('.dropdown-container').slideUp();
				$(this).find('.down').removeClass('fa-plus').addClass('fa-minus');
			}
			equalheight('ul.mobilemenu li.level2 a');
			$('ul.mobilemenu .sub-column .level1 ul').each(function() {
				var numItems = $(this).find('li').length;
				if ( parseInt(numItems) % 2 == 0) $(this).addClass('even');
			});
			$('html,body').delay(500).animate({
				scrollTop: $('#mobilemenu').offset().top
			}, 500);
		});
		$('.categories_list h2, .categories_list li.type h4').click(function() {
			if ($(this).hasClass('open')) {
				$(this).removeClass('open');
				$(this).next('ul').slideUp();
			} else {
				$(this).parent().siblings('li').find('h2,h4').removeClass('open');
				$(this).addClass('open');
				$(this).next('ul').slideDown();
			}
		});
		$('.block-layered-nav .block-title, .block-account .block-title').click(function() {
			if ($(this).hasClass('open')) {
				$(this).removeClass('open');
				$(this).find('.down').removeClass('fa-minus').addClass('fa-plus');
				$(this).siblings('.block-content').slideUp();
			} else {
				$(this).addClass('open');
				$(this).find('.down').removeClass('fa-plus').addClass('fa-minus');
				$(this).siblings('.block-content').slideDown();
			}
		});
		$('.block-layered-nav dt').click(function() {
			if ($(this).hasClass('open')) {
				$(this).removeClass('open');
				$(this).find('.down').removeClass('fa-minus').addClass('fa-plus');
				$(this).next('dd').slideUp();
			} else {
				$(this).addClass('open');
				$('.col-main .block-layered-nav dt .fa-minus').removeClass('fa-minus');
				$(this).find('.down').addClass('fa-minus');
				$(this).siblings('dt').removeClass('open').next('dd').slideUp();
				$(this).next('dd').slideDown();
			}
		});
		$('.block-layered-nav .m-filter-css-checkboxes li a').append('<span class="fa fa-spinner fa-spin"></span>');
		$('.block-layered-nav .m-filter-css-checkboxes li a').click(function() {
			$(this).find('.fa').show();
		});
		$('.categories_list li.type>h4').click(function(){
			var element = $(this).parent('li');
			if (element.hasClass('open')) {
				element.removeClass('open');
				element.find('li').removeClass('open');
				element.find('ul').slideUp();
			}
			else {
				element.addClass('open');
				element.children('ul').slideDown();
				element.siblings('li').children('ul').slideUp();
				element.siblings('li').removeClass('open');
				element.siblings('li').find('li').removeClass('open');
				element.siblings('li').find('ul').slideUp();
			}
		});
		$( ".dropdown-container .sub-column:not(:has(ul))" ).remove();
		$('#overlay').click(function (e) {
		    var container = $('#mobilemenu .menu');

		    if (!container.is(e.target) && container.has(e.target).length === 0) {
		        container.slideUp();
		        $('#overlay').hide();
		    }
		});
		$('.footer .links').each(function() {
			$(this).find('h6').click(function() {
				$(this).parent().toggleClass('opened');
				$(this).next('ul').slideToggle();
			});
		});
		// var filtered = '';
		// var query = window.location.search.substring(1);
	 //    var vars = query.split("&");
	 //    for (var i=0;i<vars.length;i++) {
	 //        var pair = vars[i].split("=");
	 //        if(pair[0] == 'o'){ filtered = pair[1]; }
	 //    }
	 //    if (filtered != '') {
	 //    	$('.block-layered-nav .block-title').click();
		// 	$('dt.'+filtered).click();
	 //    }
	 //    $('.m-filter-css-checkboxes a').each(function() {
	 //    	var url = $(this).attr('href');
	 //    	var fname = $(this).parents('dd').prev('dt').attr('data-name');
	 //    	url = url + '&o=' + fname;
	 //    	$(this).attr('href',url);
	 //    });
	}
	if (wW <= 1000) {
		// $( '.hs-cta-wrapper a' ).each(function () {
		// 	var h = $('.col-right').height();
		// 	console.log(h);
		//     this.style.setProperty( 'height', h+'px', 'important' );
		// });
		$('.overview_content table, .overview_content table td, .short-description div').css('width','');
	}


	var equalheight = function(container, important){

		var currentTallest = 0,
		     currentRowStart = 0,
		     rowDivs = new Array(),
		     $el,
		     topPosition = 0;
		$(container).each(function() {

		   	$el = $(this);
		   	$($el).height('auto')
		   	topPostion = $el.position().top;

		   	if (currentRowStart != topPostion) {
		     	for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
		       	rowDivs[currentDiv].height(currentTallest);
		    }
		    rowDivs.length = 0; // empty the array
		    currentRowStart = topPostion;
		    currentTallest = $el.height();
		    rowDivs.push($el);
		   	} else {
		     	rowDivs.push($el);
		     	currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
		  	}
		   	for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
		     	rowDivs[currentDiv].height(currentTallest);
		   	}
		});
	}
	equalheight('#accessories-product-table .item');
	equalheight('.benq_featured_products li');
	equalheight('.benq-category ul.subcategory .alb');
	equalheight('a#cta_button_159104_11722497-13b9-44f8-86a0-08cebd3ac6a5, a#cta_button_159104_d1666142-511c-4709-bcd9-4d7df1f53b8b, a#cta_button_159104_d724023a-eadc-454f-8222-6934f4f47390, a#cta_button_159104_b01addbe-7fd1-4160-8710-81dfcc93dc7f');

	$(window).resize(function(){
		equalheight('#accessories-product-table .item');
	  	equalheight('.benq_featured_products li');
	  	equalheight('.benq-category ul.subcategory .alb');
	  	equalheight('a#cta_button_159104_11722497-13b9-44f8-86a0-08cebd3ac6a5, a#cta_button_159104_d1666142-511c-4709-bcd9-4d7df1f53b8b, a#cta_button_159104_d724023a-eadc-454f-8222-6934f4f47390, a#cta_button_159104_b01addbe-7fd1-4160-8710-81dfcc93dc7f');
		if (wW <= 1000) {
			$('.overview_content table, .overview_content table td').css('width','');
		}
	});
});
} )( jQuery );
