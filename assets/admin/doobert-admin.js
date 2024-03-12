/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */
var ajaxUrl = obj.ajaxurl;
jQuery(function () {
    jQuery('.full_select').select2();
    jQuery('.show').click(function () {
        jQuery('.show_hide_icon').addClass('shown');
        jQuery('#doobert_api_key').attr('type', 'text');
    });

    jQuery('.hide').click(function () {
        jQuery('.show_hide_icon').removeClass('shown');
        jQuery('#doobert_api_key').attr('type', 'password');
    });
});

var url_to_hite = obj.admin_url;
//alert(url_to_hite);
url_to_hite = url_to_hite + 'admin.php?page=doobert-chatbot-settings&load_csv=1&_wpnonce='+obj.nonce;

jQuery('.verify_token').click(function () {
    var token = jQuery('#doobert_authorization_token').val();
    var is_valid = 0;
    if (token && token != '') {
        jQuery('.verify_token').html('please wait...');
        token = token.trim();
        if (token && token != '') {
            is_valid = 1;
            jQuery.ajax({
                url: ajaxUrl,
                        type: 'post',
                data: {
                    action: 'verify_doober_token',
                    security:obj.security,
                    token:token
                },
                success: function (res) {
                    jQuery('.verify_token').html('Verify');
                    if (res == 1) {
                        jQuery('.doobert_error').html('');
                        jQuery('.token_correct').html('Token Verified Successfully');
                        setTimeout(function () {
                            location.href = url_to_hite;
                        }, 1000);
                    } else {
                        jQuery('.doobert_error').html('Invalid token');
                        setTimeout(function () {
                            jQuery('.doobert_error').html('');
                        }, 2000);
                    }
                }
            });
        }
    }

    if (is_valid == 0) {
        jQuery('.doobert_error').html('Enter Token to continue');
        setTimeout(function () {
            jQuery('.doobert_error').html('');
        }, 2000);
    }

});



jQuery(document).ready(function ($) {

		jQuery(document).on('click', '.clear_logo', function(){
			jQuery(this).closest('tr').find('.logo_upload').val('');
		});

		var custom_uploader;
		jQuery(document).on('click', '.logo_upload', function (e) {
			e.preventDefault();
			let tg = jQuery(this);
			custom_uploader = wp.media({
				title: 'Choose Image',
				library: {
                            // limit the media library to show only image files
                            type: 'image'
                        },
                        button: {
                        	text: 'Insert Image'
                        },
                        multiple: false  // set this to true if you want to allow multiple image selection
                    });
			custom_uploader.on('select', function () {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				tg.val(attachment.url);
			});
			custom_uploader.open();
		});
	});