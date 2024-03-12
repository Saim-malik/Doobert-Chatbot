<?php

function doober_create_new_token($site_url) {
    $doobert_api_token_value = get_option('doobert_api_token_value');
    if (!empty($doobert_api_token_value)) {

        $doobert_api_token_value = openssl_decrypt($doobert_api_token_value, 'aes-256-cbc', 'doobert', 0, 16);
        $request_args = array(
            'body' => array(
                'website' => $site_url,
            ),
            'headers' => array(
                'Authorization' => 'Bearer ' . $doobert_api_token_value,
            ),
        );

        $response = wp_remote_post('https://app.doobert.com/api/v1/plugin/get-access-token', $request_args);

        if (is_wp_error($response)) {
            
        } else {
            $result_array = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($result_array['status']) && $result_array['status'] == 1) {
                $access_token_details = $result_array['access_token_details'];
                $expires_in = $access_token_details['expires_in'];
                $access_token = $access_token_details['access_token'];
                $currentTimestamp = time();
                $newTimestamp = $currentTimestamp + $expires_in;
                update_option('_doobert_token_time_stamp', $newTimestamp);
                update_option('_doobert_token_value', $access_token);
            }
        }
    }
}

add_action('wp_ajax_openai_request', 'openai_request');
add_action('wp_ajax_nopriv_openai_request', 'openai_request');

function openai_request() {
    check_ajax_referer('verify_doober_token_nonce', 'security');
    $payload = sanitize_text_field($_POST['payload']);
    $site_url = site_url();
    $dbtr_time = get_option('_doobert_token_time_stamp');
    if (time() >= $dbtr_time) {
        doober_create_new_token($site_url);
    }
    $response = call_azure_chat_api($payload, $site_url);

    $arrResult = json_decode($response, true);
    if (isset($arrResult['statusCode']) && $arrResult['statusCode'] == 401) {
        doober_create_new_token($site_url);
        $response = call_azure_chat_api($payload, $site_url);
        $arrResult = json_decode($response, true);
    }

    $resultMessage = $arrResult["choices"][0]["message"]["content"];

    $status = 1;
    if (empty($resultMessage) || strpos($resultMessage, 'requested information is not') > 1) {
        $resultMessage = 'I’m sorry but I only have information from ' . get_bloginfo('name') . ' so I am unable to answer your question. Would you like me to get you to a human that can help?';
        $status = 2;
    } else {
        $pattern = "/\[doc(\d+)\]/";
        if (preg_match_all($pattern, $resultMessage, $matches)) {
            $result = array_combine($matches[0], $matches[1]);
            if (!empty($result)) {
                $context = $arrResult["choices"][0]["message"]["context"]["messages"][0]["content"];
                if (!empty($context)) {
                    $context = json_decode($context, true);
                    if (!empty($context) && isset($context['citations'])) {
                        $doc_array = $context['citations'];
                        foreach ($result as $key => $id) {
                            $index = $id - 1;
                            $url = isset($doc_array[$index]['url']) ? $doc_array[$index]['url'] : '';
                            $title = isset($doc_array[$index]['title']) ? $doc_array[$index]['title'] : '';
                            if (!empty($url)) {
                                $link = '<a target="_blank" class="document_link" href="' . $url . '">' . $title . ', </a>';
                                $resultMessage = str_replace($key, $link, $resultMessage);
                            }
                        }
                    }
                }
            }
        }
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'doobert_chatbot_history';
    $datatime = gmdate('Y-m-d H:i:s');
    $data = array(
        'question' => $payload,
        'answer' => $resultMessage,
        'datatime' => $datatime,
    );

    $format = array(
        '%s',
        '%s',
        '%s',
    );
    $wpdb->insert($table_name, $data, $format);

    echo wp_json_encode(array('status' => $status, 'message' => $resultMessage));
    die();
}

function call_azure_chat_api($payload, $site_url) {
    $token = get_option('_doobert_token_value');
    $site_url = site_url();
    $payload = $payload;
    $system_instructions = 'You are a customer service representative for ' . get_bloginfo('name') . '. Use English language only. Use English alphabet whenever possible. Your tone should be friendly and helpful. You should summarize as though you are speaking to a 12 year old person. You should always thank them for ' . get_bloginfo('name') . '. You should anticipate their next question and try and answer it at the same time. Your responses should be no more than 2 sentences in length. You should always ask them if you answered their question and if not, try again. Do not mention anywhere in your response about retrieved data or any documents, just say it is your knowledge. also please do not return words that are meaningless in term of reading. Do not mention your source document in response. Do not justify your answers. Do not give information not mentioned in the CONTEXT INFORMATION. if information not found in anywhere in CONTEXT INFORMATION reply message should include information not available. Always respond in English language please.';

    $request_body = array(
        'dataSources' => array(
            array(
                'type' => 'AzureCognitiveSearch',
                'parameters' => array(
                    'endpoint' => 'https://chatbotsearch-service.search.windows.net',
                    'indexName' => 'websiteindex',
                    'filter' => "website eq '$site_url'",
                ),
            ),
        ),
        'messages' => array(
            array('role' => 'system', 'content' => $system_instructions),
            array('role' => 'user', 'content' => $payload),
        ),
        'temperature' => 0.5,
        'top_p' => 0.5,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
        'max_tokens' => 0,
    );

    $request_args = array(
        'body' => wp_json_encode($request_body),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ),
    );

    $response = wp_remote_post(
            'https://doobertchatinstance.openai.azure.com/openai/deployments/gpt-35-turbo/extensions/chat/completions?api-version=2023-08-01-preview',
            $request_args
    );
    $response_body = wp_remote_retrieve_body($response);
    return $response_body;
}
