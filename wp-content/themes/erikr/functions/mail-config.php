<?php
/**
 * SMTP Mail Configuration for WordPress
 * 
 * Configure WordPress to send emails via SMTP instead of PHP mail()
 * This is required for Docker/local environments and more reliable email delivery
 */

if (!defined('ABSPATH')) exit;

/**
 * Configure PHPMailer to use SMTP
 */
add_action('phpmailer_init', 'er_configure_smtp');

function er_configure_smtp($phpmailer) {
    // Support both PHP constants (production) and environment variables (Docker)
    $smtp_host = defined('SMTP_HOST') ? SMTP_HOST : getenv('SMTP_HOST');
    
    // If no SMTP host configured, use default mail()
    if (empty($smtp_host)) {
        return;
    }
    
    // Use SMTP
    $phpmailer->isSMTP();
    $phpmailer->Host = $smtp_host;
    $phpmailer->Port = defined('SMTP_PORT') ? SMTP_PORT : (getenv('SMTP_PORT') ?: 1025);
    
    // Only use authentication if credentials are provided
    $smtp_user = defined('SMTP_USER') ? SMTP_USER : getenv('SMTP_USER');
    $smtp_pass = defined('SMTP_PASS') ? SMTP_PASS : getenv('SMTP_PASS');
    
    if (!empty($smtp_user) && !empty($smtp_pass)) {
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $smtp_user;
        $phpmailer->Password = $smtp_pass;
        $smtp_secure = defined('SMTP_SECURE') ? SMTP_SECURE : getenv('SMTP_SECURE');
        if ($smtp_secure) {
            $phpmailer->SMTPSecure = $smtp_secure;
        }
    } else {
        // MailHog doesn't need authentication
        $phpmailer->SMTPAuth = false;
    }
    
    $phpmailer->From = defined('SMTP_FROM') ? SMTP_FROM : (getenv('SMTP_FROM') ?: get_option('admin_email'));
    $phpmailer->FromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : (getenv('SMTP_FROM_NAME') ?: get_bloginfo('name'));
    
    // Timeout settings to prevent hanging
    $phpmailer->Timeout = 10;
    $phpmailer->SMTPKeepAlive = false;
    
    // Character encoding
    $phpmailer->SMTPDebug = 0;
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->Encoding = 'base64';
}

/**
 * Alternative: Use a local mail catcher for development
 * Uncomment this if you're using MailHog, MailCatcher, or similar
 */
/*
add_action('phpmailer_init', 'er_configure_mailhog');

function er_configure_mailhog($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'mailhog'; // or 'localhost' if not using Docker
    $phpmailer->Port = 1025;
    $phpmailer->SMTPAuth = false;
}
*/

/**
 * Log email sending errors
 */
add_action('wp_mail_failed', 'er_log_mail_errors');

function er_log_mail_errors($wp_error) {
    error_log('WordPress Mail Error: ' . $wp_error->get_error_message());
}