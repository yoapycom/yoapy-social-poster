<?php
/**
 * Logger functionality for YoApy Social Poster
 *
 * @package YoApySocialPoster
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Logger class for tracking plugin events
 *
 * @since 1.0.0
 */
class YSP_Logger {
    public static function file_path() {
        return trailingslashit( YoApy_Social_Poster::get_upload_dir() ) . 'log.jsonl';
    }
    public static function log( $event, $data = array() ) {
        $line = array('t'=>gmdate('Y-m-d H:i:s'),'event'=>$event,'data'=>$data);
        @file_put_contents( self::file_path(), wp_json_encode($line)."
", FILE_APPEND );
    }
    public static function get_lines( $limit = 500 ) {
        $file = self::file_path(); if ( ! file_exists($file) ) return array();
        $lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        $out = array(); $start = max(0,count($lines)-$limit);
        for ($i=$start; $i<count($lines); $i++){ $row = json_decode($lines[$i], true); if ($row){ $row['_i']=$i; $out[]=$row; } }
        return $out;
    }
    public static function delete_line( $index ) {
        $file = self::file_path(); if ( ! file_exists($file) ) return;
        $lines = file( $file, FILE_IGNORE_NEW_LINES ); if ( isset($lines[$index]) ){ unset($lines[$index]); file_put_contents($file, implode("
", array_filter($lines,'strlen'))."
"); }
    }
    public static function clear() { wp_delete_file( self::file_path() ); }
}
