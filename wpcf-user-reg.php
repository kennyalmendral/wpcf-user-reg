<?php

/*
Plugin Name: WordPress ClickFunnels User Registration
Plugin URI: https://github.com/kennyalmendral/wpcf-user-reg
Description: Capture user data from a ClickFunnels form to be used for registration on a WordPress site.
Version: 1.0.0
Author: Kenny Almendral
Author URI: https://kennyalmendral.github.io/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

define('WPCFUR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPCFUR_PLUGIN_PATH', plugin_dir_path(__FILE__));

if (is_admin()) {

	function wpcfur_plugin_settings_link($links) { 
		$settings_link = '<a href="admin.php?page=wpcfur">Settings</a>'; 
		array_unshift($links, $settings_link); 

		return $links; 
	}

	$plugin = plugin_basename(__FILE__); 

	add_filter("plugin_action_links_$plugin", 'wpcfur_plugin_settings_link');

	function wpcfur_activate() {
		global $wpdb;

		$wpcfur_page_title = 'WordPress ClickFunnels User Registration';
		$wpcfur_page_name = 'wpcfur';
		$wpcfur_page_check = get_page_by_title($wpcfur_page_title);
		$wpcfur_page = array(
			'post_type' => 'page',
			'post_title' => $wpcfur_page_title,
			'post_name' => $wpcfur_page_name,
			'post_status' => 'publish',
			'post_author' => 1
		);

		$wpcfur_page_exists = $wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = '" . $wpcfur_page_name . "'", 'ARRAY_A');

		if ( ! isset($wpcfur_page_check->ID) && ! $wpcfur_page_exists) {
			$wpcfur_page_id = wp_insert_post($wpcfur_page);

			wp_update_post(array(
				'ID' => $wpcfur_page_id,
				'post_content' => '[wpcfur]'
			));

			update_option('wpcfur_url', get_permalink($wpcfur_page_id));
		}

		$info = parse_url(get_site_url());
		$site_host = $info['host'];
		
		update_option('wpcfur_name_key', "garlic:$site_host*>input.name");
		update_option('wpcfur_name_email', "garlic:$site_host*>input.email");
		update_option('wpcfur_default_password', 'wpcfur_pass');
		update_option('wpcfur_redirect_url', get_site_url());
		update_option('wpcfur_email_subject', "Here's your login credentials");

		$email_template = "<h3>Hello [name], here's your login credentials:</h3>";
		$email_template .= "<p><strong>Username:</strong> [email]<br><strong>Password:</strong> [password]</p>";

		update_option('wpcfur_email_template', $email_template);
	}

	register_activation_hook(__FILE__, 'wpcfur_activate');

	function wpcfur_admin_css_js($hook) {
		if ($hook != 'toplevel_page_wpcfur')
			return;

		wp_enqueue_style('wpcfur-trumbowyg-css', WPCFUR_PLUGIN_URL . 'vendors/trumbowyg/trumbowyg.min.css');
		wp_enqueue_style('wpcfur', WPCFUR_PLUGIN_URL . 'wpcf-user-reg.css');

		wp_enqueue_script('wpcfur-trumbowyg-js', WPCFUR_PLUGIN_URL . 'vendors/trumbowyg/trumbowyg.min.js', array('jquery'), null, true);
		wp_enqueue_script('wpcfur', WPCFUR_PLUGIN_URL . 'wpcf-user-reg.js', array('jquery'), null, true);
	}

	add_action('admin_enqueue_scripts', 'wpcfur_admin_css_js');

	function wpcfur_admin_menu() {
		add_menu_page(
			'WP ClickFunnels User Registration',
			'WP ClickFunnels User Registration',
			'manage_options',
			'wpcfur',
			'wpcfur_admin_menu_callback',
			'dashicons-admin-generic'
		);
	}

	function wpcfur_admin_menu_callback() {
		global $wpdb;

		$error = '';

		if (isset($_POST['submit'])) {
			if (wp_verify_nonce($_POST['nonce'], 'wpcfur_update_settings')) {
				if ( ! empty($_POST['wpcfur_redirect_url'])) {
					update_option('wpcfur_redirect_url', trim($_POST['wpcfur_redirect_url']));
				} else {
					$error = 'Redirect URL is required.';
				}

				if ( ! empty($_POST['wpcfur_default_password'])) {
					update_option('wpcfur_default_password', trim($_POST['wpcfur_default_password']));
				} else {
					$error = 'Default Password is required.';
				}

				if ( ! empty($_POST['wpcfur_email_subject'])) {
					update_option('wpcfur_email_subject', trim($_POST['wpcfur_email_subject']));
				} else {
					$error = 'Email Subject is required.';
				}

				if ( ! empty($_POST['wpcfur_email_template'])) {
					update_option('wpcfur_email_template', trim($_POST['wpcfur_email_template']));
				} else {
					$error = 'Email Template is required.';
				}
			}
		}

		$register_url = get_option('wpcfur_url');
		$redirect_url = get_option('wpcfur_redirect_url');
		$default_password = get_option('wpcfur_default_password');
		$email_subject = get_option('wpcfur_email_subject');
		$email_template = get_option('wpcfur_email_template');

		require_once WPCFUR_PLUGIN_PATH . 'admin/index.php';
	}

	add_action('admin_menu', 'wpcfur_admin_menu');

	function wpcfur_show_success_message() {
		echo '<div class="notice notice-success is-dismissible inline">';
			echo '<p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"></button></div>';
		echo '</div>';
	}

	function wpcfur_show_error_message($error) {
		echo '<div class="notice notice-error is-dismissible inline">';
			echo '<p><strong>' . $error . '</strong></p><button type="button" class="notice-dismiss"></button></div>';
		echo '</div>';
	}

	function wpcfur_show_warning_message($message) {
		echo '<div class="notice notice-warning is-dismissible inline">';
			echo '<p><strong>' . $message . '</strong></p><button type="button" class="notice-dismiss"></button></div>';
		echo '</div>';
	}

} else {

	function wpcfur_css_js() {
		wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_style('wpcfur', WPCFUR_PLUGIN_URL . 'wpcf-user-reg.css');
	}

	add_action('wp_enqueue_scripts', 'wpcfur_css_js');

	function wpcfur_shortcodes_init() {
		function wpcfur() {
			$info = parse_url(get_site_url());
			$site_host = $info['host'];
			$redirect_url = get_option('wpcfur_redirect_url');

			require_once WPCFUR_PLUGIN_PATH . 'index.php';
		}

		add_shortcode('wpcfur', 'wpcfur');
	}

	add_action('init', 'wpcfur_shortcodes_init');

}
