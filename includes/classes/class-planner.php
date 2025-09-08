<?php
/**
 * Planner functionality for YoApy Social Poster
 *
 * @package YoApySocialPoster
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YSP_Planner {
    private static $instance = null;
    private $option_key = 'ysp_tasks';

    public static function get_instance() {
        if ( null === self::$instance ) { self::$instance = new self(); }
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', array($this,'admin_menu'));
        add_action('wp_ajax_ysp_task_action', array($this, 'ajax_task_action'));
        add_action('wp_ajax_ysp_save_task_ajax', array($this, 'ajax_save_task'));
        add_action('wp_ajax_ysp_get_tasks_ajax', array($this, 'ajax_get_tasks'));
        add_action('wp_ajax_ysp_get_tasks', array($this, 'ajax_get_tasks')); // For admin.js compatibility
        add_action('admin_post_ysp_save_task', array($this, 'handle_save_task_fallback')); // Apenas como fallback
    }

    /**
     * Get all tasks sorted by ID (descending)
     *
     * @return array
     */
    public function get_tasks() {
        $tasks = get_option($this->option_key, array());
        $sorted_tasks = is_array($tasks) ? $tasks : array();
        // Ordena por ID decrescente para mostrar os mais novos primeiro
        usort($sorted_tasks, function($a, $b) {
            return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
        });
        return $sorted_tasks;
    }

    /**
     * Public method to get tasks (for external access)
     * @deprecated 1.6.1 Use get_tasks() directly
     */
    public function get_tasks_public() {
        return $this->get_tasks();
    }

    private function save_tasks($tasks) {
        update_option($this->option_key, array_values($tasks), false);
    }

    private function next_id($tasks){
        if (empty($tasks)) return 1;
        $ids = wp_list_pluck($tasks, 'id');
        return max($ids) + 1;
    }

    public function admin_menu(){
        add_menu_page('YoApy Planner','YoApy Planner','manage_options','ysp_planner',array($this,'render_planner'),'dashicons-share',26);
        add_submenu_page('ysp_planner','Settings','Settings','manage_options','ysp_settings',array($this,'render_settings'));
        add_submenu_page('ysp_planner','Logs','Logs','manage_options','ysp_logs',array($this,'render_logs'));
    }

    public function render_planner(){
        wp_enqueue_media();
        wp_enqueue_script('ysp-planner-js', YSP_PLUGIN_URL . 'admin/js/ysp-planner.js', array('jquery'), YSP_VERSION, true);

        wp_localize_script('ysp-planner-js', 'ysp_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('ysp_ajax_nonce'),
            'i18n'     => array(
                'saving'        => __('Saving...', 'yoapy-social-poster'),
                'taskSaved'     => __('Task saved successfully!', 'yoapy-social-poster'),
                'error'         => __('An error occurred.', 'yoapy-social-poster'),
                'deleteConfirm' => __('Are you sure you want to delete this task?', 'yoapy-social-poster'),
                'actionSuccess' => __('Action successful!', 'yoapy-social-poster'),
                'actionFailed'  => __('Action failed.', 'yoapy-social-poster'),
                'send'          => __('Post', 'yoapy-social-poster'),
                'delete'        => __('Delete', 'yoapy-social-poster'),
            ),
        ));

        $tasks = $this->get_tasks();
        $hasKeys = YSP_Client::has_keys();
        include YSP_PLUGIN_DIR.'admin/views/planner-main.php';
    }

    public function render_settings(){
        // Salvar configurações (com nonce)
        if ( isset($_POST['ysp_save_settings']) && check_admin_referer('ysp_save_settings','ysp_nonce_save') ) {
            $opt  = get_option('ysp_settings', array());
            $post = wp_unslash( $_POST ); // <- unslash UMA vez

            $form_type = isset($post['form_type']) ? sanitize_text_field( $post['form_type'] ) : '';
            if ( $form_type === 'credentials' ) {
                $opt['base_url'] = isset($post['base_url']) ? esc_url_raw( $post['base_url'] ) : 'https://api.yoapy.com';
                $opt['key_id']   = isset($post['key_id'])   ? sanitize_text_field( $post['key_id'] ) : '';
                // Secret: sanitiza como hex (sem tocar diretamente em $_POST)
                $secret_raw      = isset($post['secret']) ? (string) $post['secret'] : '';
                $opt['secret']   = preg_replace( '/[^0-9a-f]/i', '', $secret_raw );
            } elseif ( $form_type === 'accounts' ) {
                $acc_default = isset($post['account']) ? (string) $post['account'] : '';
                $opt['account']           = sanitize_text_field( ltrim( $acc_default, '@' ) );

                $acc_fb = isset($post['account_facebook']) ? (string) $post['account_facebook'] : '';
                $opt['account_facebook']  = sanitize_text_field( ltrim( $acc_fb, '@' ) );

                $acc_ig = isset($post['account_instagram']) ? (string) $post['account_instagram'] : '';
                $opt['account_instagram'] = sanitize_text_field( ltrim( $acc_ig, '@' ) );

                $acc_yt = isset($post['account_youtube']) ? (string) $post['account_youtube'] : '';
                $opt['account_youtube']   = sanitize_text_field( ltrim( $acc_yt, '@' ) );

                $acc_tt = isset($post['account_tiktok']) ? (string) $post['account_tiktok'] : '';
                $opt['account_tiktok']    = sanitize_text_field( ltrim( $acc_tt, '@' ) );
            }

            update_option( 'ysp_settings', $opt, false );
            add_settings_error( 'ysp_settings', 'saved', __( 'Settings saved.', 'yoapy-social-poster' ), 'updated' );
        }

        // Teste de conexão (com nonce)
        if ( isset($_POST['ysp_ping']) && check_admin_referer('ysp_ping','ysp_nonce_ping') ) {
            $client = new YSP_Client();
            $res    = $client->ping();
            $msg    = is_wp_error($res)
                ? __( 'Failed', 'yoapy-social-poster' ) . ' ❌ ' . $res->get_error_message()
                : ( ( (int) $res['http_code'] === 200 )
                    ? __( 'Connected', 'yoapy-social-poster' ) . ' ✅'
                    : __( 'Failed', 'yoapy-social-poster' ) . ' ❌ (HTTP ' . (int) $res['http_code'] . ')' );

            add_settings_error( 'ysp_settings', 'ping', $msg, ( (int) ( $res['http_code'] ?? 0 ) === 200 ) ? 'updated' : 'error' );
        }

        include YSP_PLUGIN_DIR.'admin/views/settings.php';
    }

    public function render_logs(){
        if ( isset($_POST['ysp_clear_logs']) && check_admin_referer('ysp_clear_logs') ) {
            YSP_Logger::clear();
        }
        if ( isset($_POST['ysp_delete_log']) && check_admin_referer('ysp_delete_log') ) {
            $post = wp_unslash( $_POST );
            $line = isset($post['line']) ? absint( $post['line'] ) : 0;
            YSP_Logger::delete_line( $line );
        }
        include YSP_PLUGIN_DIR.'admin/views/logs.php';
    }

    public function ajax_save_task() {
        if ( ! check_ajax_referer( 'ysp_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
        }

        $tasks = get_option( $this->option_key, array() ); // Pega na ordem original
        $task  = $this->build_task_from_post( $tasks, wp_unslash( $_POST ) ); // passa unslashed; sanitiza dentro

        // Set status based on schedule time
        if (!empty($task['when']) && $task['when'] > time()) {
            $task['status'] = 'scheduled';
        } else {
            // Auto-process immediate tasks (empty or past when values)
            $this->run_task($task);
        }

        $tasks[] = $task;

        update_option( $this->option_key, $tasks, false );
        wp_send_json_success( array( 'tasks' => $this->get_tasks() ) );
    }

    public function handle_save_task_fallback(){
        if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'ysp_save_task' ) ) {
            wp_die( 'forbidden' );
        }

        $tasks = get_option( $this->option_key, array() );
        $task  = $this->build_task_from_post( $tasks, wp_unslash( $_POST ) ); // passa unslashed; sanitiza dentro
        $tasks[] = $task;

        update_option( $this->option_key, $tasks, false );
        wp_redirect( admin_url( 'admin.php?page=ysp_planner&saved=1' ) );
        exit;
    }

    /**
     * Constrói a task a partir de dados de formulário (já passados por quem validou o nonce).
     * NÃO lê $_POST diretamente para evitar avisos de NonceVerification.
     *
     * @param array $tasks      Lista atual de tasks (para calcular o ID).
     * @param array $post_data  Dados do formulário (ex.: $_POST já unslashed).
     * @return array
     */
    private function build_task_from_post( $tasks, $post_data ){
        $id   = $this->next_id( $tasks );

        $title       = isset( $post_data['title'] )       ? sanitize_text_field( $post_data['title'] ) : '';
        $networks    = isset( $post_data['networks'] )    ? array_map( 'sanitize_text_field', (array) $post_data['networks'] ) : array();
        $type        = isset( $post_data['type'] )        ? sanitize_text_field( $post_data['type'] ) : 'image';
        $text        = isset( $post_data['text'] )        ? wp_kses_post( $post_data['text'] ) : '';
        $image_url   = isset( $post_data['image_url'] )   ? esc_url_raw( $post_data['image_url'] ) : '';
        $video_url   = isset( $post_data['video_url'] )   ? esc_url_raw( $post_data['video_url'] ) : '';
        $article_url = isset( $post_data['article_url'] ) ? esc_url_raw( $post_data['article_url'] ) : '';

        $when_input  = isset( $post_data['when'] ) ? sanitize_text_field( $post_data['when'] ) : '';
        $when_input  = trim( $when_input );
        $when_ts     = null;

        if ( '' !== $when_input ) {
            try {
                $tz  = wp_timezone();
                // datetime-local esperado "Y-m-d\TH:i"
                $dt  = new DateTimeImmutable( $when_input, $tz );
                $when_ts = $dt->getTimestamp();
            } catch ( Exception $e ) {
                $when_ts = null;
            }
        }

        return array(
            'id'           => $id,
            'title'        => $title,
            'networks'     => $networks,
            'type'         => $type,
            'text'         => $text,
            'image_url'    => $image_url,
            'video_url'    => $video_url,
            'article_url'  => $article_url,
            'when'         => $when_ts,
            'status'       => 'pending',
            'api_task_ids' => array(),
            'results'      => array(),
        );
    }

    public function ajax_task_action(){
        if ( ! check_ajax_referer( 'ysp_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
        }

        $post = wp_unslash( $_POST );
        $act  = isset($post['act']) ? sanitize_text_field( $post['act'] ) : '';
        $id   = isset($post['id'])  ? absint( $post['id'] ) : 0;

        $this->do_task_action( $act, $id );
        wp_send_json_success( array( 'tasks' => $this->get_tasks() ) );
    }

    private function do_task_action($act, $id){
        $tasks = get_option($this->option_key, array());
        $task_key = null;

        foreach ( $tasks as $key => $t ){
            if ( ( $t['id'] ?? 0 ) == $id ){
                $task_key = $key;
                break;
            }
        }

        if ( $task_key !== null ) {
            if ( $act === 'delete' ){
                unset( $tasks[ $task_key ] );
                update_option( $this->option_key, array_values( $tasks ), false );
            } elseif ( $act === 'send' ){
                $this->run_task( $tasks[ $task_key ] );
                // Save the updated task status back to the database
                update_option( $this->option_key, $tasks, false );
            }
        }
    }

    public function run_task(&$task){
        $client = new YSP_Client();

        if ( ! YSP_Client::has_keys() ) {
            $task['status'] = 'error';
            $task['results']['system'] = array( 'success' => false, 'message' => 'API keys are not configured.' );
            return;
        }

        // Check if this is a scheduled task
        if ( ! empty( $task['when'] ) && $task['when'] > time() ) {
            $task['status'] = 'scheduled';
            return;
        }

        $task['status'] = 'processing';
        $iso = ( ! empty( $task['when'] ) && $task['when'] > time() + 60 )
            ? gmdate( 'Y-m-d H:i:s', $task['when'] )
            : '';

        $all_successful = true;
        $has_async_tasks = false;

        foreach( $task['networks'] as $net ){
            $res = $client->create_post_json( $net, $task['type'], $task['text'], $task['image_url'], $task['video_url'], $task['article_url'], $iso );

            if ( is_wp_error( $res ) ){
                $task['status'] = 'error';
                $task['results'][ $net ] = array( 'success' => false, 'message' => $res->get_error_message() );
                $all_successful = false;
                YSP_Logger::log('task_network_error', array(
                    'task_id' => $task['id'],
                    'network' => $net,
                    'error' => $res->get_error_message()
                ));
            } else {
                $task['results'][ $net ] = $res['body'];
                if ( ! empty( $res['body']['task_id'] ) ){
                    $task['api_task_ids'][ $net ] = $res['body']['task_id'];
                    $has_async_tasks = true;
                    YSP_Logger::log('task_async_created', array(
                        'task_id' => $task['id'],
                        'network' => $net,
                        'api_task_id' => $res['body']['task_id']
                    ));
                } elseif ( isset( $res['body']['success'] ) && true === $res['body']['success'] ) {
                    // For immediate success, we can mark as complete
                    YSP_Logger::log('task_immediate_success', array(
                        'task_id' => $task['id'],
                        'network' => $net
                    ));
                } else {
                    $task['status'] = 'error';
                    $all_successful = false;
                    YSP_Logger::log('task_network_failed', array(
                        'task_id' => $task['id'],
                        'network' => $net,
                        'response' => $res['body']
                    ));
                }
            }
        }

        // If all networks were successful and we don't have async tasks, mark as complete
        if ($all_successful && !$has_async_tasks) {
            $task['status'] = 'complete';
            YSP_Logger::log('task_completed_immediately', array('task_id' => $task['id']));
        }
    }

    /**
     * Run tasks that are due (scheduled tasks that should be processed now)
     */
    public function run_due_tasks() {
        $tasks = get_option($this->option_key, array());
        $updated = false;

        foreach ($tasks as &$task) {
            // Check if task is scheduled and due time has passed
            if (isset($task['status']) && $task['status'] === 'scheduled' &&
                isset($task['when']) && $task['when'] <= time()) {
                // Run the task
                YSP_Logger::log('do_job_start', array('task_id' => $task['id']));
                $this->run_task($task);
                YSP_Logger::log('do_job_end', array('task_id' => $task['id'], 'status' => $task['status']));
                $updated = true;
            }
        }

        // Save updated tasks if any were processed
        if ($updated) {
            update_option($this->option_key, $tasks, false);
        }
    }

    /**
     * Check the status of tasks that are being processed
     */
    public function check_task_results() {
        $tasks = get_option($this->option_key, array());
        $updated = false;
        $client = new YSP_Client();
        
        YSP_Logger::log('check_task_results_start', array('total_tasks' => count($tasks)));

        foreach ($tasks as &$task) {
            // Check if task is processing and has API task IDs to check
            // Skip if task is already complete or has an error
            if (isset($task['status']) && $task['status'] === 'processing' && 
                !empty($task['api_task_ids']) && is_array($task['api_task_ids'])) {
                
                $has_errors = false;
                $task_updated = false;
                
                // Check if we have results for all networks
                $networks_with_results = 0;
                $total_networks = count($task['api_task_ids']);
                
                foreach ($task['api_task_ids'] as $network => $api_task_id) {
                    // Count networks that have results
                    if (!empty($task['results'][$network]['permalink']) || 
                        (isset($task['results'][$network]['success']) && $task['results'][$network]['success'] === false)) {
                        $networks_with_results++;
                        // Check if this network has an error
                        if (isset($task['results'][$network]['success']) && $task['results'][$network]['success'] === false) {
                            $has_errors = true;
                        }
                        continue;
                    }
                    
                    // Check the task result from the API
                    $result = $client->get_task_result($api_task_id);
                    
                    if (is_wp_error($result)) {
                        $task['results'][$network] = array(
                            'success' => false,
                            'message' => $result->get_error_message()
                        );
                        $has_errors = true;
                        $task_updated = true;
                        YSP_Logger::log('task_result_error', array(
                            'task_id' => $task['id'],
                            'network' => $network,
                            'error' => $result->get_error_message()
                        ));
                    } else {
                        $body = $result['body'];
                        // Handle the correct API response format
                        if (isset($body['status']) && $body['status'] === 'complete') {
                            // Extract network-specific data
                            $network_data = isset($body['data'][$network]) ? $body['data'][$network] : array();
                            $task['results'][$network] = array(
                                'success' => true,
                                'permalink' => $network_data['permalink'] ?? ''
                            );
                            $task_updated = true;
                            $networks_with_results++;
                            YSP_Logger::log('task_completed', array(
                                'task_id' => $task['id'],
                                'network' => $network,
                                'permalink' => $network_data['permalink'] ?? ''
                            ));
                        } elseif (isset($body['status']) && $body['status'] === 'failed') {
                            // Extract error message
                            $network_data = isset($body['data'][$network]) ? $body['data'][$network] : array();
                            $task['results'][$network] = array(
                                'success' => false,
                                'message' => $network_data['message'] ?? $body['message'] ?? 'Task failed'
                            );
                            $has_errors = true;
                            $task_updated = true;
                            $networks_with_results++;
                            YSP_Logger::log('task_failed', array(
                                'task_id' => $task['id'],
                                'network' => $network,
                                'error' => $network_data['message'] ?? $body['message'] ?? 'Task failed'
                            ));
                        } else {
                            // Task is still processing, don't increment networks_with_results
                        }
                    }
                }
                
                // Update task status based on results
                if ($task_updated) {
                    if (!$has_errors && $networks_with_results === $total_networks) {
                        $task['status'] = 'complete';
                        $updated = true;
                        YSP_Logger::log('task_all_complete', array('task_id' => $task['id']));
                    } elseif ($has_errors) {
                        $task['status'] = 'error';
                        $updated = true;
                        YSP_Logger::log('task_has_errors', array('task_id' => $task['id']));
                    }
                    // If not all complete and no errors, task remains in processing status
                }
            }
        }

        // Save updated tasks if any were processed
        if ($updated) {
            update_option($this->option_key, $tasks, false);
            YSP_Logger::log('check_task_results_updated', array('updated_tasks' => $updated));
        }
        
        YSP_Logger::log('check_task_results_end', array('updated' => $updated));
    }

    /**
     * Schedule a task for future execution
     *
     * @param int $task_id The task ID (not used in current implementation)
     * @param int $when Timestamp when the task should run
     */
    public function schedule_task($task_id, $when) {
        // In the current implementation, tasks are stored with their schedule time
        // and processed by the cron job via run_due_tasks()
        // This method exists for compatibility with the cron class
        return true;
    }

    public function ajax_get_tasks() {
        if ( ! check_ajax_referer( 'ysp_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
        }

        // Make sure we're checking for task results before returning tasks
        $this->check_task_results();

        $tasks = $this->get_tasks();
        // Debug: log the tasks to see what's being returned
        YSP_Logger::log('ajax_get_tasks_returning', array('tasks' => $tasks));

        wp_send_json_success( array( 'tasks' => $tasks ) );
    }
}
