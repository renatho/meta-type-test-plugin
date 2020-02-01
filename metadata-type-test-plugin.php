<?php

/**
 * Plugin Name: Metadata type test
 * Description: This has only a test purpose.
 * Author: Renatho Rosa
 * Version: 0.1
 */

defined( 'ABSPATH' ) || exit;

class MetaDataTypeTestPlugin {
    const META_NAME = 'my-meta-type';

    private static $instance;

    public function __construct() {}

    public function init() {
        // Register meta.
        register_post_meta(
            'post',
            'my-meta-type',
            [
                'type'              => 'integer',
                'description'       => 'An array of integers.',
                'single'            => false,
                'show_in_rest'      => true,
                'sanitize_callback' => function( $value ) {
                    return intval( $value );
                },
            ]
        );

        // Register script.
        wp_register_script(
            'plugin-sidebar-js',
            plugins_url( 'build/index.js', __FILE__ ),
            array(
                'wp-plugins',
                'wp-edit-post',
                'wp-element',
                'wp-components'
            )
        );

        add_action( 'enqueue_block_editor_assets', [ $this,'sidebar_plugin_script_enqueue' ] );
        add_action( 'added_post_meta', [ $this, 'added_meta_hook' ], 10, 4 );
        add_action( 'deleted_post_meta', [ $this, 'deleted_meta_hook' ], 10, 4 );
    }

    public function sidebar_plugin_script_enqueue() {
        wp_enqueue_script( 'plugin-sidebar-js' );
    }

    // Save log on post comments.
    private function register_log_meta( $text, $post_id ) {
        $data = array(
            'comment_post_ID'      => $post_id,
            'comment_content'      => $text,
            'user_id'              => wp_get_current_user()->ID,
            'comment_approved'     => 1,
        );
        wp_insert_comment( $data );
    }
    
    // Hook added meta.
    public function added_meta_hook( $meta_ids, $post_id, $meta_key, $meta_value ) {
        if ( $meta_key !== self::META_NAME ) { return; }
        $this->register_log_meta( date_i18n('H:i:s') . ' - Added ' . $meta_value, $post_id );
    }
    
    // Hook removed meta.
    public function deleted_meta_hook( $meta_ids, $post_id, $meta_key, $meta_value ) {
        if ( $meta_key !== self::META_NAME ) { return; }
        $this->register_log_meta( date_i18n('H:i:s')  . ' - Removed ' . $meta_value, $post_id );
    }

    public static function instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

add_action( 'init', [ MetaDataTypeTestPlugin::instance(), 'init' ] );
