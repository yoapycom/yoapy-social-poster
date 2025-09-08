<?php
/**
 * Metabox template for YoApy Social Poster
 *
 * @package YoApySocialPoster
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php if ( ! $has_keys ) : ?>
    <div style="margin-bottom:8px">
        🔑 <strong><?php esc_html_e( 'Need API Keys?', 'yoapy-social-poster' ); ?></strong> 
        <?php esc_html_e( 'Create them for free at', 'yoapy-social-poster' ); ?> 
        <a href="https://yoapy.com" target="_blank" rel="noopener">yoapy.com</a>.
    </div>
<?php endif; ?>

<?php if ( ! $has_keys ) : ?>
    <div class="notice notice-warning" style="background: #fffbeb !important; border-left: 4px solid #f59e0b !important; color: #92400e !important; padding: 12px 14px !important; border-radius: 8px !important; margin: 12px 0 !important;">
        <p style="margin: 0 !important; color: #92400e !important; font-weight: 500;">
            <?php esc_html_e( 'Configure your YoApy API keys in Settings before posting.', 'yoapy-social-poster' ); ?>
        </p>
    </div>
<?php endif; ?>

<p>
    <label>
        <input type="checkbox" name="ysp_enabled" value="1" <?php checked( '1', $enabled ); ?>>
        <?php esc_html_e( 'Auto-post when published', 'yoapy-social-poster' ); ?>
    </label>
</p>

<p>
    <label><?php esc_html_e( 'Type', 'yoapy-social-poster' ); ?></label>
    <select name="ysp_type" class="widefat">
        <option value="image" <?php selected( $type, 'image' ); ?>>
            🖼️ <?php esc_html_e( 'Image Post', 'yoapy-social-poster' ); ?>
        </option>
        <option value="video" <?php selected( $type, 'video' ); ?>>
            🎬 <?php esc_html_e( 'Video/YouTube/Reels', 'yoapy-social-poster' ); ?>
        </option>
        <option value="story" <?php selected( $type, 'story' ); ?>>
            📒 <?php esc_html_e( 'Story (FB/IG)', 'yoapy-social-poster' ); ?>
        </option>
        <option value="live_schedule" <?php selected( $type, 'live_schedule' ); ?>>
            🔴 <?php esc_html_e( 'YouTube Live Schedule', 'yoapy-social-poster' ); ?>
        </option>
    </select>
</p>

<p>
    <?php esc_html_e( 'Networks', 'yoapy-social-poster' ); ?><br>
    <?php
    $network_options = array(
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    );
    
    foreach ( $network_options as $key => $label ) :
    ?>
        <label style="display:inline-block;margin-right:8px">
            <input type="checkbox" name="ysp_networks[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $networks, true ) ); ?>>
            <?php echo esc_html( $label ); ?>
        </label>
    <?php endforeach; ?>
</p>

<p>
    <textarea name="ysp_text" class="widefat" rows="4" placeholder="<?php esc_attr_e( 'Caption / Title', 'yoapy-social-poster' ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
</p>

<p>
    <input type="text" name="ysp_image" id="ysp_image" class="widefat" placeholder="<?php esc_attr_e( 'Image URL', 'yoapy-social-poster' ); ?>" value="<?php echo esc_attr( $image ); ?>"/>
    <button type="button" class="button ysp-pick" data-target="#ysp_image">
        <?php esc_html_e( 'Choose Media (Image)', 'yoapy-social-poster' ); ?>
    </button>
</p>

<p>
    <input type="text" name="ysp_video" id="ysp_video" class="widefat" placeholder="<?php esc_attr_e( 'Video URL', 'yoapy-social-poster' ); ?>" value="<?php echo esc_attr( $video ); ?>"/>
    <button type="button" class="button ysp-pick" data-target="#ysp_video">
        <?php esc_html_e( 'Choose Media (Video)', 'yoapy-social-poster' ); ?>
    </button>
</p>

<p>
    <input type="url" name="ysp_article" class="widefat" placeholder="<?php esc_attr_e( 'Article URL (optional)', 'yoapy-social-poster' ); ?>" value="<?php echo esc_attr( $article ); ?>"/>
</p>

<p>
    <label><?php esc_html_e( 'Schedule (optional)', 'yoapy-social-poster' ); ?></label>
    <input type="datetime-local" name="ysp_when" class="widefat" value="<?php echo esc_attr( $when_local ); ?>"/>
    <small><?php esc_html_e( 'Leave empty to post immediately when published.', 'yoapy-social-poster' ); ?></small>
</p>
