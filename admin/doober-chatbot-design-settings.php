<?php
if (isset($_POST['save_design_settings']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'doobert-chatbot-settings')) {
    update_option('_doober_chatbot_design_settings', $_POST);
}
// delete_option('_doober_chatbot_design_settings');
$settings_design = get_option('_doober_chatbot_design_settings');
$bubble_icon_bk = isset($settings_design['bubble_icon_bk']) ? sanitize_hex_color($settings_design['bubble_icon_bk']) : '#ffffff';
$bubble_icon_hover_bk = isset($settings_design['bubble_icon_hover_bk']) ? sanitize_hex_color($settings_design['bubble_icon_hover_bk']) : '#8ca7b7';
$bubble_icon_ic_bk = isset($settings_design['bubble_icon_ic_bk']) ? sanitize_hex_color($settings_design['bubble_icon_ic_bk']) : '#3181b0';
$bubble_icon_border = isset($settings_design['bubble_icon_border']) ? sanitize_hex_color($settings_design['bubble_icon_border']) : '#3181b0';
$bubble_icon_ic_hover_bk = isset($settings_design['bubble_icon_ic_hover_bk']) ? sanitize_hex_color($settings_design['bubble_icon_ic_hover_bk']) : '#ffffff';
?>
<form method="post" action="">

    <?php wp_nonce_field('doobert-chatbot-settings'); ?>

    <h3><?php _e('Main Bubble Icon Settings', 'doobert-chatbot'); ?></h3>
    <table class="form-table">
        <tr>
            <th><?php _e('Background Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="bubble_icon_bk" value="<?php echo $bubble_icon_bk; ?>" /></td>
        </tr>
        <tr>
            <th><?php _e('Background Hover Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="bubble_icon_hover_bk" value="<?php echo $bubble_icon_hover_bk; ?>"/></td>
        </tr>

        <tr>
            <th><?php _e('Icon Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="bubble_icon_ic_bk" value="<?php echo $bubble_icon_ic_bk; ?>"/></td>
        </tr>
        <tr>
            <th><?php _e('Icon Hover Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="bubble_icon_ic_hover_bk" value="<?php echo $bubble_icon_ic_hover_bk; ?>"/></td>
        </tr>

        <tr>
            <th><?php _e('Border Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="bubble_icon_border" value="<?php echo $bubble_icon_border; ?>"/></td>
        </tr>

    </table>

    <h3>
        <?php _e('Header Area Settings', 'doobert-chatbot'); ?>
    </h3>

    <?php
    $header_title = isset($settings_design['header_title']) ? sanitize_text_field(stripslashes($settings_design['header_title'])) : 'Hello!';
    $header_bk_color = isset($settings_design['header_bk_color']) ? sanitize_hex_color($settings_design['header_bk_color']) : '#3181b0';
    $header_font_color = isset($settings_design['header_font_color']) ? sanitize_hex_color($settings_design['header_font_color']) : '#ffffff';
    $close_icon_bk_color = isset($settings_design['close_icon_bk_color']) ? sanitize_hex_color($settings_design['close_icon_bk_color']) : '#ffffff';
    $close_icon_front_color = isset($settings_design['close_icon_front_color']) ? sanitize_hex_color($settings_design['close_icon_front_color']) : '#3181b0';
    $close_icon_border_color = isset($settings_design['close_icon_border_color']) ? sanitize_hex_color($settings_design['close_icon_border_color']) : '#3181b0';
    $header_logo = isset($settings_design['header_logo']) ? esc_url($settings_design['header_logo']) : '';
    ?>

    <table class="form-table">
        <tr>
            <th><?php _e('Header Title', 'doobert-chatbot'); ?></th>
            <td><input type="text" name="header_title" value="<?php echo $header_title; ?>"></td>
        </tr>
        <tr>
            <th><?php _e('Header Background Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="header_bk_color" value="<?php echo $header_bk_color; ?>"></td>
        </tr>
        <tr>
            <th><?php _e('Header Front Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="header_font_color" value="<?php echo $header_font_color; ?>"></td>
        </tr>
        <tr>
            <th><?php _e('Header Logo (112 X 112 recommended)r', 'doobert-chatbot'); ?></th>
            <td><input type="text" class="logo_upload" name="header_logo" value="<?php echo $header_logo; ?>">
                <?php
                if (!empty($header_logo)) {
                    ?>
                    <a href="<?php echo $header_logo ?>" target="_blank"><?php _e('view', 'doobert-chatbot'); ?></a>&nbsp;
                    <span class="clear_logo">Clear</span>
                    <?php
                }
                ?>
            </td>

        </tr>

        <tr>
            <th><?php _e('Close Icon Background Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="close_icon_bk_color" value="<?php echo $close_icon_bk_color; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Close Icon Front Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="close_icon_front_color" value="<?php echo $close_icon_front_color; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Close Icon Border Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="close_icon_border_color" value="<?php echo $close_icon_border_color; ?>"></td>
        </tr>
    </table>
    <h3><?php _e('Chatbot Body Design Settings', 'doobert-chatbot'); ?></h3>
    <?php
    $body_logo = isset($settings_design['body_logo']) ? esc_url($settings_design['body_logo']) : '';
    $cb_body_bk = isset($settings_design['cb_body_bk']) ? sanitize_hex_color($settings_design['cb_body_bk']) : '#ffffff';
    $cb_default_msg_color = isset($settings_design['cb_default_msg_color']) ? sanitize_hex_color($settings_design['cb_default_msg_color']) : '#3181b0';
    $cb_body_chat_bk = isset($settings_design['cb_body_chat_bk']) ? sanitize_hex_color($settings_design['cb_body_chat_bk']) : '#e6e7ec';
    $cb_body_chat_front_color = isset($settings_design['cb_body_chat_front_color']) ? sanitize_hex_color($settings_design['cb_body_chat_front_color']) : '#000000';
    $cb_body_user_bk = isset($settings_design['cb_body_user_bk']) ? sanitize_hex_color($settings_design['cb_body_user_bk']) : '#3181b0';
    $cb_body_user_front_color = isset($settings_design['cb_body_user_front_color']) ? sanitize_hex_color($settings_design['cb_body_user_front_color']) : '#ffffff';
    $scroll_bar_color = isset($settings_design['scroll_bar_color']) ? sanitize_hex_color($settings_design['scroll_bar_color']) : '#3181b0';
    $send_message_placeholder = isset($settings_design['send_message_placeholder']) ? sanitize_text_field(stripslashes($settings_design['send_message_placeholder'])) : 'Type your message...';
    $send_message_bk = isset($settings_design['send_message_bk']) ? sanitize_hex_color($settings_design['send_message_bk']) : '#d4d4d4';
    $send_message_button_bk = isset($settings_design['send_message_button_bk']) ? sanitize_hex_color($settings_design['send_message_button_bk']) : '#3181b0';
    $send_message_button_icon_color = isset($settings_design['send_message_button_icon_color']) ? sanitize_hex_color($settings_design['send_message_button_icon_color']) : '#ffffff';
    ?>
    <table class="form-table">
        <tr>
            <th><?php _e('Chat Logo (112 X 112 recommended)', 'doobert-chatbot'); ?></th>
            <td><input type="text" class="logo_upload" name="body_logo" value="<?php echo $body_logo; ?>">
                <?php
                if (!empty($body_logo)) {
                    ?>
                    <a href="<?php echo $body_logo ?>" target="_blank"><?php _e('view', 'doobert-chatbot'); ?></a>&nbsp;
                    <span class="clear_logo">Clear</span>
                    <?php
                }
                ?>
            </td>
        </tr>

        <tr>
            <th><?php _e('Chatbot Background', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="cb_body_bk" value="<?php echo $cb_body_bk; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Default Messages Color and border color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="cb_default_msg_color" value="<?php echo $cb_default_msg_color; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Chatbot Chat response Background', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="cb_body_chat_bk" value="<?php echo $cb_body_chat_bk; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Chatbot Chat Front response Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="cb_body_chat_front_color" value="<?php echo $cb_body_chat_front_color; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('User Chat Background', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="cb_body_user_bk" value="<?php echo $cb_body_user_bk; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('User Chat Front Color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="cb_body_user_front_color" value="<?php echo $cb_body_user_front_color; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Scroll Bar color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="scroll_bar_color" value="<?php echo $scroll_bar_color; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Send Message input Placeholder', 'doobert-chatbot'); ?></th>
            <td><input type="text" name="send_message_placeholder" value="<?php echo $send_message_placeholder; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Send Message input Background', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="send_message_bk" value="<?php echo $send_message_bk; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Send Message Button Background', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="send_message_button_bk" value="<?php echo $send_message_button_bk; ?>"></td>
        </tr>

        <tr>
            <th><?php _e('Send Message Button Icon color', 'doobert-chatbot'); ?></th>
            <td><input type="color" name="send_message_button_icon_color" value="<?php echo $send_message_button_icon_color; ?>"></td>
        </tr>
    </table>
    <input type="submit" class="button button-primary" name="save_design_settings" value="<?php _e('Save Settings', 'doobert-chatbot'); ?>">
</form>
