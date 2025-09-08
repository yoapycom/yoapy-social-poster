<?php
/**
 * Cron functionality for YoApy Social Poster
 *
 * @package YoApySocialPoster
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cron jobs class for handling scheduled tasks
 *
 * @since 1.0.0
 */
class YSP_Cron {
    private static $instance=null;
    /**
     * Get class instance
     *
     * @return YSP_Cron
     * @since 1.0.0
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function __construct(){
        add_action('ysp_tick', array($this,'tick'));
        add_action('ysp_check_task_results', array($this,'check_task_results'));
        add_action('ysp_run_task', array($this,'run_task'));
        add_action('transition_post_status', array($this,'on_publish'), 10, 3);
        add_action('save_post', array($this,'maybe_dispatch_on_save'), 20, 3);
    }
    public function tick(){ 
        YSP_Logger::log('tick', array()); 
        $planner = YSP_Planner::get_instance();
        $planner->run_due_tasks(); 
        $planner->check_task_results();
        YSP_Logger::log('tick_end', array());
    }
    
    public function check_task_results() {
        YSP_Logger::log('check_task_results_cron_start', array());
        YSP_Planner::get_instance()->check_task_results();
        YSP_Logger::log('check_task_results_cron_end', array());
    }
    
    public function run_task($id){
        $planner = YSP_Planner::get_instance();
        $key = 'ysp_tasks'; $tasks = get_option($key, array());
        foreach($tasks as &$t){ if(($t['id']??0)==$id){ YSP_Logger::log('do_job_start', array('task_id'=>$id)); $planner->run_task($t); YSP_Logger::log('do_job_end', array('task_id'=>$id,'status'=>$t['status'])); break; } }
        update_option($key,$tasks,false);
    }
    public function on_publish($new,$old,$post){
        if($old==='publish' || $new!=='publish' || $post->post_type!=='post') return;
        YSP_Logger::log('on_publish', array('post_id'=>$post->ID,'old'=>$old,'new'=>$new));
        $this->dispatch_from_post($post->ID);
    }
    public function maybe_dispatch_on_save($post_id,$post=null,$update=null){
        if ( wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) ) return;
        $status = get_post_status($post_id);
        if ( $status!=='publish' ) return;
        // fire only once
        if(get_post_meta($post_id,'_ysp_sent_once',true)) return;
        YSP_Logger::log('on_save_publish', array('post_id'=>$post_id));
        $this->dispatch_from_post($post_id);
    }
    private function dispatch_from_post($post_id){
        $enabled = get_post_meta($post_id,'_ysp_enabled',true);
        if($enabled!=='1'){ YSP_Logger::log('skip_enabled_off', array('post_id'=>$post_id)); return; }
        if(get_post_meta($post_id,'_ysp_sent_once',true)){ YSP_Logger::log('skip_already_sent', array('post_id'=>$post_id)); return; }
        update_post_meta($post_id,'_ysp_sent_once','1');
        $type=get_post_meta($post_id,'_ysp_type',true) ?: 'image';
        $nets=(array)get_post_meta($post_id,'_ysp_networks',true);
        $text=get_post_meta($post_id,'_ysp_text',true);
        if(!$text){ $text = get_the_title($post_id); }
        $image=get_post_meta($post_id,'_ysp_image',true);
        if(!$image){ $thumb = get_the_post_thumbnail_url($post_id,'full'); if($thumb){ $image = $thumb; } }
        $video=get_post_meta($post_id,'_ysp_video',true);
        $article=get_post_meta($post_id,'_ysp_article',true);
        $when=intval(get_post_meta($post_id,'_ysp_when',true));
        
        // Get existing tasks to generate next ID
        $tasks = get_option('ysp_tasks', array());
        $next_id = 1;
        if (!empty($tasks)) {
            $ids = wp_list_pluck($tasks, 'id');
            $next_id = max($ids) + 1;
        }
        
        $task=array(
            'id' => $next_id,
            'title' => get_the_title($post_id),
            'networks' => $nets,
            'type' => $type,
            'text' => $text,
            'image_url' => $image,
            'video_url' => $video,
            'article_url' => $article,
            'when' => $when ?: null,
            'status' => (!empty($when) && $when > time()) ? 'scheduled' : 'pending',
            'api_task_ids' => array(),
            'results' => array()
        );
        
        // Process immediately if no schedule or past schedule
        if (!$when || $when <= time()) {
            YSP_Logger::log('do_job_start', array('task_id' => $next_id));
            $planner = YSP_Planner::get_instance();
            $planner->run_task($task);
            YSP_Logger::log('do_job_end', array('task_id' => $next_id, 'status' => $task['status']));
        }
        
        // Add task to planner
        $tasks[] = $task;
        update_option('ysp_tasks', $tasks, false);
    }

    /**
     * Legacy instance method for backward compatibility
     *
     * @deprecated 1.6.0 Use YSP_Cron::get_instance() instead.
     * @return YSP_Cron
     */
    public static function instance() {
        return self::get_instance();
    }
}
