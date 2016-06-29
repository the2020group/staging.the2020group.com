(function($)	{
    $(document).ready(function() {


	   //ginput_container Gravity Form
    var inputA =  $("input[name='input_1']");
    var nameVal = $("input[name='input_1']").val();

    if(nameVal == " "){
      inputA.val(nameVal.replace(' ', ''));
    }

    function closeDropdowns() {
      var navArray = new Array(
        '.menudrop',
        '.widget_wc_aelia_currencyswitcher_widget form'
      );
    }


    $(document).click(function(){
        $('.widget_wc_aelia_currencyswitcher_widget form').hide();
        $('.menudrop').hide();
    });

    $(document).on('click', '.header-content-filter dt a', function(e){
      e.preventDefault();
      e.stopPropagation();
      $('.header-content-filter dd ul').toggle();
      $('.tp_dropdown dd ul').hide();
      $('.widget_wc_aelia_currencyswitcher_widget form').hide();
    });

    // On Click open Currency
    $('.widget_wc_aelia_currencyswitcher_widget').click(function(e){
        e.stopPropagation();
        $('.menudrop').hide();
        $('.tp_dropdown dd ul').hide();
        if($(".widget_wc_aelia_currencyswitcher_widget form").css("display") == "block"){
        	$('.widget_wc_aelia_currencyswitcher_widget form').hide();
        } else {
        	$('.widget_wc_aelia_currencyswitcher_widget form').show();
        }
    });

    $('.dropdown dt a').click(function(e){
          e.stopPropagation();
          $('.menudrop').hide();
          $('.widget_wc_aelia_currencyswitcher_widget form').hide();
        	//$('.widget_wc_aelia_currencyswitcher_widget form').show();//.animate({ opacity: 0, top: "30px" }, 'fast');
    });

		/////////////////////////////////////////////
		/////// Benchmark login form ///////////////
		//////////////////////////////////////////////

		$(document).on('submit','#benchmark-login', function(e) {
			e.preventDefault();
			$('#bm-submit').html('Checking details...').prop('disabled', true);
			$('#benchmark-login .error').text('');
			$('#benchmark-login .error').css('display','none');

			var formData = {
				'email' 	: $('#benchmark-signin input[name=email]').val(),
				'password' 	: $('#benchmark-signin input[name=password]').val(),
				'action'		: 'benchmark_login',
			};


			//$('.ajax-wrapper').fadeOut(500, function () {
				// process the form
				$.ajax({
					type        : 'POST', // define the type of method we want to use (POST for our form)
					//action		: 'benchmark_login',
					url         : ajaxurl, // the url where we want to POST
					data        : formData, // our data object
					dataType    : 'json', // what type of data do we expect back from the server
					encode      : true
				})
				// using the done promise callback
				.done(function(data) {

					// log data to the console so we can see
					//console.log(data);

					// here we will handle login success or error messages
					if ( data.status === 'success') {
						// ALL GOOD! just show the returned message!

						// <input name='input_34' id='input_9_34' type='hidden' class='gform_hidden' value='1' /> hidden input field

						$('input[name=input_34]').val('1');
						$('#field_10_35').fadeOut(300);
						$('#input_10_36').val(data.firstname);
						$('#input_10_37').val(data.lastname);
						$('#input_10_40').val(data.email);
						$('#input_10_28').val(data.company);
						$('#input_10_30').val(data.address);
						$('#input_10_32').val(data.city);
						$('#input_10_33').val(data.postcode);

						$.fancybox.close();

					} else {
						//console.log(data.message);
						// handle errors
						$('#benchmark-login .error').text(data.message);
						$('#benchmark-login .error').fadeIn(300);
						$.fancybox.update();
						$('#bm-submit').html('Login').prop('disabled', false);
					}
				})
			//});

		});



		//Accordian for personas
		$('.page-template-page-persona .archive-title').click(function() {
			//console.log('clicked');
			var group = $(this).data('group');
			var show = 0;
			$('.page-template-page-persona .hideContent').slideUp();
			if($('.page-template-page-persona .hideContent[data-group="'+group+'"]').hasClass('show')) {
				$('.page-template-page-persona .hideContent[data-group="'+group+'"]').removeClass('show');
				$(this).removeClass('show');
			}
			else {
				$('.page-template-page-persona .hideContent[data-group="'+group+'"]').slideDown().addClass('show');
				$(this).addClass('show');
			}
		});

		//Personas show more
		$('.page-template-page-persona .hideContent .showMore').click(function(e) {
			e.preventDefault();
			var group = $(this).parent().data('group');
			var showing = $(this).attr('data-shown');
			var max = $(this).attr('data-max');
			var count = 0;

			showing = Number(showing);
			max = Number(max);

			$(this).parent().find('.search-result.hide-article').each(function() {
				count++;
				$(this).fadeIn();
				$(this).removeClass('hide-article');

				if(count==10) {
					return false;
				}
			});

			$(this).attr('data-shown', showing+10);

			var difference = max-(showing+10);

			if(difference<=0) {
				$(this).fadeOut();
			}
		});

		// Nav
		// Show and hide main nav on mobile
		$('.page-header .toggle').click(function(){
			$('.main-menu, .utilities').slideToggle('fast');
		});

		// On window resize reset menu to ensure nav displays
		var menu = $('.main-menu');
		$(window).on('resize', function(){
		    if(!$(".toggle").is(":visible") && !menu.is(':visible'))
		    {
		      menu.css({'display':''});
		    }
		});

		$('select.redirectme').on('change', function() {
		  location.href = $(this).val();
		});

		var submenu = $('.main-menu ul ul');
		$(window).on('resize', function(){
		    if(!$(".toggle").is(":visible") && !submenu.is(':visible'))
		    {
		      submenu.css({'display':''});
		    }
		});

    $('.touch .main-menu li.menu-item-has-children > a:first-child').on('click', function(e){
      e.preventDefault();
      var parentContainer = $(this).parent();
      var navItems = $('.main-menu li.menu-item-has-children');
      var subNav = parentContainer.find('.sub-menu');
      navItems.removeClass('opened');
      parentContainer.toggleClass('opened');

			$(subNav).first().slideToggle('fast');
    });

		/*
		$('.main-menu ul li.menu-item-has-children a').click(function(){
		event.preventDefault();
		$(this).siblings('.main-menu ul').slideToggle('fast');
		});
		*/

		// Search Bar Toggle
		$('.header-menu .search-box').click(function(){
			$('#search-popout').slideToggle('fast');
		});

		$('#search-popout .search-close').click(function(){
			$('#search-popout').slideToggle('fast');
		});

		// Load Foundation
		$(document).foundation({
      equalizer: {
         equalize_on_stack: true
      }
    });


    // load parallax script
    $(window).stellar({
	    horizontalScrolling:false
    });


    function setHeight() {
    windowHeight = $(window).innerHeight();
	    $('.intro-content').css('min-height', windowHeight);
	  };
	  setHeight();

	  $(window).resize(function() {
	    setHeight();
	  });


		// Load Carousel
		$(".membersCarousel").owlCarousel({
		    loop:true,
		    nav:true,
		    autoHeight : true,
		    singleItem:true,
		    navText:[
			    "",
					""
		    ],
		    responsive:{
		        0:{
		            items:1
		        }
		    }
		});


    function countCarousels() {
      itemCount = $('.quoteCarousel > .testimonialItem').length;

  		if (itemCount > 1) {
    		$(".quoteCarousel").owlCarousel({
    		    loop:true,
    		    nav:true,
    		    singleItem:true,
    		    autoHeight : true,
    		    navText:[
    			    "",
    					""
    		    ],
    		    responsive:{
    	        0:{
    	            items:1
    	        }
    		    }
    		});
  		}
    }

    countCarousels();

		// scroll to function
		$('a[href^="#"]').on('click',function (e) {
	    e.preventDefault();

	    var target = this.hash;
	    var $target = $(target);

	    $('html, body').stop().animate({
	        'scrollTop': $target.offset().top
	    }, 700, 'swing', function () {
	        window.location.hash = target;
	    });
		});



		// dashboard nav
		$(document).on('click','.dataload', function(e){
			e.preventDefault();
			var tabLink = $(this).data('uri');
			var tabContent = $(this).attr('href');
			var contentWrapper = $('.dash-wrap .tabs-content');
			var msg = '';
			/*console.log(tabLink);
			console.log(tabContent);
			console.log(contentWrapper);*/
			$(tabContent).load(tabLink + ' #dash-main', function(response, status, xhr) {

			 	if ( status == "error" ) {
			  		$( "#error" ).html( msg + xhr.status + " " + xhr.statusText );
			    	//contentWrapper.css('background-color', 'transparent');
			  	}
			  	if ( status == "success" ) {
						//contentWrapper.css('background-color', '#f00');
						contentWrapper.foundation();
			  	}

				if($('#profilePicture').length>0) {
					$('#profilePicture').fileUpload({
						before : function() {
							$('.profile-image').addClass('loading');
						},
						success : function(data) {
							$('.profile-image .user-img').attr('src',data.image);
						},
						complete : function() {
							$('.profile-image').removeClass('loading');
						}
					});
				}
			});

		});

		// fancybox
		$(".fancybox").fancybox({
			maxHeight	: 500,
			afterClose  : function() {

				$('#new_user').trigger("reset");
		        $('#child-users').html('<img src="/library/images/general/ajax-loader.gif" class="" alt="">');
		        $('#child-users').load('/dashboard/' + ' #current-users', function(response, status, xhr) {

		            if ( status == "error" ) {
		                $( "#error" ).html( msg + xhr.status + " " + xhr.statusText );
		                //contentWrapper.css('background-color', 'transparent');
		            }
		            if ( status == "success" ) {
		                //contentWrapper.css('background-color', '#f00');
		            }
		            //$('#child-users').goTo();
		            //reloadChildUsers();
		            $('#add-button').removeAttr('disabled');

		            parent.location.reload();

		        });
			 }
		});

		$(".fancybox-inline").fancybox();

		$(".fancybox-video").fancybox();

		$(".exhibitors-inline").fancybox({
				height: 'auto',
				maxWidth: '600',
				//scrolling: 'hidden',
		    helpers:{
	        overlay : {
		        //locked: false,
            css : {
              'background' : 'rgba(47, 35, 42, 0.9)'
            }
          }
		    }
		});

		$("a.bundle_add_to_cart_button").on('click',function (e) {
			e.preventDefault();
			$(this).parent().submit();
//			$( ".bundle_form" ).submit();
		});



		// Geo IP ReDirects
/*	 var geo_content='';



	 switch(F10GeoIP.code) {
	    case 'AU':
	      	geo_content = 'Would you like to visit <a href="http://www.smithink2020.com/">Smithink 2020</a>';
	      	break;
	    case 'US':
	      	geo_content = 'Would you like to visit <a href="http://2020groupusa.com/">2020 Group USA</a><br><br>';
	      	break;
		case 'ZA':
	   	  	geo_content = 'Would you like to visit <a href="http://www.2020sa.co.za/">2020 South Africa</a> <br><br>';
	    	break;
	    case 'CA':
	      	geo_content = 'Would you like to visit <a href="http://www.2020canada.ca/">2020 Canada</a> <br><br>';
	    	break;
	    default:
	      geo_content = 'Would you like to visit <a href="http://2020groupusa.com/">2020 Group USA</a> <br><br>';
	  }*/


   //Geo location fancybox settings
/*
    $('.fancybox-load').fancybox({
      maxWidth: 500,
      padding: 20,
      closeBtn: false,

      helpers: {
        title: null,

        overlay: {
          locked: true,
          closeClick : false,
        }
      }
     });
    //Close the geo ip popup

    $('#geo .close').click(function() {
      $('.fancybox-overlay').hide();
      document.cookie="geo=seen";
    })
*/

	$('input[type=radio][name=account-type]').click(function() {

		alert('account-type : ' + this.value);

	  	if (this.value == '1') {
            $('p.account-type-dd').hide();
        }
        if (this.value == '2') {
        	$('p.account-type-dd').show();
        }
	});

/*
    var x = document.cookie;

    if (x.indexOf('geo=seen') == -1 && (F10GeoIP.code != 'GB') && (geo_content != '')) {
      $(".fancybox-load").eq(0).trigger('click');
      $("#geo .geolink").html(geo_content);
    }
*/

    $(document).on('keyup change','.variations_form',function() {


      var $forWho = $('select.addon-select');

      if ($forWho.length == 1) {
        var qty = parseInt($('input[name=quantity]').val());
        var delegate = $('#delegate-places').val();

        //console.log('delegate : '+delegate);

        if ( (isNaN(delegate) || delegate == '' || delegate == '1') && (qty == 1 ) ) {

          $forWho.parent().prev().show();
          $forWho.show();

	        if ($forWho.val() == '') {
	        	$('.who_content').hide();
	        	$('.who_content_error').show();
	        } else {
	       		if ($forWho.val() == 'for-me-1') {
	        		$('.who_content').hide();
		        	$('.who_content_error').hide();
	        	} else {
	        		$('.who_content').show();
		        	$('.who_content_error').hide();
	        	}
	        }

        } else {
          $forWho.parent().prev().hide();
          $forWho.hide();
      	  $('.who_content').hide();
		  $('.who_content_error').hide();
		  $('select.addon-select option:eq(2)').attr('selected','selected');
        }
        //console.log('for who : '+$forWho.val());
      }
    });


      $(document).on('keyup change','.bundle_form',function() {

		var $forWho = $('select.addon-select').last();

		//console.log ('forwho'+$forWho.val());

		$('select.addon-select').val($('select.addon-select').last().val());


      if ($forWho.length == 1) {
        var qty = parseInt($('input[name=quantity]').val());
        var delegate = $('#delegate-places').val();

       //console.log('delegate : '+delegate);

        if ( (isNaN(delegate) || delegate == '' || delegate == '1') && (qty == 1 ) ) {

          $forWho.parent().prev().show();
          $forWho.show();

	        if ($forWho.val() == '') {
	        	$('.who_content').hide();
	        	$('.who_content_error').show();
	        } else {
	       		if ($forWho.val() == 'for-me-1') {
	        		$('.who_content').hide();
		        	$('.who_content_error').hide();
	        	} else {
	        		$('.who_content').show();
		        	$('.who_content_error').hide();
	        	}
	        }

        } else {
        	$('.addon-select').each(function() {
		  		//console.log('here '+$('.addon-select').val());
		  		$(this).val('for-my-colleagues-2')
		  	});
            $forWho.parent().prev().hide();
	        $forWho.hide();
	  	    $('.who_content').hide();
		    $('.who_content_error').hide();
	    }

        //console.log('for who : '+$forWho.val());
      }
    });





    $(document).on('keyup change','.cart',function() {


      var $forWho = $('select.addon-select');

      if ($forWho.length == 1) {
        var qty = parseInt($('input[name=quantity]').val());
        var delegate = $('#delegate-places').val();

        //console.log('delegate : '+delegate);

        if ( (isNaN(delegate) || delegate == '' || delegate == '1') && (qty == 1 ) ) {

          $forWho.parent().prev().show();
          $forWho.show();

	        if ($forWho.val() == '') {
	        	$('.who_content').hide();
	        	$('.who_content_error').show();
	        } else {
	       		if ($forWho.val() == 'for-me-1') {
	        		$('.who_content').hide();
		        	$('.who_content_error').hide();
	        	} else {
	        		$('.who_content').show();
		        	$('.who_content_error').hide();
	        	}
	        }

        } else {
          $forWho.parent().prev().hide();
          $forWho.hide();
      	  $('.who_content').hide();
		  $('.who_content_error').hide();
		  $('select.addon-select option:eq(2)').attr('selected','selected');
        }
        //console.log('for who : '+$forWho.val());
      }
    });

	




	});

})(jQuery);
