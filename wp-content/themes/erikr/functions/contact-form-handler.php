<?php
/**
 * Contact Form AJAX Handler
 * 
 * Handles secure form submission with reCAPTCHA validation and email sending
 */

if (!defined('ABSPATH')) exit;

// Enqueue required scripts and localize AJAX URL
add_action('wp_enqueue_scripts', function() {
    wp_localize_script('main-script', 'er_contact_form', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('er_contact_form_nonce'),
    ]);
});

// Add inline script to make ajaxurl available globally for TypeScript
add_action('wp_head', function() {
    ?>
    <script>
        window.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        window.er_contact_form_nonce = '<?php echo wp_create_nonce('er_contact_form_nonce'); ?>';
    </script>
    <?php
});

// Handle contact form submission (for logged-in and logged-out users)
add_action('wp_ajax_er_contact_form_submit', 'er_handle_contact_form_submit');
add_action('wp_ajax_nopriv_er_contact_form_submit', 'er_handle_contact_form_submit');

function er_handle_contact_form_submit() {
    // Verify nonce
    if (!check_ajax_referer('er_contact_form_nonce', 'nonce', false)) {
        wp_send_json_error([
            'message' => 'Security verification failed. Please refresh the page and try again.',
        ]);
        return;
    }
    
    // Get and sanitize form data
    $widget_id = sanitize_text_field($_POST['widget_id'] ?? '');
    $reason = sanitize_text_field($_POST['reason'] ?? '');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $recaptcha_token = sanitize_text_field($_POST['recaptcha_token'] ?? '');
    
    // Validate required fields
    if (empty($widget_id) || empty($reason) || empty($name) || empty($email) || empty($message)) {
        wp_send_json_error([
            'message' => 'Please fill in all required fields.',
        ]);
        return;
    }
    
    // Validate email
    if (!is_email($email)) {
        wp_send_json_error([
            'message' => 'Please enter a valid email address.',
        ]);
        return;
    }
    
    // Get widget settings
    $widget_settings = er_get_contact_form_settings($widget_id);
    if (!$widget_settings) {
        wp_send_json_error([
            'message' => 'Form configuration not found.',
        ]);
        return;
    }
    
    // Verify reCAPTCHA if configured
    if (!empty($widget_settings['recaptcha_site_key']) && !empty($recaptcha_token)) {
        $recaptcha_valid = er_verify_recaptcha_enterprise(
            $recaptcha_token,
            $widget_settings['recaptcha_site_key'],
            $widget_settings['recaptcha_project_id'] ?? '',
            $widget_settings['recaptcha_api_key'] ?? ''
        );
        if (!$recaptcha_valid) {
            wp_send_json_error([
                'message' => 'reCAPTCHA verification failed. Please try again.',
            ]);
            return;
        }
    }
    
    // Rate limiting: Check if user has submitted recently (prevent spam)
    // Can be disabled by setting CONTACT_FORM_RATE_LIMIT=0 in environment
    $rate_limit_enabled = getenv('CONTACT_FORM_RATE_LIMIT') !== '0';
    
    if ($rate_limit_enabled) {
        $rate_limit_key = 'contact_form_submit_' . md5($email);
        $last_submit = get_transient($rate_limit_key);
        if ($last_submit) {
            wp_send_json_error([
                'message' => 'Please wait a few minutes before submitting another message.',
            ]);
            return;
        }
        
        // Set rate limit (5 minutes)
        set_transient($rate_limit_key, time(), 5 * MINUTE_IN_SECONDS);
    }
    
    // Send email
    $email_sent = er_send_contact_form_email([
        'to' => $widget_settings['recipient_email'] ?? get_option('admin_email'),
        'reason' => $reason,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message,
    ]);
    
    if ($email_sent) {
        // Log successful submission (optional)
        er_log_contact_form_submission([
            'reason' => $reason,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'ip' => er_get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        
        wp_send_json_success([
            'message' => 'Thank you for your message! I\'ll get back to you soon.',
        ]);
    } else {
        wp_send_json_error([
            'message' => 'Sorry, there was an error sending your message. Please try again or contact me directly.',
        ]);
    }
}

/**
 * Get contact form widget settings from Elementor
 */
function er_get_contact_form_settings($widget_id) {
    global $wpdb;
    
    // Query to find the post containing this widget
    $query = $wpdb->prepare("
        SELECT post_id, meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_elementor_data' 
        AND meta_value LIKE %s
    ", '%' . $wpdb->esc_like($widget_id) . '%');
    
    $results = $wpdb->get_results($query);
    
    foreach ($results as $result) {
        $elementor_data = json_decode($result->meta_value, true);
        $settings = er_find_widget_settings_recursive($elementor_data, $widget_id);
        if ($settings) {
            return $settings;
        }
    }
    
    return null;
}

/**
 * Recursively search for widget settings in Elementor data
 */
function er_find_widget_settings_recursive($data, $widget_id) {
    if (!is_array($data)) return null;
    
    foreach ($data as $element) {
        if (isset($element['id']) && $element['id'] === $widget_id) {
            return $element['settings'] ?? null;
        }
        
        if (isset($element['elements'])) {
            $found = er_find_widget_settings_recursive($element['elements'], $widget_id);
            if ($found) return $found;
        }
    }
    
    return null;
}

/**
 * Verify reCAPTCHA Enterprise token with Google Cloud API
 */
function er_verify_recaptcha_enterprise($token, $site_key, $project_id, $api_key) {
    if (empty($project_id) || empty($api_key)) {
        return false; // Enterprise API requires both project ID and API key
    }

    $url = sprintf(
        'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s',
        $project_id,
        $api_key
    );

    $body = [
        'event' => [
            'token' => $token,
            'expectedAction' => 'contact_form',
            'siteKey' => $site_key,
        ],
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($body),
        'timeout' => 10,
    ]);
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $response_body = json_decode(wp_remote_retrieve_body($response), true);
    
    // Check if the assessment was successful
    if (isset($response_body['tokenProperties']['valid']) && $response_body['tokenProperties']['valid'] === true) {
        // Verify the action matches
        if (($response_body['tokenProperties']['action'] ?? '') === 'contact_form') {
            // Check risk score (0.0 = very likely bot, 1.0 = very likely human)
            $score = $response_body['riskAnalysis']['score'] ?? 0;
            // Threshold of 0.5 is recommended, adjust based on your needs
            return $score >= 0.5;
        }
    }
    
    return false;
}

/**
 * Send contact form email
 */
function er_send_contact_form_email($data) {
    $to = $data['to'];
    $subject = sprintf('[Contact Form] %s - %s', ucfirst($data['reason']), $data['name']);
    
    // Email headers
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>',
    ];
    
    // Email body
    $message = er_get_contact_form_email_template($data);
    
    // Send email
    return wp_mail($to, $subject, $message, $headers);
}

/**
 * Get email template
 */
function er_get_contact_form_email_template($data) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #3b82f6; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
            .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #1f2937; }
            .value { margin-top: 5px; padding: 10px; background: white; border-radius: 3px; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2 style="margin: 0;">New Contact Form Submission</h2>
            </div>
            <div class="content">
                <div class="field">
                    <div class="label">Contact Reason:</div>
                    <div class="value"><?php echo esc_html(ucfirst($data['reason'])); ?></div>
                </div>
                
                <div class="field">
                    <div class="label">Name:</div>
                    <div class="value"><?php echo esc_html($data['name']); ?></div>
                </div>
                
                <div class="field">
                    <div class="label">Email:</div>
                    <div class="value">
                        <a href="mailto:<?php echo esc_attr($data['email']); ?>">
                            <?php echo esc_html($data['email']); ?>
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($data['phone'])): ?>
                <div class="field">
                    <div class="label">Phone:</div>
                    <div class="value">
                        <a href="tel:<?php echo esc_attr($data['phone']); ?>">
                            <?php echo esc_html($data['phone']); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="field">
                    <div class="label">Message:</div>
                    <div class="value"><?php echo nl2br(esc_html($data['message'])); ?></div>
                </div>
                
                <div class="footer">
                    Sent from <?php echo esc_html(get_bloginfo('name')); ?><br>
                    <?php echo esc_html(date('F j, Y \a\t g:i a')); ?>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Log contact form submission (optional - for analytics)
 */
function er_log_contact_form_submission($data) {
    // You can log to a custom table or use WordPress custom post type
    // For now, we'll just log to a simple option (last 50 submissions)
    $logs = get_option('er_contact_form_logs', []);
    
    array_unshift($logs, array_merge($data, [
        'timestamp' => current_time('mysql'),
    ]));
    
    // Keep only last 50 submissions
    $logs = array_slice($logs, 0, 50);
    
    update_option('er_contact_form_logs', $logs, false);
}

/**
 * Get client IP address
 */
function er_get_client_ip() {
    $ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
