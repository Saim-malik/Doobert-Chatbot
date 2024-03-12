<?php

/**
 * Plugin Name:       Doobert Chatbot
 * Plugin URI:        https://doobert.com
 * Description:       Doobert’s Chatbot answers questions based on YOUR site’s pages and posts.
 * Version:           1.0
 * Author:            doobert
 * Author URI:        https://doobert.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       doobert-chatbot
 * Domain Path:       /languages
 *
 * @package           Doobert_Chatbot
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('DOBCHAPI_URL')) {
    define('DOBCHAPI_URL', plugin_dir_url(__FILE__));
}

if (!defined('DOBCHAPI_PLUGIN_DIR')) {
    define('DOBCHAPI_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

/**
 * Load plugin text domain for translation
 */
add_action('init', function () {
    load_plugin_textdomain('doobert-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/**
 * Include necessary files
 */
include_once DOBCHAPI_PLUGIN_DIR . 'admin/doobert-chatbot-with-api-admin.php';
include_once DOBCHAPI_PLUGIN_DIR . 'front/doobert-chatbot-with-api-front.php';
include_once DOBCHAPI_PLUGIN_DIR . 'doobert-chatbot-api-handle.php';

/**
 * Plugin activation function
 */
function doobertbottest_on_activation() {
    $settings = get_option('doobert-chatbot-custom-settings');
    if (empty($settings)) {
        $array = array(
            'enable_doobert' => 'enable',
            'display_pages' => '',
            'def_msg_to_show' => 'Hello, how can I help you today?',
            'def_options_to_show' => 'Foster a Dog;Foster a Cat;Donate;Volunteer',
            'contact_us_page' => ''
        );
        update_option('doobert-chatbot-custom-settings', $array);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'doobert_chatbot_history';

    $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
    if (!$table_exists) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        question LONGTEXT,
        answer LONGTEXT,
        datatime VARCHAR(50),
        PRIMARY KEY (id)
    )";
        dbDelta($sql);
    }

    if (!wp_next_scheduled('doobertbottest_daily_cron_hook')) {
        wp_schedule_event(time(), 'daily', 'doobertbottest_daily_cron_hook');
    }
}

/**
 * Plugin daily cron function
 */
function doobertbottest_daily_cron_function() {
    global $wpdb;
    $doobert_api_token_value = get_option('doobert_api_token_value');
    if (!empty($doobert_api_token_value)) {
        $doobert_api_token_value = openssl_decrypt($doobert_api_token_value, 'aes-256-cbc', 'doobert', 0, 16);
    }
    if (!empty($doobert_api_token_value)) {

        $table_name = $wpdb->prefix . 'doobert_chatbot_history';
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}doobert_chatbot_history");
        if (!empty($results)) {
            $data = array();
            foreach ($results as $key => $res) {
                $question = $res->question;
                $answer = $res->answer;
                $datetime = $res->datatime;
                $data["data[$key][user_question]"] = $question;
                $data["data[$key][chat_response]"] = $answer;
                $data["data[$key][date_time]"] = $datetime;
            }
            $data['website'] = site_url();

            $request_args = array(
                'body' => $data,
                'headers' => array(
                    'Authorization' => 'Bearer ' . $doobert_api_token_value,
                ),
            );
            $response = wp_remote_post('https://app.doobert.com/api/v1/plugin/log-chat', $request_args);
            if (is_wp_error($response)) {
                
            } else {
                $result = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($result['status']) && $result['status'] == 1) {
                    $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}doobert_chatbot_history");
                }
            }
        }
    }
}

/**
 * Register plugin activation hook
 */
register_activation_hook(__FILE__, 'doobertbottest_on_activation');

/**
 * Add settings link on plugin activation
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'apd_settings_link_doobert');

function apd_settings_link_doobert(array $links) {
    $url = get_admin_url() . "?page=doobert-chatbot-settings";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'doobert-chatbot') . '</a>';
    $links[] = $settings_link;
    $link2 = '<a target="_blank" href="https://app.doobert.com/upgrade-chatbot">Upgrade to Premium</a>';
    $links[] = $link2;
    return $links;
}

/**
 * Plugin daily cron hook
 */
add_action('doobertbottest_daily_cron_hook', 'doobertbottest_daily_cron_function');

