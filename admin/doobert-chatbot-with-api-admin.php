<?php
add_action('admin_menu', 'doobert_chatbot_settings_menu');

function doobert_chatbot_settings_menu() {
    add_menu_page(
            __('Doobert Chatbot Settings', 'doobert-chatbot'),
            __('Doobert Chatbot', 'doobert-chatbot'),
            'manage_options',
            'doobert-chatbot-settings',
            'doobert_chatbot_settings_page',
            DOBCHAPI_URL . 'assets/iconpaw.png',
            30
    );
}

function doobert_admin_enqueue_admin_styles() {
    $current_screen = get_current_screen();
    if ($current_screen && $current_screen->id === 'toplevel_page_doobert-chatbot-settings') {
        wp_enqueue_media();
        wp_enqueue_style('admin_select2_css', DOBCHAPI_URL . 'assets/admin/select2.min.css', array(), '1.0', 'all');
        wp_enqueue_style('admin_doobert_css', DOBCHAPI_URL . 'assets/admin/doobert-admin.css', array(), '1.0', 'all');
        wp_enqueue_script('admin_select2_js', DOBCHAPI_URL . 'assets/admin/select2.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('admin_doobert_js', DOBCHAPI_URL . 'assets/admin/doobert-admin.js', array('jquery', 'admin_select2_js'), '1.7', true);
        wp_localize_script('admin_doobert_js', 'obj', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'admin_url' => admin_url(),
            'security' => wp_create_nonce("verify_doober_token_nonce"),
            'nonce' => wp_create_nonce('doobert-chatbot-settings')
        ));
    }
}

add_action('admin_enqueue_scripts', 'doobert_admin_enqueue_admin_styles');

function doobert_chatbot_settings_page() {

//    $options_array = array('doobert-chatbot-custom-settings', 'doobert_api_token_value', 'is_training_data_loaded', '_doobert_token_time_stamp', '_doobert_token_value', 'doober_validation_data');
//    foreach ($options_array as $op) {
//        delete_option($op);
//    }
    ?>
    <div class="wrap">
        <div class="doobert_intro">
            <img src="<?php echo DOBCHAPI_URL . 'assets/doober_icon.png'; ?>">
            <h3><?php echo esc_html__('Doobert ChatBot', 'doobert-chatbot'); ?></h3>
        </div>
        <div class="buy_pro_link">
            <div class="button button-primary"><a style="color:white;" target="_blank" href="https://app.doobert.com/upgrade-chatbot"><?php echo esc_html__('Upgrade to Premium', 'doobert-chatbot'); ?></a></div>
        </div>
        <div class="notice notice-information is-dismissible">
            <p><b>
                    <?php echo esc_html__('We will update the information that the chatbot reads on your website every two weeks, from the day you installed the plugin', 'doobert-chatbot'); ?>
                </b></p>
        </div>
    </div>
    <?php
    if (isset($_POST['save_settings']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'doobert-chatbot-settings')) {
        $def_msg_to_show = isset($_POST['def_msg_to_show']) ? stripslashes(sanitize_text_field($_POST['def_msg_to_show'])) : '';
        $def_options_to_show = isset($_POST['def_options_to_show']) ? stripslashes(sanitize_text_field($_POST['def_options_to_show'])) : '';
        $news_letter_email = !empty($_POST['news_letter_email']) ? sanitize_email($_POST['news_letter_email']) : '';
        $contact_us_page = !empty($_POST['contact_us_page']) ? sanitize_text_field($_POST['contact_us_page']) : '';
        $display_pages = isset($_POST['display_pages']) ? array_map('intval', $_POST['display_pages']) : array();
        $array = array(
            'enable_doobert' => sanitize_text_field($_POST['enable_doobert']),
            'display_pages' => $display_pages,
            'def_msg_to_show' => $def_msg_to_show,
            'def_options_to_show' => $def_options_to_show,
            'contact_us_page' => $contact_us_page,
            'news_letter_email' => $news_letter_email
        );
        update_option('doobert-chatbot-custom-settings', $array);

        // $encrypted_api_key = wp_encrypt($_POST['doobert_api_key']);

        $key = 'doobert';
        $iv = openssl_random_pseudo_bytes(16);
        $original_string = $_POST['doobert_api_key'];
        $encrypted_value = openssl_encrypt($original_string, 'aes-256-cbc', 'doobert', 0, 16);
        update_option('doobert_api_token_value', $encrypted_value);

        if (!empty($news_letter_email) && !empty($original_string)) {
            $data = array('website' => site_url(), 'email' => $news_letter_email);
            $request_args = array(
                'body' => $data,
                'headers' => array(
                    'Authorization' => 'Bearer ' . $original_string,
                ),
                'timeout' => 60,
                'redirection' => 5,
                'sslverify' => false,
            );
            $response = wp_remote_post('https://app.doobert.com/api/v1/plugin/update-email', $request_args);
        }
    }
    $settings = get_option('doobert-chatbot-custom-settings');

    $enable_doobert = isset($settings['enable_doobert']) ? $settings['enable_doobert'] : '';
    $display_pages = isset($settings['display_pages']) ? $settings['display_pages'] : '';
    $def_msg_to_show = isset($settings['def_msg_to_show']) ? stripslashes($settings['def_msg_to_show']) : '';
    $news_letter_email = isset($settings['news_letter_email']) ? $settings['news_letter_email'] : '';
    $def_options_to_show = isset($settings['def_options_to_show']) ? stripslashes($settings['def_options_to_show']) : '';
    $contact_us_page = isset($settings['contact_us_page']) ? $settings['contact_us_page'] : '';
    $doobert_api_token_value = get_option('doobert_api_token_value');
    if (!empty($doobert_api_token_value)) {
        $doobert_api_token_value = openssl_decrypt($doobert_api_token_value, 'aes-256-cbc', 'doobert', 0, 16);
    }

    $pages = get_pages(array(
        'post_type' => 'page',
    ));

    $is_valid_user = get_option('doober_validation_data');
    $doobert_token_value = get_option('_doobert_token_value');
    if (!empty($is_valid_user) && !empty($doobert_token_value)) {

        $active_gen = 'nav-tab-active';
        $active_des = '';
        if (isset($_GET['tab']) && $_GET['tab'] == 'design') {
            $active_gen = '';
            $active_des = 'nav-tab-active';
        }
        ?>
        <div class="wrap">
            <h3><?php _e('Doobert Settings', 'doobert-chatbot'); ?></h3>
            <div class="doobert_tab nav-tab-wrapper">
                <a class="nav-tab <?php echo $active_gen; ?>" href="admin.php?page=doobert-chatbot-settings"><?php echo esc_html__('General Settings', 'doobert-chatbot'); ?></a>
                <a class="nav-tab <?php echo $active_des; ?>" href="admin.php?page=doobert-chatbot-settings&tab=design"><?php echo esc_html__('Design Settings', 'doobert-chatbot'); ?></a>
            </div>
            <?php
            if ($active_des == 'nav-tab-active') {
                include 'doober-chatbot-design-settings.php';
            } else {
                $training_loaded = get_option('is_training_data_loaded');
                if ($training_loaded != 1) {
                    ?>
                    <div class="message notice">
                        <p>Please Load train data to enable chatbot</p>
                    </div>
                    <?php
                }
                if (isset($_GET['loaded'])) {
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p>Training Data Loaded successfully</p>
                    </div>
                    <?php
                }
                $nonce = wp_create_nonce('doobert-chatbot-settings');
                ?>
                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <td><a href="?load_csv=1&_wpnonce=<?php echo $nonce; ?>" class="button button-primary"><?php echo esc_html__('Update Data', 'doobert-chatbot'); ?></a></td>
                            <td><?php echo esc_html__('If you have added more pages/posts to your website, please click the “Update Data” to the left. This will do a quick scan of your website to find any new content you’ve added so the Chatbot can respond accurately.', 'doobert-chatbot'); ?></td>
                        </tr>
                    </table>
                </form>

                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Enable/Disable', 'doobert-chatbot'); ?></th>
                            <td>
                                <select name="enable_doobert">
                                    <option <?php selected($enable_doobert, 'enable'); ?> value="enable">Enable</option>
                                    <option <?php selected($enable_doobert, 'disable'); ?> value="disable">Disable</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Doobert API Key', 'doobert-chatbot'); ?></th>
                            <td>
                                <input type="password" id="doobert_api_key" name="doobert_api_key" value="<?php echo $doobert_api_token_value; ?>" required>
                                <span class="show_hide_icon"><span class="show">Show</span><span class="hide">Hide</span></span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Display chatbot ONLY on a specific page(s)', 'doobert-chatbot'); ?></th>
                            <td>
                                <select class="full_select" name="display_pages[]" multiple>
                                    <?php
                                    foreach ($pages as $page) {
                                        $selected = '';
                                        if (!empty($display_pages) && is_array($display_pages) && in_array($page->ID, $display_pages)) {
                                            $selected = 'selected=""';
                                        }
                                        echo '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';
                                    }
                                    ?>
                                </select>

                                <br>
                                <small><?php echo esc_html__('Keep empty if you want to show on all pages', 'doobert-chatbot'); ?></small>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Choose Contact Us Page', 'doobert-chatbot'); ?></th>
                            <td>
                                <select class="full_select" name="contact_us_page">
                                    <option value="">Select</option>
                                    <?php
                                    foreach ($pages as $page) {
                                        $selected = '';
                                        if (!empty($contact_us_page) && $contact_us_page == $page->ID) {
                                            $selected = 'selected=""';
                                        }
                                        echo '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Default quick question prompts', 'doobert-chatbot'); ?></th>
                            <td><textarea name="def_msg_to_show" cols="35" rows="5"><?php echo $def_msg_to_show; ?></textarea></td>
                        </tr>

                        <tr>
                            <th><?php _e('Default Options to show (separate them by ; operator)', 'doobert-chatbot'); ?></th>
                            <td><textarea cols="35" rows="5" name="def_options_to_show"><?php echo $def_options_to_show; ?></textarea></td>
                        </tr>

                        <tr>
                            <th><?php _e('Newsletter Email', 'doobert-chatbot'); ?> <br><?php _e('(Add your email if you want to be notified of updates.)', 'doobert-chatbot'); ?></th>
                            <td>
                                <input style="margin-bottom: 30px;" type="email" name="news_letter_email" value="<?php echo $news_letter_email; ?>">
                            </td>
                        </tr>

                    </table>
                    <?php wp_nonce_field('doobert-chatbot-settings'); ?>
                    <input type="submit" name="save_settings" class="button button-primary" value="<?php _e('Save Settings', 'doobert-chatbot'); ?>">
                </form>
            <?php } ?>
        </div>
        <?php
    } else {
        ?>
        <div class="wrap">
            <h3><?php _e('Verify your identity to continue with doobert chatbot', 'doobert-chatbot'); ?></h3>
            <table>
                <tr>
                    <th><?php _e('Enter your authorization token', 'doobert-chatbot'); ?></th>
                    <td>
                        <input type="text" id="doobert_authorization_token">
                    </td>
                </tr>
                <tr>
                    <td><div class="button button-primary verify_token"><?php _e('Verify', 'doobert-chatbot'); ?></div></td>
                </tr>
            </table>
            <p style="color:red" class="doobert_error"></p>
            <p style="color:green;" class="token_correct"></p>
        </div>
        <?php
    }
}

add_action('init', 'custom_scrape_doober_init');

function custom_scrape_doober_init() {
    if (is_admin() && isset($_GET['load_csv']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'doobert-chatbot-settings')) {

        if (!wp_next_scheduled('custom_scrape_doobert_data')) {
            $is_sch = wp_schedule_single_event(time() + 60, 'custom_scrape_doobert_data');
            if (!$is_sch) {
                scrape_doober_data_to_server(3);
            }
        }
        scrape_doober_data_to_server(1);
    }
}

add_action('custom_scrape_doobert_data', 'custom_scrape_doober_callback');

function custom_scrape_doober_callback() {
    wp_clear_scheduled_hook('custom_scrape_doobert_data');
    scrape_doober_data_to_server(2);
}


function scrape_doober_data_to_server($type) {
    update_option('is_training_data_loaded', 1);
    $site_url = site_url();
    $url2 = $site_url . '/wp-admin/admin.php?page=doobert-chatbot-settings&loaded=1';
    if ($type == 1) {
        wp_redirect($url2);
        exit;
    }

    $doobert_api_token_value = get_option('doobert_api_token_value');
    if (!empty($doobert_api_token_value)) {
        $doobert_api_token_value = openssl_decrypt($doobert_api_token_value, 'aes-256-cbc', 'doobert', 0, 16);

        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $posts_data = array();
        foreach ($posts as $post) {
            $post_title = sanitize_text_field($post->post_title);
            $post_content = sanitize_text_field(wp_strip_all_tags(strip_shortcodes(remove_builders($post->post_content))));
            $post_type = $post->post_type;
            $post_url = rtrim(get_permalink($post->ID), '/');
            $site_url = site_url();
            $content_length = strlen($post_content);

            if ($content_length > 30000) {
                $chunks = str_split($post_content, 30000);

                foreach ($chunks as $index => $chunk) {
                    $posts_data[] = array(
                        'website' => $site_url,
                        'title' => $post_title,
                        'type' => $post_type,
                        'content' => $chunk,
                        'url' => $post_url,
                    );
                }
            } else {
                $posts_data[] = array(
                    'website' => $site_url,
                    'title' => $post_title,
                    'type' => $post_type,
                    'content' => $post_content,
                    'url' => $post_url,
                );
            }
        }

        $csv_content = "\xEF\xBB\xBF"; // UTF-8 BOM for proper encoding
        $csv_content .= "website,title,type,content,url\n";
        foreach ($posts_data as $data) {
            $csv_content .= '"' . implode('","', array_map('esc_csv', $data)) . "\"\n";
        }

        // Define the file path
        $host = wp_parse_url($site_url, PHP_URL_HOST);
        $file_name = preg_replace('/[^a-zA-Z0-9]/', '', $host);
        $csv_file_path = DOBCHAPI_PLUGIN_DIR . $file_name . '.csv';

        if (!function_exists('WP_Filesystem')) {
            include_once ABSPATH . 'wp-admin/includes/file.php';
        }

        if (WP_Filesystem()) {

            global $wp_filesystem;

            if (!$wp_filesystem->exists($csv_file_path)) {
                if (!$wp_filesystem->put_contents($csv_file_path, '')) {
                    
                }
            }

            if (!$wp_filesystem->put_contents($csv_file_path, $csv_content, FS_CHMOD_FILE)) {
                
            }
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.doobert.com/api/v1/plugin/add-action',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('csv_file' => new CURLFILE($csv_file_path), 'filename' => $file_name, 'website' => $site_url),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $doobert_api_token_value . ''
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        if ($type == 3) {
            wp_redirect($url2);
            exit;
        }
    }
}

function remove_builders($content) {
    $builder_patterns = array(
        '/\[vc_[^\]]*\]/i',
        '/\[et_pb_[^\]]*\]/i',
        '/\[elementor[^\]]*\]/i',
        '/\[wp_[^\]]*\]/i',
        '/\[themify_[^\]]*\]/i',
        '/\[av_[^\]]*\]/i',
    );

    return preg_replace($builder_patterns, '', $content);
}

function esc_csv($value) {
    return str_replace('"', '""', $value);
}

add_action('wp_ajax_verify_doober_token', 'verify_doober_token');

function verify_doober_token() {
    check_ajax_referer('verify_doober_token_nonce', 'security');

    $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    $site_url = site_url();
    $request_args = array(
        'body' => array(
            'website' => $site_url,
        ),
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
        ),
    );

    $response = wp_remote_post('https://app.doobert.com/api/v1/plugin/verify-token', $request_args);

    if (is_wp_error($response)) {
        echo 0;
    } else {
        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code === 200) {
            $result_array = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($result_array['status']) && $result_array['status'] == 1) {
                $expires_in = $result_array['organization_details']['expires_in'];
                $access_token = $result_array['organization_details']['access_token'];
                $currentTimestamp = time();
                $newTimestamp = $currentTimestamp + $expires_in;
                update_option('_doobert_token_time_stamp', $newTimestamp);
                update_option('_doobert_token_value', $access_token);
                update_option('doober_validation_data', $response);
                $encrypted_value = openssl_encrypt($token, 'aes-256-cbc', 'doobert', 0, 16);
                update_option('doobert_api_token_value', $encrypted_value);
                echo 1;
            }
        } else {
            echo 0;
        }
    }
    die();
}
