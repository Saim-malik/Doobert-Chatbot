<style type="text/css">

    #chat-icon svg {
        height: 45px;
    }
    #chat-icon {
        border-color: <?php echo $bubble_icon_border; ?> !important;
        background-color: <?php echo $bubble_icon_bk ?> !important;
    }
    div#chat-icon:hover {
        background-color: <?php echo $bubble_icon_hover_bk ?> !important;
    }

    #chat-icon g {
        fill: <?php echo $bubble_icon_ic_bk; ?> !important;
    }
    div#chat-icon:hover svg#chat_ic_svg g {
    	fill: <?php echo $bubble_icon_ic_hover_bk; ?> !important;
    }
    #chat-header {
        background-color: <?php echo $header_bk_color; ?> !important;
    }
    #chat-header h4 {
        color: <?php echo $header_font_color; ?> !important;
    }
    div#chat-close-button {
        border-color: <?php echo $close_icon_border_color; ?> !important;
    }
    div#chat-close-button {
        background-color: <?php echo $close_icon_bk_color; ?>;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    div#chat-close-button svg {
        width: 30px;
    }
    div#chat-close-button g {
        fill: <?php echo $close_icon_front_color; ?>;
    }
    .is-success {
        background: <?php echo $cb_body_user_bk; ?>;
    }
    .user_column .msg.notification_part {
        color: <?php echo $cb_body_user_front_color; ?>;
    }
    #chat-body,.messageHistory {
        background-color: <?php echo $cb_body_bk; ?> !important;
    }
    #chat_load .pre_button {
        color: <?php echo $cb_default_msg_color; ?>;
        border-color: <?php echo $cb_default_msg_color; ?>;
    }
    .actual_chat .notification.is-success {
        background-color: <?php echo $cb_body_chat_bk; ?> !important;
    }
    .is-success * {
        color: <?php echo $cb_body_chat_front_color; ?>;
    }
    #chat-body::-webkit-scrollbar-thumb, #chat-footer #message-input::-webkit-scrollbar-thumb {
        background-color: <?php echo $scroll_bar_color; ?>;
    }
    #chat-body::-moz-scrollbar-thumb, #chat-footer #message-input::-moz-scrollbar-thumb {
        background-color: <?php echo $scroll_bar_color; ?>;
    }
    #message-input {
        background: <?php echo $send_message_bk; ?>;
    }
    #send-message {
        background: <?php echo $send_message_button_bk; ?> !important;
    }
    #send-message svg {
        fill: <?php echo $send_message_button_icon_color ?> !important;
    }

</style>