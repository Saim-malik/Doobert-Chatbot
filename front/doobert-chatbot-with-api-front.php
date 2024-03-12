<?php

class Doobert_Chatbot_Front {

    private $is_enable;
    private $allowed_pages;
    private $default_msg;
    private $default_options;
    private $contact_us_page;

    public function __construct() {
        $settings = get_option('doobert-chatbot-custom-settings');
        $enable_doobert = isset($settings['enable_doobert']) ? $settings['enable_doobert'] : '';
        $display_pages = isset($settings['display_pages']) ? $settings['display_pages'] : '';
        $def_msg_to_show = isset($settings['def_msg_to_show']) ? sanitize_text_field(stripslashes($settings['def_msg_to_show'])) : '';
        $def_options_to_show = isset($settings['def_options_to_show']) ? sanitize_text_field(stripslashes($settings['def_options_to_show'])) : '';
        $this->contact_us_page = isset($settings['contact_us_page']) ? sanitize_text_field($settings['contact_us_page']) : '';
        $this->is_enable = $enable_doobert;
        $this->allowed_pages = $display_pages;
        $this->default_msg = $def_msg_to_show;
        $this->default_options = $def_options_to_show;
        add_action('wp_enqueue_scripts', array($this, 'doobert_enqueue_styles_and_scripts'));
        add_action('wp_footer', array($this, 'doobert_laod_chatbot'));
    }

    public function doobert_enqueue_styles_and_scripts() {
        if ($this->is_enable == 'enable') {
            wp_enqueue_style('doobert-chatbot-styles', DOBCHAPI_URL . 'assets/front.css', array(), '7.9', 'all');
            wp_enqueue_script('doobert-chatbot-script', DOBCHAPI_URL . 'assets/front.js', array('jquery'), '3.5', true);
            wp_localize_script('doobert-chatbot-script', 'obj', array(
                'security' => wp_create_nonce("verify_doober_token_nonce")
            ));
        }
    }

    public function doobert_laod_chatbot() {
        $training_loaded = get_option('is_training_data_loaded');
        $doobert_api_token_value = get_option('doobert_api_token_value');
        if ($this->is_enable == 'enable' && $training_loaded == 1 && !empty($doobert_api_token_value)) {
            $allowed_page = $this->allowed_pages;
            $is_true = 1;
            global $post;
            if (!empty($allowed_page) && is_array($allowed_page)) {
                if (!in_array($post->ID, $allowed_page)) {
                    $is_true = 0;
                }
            }
            if ($is_true == 1) {


                $settings_design = get_option('_doober_chatbot_design_settings');

                $bubble_icon_bk = isset($settings_design['bubble_icon_bk']) ? sanitize_hex_color($settings_design['bubble_icon_bk']) : '#ffffff';
                $bubble_icon_hover_bk = isset($settings_design['bubble_icon_hover_bk']) ? sanitize_hex_color($settings_design['bubble_icon_hover_bk']) : '#8ca7b7';
                $bubble_icon_ic_bk = isset($settings_design['bubble_icon_ic_bk']) ? sanitize_hex_color($settings_design['bubble_icon_ic_bk']) : '#3181b0';
                $bubble_icon_border = isset($settings_design['bubble_icon_border']) ? sanitize_hex_color($settings_design['bubble_icon_border']) : '#3181b0';
                $bubble_icon_ic_hover_bk = isset($settings_design['bubble_icon_ic_hover_bk']) ? sanitize_hex_color($settings_design['bubble_icon_ic_hover_bk']) : '#ffffff';

                $header_title = isset($settings_design['header_title']) ? sanitize_text_field(stripslashes($settings_design['header_title'])) : 'Hello!';
                $header_bk_color = isset($settings_design['header_bk_color']) ? sanitize_hex_color($settings_design['header_bk_color']) : '#3181b0';
                $header_font_color = isset($settings_design['header_font_color']) ? sanitize_hex_color($settings_design['header_font_color']) : '#ffffff';
                $close_icon_bk_color = isset($settings_design['close_icon_bk_color']) ? sanitize_hex_color($settings_design['close_icon_bk_color']) : '#ffffff';
                $close_icon_front_color = isset($settings_design['close_icon_front_color']) ? sanitize_hex_color($settings_design['close_icon_front_color']) : '#3181b0';
                $close_icon_border_color = isset($settings_design['close_icon_border_color']) ? sanitize_hex_color($settings_design['close_icon_border_color']) : '#3181b0';
                $header_logo = isset($settings_design['header_logo']) ? esc_url($settings_design['header_logo']) : '';

                $body_logo = isset($settings_design['body_logo']) ? esc_url($settings_design['body_logo']) : '';
                $cb_body_bk = isset($settings_design['cb_body_bk']) ? sanitize_hex_color($settings_design['cb_body_bk']) : '#ffffff';
                $cb_default_msg_color = isset($settings_design['cb_default_msg_color']) ? sanitize_hex_color($settings_design['cb_default_msg_color']) : '#3181b0';
                $cb_body_chat_bk = isset($settings_design['cb_body_chat_bk']) ? sanitize_hex_color($settings_design['cb_body_chat_bk']) : '#e6e7ec';
                $cb_body_chat_front_color = isset($settings_design['cb_body_chat_front_color']) ? sanitize_hex_color($settings_design['cb_body_chat_front_color']) : '#000000';
                $cb_body_user_bk = isset($settings_design['cb_body_user_bk']) ? sanitize_hex_color($settings_design['cb_body_user_bk']) : '#3181b0';
                $cb_body_user_front_color = isset($settings_design['cb_body_user_front_color']) ? sanitize_hex_color($settings_design['cb_body_user_front_color']) : '#ffffff';
                $scroll_bar_color = isset($settings_design['scroll_bar_color']) ? sanitize_hex_color($settings_design['scroll_bar_color']) : '#3181b0';

                $send_message_placeholder = isset($settings_design['send_message_placeholder']) ? sanitize_text_field(stripslashes($settings_design['send_message_placeholder'])) : 'Type your message...';
                $send_message_bk = isset($settings_design['send_message_bk']) ? sanitize_hex_color($settings_design['send_message_bk']) : '#fffbfb';
                $send_message_button_bk = isset($settings_design['send_message_button_bk']) ? sanitize_hex_color($settings_design['send_message_button_bk']) : '#3181b0';
                $send_message_button_icon_color = isset($settings_design['send_message_button_icon_color']) ? sanitize_hex_color($settings_design['send_message_button_icon_color']) : '#ffffff';

                include 'doobert-chatbot-css.php';

                $pw_icon = DOBCHAPI_URL . 'assets/icon-paw2.png';

                if (!empty($body_logo)) {
                    $pw_icon = $body_logo;
                }


                $yes_option = '';
                if (!empty($this->contact_us_page)) {
                    $yes_option = $this->contact_us_page;
                    $yes_option = get_permalink($yes_option);
                } else {
                    $yes_option = get_option('admin_email');
                }
                $chat_ic = '<svg id="chat_ic_svg" xmlns="http://www.w3.org/2000/svg" version="1.0" width="225.000000pt" height="225.000000pt" viewBox="0 0 225.000000 225.000000" preserveAspectRatio="xMidYMid meet">
				<g transform="translate(0.000000,225.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none" style="&#10;    fill: red;&#10;">
				<path d="M354 2071 c-98 -33 -144 -91 -169 -208 -21 -100 -21 -579 0 -651 29 -95 109 -161 226 -185 l64 -13 5 -234 c3 -160 9 -235 16 -237 19 -6 138 106 359 338 50 52 97 104 106 115 15 20 29 21 265 27 287 7 331 15 399 78 78 72 87 107 97 399 13 363 -5 470 -90 540 -19 16 -57 34 -84 40 -31 6 -250 10 -596 9 -493 0 -552 -2 -598 -18z m290 -395 c51 -21 76 -60 76 -117 0 -68 -36 -109 -103 -116 -90 -10 -142 35 -142 124 0 41 5 57 25 78 42 46 86 55 144 31z m724 -6 c69 -42 77 -137 16 -197 -28 -29 -38 -33 -86 -33 -82 0 -128 45 -128 125 0 103 108 160 198 105z m-338 -19 c52 -51 52 -125 1 -180 -24 -26 -37 -31 -74 -31 -25 0 -57 4 -71 10 -37 14 -66 67 -66 119 0 36 6 49 34 77 31 31 40 34 91 34 49 0 60 -4 85 -29z"/>
				<path d="M1834 1846 c-3 -7 -4 -35 -2 -62 3 -47 4 -49 46 -64 24 -8 48 -23 53 -32 6 -10 11 -165 12 -360 2 -480 12 -461 -256 -466 -118 -2 -174 -7 -180 -15 -4 -7 -7 -61 -6 -122 2 -132 2 -135 -11 -135 -6 0 -71 61 -146 135 l-135 135 -105 0 -104 0 -47 -42 c-56 -50 -82 -80 -83 -91 0 -5 23 -6 51 -2 28 4 96 4 152 0 l102 -7 181 -179 c157 -156 185 -179 212 -179 53 0 66 38 70 213 4 170 -10 153 133 160 142 7 223 57 275 169 l29 63 3 266 c4 330 -8 510 -35 548 -42 57 -194 106 -209 67z"/>
				</g>
				</svg>';
                ?>
                <div id="chat-popup">
                    <div id="chat-icon">
                        <?php echo $chat_ic; ?>
                    </div>

                    <div id="chat-box">
                        <div id="chat-close-button">

                            <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="40.000000pt" height="40.000000pt" viewBox="0 0 40.000000 40.000000" preserveAspectRatio="xMidYMid meet">
                                <g transform="translate(0.000000,40.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                    <path d="M66 323 c-3 -4 23 -36 58 -72 l66 -66 -60 -59 c-33 -32 -60 -62 -60 -67 0 -22 29 -5 82 48 l58 57 59 -58 c39 -39 65 -57 77 -54 16 3 11 12 -31 54 -88 88 -87 77 -15 149 49 50 60 66 48 70 -11 5 -35 -13 -75 -54 l-59 -61 -59 60 c-57 58 -74 68 -89 53z"/>
                                </g>
                            </svg>

                        </div>
                        <div id="chat-header">
                            <?php
                            if (!empty($header_logo)) {
                                ?>
                                <div class="chat_d_icon"><img src="<?php echo $header_logo ?>"></div>
                                <?php
                            } else {
                                ?>
                                <div class="chat_d_icon"><img src="<?php echo DOBCHAPI_URL . 'assets/icon_d2.png' ?>"></div>
                            <?php } ?>
                            <h4><?php _e($header_title, 'doobert-chatbot'); ?></h4>
                        </div>
                        <div class="chat_load">
                            <div id="chat-body">

                                <div id="default_msgs">
                                    <div class="columns response_msg">
                                        <div class="column actual_chat">
                                            <img src="<?php echo $pw_icon; ?>"  class="chat_icon" />
                                            <div class="notification is-success">
                                                <div class="msg notification_part default_msgs_top"><?php echo $this->default_msg; ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pre_defined_msg">
                                        <?php
                                        $pre = $this->default_options;
                                        if (!empty($pre)) {
                                            $pre_arr = explode(';', $pre);
                                            if (!empty($pre_arr)) {
                                                foreach ($pre_arr as $m) {
                                                    ?>
                                                    <div class="pre_button"><?php echo $m; ?></div>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </div>

                                </div>

                                <div class="chat_time"></div>
                                <div class="messageHistory chat_content"></div>
                            </div>
                            <div id="chat-footer">
                                <textarea id="message-input" placeholder="<?php _e($send_message_placeholder, 'doobert-chatbot'); ?>"></textarea>
                                <button id="send-message"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="20" height="20" viewBox="0 0 20 20">
                                        <title><?php _e('send', 'doobert-chatbot'); ?></title>
                                        <path d="M0 0l20 10-20 10v-20zM0 8v4l10-2-10-2z"/>
                                    </svg></button>
                            </div>
                            <div class="loading_overlay"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="ajax_url" value="<?php echo admin_url('admin-ajax.php'); ?>" />
                <input type="hidden" id="chat_icon" value="<?php echo $pw_icon; ?>">
                    <input type="hidden" id="doobert_yes_option" value="<?php echo $yes_option; ?>">
                        <input type="hidden" id="waiting_icon" value="<?php echo DOBCHAPI_URL . 'assets/loading1.gif'; ?>">
                            <?php
                            $avatar = '';
                            if (is_user_logged_in()) {
                                $user_id = get_current_user_id();
                                $avatar = get_avatar($user_id, 96);
                                preg_match('/src=["\'](.*?)["\']/', $avatar, $matches);

                                // Display the avatar URL
                                $avatar = isset($matches[1]) ? $matches[1] : '';
                            } else {
                                $avatar = DOBCHAPI_URL . 'assets/avatar.png';
                            }
                            echo '<input type="hidden" id="avatar_user" value="' . $avatar . '"/>';
                        }
                    }
                }
            }

            new Doobert_Chatbot_Front();
            