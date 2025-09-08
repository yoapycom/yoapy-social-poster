<?php
/**
 * API Client for YoApy Social Poster
 *
 * @package YoApySocialPoster
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * YoApy API Client class
 *
 * @since 1.0.0
 */
class YSP_Client {
    private $base_url;
    private $key_id;
    private $secret_hex;
    private $account_default;
    private $per_network;
    public function __construct() {
        $opt = get_option('ysp_settings', array());
        $this->base_url = isset($opt['base_url']) ? rtrim($opt['base_url'], '/') : 'https://api.yoapy.com';
        $this->key_id   = $opt['key_id'] ?? '';
        $this->secret_hex = preg_replace('/[^0-9a-f]/i','',$opt['secret'] ?? '');
        $this->account_default = ltrim($opt['account'] ?? '', '@');
        $this->per_network = array(
            'facebook'  => ltrim($opt['account_facebook']  ?? '', '@'),
            'instagram' => ltrim($opt['account_instagram'] ?? '', '@'),
            'youtube'   => ltrim($opt['account_youtube']   ?? '', '@'),
            'tiktok'    => ltrim($opt['account_tiktok']    ?? '', '@'),
        );
        YSP_Logger::log('client_init', array('base_url'=>$this->base_url,'account_selector'=>$this->account_default));
    }
    public static function has_keys(){
        $opt = get_option('ysp_settings', array());
        return !empty($opt['key_id']) && !empty($opt['secret']);
    }
    private function account_for($network){ $h = trim($this->per_network[$network] ?? ''); return $h!=='' ? $h : $this->account_default; }
    private function hmac_headers($method,$path,$body_raw){
        $ts = (string) time(); $nonce = bin2hex(random_bytes(12));
        $hash = hash('sha256', $body_raw ?? '');
        $canonical = implode("\n", array($method,$path,$ts,$nonce,$hash));
        $key = pack("H*",$this->secret_hex); $sig = base64_encode(hash_hmac('sha256',$canonical,$key,true));
        YSP_Logger::log('hmac_build', array('method'=>$method,'path'=>$path,'ts'=>$ts,'nonce'=>$nonce,'body_sha256'=>$hash));
        return array('X-Key-Id'=>$this->key_id,'X-Timestamp'=>$ts,'X-Nonce'=>$nonce,'X-Signature'=>$sig);
    }
    public function ping(){
        $path='/v1/auth_ping'; $url=$this->base_url.$path;
        $res = wp_remote_get($url, array('headers'=>$this->hmac_headers('GET',$path,''),'timeout'=>20));
        if ( is_wp_error($res) ) return $res;
        return array('http_code'=>wp_remote_retrieve_response_code($res),'body'=>wp_remote_retrieve_body($res));
    }
    public function create_post_json($network,$type,$text,$image_url,$video_url,$article_url='',$scheduled_iso=''){
        $path='/v1/posts'; $url=$this->base_url.$path;
        $payload = array('account'=>$this->account_for($network),'account_ids'=>array($network),'post_type'=>$type);
        if ($text!=='') $payload['text']=$text;
        if ($image_url!=='' && in_array($type,array('image','story'))) $payload['media_urls']=array($image_url);
        if ($video_url!=='' && in_array($type,array('video','reels','live_schedule'))) $payload['media_urls']=array($video_url);
        if ($article_url!=='') $payload['article_url']=$article_url;
        if ($scheduled_iso!=='') $payload['scheduled_time']=$scheduled_iso;
        $raw = wp_json_encode($payload);
        $headers = array_merge(array('Content-Type'=>'application/json'), $this->hmac_headers('POST',$path,$raw));
        YSP_Logger::log('dispatch', array('payload'=>$payload));
        YSP_Logger::log('req_create_post_json', array('url'=>$url,'endpoint'=>$path,'headers'=>$headers,'body_raw'=>$raw,'json'=>$payload));
        $res = wp_remote_post($url, array('headers'=>$headers,'body'=>$raw,'timeout'=>60));
        if ( is_wp_error($res) ) return $res;
        $code = wp_remote_retrieve_response_code($res); $body = wp_remote_retrieve_body($res);
        YSP_Logger::log('res_create_post_json', array('http_code'=>$code,'response_body'=>$body));
        return array('code'=>$code,'body'=>json_decode($body,true),'raw'=>$body);
    }
    public function get_task_result($task_id){
        $path='/v1/get_task_result?task_id='.rawurlencode($task_id); $url=$this->base_url.$path;
        $headers=$this->hmac_headers('GET','/v1/get_task_result','');
        $res = wp_remote_get($url, array('headers'=>$headers,'timeout'=>20));
        if ( is_wp_error($res ) ) return $res;
        $code = wp_remote_retrieve_response_code($res); $body = wp_remote_retrieve_body($res);
        YSP_Logger::log('res_get_task_result', array('http_code'=>$code,'response_body'=>$body));
        return array('code'=>$code,'body'=>json_decode($body,true),'raw'=>$body);
    }

    /**
     * Legacy instance method for backward compatibility
     *
     * @deprecated 1.6.0 Use new YSP_Client() instead.
     * @return YSP_Client
     */
    public static function instance() {
        return new self();
    }
}
