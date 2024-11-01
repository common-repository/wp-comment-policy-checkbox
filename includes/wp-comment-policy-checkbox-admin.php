<?php

/**
 * Add section
 *
 */
function wpcpc_add_settings_section()
{
    add_settings_section(
        'wpcpc-section',
        __( 'Comments Policy Checkbox', 'wp-comment-policy-checkbox'),
        'wpcpc_settings_section_description',
        'discussion'
    );
}
    
add_action( 'admin_init', 'wpcpc_add_settings_section' );


/**
 * Register settings
 *
 */
function wpcpc_register_settings() {

    register_setting (
        'discussion',
        'wpcpc_policy_page_id'
    );
    
    register_setting (
        'discussion',
        'wpcpc_external_policy_page'
    );
    
    register_setting (
        'discussion',
        'wpcpc_policy_top_copy'
    );

    register_setting (
        'discussion',
        'wpcpc_policy_page_link_open_same_tab'
    );
    
    register_setting (
        'discussion',
        'wpcpc_policy_page_link_types'
    );

};

add_action( 'admin_init', 'wpcpc_register_settings' );


/**
 * Creating setting field
 *
 */
function wpcpc_add_setting_fields () {

    add_settings_field (
        'wpcpc_settings_policy_page_id',
        __( 'Privacy policy internal page', 'wp-comment-policy-checkbox'),
        'wpcpc_settings_policy_page_id_callback',
        'discussion',
        'wpcpc-section'
    );

    add_settings_field (
        'wpcpc_external_policy_page',
        __( 'Privacy policy custom page', 'wp-comment-policy-checkbox'),
        'wpcpc_settings_external_policy_page_callback',
        'discussion',
        'wpcpc-section'
    );

    add_settings_field (
        'wpcpc_settings_policy_top_copy',
        __( 'Privacy policy basic information', 'wp-comment-policy-checkbox'),
        'wpcpc_settings_top_copy_callback',
        'discussion',
        'wpcpc-section'
    );

    add_settings_field (
        'wpcpc_policy_page_link_open_same_tab',
        __( 'Privacy policy page link opening', 'wp-comment-policy-checkbox'),
        'wpcpc_settings_policy_page_link_same_tab_callback',
        'discussion',
        'wpcpc-section'
    );

    add_settings_field (
        'wpcpc_policy_page_link_types',
        __( 'Privacy policy page link types', 'wp-comment-policy-checkbox'),
        'wpcpc_settings_policy_page_link_types_callback',
        'discussion',
        'wpcpc-section'
    );

}

add_action('admin_init','wpcpc_add_setting_fields');


/*
 * Setting Section Description
 * 
 */
function wpcpc_settings_section_description( $arg ) {
	echo '<span id="' . $arg['id'] . '"style="visibility:hidden"></span>';
	echo '<p>' . __( 'Here you can configure the options for the comment policy checkbox.', 'wp-comment-policy-checkbox') . '</p>';
	echo '<p>' . __( 'The check box for reading and accepting the privacy policy in the comment forms is active. To deactivate it you must deactivate the WP Comment Policy Check plugin.', 'wp-comment-policy-checkbox') . '</p>';
}


/**
 * Rendering option page
 *
 */
function wpcpc_settings_policy_page_id_callback() { ?>

    <div class='wrap'>

        <p>
            <?php esc_html_e( 'The page with the privacy policy to which you will link the text of the checkbox will be: ', 'wp-comment-policy-checkbox') ?>

            <select name='wpcpc_policy_page_id'>

                <?php $empty_option_value = __( '-- none --', 'wp-comment-policy-checkbox' ); ?>

                <option> <?php echo esc_attr( $empty_option_value ) ?> </option>

                <?php $pages = get_pages( array( 'lang' => '' ) ); ?>
                <?php if ( $pages ) { ?>
                    <?php foreach ( $pages as $page ) { ?>
                        <option
                            value='<?php echo esc_attr( $page -> ID ); ?>'
                            <?php selected( get_option( 'wpcpc_policy_page_id' ), $page -> ID ); ?>>
                            <?php echo esc_attr( $page -> post_title ); ?>
                        </option>
                    <?php } ?>
                <?php } ?>

            </select>
        </p>

    </div>

<?php }


function wpcpc_settings_external_policy_page_callback() { ?>

    <div class='wrap'>

        <p>

            <?php esc_html_e( 'This external URL will replace the previous option.', 'wp-comment-policy-checkbox') ?>
            <br>
            <input type="url" name="wpcpc_external_policy_page" value="<?php echo get_option( 'wpcpc_external_policy_page' ) ?>" style="width:100%">

        </p>

    </div>

<?php }


function wpcpc_settings_top_copy_callback() { ?>

    <div class='wrap'>
        
        <p>
            <?php esc_html_e( 'Write down the basic information about data protection.', 'wp-comment-policy-checkbox') ?>
            <br>
            <?php esc_html_e( 'This information will appear above the check box of the privacy policy.', 'wp-comment-policy-checkbox' ) ?>
        </p>

        <?php 
        $content   = get_option( 'wpcpc_policy_top_copy' );
        $editor_id = 'wpcpcpolicytopcopy';
        $settings = array(
            'textarea_name' => 'wpcpc_policy_top_copy',
            'media_buttons' => false,
            'teeny' => true,
            'textarea_rows' => 10
        ); 
        wp_editor( $content, $editor_id, $settings );
        ?>

    </div>

<?php }


function wpcpc_settings_policy_page_link_same_tab_callback() { ?>

    <label for="wpcpc_policy_page_link_open_same_tab">
        <input
            type="checkbox"
            id="wpcpc_policy_page_link_open_same_tab"
            name="wpcpc_policy_page_link_open_same_tab"
            value="1"
            <?php checked(true, get_option( 'wpcpc_policy_page_link_open_same_tab' )) ?>
        >
        <?php esc_html_e('Open policy page in same tab', 'wp-comment-policy-checkbox') ?>
    </label>

<?php }

function wpcpc_settings_policy_page_link_types_callback() { ?>

<div class='wrap'>

    <p>

        <?php
            $link_types_link = '<a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Link_types">' . __( 'link types', 'wp-comment-policy-checkbox' ) . '</a>';
            $link_types_samples = '<kbd>nofollow</kbd>, <kbd>external</kbd>';
            $nofollow_code = '<code>rel="nofollow"</code>';
            printf(
                /* translators: 1: 'Link types' link text 2: 'nofollow' code 3: 'Link types' samples */
                __( 'This is for the HTML %1$s attribute of the policy page link. Use space separated words. This can be useful for use %2$s for SEO reasons. Here are some options you could use: %3$s', 'wp-comment-policy-checkbox' ),
                $link_types_link,
                $nofollow_code,
                $link_types_samples
            );
        ?>
        <br>
        <input type="text" name="wpcpc_policy_page_link_types" value="<?php echo get_option( 'wpcpc_policy_page_link_types' ) ?>" style="width:100%">

    </p>

</div>

<?php }
