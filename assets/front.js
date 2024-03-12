var $ = jQuery;
var ajax_url = '';
var chat_icon = '';
var chatBox = '';
$(function () {
    jQuery(document).on('click', '.not_found_div_inner', function () {
        var type = jQuery(this).attr('data-type');
        if (type == 'yes') {
            var doobert_yes_option = jQuery('#doobert_yes_option').val();
            var h = '';
            if (doobert_yes_option.indexOf('@') > 1) {
                h = `<div class="columns response_msg response_bot">
				<div class="column actual_chat">
				<img src="` + chat_icon + `"  class="chat_icon" />
				<div class="notification is-success">
				<div class="msg notification_part"><div class="contact_here">Contact <a href="mailto:` + doobert_yes_option + `">` + doobert_yes_option + `</a> here for more information</div></div>
				</div>
				</div>
				</div>`;
            } else {
                h = `<div class="columns response_msg response_bot">
				<div class="column actual_chat">
				<img src="` + chat_icon + `"  class="chat_icon" />
				<div class="notification is-success">
				<div class="msg notification_part"><div class="contact_here"><a href="` + doobert_yes_option + `" target="_blank">Contact here</a> for more information</div></div>
				</div>
				</div>
				</div>`;
            }
        } else {
            var default_msgs_top = jQuery('.default_msgs_top').html();
            h = `<div class="columns response_msg response_bot">
			<div class="column actual_chat">
			<img src="` + chat_icon + `"  class="chat_icon" />
			<div class="notification is-success">
			<div class="msg notification_part">` + default_msgs_top + `</div>
			</div>
			</div>
			</div>`;
        }
        jQuery(this).closest('.not_found_div_main').hide();
        chatBox.innerHTML += h;
        chatBox.scrollIntoView(false);
    });
    jQuery(document).on('click', '.pre_button', function () {
        var txt = jQuery(this).html();
        jQuery('#message-input').val(txt);
        jQuery('#send-message').click();
    });

    chat_icon = jQuery('#chat_icon').val();
    ajax_url = jQuery('#ajax_url').val();
    $('#chat-icon-hidden').click(function () {
        jQuery('#chat-box').toggleClass('show_chat');
        jQuery('#chat-icon').toggleClass('chat_active');
        jQuery('#message-input').focus();
    });

    $('#chat-icon').click(function () {
        jQuery('#chat-box').toggleClass('show_chat');
        jQuery('#chat-icon').toggleClass('chat_active');
        jQuery('#message-input').focus();
    });

    $('#chat-close-button').click(function () {
        jQuery('#chat-box').toggleClass('show_chat');
        jQuery('#chat-icon').toggleClass('chat_active');
        jQuery('#message-input').focus();
    });

    $('#close-chat').click(function () {
        jQuery('#chat-box').removeClass('show_chat');
    });

    $('#message-input').keypress(function (e) {
        if (e.which === 13) {
            $('#send-message').click();
        }
    });
    var new_chat = 0;
    var user_img = jQuery('#avatar_user').val();
    var waiting_icon = jQuery('#waiting_icon').val();

    $('#send-message').click(function () {
        var message = jQuery('#message-input').val();
        if (message && message != '' && message.trim() != '') {
            jQuery('.chat_load').addClass('loading');
            message = message.charAt(0).toUpperCase() + message.slice(1);
            const userMsgTemplate = `<div class="columns user_msg">
			<div class="column is-one-third"></div>
			<div class="column user_column">
			<div class="notification is-success">
			<div class="msg notification_part">${message}</div>
			</div>
			<div class="user_avatar_outer"><img class="user_avatar" src="` + user_img + `" /></div>
			</div>
			</div>`;
            if (new_chat == 0) {
                new_chat = 1;
                let currHour = new Date();
                var hours = currHour.getHours();
                var minutes = currHour.getMinutes();
                var formattedMinutes = minutes.toString().padStart(2, '0');
                var timeString = 'Today <span>' + (hours + ":" + formattedMinutes) + '</span>';
                jQuery('.chat_time').html(timeString);
            }
            chatBox = document.querySelector(".messageHistory");
            chatBox.innerHTML += userMsgTemplate;

            var html_to_show1 = `<div class="columns waiting_for_response"><div class="chat-bubble">
			<img src="` + chat_icon + `"  class="chat_icon" /><img src="` + waiting_icon + `" />
			</div></div>`;
            chatBox.innerHTML += html_to_show1;
            chatBox.scrollIntoView(false);
            // const payload = JSON.stringify({
            // 	"message": message
            // });
            jQuery('#message-input').val('');

            $.ajax({
                url: ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'openai_request',
                    payload: message,
                    security: obj.security,
                },
                success: function (response) {
                    jQuery('.chat_load').removeClass('loading');
                    jQuery('.waiting_for_response').remove();
                    var html_to_show = '';
                    // let currHour = new Date();
                    if (response.status == 2) {

                        html_to_show = `<div class="columns response_msg">
					<div class="column actual_chat">
					<img src="` + chat_icon + `"  class="chat_icon" />
					<div class="notification is-success">
					<div class="msg notification_part">${response.message}</div>
					</div>
					</div>
					</div>
					<div class="not_found_div_main"><div class="not_found_div_inner op_yes" data-type="yes">YES</div><div class="not_found_div_inner op_no" data-type="no">NO</div></div>`;

                        chatBox.innerHTML += html_to_show;
                        chatBox.scrollIntoView(false);

                    } else if (response.status == 0) {

                        html_to_show = `<div class="columns response_msg error_msg">
					<div class="column is-one-third"></div>
					<div class="column actual_chat">
					<img src="` + chat_icon + `"  class="chat_icon" />
					<div class="notification is-success">
					<div class="msg notification_part">Some error while response</div>
					</div>
					</div>
					</div>`;

                        chatBox.innerHTML += html_to_show;
                        chatBox.scrollIntoView(false);

                    } else {
                        if (response.message && response.message != null) {

                            html_to_show = `<div class="columns response_msg">
						<div class="column actual_chat">
						<img src="` + chat_icon + `"  class="chat_icon" />
						<div class="notification is-success">
						<div class="msg notification_part">${response.message}</div>
						</div>
						</div>
						</div>`;

                            chatBox.innerHTML += html_to_show;
                            chatBox.scrollIntoView(false);

                        } else {
                            html_to_show = `<div class="columns response_msg">
						<div class="column actual_chat">
						<img src="` + chat_icon + `"  class="chat_icon" />
						<div class="notification is-success">
						<div class="msg notification_part">Some error while response</div>
						</div>
						</div>
						</div>`;

                            chatBox.innerHTML += html_to_show;
                            chatBox.scrollIntoView(false);
                        }
                    }
                }
            });
        }
    });
});


