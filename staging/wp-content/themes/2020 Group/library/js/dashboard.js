jQuery(document).ready(function($) {

	$(document).on('change','#profilePicture input[name="image"]',function(e){
		$('#profilePicture').submit();
	});

	$(document).on('click','#updateProfilePicture',function(e){
		e.preventDefault();
		$('#profilePicture input[name="image"]').click();
	});

	$('#profilePicture').fileUpload({before : function() { alert('test1'); }, beforeSubmit : function() { alert('test2'); }});

    $(document).on('click','.save-webinars',function(e){
        e.preventDefault();

        $('.save-webinars').attr('disabled','disabled');

        var form_counter = $(this).data('counter');
        var container_block = $(this).closest('.dash-block');

        $('.dash-ajax-overlay', container_block).fadeIn(300);

        $.ajax({
            type: "POST",
            url: ajaxurl+'?action=assign_users_to_webinar',
            data: $('#purchase-'+form_counter).serialize(),  // what data should go there?
            success: function(output) {

                var url = $('.dash-btns .silver').data('uri');

                if ($.trim(output) !='true') {
                    $('#my-purchases').load(url + ' #dash-main', function(response, status, xhr) {
                        $('.dash-ajax-overlay', container_block).fadeOut(300);
                    });
                }
                else {
                    //reloadChildUsers();
                    $('#my-purchases').load(url + ' #dash-main', function(response, status, xhr) {
                        $('.dash-ajax-overlay', container_block).fadeOut(300);
                    });
                }
            },
            error: function() {
                $('.dash-ajax-overlay', container_block).fadeOut(300);
            }
        });
    });

    $(document).on('click','.save-conferences',function(e){
        e.preventDefault();

        $('.save-conferences').attr('disabled','disabled');

        var form_counter = $(this).data('counter');
        var container_block = $(this).closest('.dash-block');

        $('.dash-ajax-overlay', container_block).fadeIn(300);

        //alert (JSON.stringify($(this).data));

        $.ajax({
            type: "POST",
            url: ajaxurl+'?action=assign_users_to_conference',
            data: $('#purchase-'+form_counter).serialize(),  // what data should go there?
            success: function(output) {

                if ($.trim(output) !='true') {
                    $('#my-purchases').load(url + ' #dash-main', function(response, status, xhr) {
                        $('.dash-ajax-overlay', container_block).fadeOut(300);
                    });
                }
                else {
                   //reloadChildUsers();
                    var url = $('.dash-btns .silver').data('uri');
                    $('#my-purchases').load(url + ' #dash-main', function(response, status, xhr) {
                        $('.dash-ajax-overlay', container_block).fadeOut(300);
                    });
                }
            },
            error: function() {
                $('.dash-ajax-overlay', container_block).fadeOut(300);
            }
        });
    });

    $(document).on('click','.save-workshops',function(e){
        e.preventDefault();

        $('.save-workshops').attr('disabled','disabled');

        var form_counter = $(this).data('counter');

        $.ajax({
            type: "POST",
            url: ajaxurl+'?action=assign_users_to_workshop',
            data: $('#purchase-'+form_counter).serialize(),  // what data should go there?
            success: function(output) {

                if ($.trim(output) !='true') {

                }
                else {
                   //reloadChildUsers();
                    var url = $('.dash-btns .silver').data('uri');

                    $('#my-purchases').load(url + ' #dash-main', function(response, status, xhr) {
                    });
                }
            }
        });
    });

	$(document).on('click','.save-focusgroups',function(e){
        e.preventDefault();

        $('.save-focusgroups').attr('disabled','disabled');

        var form_counter = $(this).data('counter');

        $.ajax({
            type: "POST",
            url: ajaxurl+'?action=assign_users_to_focusgroup',
            data: $('#purchase-'+form_counter).serialize(),  // what data should go there?
            success: function(output) {

                if ($.trim(output) !='true') {

                }
                else {
                   //reloadChildUsers();
                    var url = $('.dash-btns .silver').data('uri');

                    $('#my-purchases').load(url + ' #dash-main', function(response, status, xhr) {
                    });
                }
            }
        });
    });


    // submit form if enter is pressed on any edit-field
    $(document).on('keypress','.edit-field',function(e){
        if(e.which == 13) {
            updateUser($('#editProfile'));
        }
    });

    // change fields to editable fields
    $(document).on('click','.inline-edit',function(e){
        e.preventDefault();

        var buttonText = $(this).html();

        if (buttonText == 'Edit') {
            $(this).html('Save');

            var scope = $(this).data('scope');
            //$(this).parent().append('<button class="" scope="'+scope+'">Cancel</button>');
            $(this).parent().append('<a href="" class="edit-prof gen-btn cancel-btn" id="cancel-edit-profile" data-scope="'+scope+'" >Cancel</a>');
            $('#'+scope+' .editable').each(function(){

				if($(this).parent().parent().hasClass('showHidden')) {
					$(this).hide();
					$(this).parent().parent().find('.editable-h').show();
				}
				else {
					var field = '';
					var label = '';
					var currentContent = $.trim($(this).html());
					var type = 'text';

					// check if data-type is set ie password, number, phone
					if ($(this).data('type')) {
						type = $(this).data('type');
					}

					// we don't want to display any content in the password field
					if ($(this).data('type')=='password') {
						currentContent = '';
					}

					if (type!='textarea') {
						// set the field
						field = '<input type="'+type+'" id="change-'+$(this).data('id')+'" data-original="'+currentContent+'" class="edit-field" value="'+currentContent+'" />';
					}
					else {
						field = '<textarea id="change-'+$(this).data('id')+'" data-original="'+currentContent+'" class="edit-field">'+currentContent+'</textarea>';
					}

					// if data-label is set we need to create the label too.
					if ($(this).data('label')) {
						label = '<label for="change-'+$(this).data('id')+'">'+$(this).data('label')+'</label>';
					}

					// replace the text with the fields.
					$(this).html(label+ ' '+ field);
				}

            });
        }

        if (buttonText == 'Save') {
            updateUser($(this));
        }

    });

	//Change International Location
	$(document).on('change','select[name="intl_continent"]',function(e){
		var continentId = $(this).val();
		$('select[name="intl_country"]').hide();
		$('select[name="intl_country"] option').hide();
		if(continentId != '') {
			$('select[name="intl_country"] option[data-continent="'+continentId+'"]').show();
			$('select[name="intl_country"]').show();
			$('select[name="intl_country"]').val($('select[name="intl_country"] option:visible').first().val());
		}
	});

    $(document).on('click','#cancel-edit-profile',function(e){
        e.preventDefault();
        $('#editProfile').html('Edit');
        var scope = $(this).data('scope');
        $(this).remove();
        changeEditableFields('cancel',scope);
    });

    $(document).on('click','#cancel-edit-int',function(e){
        e.preventDefault();
        $('#editInt').html('Edit');
        var scope = $(this).data('scope');
        $(this).remove();
        changeEditableFields('cancel',scope);
    });


    // change international fields to editable fields
    $(document).on('click','.edit-int',function(e){
        e.preventDefault();

        var buttonText = $(this).html();

        if (buttonText == 'Edit') {
            $(this).html('Save');

            var scope = $(this).data('scope');
            //console.log('scope: ' + scope);
            //$(this).parent().append('<button class="" scope="'+scope+'">Cancel</button>');
            $(this).parent().append('<a href="" class="edit-int gen-btn cancel-btn" id="cancel-edit-int" data-scope="'+scope+'" >Cancel</a>');
            $('#'+scope+' .editable').each(function(){

				if($(this).parent().parent().hasClass('showHidden')) {
					$(this).hide();
					$(this).parent().parent().find('.editable-h').show();
				}
				else {
					var field = '';
					var label = '';
					var currentContent = $.trim($(this).html());
					//console.log(currentContent);
					var type = 'text';

					// check if data-type is set ie password, number, phone
					if ($(this).data('type')) {
						type = $(this).data('type');
					}

					// we don't want to display any content in the password field
					if ($(this).data('type')=='password') {
						currentContent = '';
					}

					if (type!='textarea') {
						// set the field
						field = '<input type="'+type+'" id="change-'+$(this).data('id')+'" data-original="'+currentContent+'" class="edit-field" value="'+currentContent+'" />';
					}
					else {
						field = '<textarea id="change-'+$(this).data('id')+'" data-original="'+currentContent+'" class="edit-field">'+currentContent+'</textarea>';
					}

					// if data-label is set we need to create the label too.
					if ($(this).data('label')) {
						label = '<label for="change-'+$(this).data('id')+'">'+$(this).data('label')+'</label>';
					}

					// replace the text with the fields.
					$(this).html(label+ ' '+ field);
				}

            });
        }

        if (buttonText == 'Save') {
            updateInternational($(this));
        }

    });

    function validateEmail($email) {
       var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,20})?$/;

        console.log(emailReg.test( $email ));

        if ( emailReg.test( $email ) ) {
            return true;
        } else {
            return false;
        }
    }

    function updateUser(obj) {
        var scope = obj.data('scope');
        var postData = {};
        var validation = true;
        $('#'+scope+' .edit-field').each(function(){

            if ( ($(this).attr('id') == 'change-email' && !validateEmail($(this).val()) ) || ( ($(this).val()=='' && $(this).attr('id') != 'change-county') && ($(this).val()=='' && $(this).attr('id') != 'change-line-2') && $(this).attr('id') != 'change-password')) {
                $(this).addClass('error');
                validation = false;
            }
            else {
                 $(this).removeClass('error');
            }

            postData[$(this).attr('id')] = $(this).val();

        });

        if (validation) {
             obj.html('Edit');
             $('#cancel-edit-profile').remove();
            // send data via ajax
            $.ajax({
                type:  'POST',
                cache: false,
                url:   ajaxurl+'?action=update_user',
                data: postData,

                success : function(output) {
                    //$('#editProfile').html('Edit');
                    //obj.html('Edit');
                    //$('#cancel-edit-profile').remove();

                }
             });

            changeEditableFields('edit',scope);
        }
        else {

        }
    }

	function updateInternational(obj) {

        //console.log('triggered updateInternational');

        var scope = obj.data('scope');
        var postData = {};
        var validation = true;
        $('#'+scope+' .edit-field').each(function(){
			if(typeof $(this).attr('id') != 'undefined') {
				postData[$(this).attr('id')] = $(this).val();
			}
        });

        //console.log(postData);

        if (validation) {
            obj.html('Edit');
            $('#cancel-edit-int').remove();
            //console.log('validation true');
            // send data via ajax
            $.ajax({
                type:  'POST',
                cache: false,
                url:   ajaxurl+'?action=international',
                data: postData,
                success : function(output) {
                    $('#editProfile').html('Edit');

                }
             });

            changeEditableFields('edit',scope);
        }
        else {
            //console.log('else');
        }

    }

    $(document).on('submit','#new_user',function(e) {
        e.preventDefault();
        $('#add-button').attr('disabled','disabled');
        var validation = true;

        $('#new_first_name').removeClass('error');
        $('#new_last_name').removeClass('error');
        $('#new_email').removeClass('error');

        if($('#new_first_name').val() == '' || $('#new_last_name').val() == '' || $('#new_email').val() == '') {
            validation = false;
            if ($('#new_first_name').val() == '') {
                $('#new_first_name').addClass('error');
            }

            if ($('#new_last_name').val() == '') {
                $('#new_last_name').addClass('error');
            }

            if ($('#new_email').val() == '') {
                $('#new_email').addClass('error');
            }

        }

        if (validation) {
            $.ajax({
                type: "POST",
                url: ajaxurl+'?action=create_user',
                data: $('#new_user').serialize(),  // what data should go there?
                success: function(output) {
                    if (output =='nopartners') {
                        alert('Couldn\'t create useraccount as you have no partner licenses left');
                    }
                    else if (output !='true') {
                        alert('We cannot create this user account as an account with this email address already exists – please contact 2020.');
                    }
                    else {
                       reloadChildUsers();
                    }
                }
            });
        }
        else {
            $('#add-button').removeAttr('disabled');
        }

    });

    $('#international').on('submit',function(e) {
        e.preventDefault();


        $.ajax({
            type: "POST",
            url: ajaxurl+'?action=international',
            data: $('#international').serialize(),  // what data should go there?
            success: function(output) {


                    alert(output);

            }
        });


    });

    function reloadChildUsers() {

        $('#new_user').trigger("reset");
        $('#child-users').html('<img src="/library/images/general/ajax-loader.gif" class="" alt="">');
        $('#child-users').load('/dashboard/my-details/' + ' #current-users', function(response, status, xhr) {

            if ( status == "error" ) {
                $( "#error" ).html( msg + xhr.status + " " + xhr.statusText );
                //contentWrapper.css('background-color', 'transparent');
            }
            if ( status == "success" ) {
                //contentWrapper.css('background-color', '#f00');
            }
            $('#child-users').goTo();
            $('#add-button').removeAttr('disabled');
        });
    }

    function changeEditableFields(action_type,scope) {

        // remove cancel button
        $('.cancel-edit-profile').remove();
        $('#edit-profile').html('Edit');

        // loop through all editable fields
        $('#'+scope+' .edit-field').each(function(){

            // password fields are handled differently
            if ($(this).attr('type')=='password') {
                $(this).parent().html('*********');
            }
            else if($(this).closest('.columns').hasClass('showHidden')) {
				$(this).parent().hide();
				if($(this).attr('name') == 'intl_continent') {
					var continent = $(this).find('option:selected').text();
					var country = $(this).parent().parent().find('select[name="intl_country"] option:selected').text();
					if($(this).val() == '') {
						$(this).parent().parent().find('.editable').html('&nbsp;').show();
					}
					else {
						$(this).parent().parent().find('.editable').html(continent+' > '+country).show();
					}
				}
				else if($(this).attr('name') == 'intl_specialisms') {
					var value = '<ul>';
					$(this).find('option:selected').each(function() {
						value += '<li>'+$(this).text()+'</li>';
					});
					value += '</ul>';
					if($(this).val() == '') {
						$(this).parent().parent().find('.editable').html('&nbsp;').show();
					}
					else {
						$(this).parent().parent().find('.editable').html(value).show();
					}
				}
				$(this).parent().parent().find('.editable').show();
			}
            else {
                if (action_type == 'edit') {
                    $(this).parent().html($(this).val());
                }
                else {
                    $(this).parent().html($(this).data('original'));
                }
            }
        });
    }


    $('.edit-inline').on('click',function(){
        var scope = $(this).data('scope');

        $('#'+scope+' .editable').each(function(){
            $(this).css('display','none');
        });

        $('#'+scope+' .editable-h').each(function(){
            $(this).css('display','');
        });

    });

    $('.cancel-inline').on('click',function(){
        var scope = $(this).data('scope');
        $('#'+scope+' .editable-h').each(function(){
            $(this).css('display','none');
        });

        $('#'+scope+' .editable').each(function(){
            $(this).css('display','');
        });

    });

     $('.save-inline').on('click',function(){

        var scope = $(this).data('scope');
        var action = $(this).data('action');
        var $this = $(this);

        $('#'+scope+' .editable-h').each(function(){

            var $child = $(this).children(":first");

            if ($child.is('input')) {
                if ($child.is('input:text') || $child.is('input:email')) {
                    $(this).next().html($child.val());
                }
                else if ($child.is('input:password')) {
                    $(this).next().html('*****');
                }

            }
            else if ($child.is('textarea')) {
                $(this).next().html($child.val());
            }
            else if ($child.is('select')) {
                $(this).next().html($child.val());
            }
            else if ($child.is('ul')) {
                var cats = '';
                $('#'+$child.attr('id')+' input:checkbox').each(function(){
                    if ($(this).prop('checked')) {
                        cats = cats + '<li>'+$(this).val()+'</li>';
                    }
                });
                $(this).next().children(':first').html(cats);

            }
            $(this).css('display','none');
        });

        $('#'+scope+' .editable').each(function(){
            $(this).css('display','');
        });

        var postData = $('#'+action).serialize();


        // send data via ajax
        $.ajax({
            type:  'POST',
            cache: false,
            url:   ajaxurl+'?action='+action,
            data: postData,

            success : function(output) {
                alert(output);
            }
         });


    });

    $('.delete-cpd').on('click',function(e) {
        e.preventDefault();
        if (confirm('do you really want to delete this?')) {
            var postData = {};
            postData['cpd-log-id'] = $(this).data('cpd-id');
            // send data via ajax
            $.ajax({
                type:  'POST',
                cache: false,
                url:   ajaxurl+'?action=delete_cpd_log',
                data: postData,

                success : function(output) {
                    if (output == 'true') {
                        $('#cpd-'+postData['cpd-log-id']).hide();
                    }
                    else {
                        alert('ERROR');
                    }
                },
                error : function() {
                    alert('ERROR');
                }

             });
        }
    });
    (function($) {
        $.fn.goTo = function() {
            $('html, body').animate({
                scrollTop: $(this).offset().top + 'px'
            }, 'slow');
            return this; // for chaining...
        }
    })(jQuery);
});