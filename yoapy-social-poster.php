<?php
/**
 * Plugin Name: YoApy Social Poster
 * Plugin URI: https://yoapy.com
 * Description: Schedule and publish posts to social networks (Facebook, Instagram, YouTube, TikTok) via YoApy API, with visual planner, integrated metabox and detailed logs.
 * Version: 1.6.0
 * Author: YoApy Team
 * Author URI: https://yoapy.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: yoapy-social-poster
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 *
 * @package YoApySocialPoster
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constantss
if ( ! defined( 'YSP_VERSION' ) ) {
    define( 'YSP_VERSION', '1.6.0' );
}
if ( ! defined( 'YSP_PLUGIN_FILE' ) ) {
    define( 'YSP_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'YSP_PLUGIN_DIR' ) ) {
    define( 'YSP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'YSP_PLUGIN_URL' ) ) {
    define( 'YSP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'YSP_SLUG' ) ) {
    define( 'YSP_SLUG', 'yoapy-social-poster' );
}
if ( ! defined( 'YSP_TEXT_DOMAIN' ) ) {
    define( 'YSP_TEXT_DOMAIN', 'yoapy-social-poster' );
}

/**
 * Main YoApy Social Poster Class
 *
 * @final
 * @since 1.0.0
 */
final class YoApy_Social_Poster {

    /**
     * Plugin instance
     *
     * @var YoApy_Social_Poster
     * @since 1.0.0
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return YoApy_Social_Poster
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
        $this->define_hooks();
        $this->includes();
        $this->init();
    }

    /**
     * Define WordPress hooks
     *
     * @since 1.0.0
     */
    private function define_hooks() {
        register_activation_hook( YSP_PLUGIN_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( YSP_PLUGIN_FILE, array( $this, 'deactivate' ) );
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );
    }

    /**
     * Load plugin textdomain
     *
     * @since 1.0.0
     */
    public function load_textdomain() {
//        load_plugin_textdomain(
//            'yoapy-social-poster',
//            false,
//            dirname( plugin_basename( YSP_PLUGIN_FILE ) ) . '/languages/'
//        );
    }

    /**
     * Include required files
     *
     * @since 1.0.0
     */
    private function includes() {
        // Core classes
        require_once YSP_PLUGIN_DIR . 'includes/classes/class-logger.php';
        require_once YSP_PLUGIN_DIR . 'includes/classes/class-client.php';
        require_once YSP_PLUGIN_DIR . 'includes/classes/class-planner.php';
        require_once YSP_PLUGIN_DIR . 'includes/classes/class-cron.php';
        require_once YSP_PLUGIN_DIR . 'includes/classes/class-admin.php';
    }

    /**
     * Initialize plugin components
     *
     * @since 1.0.0
     */
    private function init() {
        // Initialize components
        if ( is_admin() ) {
            YSP_Admin::get_instance();
        }
        YSP_Planner::get_instance();
        YSP_Cron::get_instance();
    }

    /**
     * Plugin activation
     *
     * @since 1.0.0
     */
    public function activate() {
        // Clear any existing cron jobs
        $timestamp = wp_next_scheduled( 'ysp_tick' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'ysp_tick' );
        }

        $timestamp = wp_next_scheduled( 'ysp_check_task_results' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'ysp_check_task_results' );
        }

        // Schedule cron job every 30 seconds
        wp_schedule_event( time() + 30, 'ysp_30sec', 'ysp_tick' );

        // Schedule task result checking every 30 seconds for faster updates
        wp_schedule_event( time() + 30, 'ysp_30sec', 'ysp_check_task_results' );

        // Log activation
        YSP_Logger::log( 'plugin_activated', array( 'version' => YSP_VERSION ) );

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     *
     * @since 1.0.0
     */
    public function deactivate() {
        // Clear scheduled cron jobs
        $timestamp = wp_next_scheduled( 'ysp_tick' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'ysp_tick' );
        }

        // Clear task result checking cron job
        $timestamp = wp_next_scheduled( 'ysp_check_task_results' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'ysp_check_task_results' );
        }

        // Log deactivation
        YSP_Logger::log( 'plugin_deactivated', array( 'version' => YSP_VERSION ) );

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Actions to run when plugins are loaded
     *
     * @since 1.0.0
     */
    public function plugins_loaded() {
        YSP_Logger::log( 'plugins_loaded', array( 'version' => YSP_VERSION ) );
    }

    /**
     * Add custom cron schedules
     *
     * @param array $schedules Existing cron schedules.
     * @return array Modified cron schedules.
     * @since 1.0.0
     */
    public function add_cron_schedules( $schedules ) {
        $schedules['ysp_2min'] = array(
            'interval' => 120,
            'display'  => __( 'Every 2 minutes (YoApy)', 'yoapy-social-poster' )
        );
        $schedules['ysp_1min'] = array(
            'interval' => 60,
            'display'  => __( 'Every 1 minute (YoApy)', 'yoapy-social-poster' )
        );
        $schedules['ysp_30sec'] = array(
            'interval' => 30,
            'display'  => __( 'Every 30 seconds (YoApy)', 'yoapy-social-poster' )
        );
        return $schedules;
    }

    /**
     * Get upload directory for YoApy files
     *
     * @return string Upload directory path.
     * @since 1.0.0
     */
    public static function get_upload_dir() {
        $upload = wp_upload_dir();
        $dir = trailingslashit( $upload['basedir'] ) . 'yoapy-social-poster';

        if ( ! file_exists( $dir ) ) {
            wp_mkdir_p( $dir );
        }

        return $dir;
    }
}

/**
 * Initialize the plugin
 *
 * @since 1.0.0
 */
function yoapy_social_poster() {
    return YoApy_Social_Poster::get_instance();
}

// Initialize plugin
yoapy_social_poster();

/**
 * Legacy function for backward compatibility
 *
 * @deprecated 1.6.0 Use YoApy_Social_Poster::get_upload_dir() instead.
 * @return string Upload directory path.
 */
function ysp_upload_dir() {
    return YoApy_Social_Poster::get_upload_dir();
}


// CSS comum do admin (Tailwind compilado) — carrega nas páginas do plugin e no editor de post
add_action( 'admin_enqueue_scripts', 'ysp_enqueue_common_admin_assets' );

function ysp_enqueue_common_admin_assets( $hook_suffix ) {
    // Descobre a tela atual
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    // Telas do plugin (menu principal + submenus)
    $is_plugin_screen = false;
    if ( $screen ) {
        // Cobre variações de id/base (toplevel_page_*, *_page_*)
        $is_plugin_screen =
            ( false !== strpos( $screen->id,  'ysp_planner'  ) ) ||
            ( false !== strpos( $screen->id,  'ysp_settings' ) ) ||
            ( false !== strpos( $screen->id,  'ysp_logs'     ) ) ||
            ( false !== strpos( $screen->base, 'ysp_planner'  ) ) ||
            ( false !== strpos( $screen->base, 'ysp_settings' ) ) ||
            ( false !== strpos( $screen->base, 'ysp_logs'     ) );
    }

    // Telas do editor de post onde o metabox aparece
    $is_post_editor = false;
    if ( $screen ) {
        // Por padrão, aplicamos no post type "post". Amplie via filtro se usar CPT.
        $post_types = apply_filters( 'ysp_tailwind_post_types', array( 'post' ) );

        // No editor clássico e no Gutenberg, $screen->base costuma ser 'post'
        if ( 'post' === $screen->base && ! empty( $screen->post_type ) && in_array( $screen->post_type, $post_types, true ) ) {
            $is_post_editor = true;
        }
    }

    if ( ! $is_plugin_screen && ! $is_post_editor ) {
        return; // não é tela do plugin nem editor suportado -> não carrega nada
    }

    // Caminhos do CSS compilado
    $base_dir = defined('YSP_PLUGIN_DIR') ? YSP_PLUGIN_DIR : plugin_dir_path( __FILE__ );
    $base_url = defined('YSP_PLUGIN_URL') ? YSP_PLUGIN_URL : plugin_dir_url( __FILE__ );

    $css_path = $base_dir . 'assets/css/admin.css';
    $css_url  = $base_url . 'assets/css/admin.css';
    $ver      = file_exists( $css_path ) ? filemtime( $css_path ) : ( defined('YSP_VERSION') ? YSP_VERSION : false );

    wp_enqueue_style( 'ysp-admin', $css_url, array(), $ver );
}





