<?php
/**
 * Admin functionality for YoApy Social Poster
 *
 * @package YoApySocialPoster
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin functionality class
 *
 * @since 1.0.0
 */
class YSP_Admin {

    /**
     * Class instance
     *
     * @var YSP_Admin
     * @since 1.0.0
     */
    private static $instance = null;

    /**
     * Get class instance
     *
     * @return YSP_Admin
     * @since 1.0.0
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
        add_action( 'save_post', array( $this, 'save_post_meta' ) );
    }

    /**
     * Add meta box to post edit screen
     *
     * @since 1.0.0
     */
    public function add_metabox() {
        add_meta_box(
            'ysp_metabox',
            __( 'YoApy Social Poster', 'yoapy-social-poster' ),
            array( $this, 'render_metabox' ),
            'post',
            'side',
            'high'
        );
    }

    /**
     * Render the metabox content
     *
     * @param WP_Post $post Current post object.
     * @since 1.0.0
     */
    public function render_metabox( $post ) {
        // Security check
        if ( ! current_user_can( 'edit_post', $post->ID ) ) {
            return;
        }

        $has_keys = YSP_Client::has_keys();

        // Nonce for security
        wp_nonce_field( 'ysp_metabox', 'ysp_nonce' );

        // Get meta values
        $enabled = get_post_meta( $post->ID, '_ysp_enabled', true );
        $type = get_post_meta( $post->ID, '_ysp_type', true );
        $networks = (array) get_post_meta( $post->ID, '_ysp_networks', true );
        $text = get_post_meta( $post->ID, '_ysp_text', true );
        $image = get_post_meta( $post->ID, '_ysp_image', true );
        $video = get_post_meta( $post->ID, '_ysp_video', true );
        $article = get_post_meta( $post->ID, '_ysp_article', true );
        $when = get_post_meta( $post->ID, '_ysp_when', true );

        // Convert timestamp to local datetime for input
        $when_local = '';
        if ( $when ) {
            $when_local = get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $when ), 'Y-m-d\TH:i' );
        }

        // Enqueue required scripts and styles
        wp_enqueue_media();
        wp_enqueue_script(
            'ysp-admin',
            YSP_PLUGIN_URL . 'admin/js/ysp-admin.js',
            array( 'jquery' ),
            YSP_VERSION,
            true
        );
        wp_localize_script(
            'ysp-admin',
            'YSP',
            array(
                'ajax'    => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'ysp_metabox' ),
                'hasKeys' => $has_keys,
                'i18n'    => array(
                    'chooseMedia'   => __( 'Choose Media', 'yoapy-social-poster' ),
                    'title'         => __( 'Title', 'yoapy-social-poster' ),
                    'networks'      => __( 'Networks', 'yoapy-social-poster' ),
                    'type'          => __( 'Type', 'yoapy-social-poster' ),
                    'when'          => __( 'When', 'yoapy-social-poster' ),
                    'status'        => __( 'Status', 'yoapy-social-poster' ),
                    'actions'       => __( 'Actions', 'yoapy-social-poster' ),
                    'send'          => __( 'Send', 'yoapy-social-poster' ),
                    'refreshStatus' => __( 'Refresh status', 'yoapy-social-poster' ),
                    'delete'        => __( 'Delete', 'yoapy-social-poster' ),
                ),
            )
        );
        wp_enqueue_style(
            'ysp-admin',
            YSP_PLUGIN_URL . 'admin/css/ysp-admin.css',
            array(),
            YSP_VERSION
        );

        // Include metabox template
        include YSP_PLUGIN_DIR . 'admin/views/metabox.php';
    }

    /**
     * Save post meta data
     *
     * @param int $post_id Post ID.
     * @since 1.0.0
     */
    public function save_post_meta( $post_id ) {
        // Security checks
        if ( ! isset( $_POST['ysp_nonce'] ) ) {
            return;
        }

        // Unslash + sanitize nonce before verifying (fixes MissingUnslash/InputNotSanitized)
        $nonce = sanitize_text_field( wp_unslash( $_POST['ysp_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'ysp_metabox' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save meta fields (always unslash first, then sanitize)

        // Checkbox
        $enabled = isset( $_POST['ysp_enabled'] ) ? '1' : '';
        update_post_meta( $post_id, '_ysp_enabled', $enabled );

        // Type
        $type = isset( $_POST['ysp_type'] ) ? sanitize_text_field( wp_unslash( $_POST['ysp_type'] ) ) : 'image';
        update_post_meta( $post_id, '_ysp_type', $type );

        // Networks (array) — sanitize on the same line to satisfy the sniff
        $networks_sanitized = isset( $_POST['ysp_networks'] )
            ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['ysp_networks'] ) )
            : array();
        update_post_meta( $post_id, '_ysp_networks', $networks_sanitized );

        // Text (allow safe HTML)
        $text = isset( $_POST['ysp_text'] ) ? wp_kses_post( wp_unslash( $_POST['ysp_text'] ) ) : '';
        update_post_meta( $post_id, '_ysp_text', $text );

        // URLs
        $image = isset( $_POST['ysp_image'] ) ? esc_url_raw( wp_unslash( $_POST['ysp_image'] ) ) : '';
        update_post_meta( $post_id, '_ysp_image', $image );

        $video = isset( $_POST['ysp_video'] ) ? esc_url_raw( wp_unslash( $_POST['ysp_video'] ) ) : '';
        update_post_meta( $post_id, '_ysp_video', $video );

        $article = isset( $_POST['ysp_article'] ) ? esc_url_raw( wp_unslash( $_POST['ysp_article'] ) ) : '';
        update_post_meta( $post_id, '_ysp_article', $article );

        // Handle scheduling (datetime-local string)
        $when_input = isset( $_POST['ysp_when'] ) ? sanitize_text_field( wp_unslash( $_POST['ysp_when'] ) ) : '';
        $when_input = trim( $when_input );

        if ( '' !== $when_input ) {
            try {
                $timezone = wp_timezone();
                // Expecting format "Y-m-d\TH:i"
                $datetime = new DateTimeImmutable( $when_input, $timezone );
                update_post_meta( $post_id, '_ysp_when', $datetime->getTimestamp() );
            } catch ( Exception $e ) {
                // Invalid date format, ignore
                delete_post_meta( $post_id, '_ysp_when' );
            }
        } else {
            delete_post_meta( $post_id, '_ysp_when' );
        }
    }

    /**
     * Legacy instance method for backward compatibility
     *
     * @deprecated 1.6.0 Use YSP_Admin::get_instance() instead.
     * @return YSP_Admin
     */
    public static function instance() {
        return self::get_instance();
    }
}
