<?php

/**
 * WP Comment Policy Checkbox bootstrap file
 *
 * @wordpress-plugin
 * Plugin Name:       WP Comment Policy Checkbox
 * Plugin URI:        https://github.com/fcojgodoy/wp-comment-policy-checkbox
 * Description:       Add a checkbox and custom text to the comment forms so that the user can be informed and give consent to the web's privacy policy. And save this consent in the database.
 * Version:           0.4.1
 * Author:            Fco. J. Godoy
 * Author URI:        franciscogodoy.es
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-comment-policy-checkbox
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Admin
 *
 */
if ( is_admin() ) {
    // We are in admin mode
    require_once ( plugin_dir_path ( __FILE__ ) . 'includes/wp-comment-policy-checkbox-admin.php' );
}


/**
 * Add settings link in the Plugins list table
 * 
 */
function wpcpc_add_settings_link( $links ) {

	$url = esc_url( get_admin_url(null, 'options-discussion.php#wpcpc-section'));

	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';

	array_push(
		$links,
		$settings_link
	);
	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpcpc_add_settings_link' );


/**
 * Personal data exporter and eraser
 *
 */
    require_once ( plugin_dir_path ( __FILE__ ) . 'includes/wp-comment-policy-checkbox-data-exporter.php' );
    require_once ( plugin_dir_path ( __FILE__ ) . 'includes/wp-comment-policy-checkbox-data-eraser.php' );


/**
 * Load the plugin text domain for translation.
 *
 */
function wpcpc_load_plugin_textdomain() {

	load_plugin_textdomain(
		'wp-comment-policy-checkbox',
		false,
		basename( dirname( __FILE__ ) ) . '/languages/'
	);

}

add_action( 'plugins_loaded', 'wpcpc_load_plugin_textdomain' );


/**
 * Create custom fields
 *
 */
function wpcpc_custom_fields( $fields ) {

	if ( get_option( 'wpcpc_external_policy_page' ) ) {
		$url = get_option( 'wpcpc_external_policy_page' );
	} else {
		$url = get_permalink( get_option( 'wpcpc_policy_page_id' ) );
	}

	if ( get_option( 'wpcpc_policy_page_link_types' ) ) {
		$policy_page_link_types = get_option( 'wpcpc_policy_page_link_types' );
	} else {
		$policy_page_link_types = '';
	}

	if (( 1 === (int) get_option( 'wpcpc_policy_page_link_open_same_tab' ) )) {
		$policy_page_link_opening = '_self';
	} else {
		$policy_page_link_opening = '_blank';
	}

	$privacy_policy = __( 'Privacy Policy', 'wp-comment-policy-checkbox' );

	$link =
		'<a
			href="' . esc_url( $url ) . '"
			target="' . esc_attr($policy_page_link_opening) . '"
			rel="' . esc_attr( $policy_page_link_types ) . '"
			class="comment-form-policy__see-more-link">' . esc_html( $privacy_policy ) . '
		</a>';

    $fields[ 'top_copy' ] =
        '<div role="note" class="comment-form-policy-top-copy" style="font-size:80%">'.
            wpautop( get_option( 'wpcpc_policy_top_copy' ) ) .
        '</div>';

    $fields[ 'policy' ] =
        '<p class="comment-form-policy">
            <label for="policy" style="display:block !important">
                <input id="policy" name="policy" value="policy-key" class="comment-form-policy__input" type="checkbox" style="width:auto; margin-right:7px;" aria-required="true">' .
					sprintf(
						/* translators: %s: Privacy Policy page link */
						__( 'I have read and accepted the %s', 'wp-comment-policy-checkbox' ),
						$link
					) .
                '<span class="comment-form-policy__required required"> *</span>
            </label>
        </p>';

    return $fields;
}

add_filter('comment_form_fields', 'wpcpc_custom_fields');


/**
 * Add comment meta for each comment.
 *
 * Previously check if the comment comes from the post comments, 
 * and not from another source like Webmention.
 * And check if a email has been sent.
 */
function wpcpc_add_custom_comment_field( $comment_ID, $comment_approved, $commentdata ) {
	if (
		(isset( $commentdata['comment_type'] ) && $commentdata['comment_type'] === 'comment')
		&& array_key_exists('email', $_POST)
	) {
		add_comment_meta( $comment_ID, 'wpcpc_private_policy_accepted', $_POST[ 'email' ], true );
	}
}

add_action( 'comment_post', 'wpcpc_add_custom_comment_field', 10, 3 );


/**
 * Add the filter to check whether the comment meta data has been filled
 *
 */
function wpcpc_verify_policy_check( $commentdata ) {
    if ( ! isset( $_POST['policy'] ) && ! is_user_logged_in() && $commentdata['comment_type'] != 'webmention' )

    	wp_die( '<strong>' . __( 'WARNING: ', 'wp-comment-policy-checkbox' ) . '</strong>' . __( 'you must accept the Privacy Policy.', 'wp-comment-policy-checkbox' ) . '<p><a href="javascript:history.back()">' . __( '&laquo; Back' ) . '</a></p>');

    return $commentdata;
}

add_filter( 'preprocess_comment', 'wpcpc_verify_policy_check' );
